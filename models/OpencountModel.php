<?php
/*
开通和支付服务相关Model类
*/
class OpencountModel extends CEbhModel{
	/**
	*插入支付记录，默认情况下为未支付记录
	*/
	public function insert($param) {
		$setarr = array ();
		if(!empty($param ['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param ['username'])){
			$setarr['username'] = $param['username'];
		}
		if(!empty($param ['realname'])){
			$setarr['realname'] = $param['realname'];
		}
		if(!empty($param ['sex'])){
			$setarr['sex'] = intval($param['sex']);
		}
		if(!empty($param ['birthday'])){
			$setarr['birthday'] = intval($param['birthday']);
		}
		if(!empty($param ['mobile'])){
			$setarr['mobile'] = $param['mobile'];
		}
		if(!empty($param ['email'])){
			$setarr['email'] = $param['email'];
		}
		if(!empty($param ['school'])){
			$setarr['school'] = $param['school'];
		}
		if(!empty($param ['grade'])){
			$setarr['grade'] = $param['grade'];
		}
		if(!empty($param ['class'])){
			$setarr['class'] = $param['class'];
		}
		if(!empty($param ['citycode'])){
			$setarr['citycode'] = $param['citycode'];
		}
		if(!empty($param ['address'])){
			$setarr['address'] = $param['address'];
		}
		if(!empty($param ['password'])){
			$setarr['password'] = $param['password'];
		}
		if(!empty($param ['type'])){
			$setarr['type'] = intval($param['type']);//type{1 为激活，2为充值}
		}
		if(!empty($param ['paytime'])){
			$setarr['paytime'] = intval($param['paytime']);
		}
		if(!empty($param ['ordernumber'])){
			$setarr['ordernumber'] = $param['ordernumber'];
		}
		if(!empty($param ['addtime'])){
			$setarr['addtime'] = intval($param['addtime']);
		}
		if(!empty($param ['status'])){
			$setarr['status'] = $param ['status'];
		}else{
			$setarr['status'] = 0;
		}
		if(!empty($param ['money'])){
			$setarr['money'] = $param['money'];
		}else{
			$setarr['money'] = 0;
		}
		if(!empty($param ['ip'])){
			$setarr['ip'] = $param['ip'];
		}
		if(!empty($param ['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}else{
			$setarr['dateline'] = SYSTIME;
		}	
		if(!empty($param ['crid'])){
			$setarr['crid'] = $param ['crid'];
		}
		if(!empty($param ['payfrom'])){	//支付来源 1为年卡 2为快钱 3为支付宝 
			$setarr['payfrom'] = $param ['payfrom'];
		}
		if(!empty($param ['paycode'])){	//支付交易号，适用于快钱和支付宝交易
			$setarr['paycode'] = $param ['paycode'];
		}
		if(!empty($param ['bankid'])){	//银行代码，适用于快钱
			$setarr['bankid'] = $param ['bankid'];
		}
		
		return $this->ebhdb->insert('ebh_tempstudents',$setarr);
	}
	/**
	* 更新支付记录
	* 在支付完成后可更新记录
	*/
	function update($param,$wherearr=array()) {
		if(empty($wherearr)) {
			return FALSE;
		}
		$setarr = array();
		if(!empty($param ['uid'])){
			$setarr['uid'] = $param['uid'];
		}
		if(!empty($param ['username'])){
			$setarr['username'] = $param['username'];
		}
		if(!empty($param ['realname'])){
			$setarr['realname'] = $param['realname'];
		}
		if(!empty($param ['sex'])){
			$setarr['sex'] = intval($param['sex']);
		}
		if(!empty($param ['birthday'])){
			$setarr['birthday'] = intval($param['birthday']);
		}
		if(!empty($param ['mobile'])){
			$setarr['mobile'] = $param['mobile'];
		}
		if(!empty($param ['email'])){
			$setarr['email'] = $param['email'];
		}
		if(!empty($param ['school'])){
			$setarr['school'] = $param['school'];
		}
		if(!empty($param ['grade'])){
			$setarr['grade'] = $param['grade'];
		}
		if(!empty($param ['class'])){
			$setarr['class'] = $param['class'];
		}
		if(!empty($param ['citycode'])){
			$setarr['citycode'] = $param['citycode'];
		}
		if(!empty($param ['address'])){
			$setarr['address'] = $param['address'];
		}
		if(!empty($param ['status'])){
			$setarr['status'] = intval($param['status']);
		}
		if(!empty($param ['dateline'])){
			$setarr['dateline'] = intval($param['dateline']);
		}
		if(!empty($param ['ordernumber'])){
			$setarr['ordernumber'] = $param['ordernumber'];
		}
		if(!empty($param ['money'])){
			$setarr['money'] = $param['money'];
		}
		if(!empty($param ['addtime'])){
			$setarr['addtime'] = intval($param['addtime']);
		}
		if(!empty($param ['paytime'])){
			$setarr['paytime'] = intval($param['paytime']);
		}
		if(!empty($param ['payfrom'])){	//支付来源 1为年卡 2为快钱 3为支付宝 
			$setarr['payfrom'] = $param ['payfrom'];
		}
		if(!empty($param ['type'])){	//支付类型，1 为激活，2为充值
			$setarr['type'] = $param ['type'];
		}
		if(!empty($param ['paycode'])){	//支付交易号，适用于快钱和支付宝交易
			$setarr['paycode'] = $param ['paycode'];
		}
		if(!empty($param ['bankid'])){	//银行代码，适用于快钱
			$setarr['bankid'] = $param ['bankid'];
		}
		return $this->ebhdb->update('ebh_tempstudents',$setarr,$wherearr);
		
	}
	/**
	*删除记录
	*/
	function delete($wherearr = array()){
		if(empty($wherearr))
			return FALSE;
		return $this->ebhdb->delete('ebh_tempstudents',$wherearr);
	}
	/**
	*根据编号获取订单详情
	*/
	public function getOpenCountByStid($stid) {
		$sql = "select t.username,t.crid,t.dateline,t.addtime,t.status from ebh_tempstudents t where t.stid=$stid";
		return $this->ebhdb->query($sql)->row_array();
	}
	/*
	开通列表
	@param array $param
	@return array
	*/
	public function getopencountlist($param){
		$sql = 'select t.username,c.crname,t.ordernumber,t.money,t.dateline,t.addtime,t.payfrom,t.realname from ebh_tempstudents t left join ebh_classrooms c on t.crid=c.crid ';
		$wherearr[]= 't.status =1';
		if(!empty($param['q']))
			$wherearr[]= '  (t.username like \'%'. $this->ebhdb->escape_str($param['q']) .'%\' or c.crname like \'%' . $this->ebhdb->escape_str($param['q']) .'%\')';
		if(!empty($param['crid'])){
			if(empty($param['upid']))
				$wherearr[]='c.crid='.intval($param['crid']);
			else
				$wherearr[] = '(c.crid='.intval($param['crid']).' or c.upid='.intval($param['upid']).')';
		}
		if(!empty($param['addtime'])){
			$wherearr[]='t.addtime='.intval($param['addtime']);
		}
		if(!empty($param['ordernumber'])){
			$wherearr[]='t.ordernumber='.intval($param['ordernumber']);
		}
		if(!empty($param['payfrom'])){
			$wherearr[]='t.payfrom='.intval($param['payfrom']);
		}
		if(!empty($param['ordernumber'])){
			$wherearr[]='t.ordernumber='.intval($param['ordernumber']);
		}
		if(!empty($param['begintime'])){
			$wherearr[]='t.dateline>='.intval($param['begintime']);
		}
		if(!empty($param['endtime'])){
			$wherearr[]='t.dateline<='.intval($param['endtime']);
		}
		if(!empty($wherearr))
			$sql.= ' where ' .implode(' AND ',$wherearr);
		$sql.=' order by t.stid desc';
		if(!empty($param['limit']))
			$sql.= ' limit ' . $param['limit'];
		//var_dump($sql);
		return $this->ebhdb->query($sql)->list_array();
	}
	/*
	开通数量
	@param array $param
	@return int
	*/
	public function getopencountcount($param){
		$sql = 'select count(*) count from ebh_tempstudents t left join ebh_classrooms c on t.crid=c.crid ';
		$wherearr[]= 't.status =1';
		if(isset($param['q']))
			$wherearr[]= '  (t.username like \'%'. $this->ebhdb->escape_str($param['q']) .'%\' or c.crname like \'%' . $this->ebhdb->escape_str($param['q']) .'%\')';
		if(!empty($param['crid'])){
			$wherearr[]='c.crid='.intval($param['crid']);
		}
		if(!empty($param['addtime'])){
			$wherearr[]='t.addtime='.intval($param['addtime']);
		}
		if(!empty($param['ordernumber'])){
			$wherearr[]='t.ordernumber='.intval($param['ordernumber']);
		}
		if(!empty($param['payfrom'])){
			$wherearr[]='t.payfrom='.intval($param['payfrom']);
		}
		if(!empty($param['ordernumber'])){
			$wherearr[]='t.ordernumber='.intval($param['ordernumber']);
		}
		if(!empty($param['begintime'])){
			$wherearr[]='t.dateline>='.intval($param['begintime']);
		}
		if(!empty($param['endtime'])){
			$wherearr[]='t.dateline<='.intval($param['endtime']);
		}
		if(!empty($wherearr))
			$sql.= ' where ' .implode(' AND ',$wherearr);
		//var_dump($sql);
		$count = $this->ebhdb->query($sql)->row_array();
		return $count['count'];
	}
}

?>