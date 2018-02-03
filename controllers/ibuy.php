<?php
/**
 * 服务产品开通和充值控制器
 */
class IbuyController extends CAdminControl {
	//需要更新缓存和SNS同步操作的学校
	private $sync_crlist = array();
	//需要更新缓存的班级
	private $sync_classlist = array();

	
	/**
	*生成订单信息
	*@param $payfrom 来源
	*/
	private function buildOrder($detail) {
		// $user = Ebh::app()->user->getloginuser();
		// if(empty($user))
			// return FALSE;
		// $itemidlist = $this->input->post('itemid');
		$itemidlist = array($detail['itemid']);
		if(empty($itemidlist))
			return FALSE;
		foreach($itemidlist as $itemid) {	//详情编号必须都为正整数
			if(!is_numeric($itemid) || $itemid <= 0)
				return FALSE;
		}
		$itemidstr = implode(',',$itemidlist);
		$pitemmodel = $this->model('PayItem');
		$itemparam = array('itemidlist'=>$itemidstr);
		$itemlist = $pitemmodel->getItemList($itemparam);
		if(empty($itemlist))
			return FALSE;
		$payordermodel = $this->model('PayOrder');
		$orderparam = array();
		
		$orderparam['dateline'] = SYSTIME;
		$orderparam['ip'] = $this->input->getip();
		$orderparam['uid'] = $detail['uid'];
		$orderparam['payfrom'] = $detail['type'];
		$ordername = '';	//订单名称
		$remark = '';		//订单备注
		$totalfee = 0;
		$comfee = 0;	//公司分到总额
		$roomfee = 0;	//平台分到总额
		$providerfee = 0;	//内容提供商分到总额
		$pid = 0;	//订单所属服务包编号
		for($i = 0; $i < count($itemlist); $i ++) {
			if(!empty($detail['money']) || ($orderparam['payfrom']==5) ){
				$itemlist[$i]['iprice'] = $detail['money'];
			}
			if($orderparam['payfrom']==5){
				$itemlist[$i]['fee'] = $itemlist[$i]['iprice'] = 0;
			}else{
				$itemlist[$i]['fee'] = $itemlist[$i]['iprice'];
			}
			$itemlist[$i]['pid'] = $itemlist[$i]['pid'];
			$pid = $itemlist[$i]['pid'];
			$itemlist[$i]['oname'] = $itemlist[$i]['iname'];
			$itemlist[$i]['omonth'] = $itemlist[$i]['imonth'];
			$itemlist[$i]['oday'] = $itemlist[$i]['iday'];
			$itemlist[$i]['osummary'] = $itemlist[$i]['isummary'];
			$itemlist[$i]['uid'] = $detail['uid'];
			$itemlist[$i]['rname'] = $itemlist[$i]['crname'];
			
			
			if($orderparam['payfrom']==5){
				$itemlist[$i]['roomfee'] = 0;
				$itemlist[$i]['providerfee'] = 0;
				$itemlist[$i]['comfee'] = 0;
			}else{
				$itemlist[$i]['roomfee'] = $itemlist[$i]['roomfee'];
				$itemlist[$i]['providerfee'] = $itemlist[$i]['providerfee'];
				$itemlist[$i]['comfee'] = $itemlist[$i]['comfee'];
				$totalfee += $itemlist[$i]['iprice'];
				$comfee += $itemlist[$i]['comfee'];
				$roomfee += $itemlist[$i]['roomfee'];
				$providerfee += $itemlist[$i]['providerfee'];
			}

			
			if(empty($ordername)) 
				$ordername = $itemlist[$i]['oname'];
			else
				$ordername .= ','.$itemlist[$i]['oname'];
			$theremark = $itemlist[$i]['iname'].'_'.(empty($itemlist[$i]['omonth']) ? $itemlist[$i]['oday'].' 天 _':$itemlist[$i]['omonth'].' 月 _').$itemlist[$i]['fee'].' 元';
			if(empty($remark)) {
				$remark = $theremark;
			} else {
				$remark .= '/'.$theremark;
			}
			$providercrid = $itemlist[$i]['providercrid'];
		}
		$orderparam['crid'] = $itemlist[0]['crid'];
		$orderparam['providercrid'] = $itemlist[0]['providercrid'];	//来源平台crid
		$orderparam['pid'] = $pid;
		$orderparam['itemlist'] = $itemlist;
		if($orderparam['payfrom']==5){
			$orderparam['totalfee'] = 0;
			$orderparam['comfee'] = 0;
			$orderparam['roomfee'] = 0;
			$orderparam['providerfee'] = 0;
		}else{
			$orderparam['totalfee'] = $totalfee;
			$orderparam['comfee'] = $comfee;
			$orderparam['roomfee'] = $roomfee;
			$orderparam['providerfee'] = $providerfee;
		}
		$orderparam['ordername'] = '开通 '.$ordername.' 服务';
		$orderparam['remark'] = $remark;
		$orderid = $payordermodel->addOrder($orderparam);
		if($orderid > 0) {
			$orderparam['orderid'] = $orderid;
			return $orderparam;
		}else{
			return 0;
		}	
	}
	/**
	*支付成功后的订单处理
	*/
	private function notifyOrder($param) {
		$this->sync_crlist = array();//初始化同步学校列表
		$this->sync_classlist = array();//初始化同步班级列表

		//商户订单号
		$orderid = $param['orderid'];
		//交易号
		// $ordernumber = $param['ordernumber'];
		$buyer_id = empty($param['buyer_id'])?'':$param['buyer_id'];
		$buyer_info = empty($param['buyer_info'])?'':$param['buyer_info'];
		Ebh::app()->getDb()->set_con(0);
		$providercrids = array();	//订单下内容提供商的crid列表，如果大于1，需要拆分订单
		$pordermodel = $this->model('PayOrder');

		$myorder = $pordermodel->getOrderById($orderid);
		if(empty($myorder)) {//订单不存在
			return FALSE;
		}
		if($myorder['status'] == 1) {//订单已处理，则不重复处理
			return $myorder;
		}
		// $myorder['detaillist'] = $param['itemlist'];
		//处理订单详情中的内容
		if(empty($myorder['detaillist'])) {
			return FALSE;
		}

		foreach($myorder['detaillist'] as $detail) {
			$detail['uid'] = $myorder['uid'];
			$this->doOrderItem($detail);
			$detailprovidercrid = $detail['providercrid'];
			if(!empty($detailprovidercrid) && !isset($providercrids[$detailprovidercrid]))
				$providercrids[$detailprovidercrid] = $detailprovidercrid;
		}
		$myorder['itemlist'] = $myorder['detaillist'];
		//更新订单状态
		$myorder['status'] = 1;
		$myorder['payip'] = $this->input->getip();
		$myorder['paytime'] = SYSTIME;
		// $myorder['ordernumber'] = $ordernumber;
		$myorder['buyer_id'] = $buyer_id;
		$myorder['buyer_info'] = $buyer_info;

		
		//拆分订单处理，当订单明细的提供商crid不同时，则将订单改成每个订单明细对应一个订单。
		$providercount = count($providercrids);
		if($providercount > 1) {
			for ($i = 0; $i < count($myorder['detaillist']); $i ++) {
				if($i == 0) {
					$myorder['providercrid'] = $myorder['detaillist'][$i]['providercrid'];
					if($myorder['payfrom'] == 5){
						$myorder['totalfee'] = 0;
						$myorder['comfee'] = 0;
						$myorder['roomfee'] = 0;
						$myorder['providerfee'] = 0;
					}else{
						$myorder['totalfee'] = $myorder['detaillist'][$i]['fee'];
						$myorder['comfee'] = $myorder['detaillist'][$i]['comfee'];
						$myorder['roomfee'] = $myorder['detaillist'][$i]['roomfee'];
						$myorder['providerfee'] = $myorder['detaillist'][$i]['providerfee'];
					}
					
					$myorder['ordername'] = '开通 '.$myorder['detaillist'][$i]['oname'].' 服务';
					$myorder['remark'] = $myorder['detaillist'][$i]['oname'].'_'.(empty($myorder['detaillist'][$i]['omonth']) ? $myorder['detaillist'][$i]['oday'].' 天 _':$myorder['detaillist'][$i]['omonth'].' 月 _').$myorder['detaillist'][$i]['fee'].' 元';
				} else {
					$neworder = $myorder;
					if($myorder['payfrom'] == 5){
						$neworder['totalfee'] = 0;
						$neworder['comfee'] = 0;
						$neworder['roomfee'] = 0;
						$neworder['providerfee'] = 0;
					}else{
						$neworder['totalfee'] = $myorder['detaillist'][$i]['fee'];
						$neworder['comfee'] = $myorder['detaillist'][$i]['comfee'];
						$neworder['roomfee'] = $myorder['detaillist'][$i]['roomfee'];
						$neworder['providerfee'] = $myorder['detaillist'][$i]['providerfee'];
					}
					$neworder['providercrid'] = $myorder['detaillist'][$i]['providercrid'];
					
					$neworder['ordername'] = '开通 '.$myorder['detaillist'][$i]['oname'].' 服务';
					$neworder['remark'] = $myorder['detaillist'][$i]['oname'].'_'.(empty($myorder['detaillist'][$i]['omonth']) ? $myorder['detaillist'][$i]['oday'].' 天 _':$myorder['detaillist'][$i]['omonth'].' 月 _').$myorder['detaillist'][$i]['fee'].' 元';
					$neworderid = $pordermodel->addOrder($neworder,TRUE);
					$myorder['detaillist'][$i]['orderid'] = $neworderid;
				}
			}
		}

		$myorder['itemlist'] = $myorder['detaillist'];

		$res = $pordermodel->updateOrder($myorder);

		//更新学校学生缓存和同步SNS数据
		if (!empty($this->sync_crlist))
		{
			foreach ($this->sync_crlist as $crid) {
				//更新学校学生缓存
				Ebh::app()->lib('Sns')->updateRoomUserCache(array('crid'=>$crid,'uid'=>$myorder['uid']));
				//同步SNS数据(网校操作)
				Ebh::app()->lib('Sns')->do_sync($myorder['uid'], 4);
			}
		}
		//更新班级学生缓存
		if (!empty($this->sync_classlist))
		{
			foreach ($this->sync_classlist as $classid)
			{
				//更新班级学生缓存
				Ebh::app()->lib('Sns')->updateClassUserCache(array('classid'=>$classid,'uid'=>$myorder['uid']));
			}
		}

		//记录日志		
		if ($res)
		{
			$user_info = $this->model('ebhuser')->getuserbyuid($myorder['uid']);
			$pack_info = $this->model('paypackage')->getPackByPid($myorder['pid']);
			admin_log('用户管理', '开通服务', $user_info['username'], $myorder['uid'], $myorder['ordername'] . ' (所属网校：' . $pack_info['crname'] . ' 所属服务包：' . $pack_info['pname'] . ')');
		}
		return $res;
	}
	/**
	*支付成功后处理订单详情（主要为生成权限）
	*/
	private function doOrderItem($orderdetail) {
		$crid = $orderdetail['crid'];
		$folderid = $orderdetail['folderid'];
		$uid = $orderdetail['uid'];
		$omonth= $orderdetail['omonth']; 
		$oday= $orderdetail['oday']; 
		
		$roommodel = $this->model('Classroom');
		$roominfo = $roommodel->getRoomByCrid($crid);
		if(empty($roominfo))
			return FALSE;
		$usermodel = $this->model('Ebhuser');
		$user = $usermodel->getuserbyuid($uid);
		if(empty($user))
			return FALSE;
		//获取用户是否在此平台
		$rumodel = $this->model('Roomuser');
		$ruser = $rumodel->getroomuserdetail($crid,$uid);
		$type = 0;
		if(empty($ruser)) {	//不存在 
			$enddate = 0;
			if(!empty($crid)) {
				if(!empty($omonth)) {
					$enddate = strtotime("+$omonth month");
				} else {
					$enddate = strtotime("+$oday day");
				}
			}
			$param = array('crid'=>$crid,'uid'=>$user['uid'],'begindate'=>SYSTIME,'enddate'=>$enddate,'cnname'=>$user['realname'],'sex'=>$user['sex']);
			$result = $rumodel->insert($param);
			$type = 1;
			if($result !== FALSE) {
				if($roominfo['isschool'] == 6 || $roominfo['isschool'] == 7) {	//如果是收费学校，则会将账号默认添加到学校的第一个班级中
					$this->setmyclass($crid,$user['uid']);
				} else {
					//更新教室学生数
					$roommodel->addstunum($crid);
				}
				//记录需要更新缓存和SNS同步操作的学校项目
				$this->sync_crlist[] = $crid;
			}
		} else {	//已存在
			if($roominfo['isschool'] == 6 || $roominfo['isschool'] == 7){
				$this->setmyclass($roominfo['crid'],$user['uid']);//防止中途改变学校类型,导致学生在学校里面但是不在班级里面(网校改成学校) zkq 2014.07.22
			}
			$enddate=$ruser['enddate'];
			$newenddate=0;
			if(!empty($crid)) {
				if(!empty($omonth)) {
					if(SYSTIME>$enddate){//已过期的处理
						$newenddate=strtotime("+$omonth month");
					}else{	//未过期，则直接在结束时间后加上此时间
						$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." +$omonth month");
					}
				}else {
					if(SYSTIME>$enddate){//已过期的处理
						$newenddate=strtotime("+$oday day");
					}else{	//未过期，则直接在结束时间后加上此时间
						$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." +$oday day");
					}
				}
			}
			$param = array('crid'=>$crid,'uid'=>$user['uid'],'enddate'=>$newenddate,'cstatus'=>1);
			$result = $rumodel->update($param);
			$type = 2;
		}
		//处理用户权限
		$userpmodel = $this->model('UserPermission');
		if(empty($orderdetail['folderid'])) {
			$myperm = $userpmodel->getPermissionByItemId($orderdetail['itemid'],$uid);
		} else {
			$myperm = $userpmodel->getPermissionByFolderId($orderdetail['folderid'],$uid);
		}
		$startdate = 0;
		$enddate = 0;
		if(empty($myperm)) {	//不存在则添加权限，否则更新
			$startdate = SYSTIME;
			if(!empty($omonth)) {
				$enddate = strtotime("+$omonth month");
			} else {
				$enddate = strtotime("+$oday day");
			}
			$ptype = 0;
			if(!empty($folderid) || !empty($crid)) {
				$ptype = 1;
			}
			$perparam = array('itemid'=>$orderdetail['itemid'],'type'=>$ptype,'uid'=>$uid,'crid'=>$crid,'folderid'=>$folderid,'startdate'=>$startdate,'enddate'=>$enddate);
			$result = $userpmodel->addPermission($perparam);
		} else {
			$enddate=$myperm['enddate'];
			$newenddate=0;
			if(!empty($omonth)) {
				if(SYSTIME>$enddate){//已过期的处理
					$newenddate=strtotime("+$omonth month");
				}else{	//未过期，则直接在结束时间后加上此时间
					$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." +$omonth month");
				}
			}else {
				if(SYSTIME>$enddate){//已过期的处理
					$newenddate=strtotime("+$oday day");
				}else{	//未过期，则直接在结束时间后加上此时间
					$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." +$oday day");
				}
			}
			$enddate = $newenddate;
			$myperm['enddate'] = $enddate;
			if(!empty($orderdetail['itemid'])) {
				$myperm['itemid'] = $orderdetail['itemid'];
			}
			$result = $userpmodel->updatePermission($myperm);
		}
		//用户平台信息更新成功则生成记录并更新年卡信息
		return $result;
	}
	
	

	/**
	*设置用户的默认班级信息
	* 一般为收费学校用户开通学校服务时候处理，需要将学生加入到默认的班级中
	* 如果不存在新班级，则需要创建一个默认班级
	*/
	private function setmyclass($crid,$uid) {
		$classmodel = $this->model('Classes');
		//先判断是否已经加入班级，已经加入则无需重新加入
		$myclass = $classmodel->getClassByUid($crid,$uid);
		if(empty($myclass)) {
			$classid = 0;
			$defaultclass = $classmodel->getDefaultClass($crid);
			if(empty($defaultclass)) {	//不存在默认班级，则创建默认班级
				$param = array('crid'=>$crid,'classname'=>'默认班级');
				$classid = $classmodel->addclass($param);
			} else {
				$classid = $defaultclass['classid'];
			}
			$param = array('crid'=>$crid,'classid'=>$classid,'uid'=>$uid);
			$classmodel->addclassstudent($param);

			//记录需要更新缓存的班级项目
			$this->sync_classlist[] = $classid;
		}
	}
	public function bank() {
		$room = Ebh::app()->room->getcurroom();
		$this->assign('room',$room);
		$this->display('common/classactive_bank');
	}

	public function manualnotify($orderdetail = array()){
		// EBH::app()->getDb()->begin_trans();
		if(empty($orderdetail)){
			$orderdetail = $this->input->post();
		}else{
			$tag = true;
			$orderdetail = $orderdetail;
		}
		
		//检查是否有该学校权限
		if (!Ebh::app()->lib('Access')->checkClassroomAccess($orderdetail['crid']))
		{
			//没有权限直接返回开通失败
			if(empty($tag)){
				echo 0;
				exit;
			}else{
				return 0;
			}
		}
		//生成订单
		$orderparam = $this->buildOrder($orderdetail);
		if(empty($orderparam)){
			if(empty($tag)){
				echo 0;exit;
			}else{
				return 0;
			}
			
		}

		//处理订单
		
			
		if(empty($tag)){
			echo $this->notifyOrder($orderparam);
		}else{
			return $this->notifyOrder($orderparam);
		}
		
		// EBH::app()->getDb()->commit_trans();
	}

	

	/**
	 *退款操作
	 */
	public function refund(){
		$info = array();
		$orderid = intval($this->input->post('orderid'));
		$money = floatval($this->input->post('money'));
		$real = intval($this->input->post('real'));
		$payordermodel = $this->model('payorder');
		//判断是否是已退款订单
		$oldpayorder = $payordermodel->getOrderById($orderid);
		if($oldpayorder['refunded']==2){
			$info['msg'] = '已经退款';
			$info['status'] = '-3';
			echo json_encode($info);
			exit;
		}
		//将原订单改为等待退款的订单
		$payordermodel->updateOrder(array('orderid'=>$orderid,'refunded'=>1));

		$payorder = $payordermodel->getOrderById($orderid);
		if($money>$payorder['totalfee']){
			$info['msg'] = '退款金额超过支付金额';
			$info['status'] = '-1';
			echo json_encode($info);
			exit;
		}
		$remark = $payorder['remark'].'-'.'[退款金额:'.$money.']';

		$payorder['sourceid'] = $payorder['orderid'];
		$payorder['totalfee'] = -$money;
		$payorder['comfee'] = -$payorder['comfee'];
		$payorder['roomfee'] = -$payorder['roomfee'];
		$payorder['providerfee'] = -$payorder['providerfee'];
		$payorder['remark'] = '退款'.$money.'元';
		$payorder['status'] = 0;
		$payorder['itemlist'] = array();

		foreach ($payorder['detaillist'] as $detail) {
			$detail['fee'] = -$detail['fee'];
			$detail['comfee'] = -$detail['comfee'];
			$detail['roomfee'] = -$detail['roomfee'];
			$detail['providerfee'] = -$detail['providerfee'];
			$detail['osummary'] = '退款';
			$detail['uid'] = $payorder['uid'];
			$detail['refunded'] = 2;
			$detail['pid'] = $payorder['pid'];
			$payorder['itemlist'][] = $detail;
		}

		$orderid = $payordermodel->addOrder($payorder);
		if($orderid){
			$param = array(
				'orderid'=>$orderid,
				'status'=>1,
				'refunded'=>2,
				// 'itemlist'=>$payorder['itemlist']
			); 
			$tag = true;
			if(($payorder['payfrom']==6||$payorder['payfrom']==7)){
				if($real==1){
					$reparam = array(
						'TrxAmount'=>$money,
						'OrderNo'=>$payorder['orderid'],
						'NewOrderNo'=>$orderid,
						'PayFrom'=>$payorder['payfrom']
					);
				$tag = EBH::app()->lib('Abcpay2')->refund($reparam);
				}
				
			}
			if($tag==false){
				$info['msg'] = '银行未退款';
				$info['status'] = '-4';
				echo json_encode($info);
				exit;
			}

			foreach ($payorder['itemlist'] as $item) {
				$this->_refund($item);
			}

			$res = $payordermodel->updateOrder($param);
			if($res){
				$info['msg'] = '退款成功';
				$info['status'] = '1';
			}else{
				$info['msg'] = '订单修改失败';
				$info['status'] = '-2';
			}

			//将原订单改为已退款订单
			$oldorderid = intval($this->input->post('orderid'));
			$payordermodel->updateOrder(array('orderid'=>$oldorderid,'refunded'=>2));
			$payordermodel->setPayDetailStatus(array('dstatus'=>1),array('orderid'=>$orderid));
			echo json_encode($info);
			
			
		}
	}

	/**
	 *清除权限
	 */
	private function _refund($item){

		$crid = $item['crid'];
		$folderid = $item['folderid'];
		$omonth = $item['omonth'];
		$oday = $item['oday'];

		$rumodel = $this->model('Roomuser');
		$ruser = $rumodel->getroomuserdetail($item['crid'],$item['uid']);
		$enddate=$ruser['enddate'];
		$newenddate=0;
		if(!empty($crid)) {
			if(!empty($omonth)) {
				if(SYSTIME>$enddate){//已过期的处理
					$newenddate=strtotime("-$omonth month");
				}else{	//未过期，则直接在结束时间后加上此时间
					$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." -$omonth month");
				}

			}else {
				if(SYSTIME>$enddate){//已过期的处理
					$newenddate=strtotime("-$oday day");
				}else{	//未过期，则直接在结束时间后加上此时间
					$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." -$oday day");
				}
			}
		}
		$param = array('crid'=>$crid,'uid'=>$item['uid'],'enddate'=>$newenddate,'cstatus'=>1);
		$result = $rumodel->update($param);


		$userpmodel = $this->model('UserPermission');
		$myperm = $userpmodel->getPermissionByItemId($item['itemid'],$item['uid']);
		$startdate = 0;
		$enddate = 0;

		$enddate=$myperm['enddate'];
		$newenddate=0;
		if(!empty($omonth)) {
			if(SYSTIME>$enddate){//已过期的处理
				$newenddate=strtotime("-$omonth month");
			}else{	//未过期，则直接在结束时间后加上此时间
				$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." -$omonth month");
			}
		}else {
			if(SYSTIME>$enddate){//已过期的处理
				$newenddate=strtotime("-$oday day");
			}else{	//未过期，则直接在结束时间后加上此时间
				$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." -$oday day");
			}
		}
		$enddate = $newenddate;
		$myperm['enddate'] = $enddate;
		$result = $userpmodel->updatePermission($myperm);

		return $result;
	}

	/**
	 *获取银行订单详情
	 *@param int $orderid
	 *@retuern string
	 */
	public function getOrder(){
		$orderid = intval($this->input->post('orderid'));

		$payordermodel = $this->model('payorder');
		$payorder = $payordermodel->getOrderById($orderid);

		if(empty($payorder)){
			echo '订单不存在';exit;
		}

		$payfrom = intval($payorder['payfrom']);
		if($payfrom==6||$payfrom==7){
			EBH::app()->lib('Abcpay2')->getOrder($orderid,1,$payfrom);
		}else{
			echo '非银行订单';
		}
		
		
	}
	/**
	 *批量开通服务处理
	 */
	public function manualnotify_all(){
		$orders = $this->input->post('dataStroage');
		$res = array();
		foreach ($orders as $order) {
			$res[$order['itemid']] = $this->manualnotify($order);
		}
		echo json_encode($res);
	}
}
?>
