<?php
/*
服务包订单
*/
class PayorderModel extends CEbhModel{
	/**
	*根据订单明细内容生成订单信息
	*$noitemlist 如果为TRUE，则允许订单明细为空，默认不允许
	*/
	public function addOrder($param = array(),$noitemlist = FALSE) {
		if(empty($param) || (empty($param['itemlist']) && !$noitemlist))
			return FALSE;
		$setarr = array();
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(!empty($param['providercrid']))
			$setarr['providercrid'] = $param['providercrid'];
		if(!empty($param['ordername']))
			$setarr['ordername'] = $param['ordername'];
		if(!empty($param['sourceid']))
			$setarr['sourceid'] = $param['sourceid'];
		if(!empty($param['pid']))
			$setarr['pid'] = $param['pid'];
		if(!empty($param['uid']))
			$setarr['uid'] = $param['uid'];
		if(!empty($param['paytime']))
			$setarr['paytime'] = $param['paytime'];
		if(!empty($param['payfrom']))
			$setarr['payfrom'] = $param['payfrom'];
		if(!empty($param['totalfee']))
			$setarr['totalfee'] = $param['totalfee'];
		if(!empty($param['comfee']))
			$setarr['comfee'] = $param['comfee'];
		if(!empty($param['roomfee']))
			$setarr['roomfee'] = $param['roomfee'];
		if(!empty($param['providerfee']))
			$setarr['providerfee'] = $param['providerfee'];
		if(!empty($param['ip']))
			$setarr['ip'] = $param['ip'];
		if(!empty($param['payip']))
			$setarr['payip'] = $param['payip'];
		if(!empty($param['paycode']))
			$setarr['paycode'] = $param['paycode'];
		if(!empty($param['bankid']))
			$setarr['bankid'] = $param['bankid'];
		if(!empty($param['buyer_id']))
			$setarr['buyer_id'] = $param['buyer_id'];
		if(!empty($param['buyer_info']))
			$setarr['buyer_info'] = $param['buyer_info'];
		if(!empty($param['remark']))
			$setarr['remark'] = $param['remark'];
		if(!empty($param['ordernumber']))
			$setarr['ordernumber'] = $param['ordernumber'];
		if(!empty($param['status']))
			$setarr['status'] = $param['status'];
		if(!empty($param['dateline']))
			$setarr['dateline'] = $param['dateline'];
		else 
			$setarr['dateline'] = SYSTIME;
		if(!empty($param['refunded']))
			$setarr['refunded'] = $param['refunded'];
		$orderid = $this->ebhdb->insert('ebh_pay_orders',$setarr);
		if($orderid > 0 && !empty($param['itemlist'])) {	//处理订单明细
			foreach($param['itemlist'] as $item) {
				$item['orderid'] = $orderid;
				if(!empty($param['status']))
					$item['dstatus'] = $param['status'];
				$detailid = $this->addOrderDetail($item);
			}
		}
		return $orderid;
	}

	/**
	*更新订单信息，如果包含明细，则同时更新明细信息
	*/
	public function updateOrder($param = array()) {
		if(empty($param) || empty($param['orderid']))
			return FALSE;
		$setarr = array();
		$wherearr = array('orderid'=>$param['orderid']);
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(!empty($param['providercrid']))
			$setarr['providercrid'] = $param['providercrid'];
		if(!empty($param['ordername']))
			$setarr['ordername'] = $param['ordername'];
		if(!empty($param['paytime']))
			$setarr['paytime'] = $param['paytime'];
		if(!empty($param['payfrom']))
			$setarr['payfrom'] = $param['payfrom'];
		if(!empty($param['totalfee']))
			$setarr['totalfee'] = $param['totalfee'];
		if(!empty($param['comfee']))
			$setarr['comfee'] = $param['comfee'];
		if(!empty($param['roomfee']))
			$setarr['roomfee'] = $param['roomfee'];
		if(!empty($param['providerfee']))
			$setarr['providerfee'] = $param['providerfee'];
		if(!empty($param['ip']))
			$setarr['ip'] = $param['ip'];
		if(!empty($param['payip']))
			$setarr['payip'] = $param['payip'];
		if(!empty($param['paycode']))
			$setarr['paycode'] = $param['paycode'];
		if(!empty($param['bankid']))
			$setarr['bankid'] = $param['bankid'];
		if(!empty($param['buyer_id']))
			$setarr['buyer_id'] = $param['buyer_id'];
		if(!empty($param['buyer_info']))
			$setarr['buyer_info'] = $param['buyer_info'];
		if(!empty($param['remark']))
			$setarr['remark'] = $param['remark'];
		if(!empty($param['ordernumber']))
			$setarr['ordernumber'] = $param['ordernumber'];
		if(isset($param['status']))
			$setarr['status'] = $param['status'];
		if(!empty($param['refunded']))
			$setarr['refunded'] = $param['refunded'];
		$afrows = $this->ebhdb->update('ebh_pay_orders',$setarr,$wherearr);
		if($afrows !== FALSE&&(!empty($param['itemlist']))) {	//处理订单明细
			foreach($param['itemlist'] as $item) {
				if(isset($param['status']))
					$item['dstatus'] = $param['status'];
				$dafrows = $this->updateOrderDetail($item);
			}
		}
		return $afrows;
	}
	/**
	*根据订单编号获取订单和订单详情信息
	*/
	public function getOrderById($orderid) {
		$sql = "select o.orderid,o.ordername,o.refunded,o.crid,o.uid,o.dateline,o.paytime,o.payfrom,o.totalfee,o.ip,o.payip,o.paycode,o.ordernumber,o.bankid,o.remark,o.status,o.pid,o.providercrid,o.comfee,o.roomfee,o.providerfee from ebh_pay_orders o where o.orderid=$orderid";
		$myorder = $this->ebhdb->query($sql)->row_array();
		if(!empty($myorder)) {
			$myorder['detaillist'] = $this->getOrderDetailListByOrderId($orderid);
		}
		return $myorder;
	}

	/**
	*添加订单明细
	*/
	public function addOrderDetail($param) {
		if(empty($param) || empty($param['orderid'])) 
			return FALSE;
		$setarr = array();
		if(!empty($param['orderid']))
			$setarr['orderid'] = $param['orderid'];
		if(!empty($param['pid']))
			$setarr['pid'] = $param['pid'];
		if(!empty($param['uid']))
			$setarr['uid'] = $param['uid'];
		if(!empty($param['itemid']))
			$setarr['itemid'] = $param['itemid'];
		if(!empty($param['fee']))
			$setarr['fee'] = $param['fee'];
		if(!empty($param['comfee']))
			$setarr['comfee'] = $param['comfee'];
		if(!empty($param['roomfee']))
			$setarr['roomfee'] = $param['roomfee'];
		if(!empty($param['providerfee']))
			$setarr['providerfee'] = $param['providerfee'];
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(!empty($param['providercrid']))
			$setarr['providercrid'] = $param['providercrid'];
		if(!empty($param['folderid']))
			$setarr['folderid'] = $param['folderid'];
		if(!empty($param['rname']))
			$setarr['rname'] = $param['rname'];
		if(!empty($param['oname']))
			$setarr['oname'] = $param['oname'];
		if(!empty($param['omonth']))
			$setarr['omonth'] = $param['omonth'];
		if(!empty($param['oday']))
			$setarr['oday'] = $param['oday'];
		if(!empty($param['osummary']))
			$setarr['osummary'] = $param['osummary'];
		if(isset($param['dstatus']))
			$setarr['dstatus'] = $param['dstatus'];
		if(empty($setarr))
			return FALSE;
		$detailid = $this->ebhdb->insert('ebh_pay_orderdetails',$setarr);
		return $detailid;
	}
	/**
	*修改订单明细
	*/
	public function updateOrderDetail($item) {
		if(empty($item) || empty($item['detailid'])) 
			return FALSE;
		$setarr = array();
		$wherearr = array('detailid'=>$item['detailid']);
		if(!empty($item['orderid']))
			$setarr['orderid'] = $item['orderid'];
		if(!empty($item['fee']))
			$setarr['fee'] = $item['fee'];
		if(!empty($item['comfee']))
			$setarr['comfee'] = $item['comfee'];
		if(!empty($item['roomfee']))
			$setarr['roomfee'] = $item['roomfee'];
		if(!empty($item['providerfee']))
			$setarr['providerfee'] = $item['providerfee'];
		if(!empty($item['crid']))
			$setarr['crid'] = $item['crid'];
		if(!empty($item['providercrid']))
			$setarr['providercrid'] = $item['providercrid'];
		if(!empty($item['folderid']))
			$setarr['folderid'] = $item['folderid'];
		if(!empty($item['oname']))
			$setarr['oname'] = $item['oname'];
		if(!empty($item['omonth']))
			$setarr['omonth'] = $item['omonth'];
		if(!empty($item['oday']))
			$setarr['oday'] = $item['oday'];
		if(isset($item['dstatus']))
			$setarr['dstatus'] = $item['dstatus'];
		if(empty($setarr))
			return FALSE;
		$afrows = $this->ebhdb->update('ebh_pay_orderdetails',$setarr,$wherearr);
		return $afrows;
	}
	/**
	*根据订单编号获取订单详情
	*/
	public function getOrderDetailListByOrderId($orderid) {
		$sql = "select d.detailid,d.orderid,d.itemid,d.fee,d.crid,d.folderid,d.oname,d.osummary,d.omonth,d.oday,d.rname,d.providercrid,d.comfee,d.roomfee,d.providerfee from ebh_pay_orderdetails d where d.orderid=$orderid";
		return $this->ebhdb->query($sql)->list_array();
	}
	
	public function getOrderList($param){
		$sql = 'select o.orderid,o.ordername,o.payfrom,o.paytime,o.refunded,o.sourceid,o.dateline,o.totalfee,o.ordernumber,o.remark,cr.crname,cr.crname,u.username,u.realname,o.totalfee,o.status from ebh_pay_orders o left join ebh_classrooms cr on o.crid=cr.crid left join ebh_users u on u.uid=o.uid ';
		$wherearr = array();
		
		if(!empty($param['q'])){
			$q = $this->ebhdb->escape_str($param['q']);
			$wherearr[] = '(o.ordername like \'%'.$q.'%\' or u.username like \'%'.$q.'%\' )';
		}
		if(!empty($param['crid'])) {
			$wherearr[] = 'o.crid='.$param['crid'];
		}
		if(isset($param['status'])) {
			$wherearr[] = 'o.status='.$param['status'];
		}
		if(isset($param['payfrom'])) {
			$wherearr[] = 'o.payfrom='.$param['payfrom'];
		}
		if(!empty($param['uid'])){
			$wherearr[] = 'o.uid='.$param['uid'];
 		}
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY orderid desc';
        }
		if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
		return $this->ebhdb->query($sql)->list_array();
	}
	
	public function getOrderCount($param){
		$count = 0;
		$sql = 'select count(*) count from ebh_pay_orders o left join ebh_users u on o.uid = u.uid left join ebh_classrooms cr on o.crid = cr.crid ';
		$wherearr = array();
		if(!empty($param['q'])){
			$q = $this->ebhdb->escape_str($param['q']);
			$wherearr[] = '(o.ordername like \'%'.$q.'%\' or u.username like \'%'.$q.'%\' )';
		}
		if(!empty($param['crid'])) {
			$wherearr[] = 'o.crid='.$param['crid'];
		}
		if(isset($param['status'])) {
			$wherearr[] = 'o.status='.$param['status'];
		}
		if(isset($param['payfrom'])) {
			$wherearr[] = 'o.payfrom='.$param['payfrom'];
		}
		if(!empty($param['uid'])){
			$wherearr[] = 'o.uid='.$param['uid'];
 		}
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		$res = $this->ebhdb->query($sql)->row_array();
		if(!empty($res))
			$count = $res['count'];
		return $count;
	}
	
	public function getOrderByOrderid($orderid){
		$sql = 'select * from ebh_pay_orders o join ebh_pay_orderdetails d on o.orderid=d.orderid join ebh_pay_items i on i.itemid=d.itemid';
		return $this->ebhdb->query($sql)->row_array();
	}
	/**
	*根据crid,orderid,pid等信息获取开通详情记录
	*/
	public function getOrderDetailList($param) {
		if(empty($param['crid']) && empty($param['pid']) && empty($param['orderid']) && empty($param['uid'])) {	//至少需要有个参数
			return FALSE;
		}
		$sql = 'select pd.detailid,pd.fee,pd.rname,pd.oname,pd.osummary,pd.omonth,pd.oday,o.payfrom,o.paytime,u.username,u.realname,u.sex,u.face from ebh_pay_orderdetails pd '.
				'join ebh_pay_orders o on (pd.orderid=o.orderid) '.
				'join ebh_users u on (u.uid=o.uid) ';
		$wherearr = array();
		if(!empty($param['crid']))
			$wherearr[] = 'pd.crid='.$param['crid'];
		if(!empty($param['pid']))
			$wherearr[] = 'pd.pid='.$param['pid'];
		if(!empty($param['uid']))
			$wherearr[] = 'o.uid='.$param['uid'];
		if(!empty($param['orderid']))
			$wherearr[] = 'pd.orderid='.$param['orderid'];
		if(isset($param['status']))
			$wherearr[] = 'o.status='.$param['status'];
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY pd.detailid desc';
        }
		if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	*根据crid,orderid,pid等信息获取开通详情记录
	*/
	public function getOrderDetailListCount($param) {
		$count = 0;
		if(empty($param['crid']) && empty($param['pid']) && empty($param['orderid']) && empty($param['uid'])) {	//至少需要有个参数
			return $count;
		}
		$sql = 'select count(*) count from ebh_pay_orderdetails pd '.
				'join ebh_pay_orders o on (pd.orderid=o.orderid) ';
		$wherearr = array();
		if(!empty($param['crid']))
			$wherearr[] = 'pd.crid='.$param['crid'];
		if(!empty($param['pid']))
			$wherearr[] = 'pd.pid='.$param['pid'];
		if(!empty($param['uid']))
			$wherearr[] = 'o.uid='.$param['uid'];
		if(!empty($param['orderid']))
			$wherearr[] = 'pd.orderid='.$param['orderid'];
		if(isset($param['status']))
			$wherearr[] = 'o.status='.$param['status'];
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		$countrow = $this->ebhdb->query($sql)->row_array();
		if(!empty($countrow))
			$count = $countrow['count'];
		return $count;
	}

	public function setPayDetailStatus($param = array(),$where = array()){
		if(empty($param) || empty($where)){
			return ;
		}
		return $this->ebhdb->update('ebh_pay_orderdetails',$param,$where);
	}

	//根据itemids合集和用户uid获取用户获取订单详情信息(成功的订单)
	public function getOrdersByItemidsAndUid($itemids = array(),$uid=0){
		if(empty($itemids) || empty($uid)){
			return array();
		}
		$sql = 'select od.itemid,o.paytime,o.payfrom from ebh_pay_orderdetails od join ebh_pay_orders o on od.orderid = o.orderid where od.uid = '.$uid.' AND od.itemid in ('.implode(',', $itemids).')'.' AND od.dstatus = 1';
		return $this->ebhdb->query($sql)->list_array();
	}

	public function getOrderDetailById($orderid) {
		$sql = "select u.username,u.realname,cr.crname,o.refunded,o.invalid,o.buyer_id,o.buyer_info,o.orderid,o.ordername,o.crid,o.uid,o.dateline,o.paytime,o.payfrom,o.totalfee,o.ip,o.payip,o.paycode,o.ordernumber,o.bankid,o.remark,o.status from ebh_pay_orders o 
				left join ebh_users u on o.uid = u.uid  
				left join ebh_classrooms cr on cr.crid= o.crid where o.orderid=$orderid";
		$myorder = $this->ebhdb->query($sql)->row_array();
		if(!empty($myorder)) {
			$myorder['detaillist'] = $this->getOrderDetailListByOrderId($orderid);
		}
		return $myorder;
	}
}

?>