<?php
/**
 * 平台开通和充值控制器
 */
class ClassactiveController extends CAdminControl {

	
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
		}
	}
	/**
	 *后台人工开通网校方法
	 */
	public function manualnotify(){
		//教室crid
		$crid = $this->input->post('crid');
		//检查是否有该学校权限
		if (!Ebh::app()->lib('Access')->checkClassroomAccess($crid))
		{
			echo 0;//没有权限直接返回开通失败
		}
		//用户uid
		$uid = $this->input->post('uid');
		//开通月数
		$addtime = $this->input->post('month');
		//开通类型
		$type = $this->input->post('type');
		if(empty($type))$type=4;
		//用户付款金额数
		$money = $this->input->post('money');
		if($money>10000000)return 0;
		$roominfo = $this->model('classroom')->getclassroomdetail($crid);
		$openmodel = $this->model('Opencount');
		
		$usermodel = $this->model('Ebhuser');
		$user = $usermodel->getuserbyuid($uid);
		//获取用户是否在此平台
		$rumodel = $this->model('Roomuser');//
		$ruser = $rumodel->getroomuserdetail($crid,$user['uid']);
		$cardmonth = $addtime;	//充值的服务周期，一般为12个月
		if(empty($ruser)) {	//不存在 
			$enddate = strtotime("+$cardmonth month");
			$param = array('crid'=>$crid,'uid'=>$user['uid'],'begindate'=>SYSTIME,'enddate'=>$enddate,'cnname'=>$user['realname'],'sex'=>$user['sex']);
			$result = $rumodel->insert($param);
			if($result !== FALSE) {
				if($roominfo['isschool'] == 6 || $roominfo['isschool'] == 7) {	//如果是收费学校，则会将账号默认添加到学校的第一个班级中

					$this->setmyclass($roominfo['crid'],$user['uid']);
				} else {
					//更新教室学生数
					$roommodel = $this->model('Classroom');
					$roommodel->addstunum($roominfo['crid']);
				}
			}
		} else {
			if($roominfo['isschool'] == 6 || $roominfo['isschool'] == 7) {
				$this->setmyclass($crid,$uid);//防止中途改变学校类型,导致学生在学校里面但是不在班级里面(网校改成学校) zkq 2014.07.22
			}
			$enddate=$ruser['enddate'];
			$newenddate=0;
			if(SYSTIME>$enddate){//已过期的处理
				$newenddate=strtotime("+$cardmonth month");
			}else{	//未过期，则直接在结束时间后加上此时间
				$newenddate=strtotime( date('Y-m-d H:i:s',$enddate)." +$cardmonth month");
			}

			$param = array('crid'=>$crid,'uid'=>$user['uid'],'enddate'=>$newenddate,'cstatus'=>1);

			$result = $rumodel->update($param);
		}
		//用户平台信息更新成功则生成记录并更新年卡信息
		if($result !== FALSE) {
			$ordernumber = $type==4?'MANUAL_'.strtoupper(uniqid()):'INNERTEST_'.strtoupper(uniqid());
			$openmodel = $this->model('Opencount');
			$openparam = array('username'=>$user['username'],'realname'=>$user['realname'],'money'=>$money,'type'=>$type,'paytime'=>SYSTIME,'addtime'=>$cardmonth,'status'=>1,'ip'=>$this->input->getip(),'crid'=>$crid,'payfrom'=>$type,'ordernumber'=>$ordernumber);
			$insid = $openmodel->insert($openparam);
			if($insid>0){
				admin_log('用户管理', '开通网校', '用户', $uid, '开通 '.$roominfo['crname'].':' . $crid . ' ' . $cardmonth.'个月');
				echo 1;
			}
		} else {	//更新失败
			echo 0;
		}
	}
}
?>
