<?php
/**
 *SNS blog Model类
 */
class BlogModel extends CSnsModel {
	/**
	 * 查询blog列表
	 */
	public function getbloglist($param){
		$wherearr = array();
		$sql = 'select b.uid, c.catename, b.bid, b.iszhuan, b.cid, b.title,b.permission, b.dateline, b.status,b.ip, ck.admin_status,ck.teach_status,ck.del,ck.admin_uid from ebh_sns_blogs b left join ebh_sns_categorys c on b.cid = c.cid left join ebh_sns_billchecks ck on ck.toid = b.bid and ck.type=9';
		if (!empty($param['q']))
			$wherearr[] = "b.title like '%" . $this->snsdb->escape_str($param['q']) . "%'";
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
			$sql.=" ORDER BY b.bid DESC";
		}

		if(!empty($param['limit'])){
			$sql.=" LIMIT ".$param['limit'];
		}else{
			$sql.=" LIMIT 10 ";
		}

		return $this->snsdb->query($sql)->list_array();

	}

	/**
	 * 查询blog数量
	 * @param unknown $param
	 */
	public function getblogcount($param){
		$wherearr = array();
		$sql = "select count(*) count from ebh_sns_blogs b left join ebh_sns_billchecks ck on ck.toid = b.bid and ck.type=9";

		if (!empty($param['q']))
			$wherearr[] = "b.title like '%" . $this->snsdb->escape_str($param['q']) . "%'";
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
	 * 获取一条日志信息
	 */
	public function getblogbybid($bid){
		$sql = 'select b.uid, c.catename, b.bid, b.pbid, b.tbid, b.iszhuan, b.cid, b.title, b.tutor, b.content, b.permission, b.dateline, b.images, b.status, b.upcount, b.cmcount, b.zhcount,b.ip,ck.admin_status,ck.admin_remark,ck.teach_status,ck.teach_remark,ck.del,ck.admin_dateline,ck.teach_dateline,ck.delline,ck.admin_ip,ck.teach_ip,ck.admin_uid from ebh_sns_blogs b left join ebh_sns_categorys c on b.cid = c.cid';
		$sql .= ' left join ebh_sns_billchecks ck on ck.toid = b.bid';
		$sql .= ' where b.bid=' . intval($bid);
		return $this->snsdb->query($sql)->row_array();
	}
}