<?php
/**
 *SNS feeds Model类
 */
class FeedsModel extends CSnsModel {
	/**
	 * 查询feeds
	 */
	public function getfeedslist($param){
		$wherearr = array();
		$sql = 'select f.fid,f.fromuid,f.message,f.category,f.toid,f.dateline,f.ip,ck.admin_status,ck.teach_status,ck.del,ck.admin_uid from ebh_sns_feeds f left join ebh_sns_billchecks ck on ck.toid = f.fid and ck.type=8';
		if (!empty($param['q']))
			$wherearr[] = "f.message like '%" . $this->snsdb->escape_str($param['q']) . "%'";
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
		if (!empty($wherearr)){
			$sql.=" where ".implode(" AND ",$wherearr);
		}
		if(!empty($param['orderbby'])){
			$sql.=" ORDER BY ".$param['orderbby'];
		}else{
			$sql.=" ORDER BY f.fid DESC";
		}

		if(!empty($param['limit'])){
			$sql.=" LIMIT ".$param['limit'];
		}else{
			$sql.=" LIMIT 10 ";
		}

		return $this->snsdb->query($sql)->list_array();

	}

	/**
	 * 查询feeds数量
	 * @param unknown $param
	 */
	public function getfeedscount($param){
		$wherearr = array();
		$sql = "select count(*) count from ebh_sns_feeds f left join ebh_sns_billchecks ck on ck.toid = f.fid and ck.type=8";

		if (!empty($param['q']))
			$wherearr[] = "f.message like '%" . $this->snsdb->escape_str($param['q']) . "%'";
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
		if(!empty($wherearr)){
			$sql.=" where ".implode(" AND ",$wherearr);
		}
		//echo $sql;
		$row =  $this->snsdb->query($sql)->row_array();
		if(empty($row['count'])){
			$row['count'] = 0;
		}
		//dump($row['count']);
		return  $row['count'];
	}

	/**
	 * 获取一条动态信息
	 */
	public function getfeedsbyfid($fid){
		$sql = 'select f.fid,f.fromuid,f.message,f.category,f.toid,f.dateline,f.ip,o.pfid,o.tfid,o.iszhuan,o.uid,o.upcount,o.cmcount,o.zhcount,ck.admin_status,ck.admin_remark,ck.teach_status,ck.teach_remark,ck.del,ck.admin_dateline,ck.teach_dateline,ck.delline,ck.admin_ip,ck.teach_ip,ck.admin_uid from ebh_sns_feeds f';
		$sql .= ' left join ebh_sns_outboxs o on f.fid=o.fid';
		$sql .= ' left join ebh_sns_billchecks ck on ck.toid = f.fid';
		$sql .= ' where f.fid=' . intval($fid);
		return $this->snsdb->query($sql)->row_array();
	}

	/**
	 * 检测动态时候被删除
	 */
	public function checkfeedsdelete($fid){
		$sql = "select count(*) count  from ebh_sns_dels where toid = $fid and type = 1 ";
		$row = $this->snsdb->query($sql)->row_array();
		if($row['count']>0){
			return true;
		}else{
			return false;
		}
	}
}