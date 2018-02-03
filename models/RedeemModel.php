<?php

/**
 * 兑换model
 */
class RedeemModel extends CEbhModel{

	/*
      列表
      @param array $param
      @return array 列表数组
     */
    public function getlist($param) {
        $wherearr = array();
        $sql = 'select i.iprice as fprice,ck.admin_status,ck.teach_status,ck.del,ck.admin_uid,r.lotid,r.crid,r.effecttime,r.name,r.dateline,r.lotcode,r.price,r.number,f.foldername from ebh_redeem_lots r left join ebh_folders f using(folderid) left join ebh_pay_items i on i.itemid=r.itemid '
                .' left join ebh_billchecks ck on ck.toid = r.lotid and ck.type=15 ';
        if (!empty($param['q']))
            $wherearr[] = '(r.name like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' or f.foldername like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' )';
        if(!empty($param['crid'])){
            if(is_array($param['crid'])){
                $wherearr[] = 'r.crid in( '.implode(',', $param['crid']).')';
            }else{
                $wherearr[] = 'r.crid in( '.$param['crid'].')';
            }
        }
        //管理员
        if($param['role']=='admin'){
            if($param['admin_status']>0){
                if ($param['admin_status'] == 5) {//待审核
                    $wherearr[] = '(ck.admin_status is null or ck.admin_status = 0 or ck.admin_status = 3)';
                } else {
                    $wherearr[] = 'ck.admin_status ='.$param['admin_status'];
                }
                
            }
            if($param['cat']==0){
                $wherearr[] = '(ck.admin_status is null or ck.admin_status = 0 or ck.admin_status = 3)';
            }
            if($param['cat']==1){
                $wherearr[] = '(ck.admin_status in(1,2) and ck.del=0)';
            }
            if($param['cat']==2){
                $wherearr[] = 'ck.del=1';
            }
            //教师
        }elseif($param['role']=='teach'){
            if($param['teach_status']>0){
                $wherearr[] = 'ck.teach_status ='.$param['teach_status'];
            }
            if($param['cat']==0){
                $wherearr[] = 'ck.teach_status is null or ck.teach_status=0 ';
            }
            if($param['cat']==1){
                $wherearr[] = 'ck.teach_status>0 and ck.del=0';
            }
            if($param['cat']==2){
                $wherearr[] = 'ck.del=1';
            }
        }
        $wherearr[] = 'r.status<>0';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($param['order'])) {
            $sql .= ' order by ' . $param['order'];
        } else {
            $sql .= ' order by r.lotid desc ';
        }
        if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        }
        $rows =  $this->ebhdb->query($sql)->list_array();
        if (empty($rows)) {
        	return array();
        }
        //下面是对应优化代码
        $uidstr = '';
        $cridstr = '';
        foreach($rows as $key=>$row){
            if(!empty($row['uid'])){
                $uidstr.=$row['uid'].',';
            }
            if(!empty($row['crid'])){
                $cridstr.= $row['crid'].',';
            }
        }
        $uidstr = rtrim($uidstr, ',');
        $cridstr = rtrim($cridstr, ',');
        //用户信息
        if($uidstr!=''){
            $usql = 'select uid,username,realname from ebh_users where uid in('.$uidstr.')';
            $uidrows =  $this->ebhdb->query($usql)->list_array();
            $uidrows = $this->_arraycoltokey($uidrows,'uid');
        }
        //学校名称
        if($cridstr!=''){
            $ssql = 'select crid,crname from ebh_classrooms where crid in('.$cridstr.')';
            $cridrows =  $this->ebhdb->query($ssql)->list_array();
            $cridrows = $this->_arraycoltokey($cridrows,'crid');
        }
        
        foreach($rows as &$row){
            $row['username'] = $uidrows[$row['uid']]['username'];
            $row['realname'] = $uidrows[$row['uid']]['realname'];
            $row['crname'] = $cridrows[$row['crid']]['crname'];
        }
        
        return $rows;
    }

    /*
      数量
      @param array $param
      @return int
     */
    public function getcount($param) {
        $sql = 'select count(1) as count from ebh_redeem_lots r left join ebh_folders f using(folderid)'
                .' left join ebh_billchecks ck on ck.toid = r.lotid and ck.type=15 ';
        if (!empty($param['q']))
            $wherearr[] = '(r.name like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' or f.foldername like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' )';
        if(!empty($param['crid'])){
            if(is_array($param['crid'])){
                $wherearr[] = 'r.crid in( '.implode(',', $param['crid']).')';
            }else{
                $wherearr[] = 'r.crid in( '.$param['crid'].')';
            }
        }
        //管理员
        if($param['role']=='admin'){
            if($param['admin_status']>0){
                $wherearr[] = 'ck.admin_status ='.$param['admin_status'];
            }
            if($param['cat']==0){
                $wherearr[] = '(ck.admin_status is null or ck.admin_status = 0 or ck.admin_status = 3)';
            }
            if($param['cat']==1){
                $wherearr[] = '(ck.admin_status in(1,2) and ck.del=0)';
            }
            if($param['cat']==2){
                $wherearr[] = 'ck.del=1';
            }
            //教师
        }elseif($param['role']=='teach'){
            if($param['teach_status']>0){
                $wherearr[] = 'ck.teach_status ='.$param['teach_status'];
            }
            if($param['cat']==0){
                $wherearr[] = 'ck.teach_status is null or ck.teach_status=0 ';
            }
            if($param['cat']==1){
                $wherearr[] = 'ck.teach_status>0 and ck.del=0';
            }
            if($param['cat']==2){
                $wherearr[] = 'ck.del=1';
            }
        }
        $wherearr[] = 'r.status<>0';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        $count = $this->ebhdb->query($sql)->row_array();
        return $count['count'];
    }
      /**
     * 二维数组某个列的值作为索引键
     * @param unknown $data
     * @param string $key
     *
     */
    protected  function _arraycoltokey($array, $key = '') {
        if(empty($key)) return ;
        $newarray = array();
        foreach ($array as $row){
            $newarray[$row[$key]] = $row;
        }
        return $newarray;
    }

	/**
	 * [insert 生成新的订单]
	 * @return [type] [description]
	 */
	public function add($param = array()){
		$setarr = array();
		if(isset($param['toid'])){
			$setarr['toid'] = $param['toid'];
		}
		if(!empty($param['type'])){
			$setarr['type'] = $param['type'];
		}
		if(!empty($param['lotcode'])){
			$setarr['lotcode'] = $param['lotcode'];
		}
		if(!empty($param['price'])){
			$setarr['price'] = $param['price'];
		}
		if(!empty($param['dateline'])){
			$setarr['dateline'] = $param['dateline'];
		}
		if(!empty($param['ordernumber'])){
			$setarr['ordernumber'] = $param['ordernumber'];
		}
		if(!empty($param['status'])){
			$setarr['status'] = $param['status'];
		}
		return $this->ebhdb->insert('ebh_redeem_records', $setarr);
	}

	public function updateOrder($param){
		if(empty($param['id']))
			exit;
		if(isset($param['status']))
			$spiarr['status'] = $param['status'];
		if(isset($param['dateline']))
			$spiarr['dateline'] = $param['dateline'];
		return $this->ebhdb->update('ebh_redeem_records',$spiarr,'id='.$param['id']);
	}

	/**
	 *获取批次号
	 */
	public function getRedeem($id=0) {
		if (!$id) {
			return array();
		}
		$sql = 'select r.lotid,r.name,r.lotcode,f.fprice,r.price,r.number,f.foldername from ebh_redeem_lots r left join ebh_folders f using(folderid) where r.lotid='.$id;
		$res = $this->ebhdb->query($sql)->row_array();
        return $res;
	}

    /**
     *获取批次号
     */
    public function getRedeemInfo($id=0) {
        if (!$id) {
            return array();
        }
        $sql = 'select r.lotid,r.name,r.lotcode,r.price,r.number,c.crname from ebh_redeem_lots r left join ebh_classrooms c using(crid) where r.lotid='.$id;
        $res = $this->ebhdb->query($sql)->row_array();
        return $res;
    }

	/**
	 *获取批次的付款记录
	 */
	public function getRedeemRecord($param) {
		$wherearr = array();
		$sql = 'select r.id,r.ordernumber,r.status from ebh_redeem_records r ';
		if (!empty($param['join'])) {//关联批次表
			$sql = 'select r.id,r.ordernumber,r.status,l.name,l.lotcode,l.crid,l.folderid,l.price,l.number,l.effecttime,l.dateline as ldateline from ebh_redeem_records r left join ebh_redeem_lots l on l.toid=r.toid ';
		}
		if(!empty($param['q']))
			$wherearr[] = ' (r.lotcode like \'%'. $this->ebhdb->escape_str($param['q']) .'%\')';
		if(!empty($param['type']))
			$wherearr[] = 'r.type ='.intval($param['type']);
		if(!empty($param['toid'])){
			$wherearr[] = 'r.toid='.intval($param['toid']);
		}
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);

		if (!empty($param['toid'])) {//返回单条记录
			return $this->ebhdb->query($sql)->row_array();
		} else {
			return $this->ebhdb->query($sql)->list_array();
		}
		
	}

	/**
     *获取兑换码信息
     */
    public function getRedeemCardsInfo($param) {
    	$wherearr = array();
		$sql = 'select f.foldername,f.fprice,r.cardid,r.redeemid,r.redeemnumber,r.usetime,r.status,r.crid,l.name,l.lotcode,l.folderid,l.price,l.number,l.effecttime,l.dateline as ldateline from ebh_redeem_cards r left join ebh_redeem_lots l on l.lotid=r.redeemid left join ebh_folders f on l.folderid=f.folderid ';
		if(!empty($param['redeemnumber']))
			$wherearr[] = ' (r.redeemnumber like \'%'. $this->ebhdb->escape_str($param['redeemnumber']) .'%\')';
		if(!empty($param['name']))
			$wherearr[] = ' (l.name like \'%'. $this->ebhdb->escape_str($param['name']) .'%\')';
		if(!empty($param['foldername']))
			$wherearr[] = ' (f.foldername like \'%'. $this->ebhdb->escape_str($param['foldername']) .'%\')';
		if(!empty($param['folderid']))
			$wherearr[] = 'l.folderid ='.intval($param['folderid']);
		if(!empty($param['crid']))
			$wherearr[] = 'r.crid ='.intval($param['crid']);
		if(!empty($param['status'])){
			$wherearr[] = 'r.status='.intval($param['status']);
		}
		if(!empty($wherearr))
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
		return $this->ebhdb->query($sql)->list_array();
    }

    //获取课程
    public function getFoldersByFolderid($folderid){
        if(empty($folderid)){
            return false;
        }
        $sql = " select foldername,folderid from  ebh_folders where folderid in ( "."'" . implode("','", $folderid) . "'"." )";
        $row = $this->ebhdb->query($sql)->list_array();
        return $row;

    }
}