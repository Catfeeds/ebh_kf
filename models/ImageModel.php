<?php
/**
 *SNS 照片 Model类
 */
class ImageModel extends CSnsModel {
	/**
	 * 查询blog列表
	 */
	public function getimagelist($param){
		$wherearr = array();
		$sql = 'select i.gid,i.uid,i.path,i.sizes,i.dateline,i.status,ck.admin_status,ck.teach_status,ck.del, ck.admin_uid from ebh_sns_images i left join ebh_sns_billchecks ck on ck.toid = i.gid and ck.type=10';
		if(!empty($param['begindate'])){
			$wherearr[] = ' i.dateline>='.$param['begindate'];
		}
		if(!empty($param['enddate'])){
			$wherearr[] = ' i.dateline<='.$param['enddate'];
		}
        if (!empty($param['ids'])){
        	$wherearr[] = ' i.gid in ('.$param['ids'].')';
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
		if (!empty($wherearr)){
			$sql.=" where ".implode(" AND ",$wherearr);
		}
		if(!empty($param['orderbby'])){
			$sql.=" ORDER BY ".$param['orderbby'];
		}else{
			$sql.=" ORDER BY i.gid DESC";
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
	public function getimagecount($param){
		$wherearr = array();
		$sql = "select count(*) count from ebh_sns_images i left join ebh_sns_billchecks ck on ck.toid = i.gid and ck.type=10";
		if(!empty($param['begindate'])){
			$wherearr[] = ' i.dateline>='.$param['begindate'];
		}
		if(!empty($param['enddate'])){
			$wherearr[] = ' i.dateline<='.$param['enddate'];
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
		if(!empty($wherearr)){
			$sql.=" where ".implode(" AND ",$wherearr);
		}
//		echo $sql;die;
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
	public function getimagebygid($gid){
		$sql = 'select i.gid,i.uid,i.path,i.sizes,i.dateline,i.status,ck.admin_status,ck.admin_remark,ck.teach_status,ck.teach_remark,ck.del,ck.admin_dateline,ck.teach_dateline,ck.delline,ck.admin_ip,ck.teach_ip,ck.admin_uid FROM ebh_sns_images i left join ebh_sns_billchecks ck on ck.toid = i.gid where i.gid=' . intval($gid);
		return $this->snsdb->query($sql)->row_array();
	}

	//根据gid获取图片详情
	public function getimgs($arr){
		if(empty($arr)) return array();
		$where = array();
		$sql = "select gid, path, sizes from ebh_sns_images";
		if(!empty($arr)){
			$where['gid'] = 'gid in ('.implode(',',$arr).')';
		}
		if(!empty($where)){
			$sql.= " WHERE ".implode(" AND ",  $where);
		}
		return $this->snsdb->query($sql)->list_array();
	}
}