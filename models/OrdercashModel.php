<?php
/*
汇款单模型
*/
class OrdercashModel extends CEbhModel{
	//汇款单列表
	public function getlist($param){
		$where = array();
		$sql = "select o.*, ck.admin_status, ck.admin_dateline, ck.admin_remark, ck.admin_ip ,ck.admin_uid from ebh_ordercash o left join ebh_billchecks ck on ck.toid = o.id and ck.type = 12";
		if(!empty($param['id'])){
			$where[] = ' o.id = '.$param['id'];
		}
		if(!empty($param['ids'])){
			$where[] = ' o.id in ('.implode(',', $param['ids']).')';
		}
		if(isset($param['cat']) && $param['cat'] >= 0){
			if($param['cat'] == 0){
				$where[] = ' ck.admin_status is null or ck.admin_status = 0';
			}else if($param['cat'] == 1){
				$where[] = ' ck.admin_status in (1,2)';
			}
		}
		if($param['admin_status'] >0){
			$where[] = ' ck.admin_status = '.$param['admin_status'];
		}
		if(!empty($param['crid'])){
			$where[] = ' o.crid = '.$param['crid'];
		}
		if(!empty($param['starttime'])){
			$where[] = ' o.dateline >= '.$param['starttime'];
		}
		if(!empty($param['endtime'])){
			$where[] = ' o.dateline <= '.$param['endtime'];
		}
		if(!empty($param['q'])){
			$where[] = ' (o.contact like \'%'.$param['q'].'%\') or (o.remark like \'%'.$param['q'].'%\')';
		}
		if (!empty($where)){
			$sql.= ' where ' . implode(' AND ', $where);
		}
		$sql.=' order by o.dateline desc';
		if (!empty($param['limit'])){
			$sql.= ' limit ' . $param['limit'];
		}
		$rows =  $this->ebhdb->query($sql)->list_array();
		return $rows;
	}
	//汇款单数量
	public function getcount($param){
		$where = array();
		$sql = "select count(*) count from ebh_ordercash o left join ebh_billchecks ck on ck.toid = o.id and ck.type = 12";
		if($param['id']){
			$where[] = ' o.id = '.$param['id'];
		}
		if($param['cat'] >= 0){
			if($param['cat'] == 0){
				$where[] = ' ck.admin_status in(0,3)';
			}else if($param['cat'] == 1){
				$where[] = ' ck.admin_status in (1,2)';
			}
		}
		if($param['admin_status'] >0){
			$where[] = ' ck.admin_status = '.$param['admin_status'];
		}
		if(!empty($param['crid'])){
			$where[] = ' o.crid = '.$param['crid'];
		}
		if(!empty($param['starttime'])){
			$where[] = ' o.dateline >= '.$param['starttime'];
		}
		if(!empty($param['endtime'])){
			$where[] = ' o.dateline <= '.$param['endtime'];
		}
		if(!empty($param['q'])){
			$where[] = ' (o.contact like \'%'.$param['q'].'%\') or (o.remark like \'%'.$param['q'].'%\')';
		}
		if(!empty($where)){
			$sql.= ' where ' . implode(' AND ', $where);
		}
		$row = $this->ebhdb->query($sql)->row_array();
		return !empty($row['count']) ? $row['count'] : 0;
	}
}