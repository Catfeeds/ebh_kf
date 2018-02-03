<?php
/**
 *个人空间审核model类,针对ebh_sns_billchecks表
 *
 */
class SnsbillchecksModel extends CSnsModel{
	/**
	 * 审核处理
	 *
	 */
	public function check($param){
		$toid = $param['toid'];
		$role = $param['role'];
		$type = $param['type'];
		if(!toid){return false;}
		
		//检查是否持存在
		$sql = "select count(*) as count from ebh_sns_billchecks where toid = {$toid} and type = {$type}";
		$row = $this->snsdb->query($sql)->row_array();
		//var_dump($row);exit;
		if($row['count']>0){
			//更新
			if($role=='admin'){//管理员审核
				$setArr['admin_uid'] = $param['admin_uid'];
				$setArr['admin_status'] = $param['admin_status'];
				$setArr['admin_remark'] = htmlentities($param['admin_remark'],ENT_NOQUOTES,"utf-8");
				$setArr['admin_ip'] = $param['admin_ip'];
				$setArr['admin_dateline'] = time();
//                $setArr['teach_remark'] = '';
			}elseif($role=='teach'){//教师审核
				$setArr['teach_uid'] = $param['teach_uid'];
				$setArr['teach_status'] = $param['teach_status'];
				$setArr['teach_remark'] = $param['teach_remark'];
				$setArr['teach_ip'] = $param['teach_ip'];
				$setArr['teach_dateline'] = time();
			}
			$this->snsdb->update("ebh_sns_billchecks",$setArr,array('toid'=>$toid,'type'=>$type));
			
			//网校对应修改课件等状态
			
		}else{
			//添加
			if($role=='admin'){//管理员审核
				$data = array(
					'toid'=>$toid,
					'type'=>$type,
					'admin_uid'=>$param['admin_uid'],
					'admin_status'=>$param['admin_status'],
					'admin_remark'=>htmlentities($param['admin_remark'],ENT_NOQUOTES,"utf-8"),
                    'teach_remark'=>0,
					'admin_ip'=>$param['admin_ip'],
					'admin_dateline'=>time(),
                    'teach_remark'=>'',
				);
			}elseif($role=='teach'){//教师审核
				$data = array(
					'toid'=>$toid,
					'type'=>$type,
					'teach_uid'=>$param['teach_uid'],
					'teach_status'=>$param['teach_status'],
					'teach_remark'=>$param['teach_remark'],
					'teach_ip'=>$param['teach_ip'],
					'teach_dateline'=>time()			
				);
			}
			
			$this->snsdb->insert("ebh_sns_billchecks",$data);
		}
		
		//更新feeds/photos/blogs表
		if($param['teach_status']==2||$param['admin_status']==2){
			$result = $this->updatestatus($toid,  $type);
            if($result){
                return true;
            }
		}
		return $this->snsdb->affected_rows();
		
	}

	/**
	 * 批量审核
	 */
	public function multcheck($param){
		$idarr = explode(",", $param['ids']);
		if(!is_array($idarr)){
			return false;
		}
		foreach($idarr as $id){
			$param['toid'] = $id;
			$params = $param;
			$ck = $this->check($params);
			if($ck<=0){
				break;
				return false;
			}
		}
		return true;
	}

    /**
     * 审核撤销,还原信息
     *
     */
    public function revoke($param){
        $toid = $param['toid'];
        $type = $param['type'];
        if(!toid){return false;}

        //检查是否持存在
        $sql = "select count(*) as count from ebh_sns_billchecks where toid = {$toid} and type = {$type}";
        $row = $this->snsdb->query($sql)->row_array();
        if(!$row){
            return false;
        }
        $data = array(
            'admin_status' => $param['admin_status'],
            'admin_uid' => $param['admin_uid'],
        );
        $this->snsdb->update("ebh_sns_billchecks",$data,array('toid'=>$toid,'type'=>$type));
        if($param['status'] == 2){
            $old_status = $this->snsdb->query('select old_status from ebh_sns_billchecks where toid = '.$toid.' and type = '.$type)->row_array();
            $old_status = $old_status['old_status'];
            switch ($type){
                case 8 : $table = 'ebh_sns_feeds';
                    $setarr=array('status'=>$old_status);
                    $where= array('fid'=>$toid);
                    break;//新鲜事状态标识 0正常 1删除 2审核失败
                case 9 : $table = 'ebh_sns_blogs';
                    $setarr=array('status'=>$old_status);
                    $where= array('bid'=>$toid);
                    break;//日志状态标识 0正常 1删除 2审核失败
                case 10 : $table = 'ebh_sns_images';
                    $setarr=array('status'=>$old_status);
                    $where= array('gid'=>$toid);
                    break;//相册status1禁止0正常
            }
            if ($type == 8) {
                $this->snsdb->update('ebh_sns_classfeeds',$setarr,$where);
                $this->snsdb->update('ebh_sns_roomfeeds',$setarr,$where);
            }
            $this->snsdb->update($table,$setarr,$where);
        }
        return $this->snsdb->affected_rows();
    }
	/**
	 * 更新网校课件,附件等状态
	 * 
	 */
	public function updatestatus($toid,$type){
		switch ($type){
			case 8 : $table = 'ebh_sns_feeds';
					 $setarr=array('status'=>2);
					 $where= array('fid'=>$toid);
					 break;//新鲜事status2审核失败
			case 9 : $table = 'ebh_sns_blogs';
					 $setarr=array('status'=>2);
					 $where= array('bid'=>$toid);
					 break;//日志status2审核失败
			case 10 : $table = 'ebh_sns_images';
					 $setarr=array('status'=>1);
					 $where= array('gid'=>$toid);
					 break;//相册status1禁止
		}
        //记录审核不同过之前的状态
        if(in_array($type,array(8,9,10))){
            $key = array_keys($setarr);
            $wherekey = array_keys($where);
            $sta = $this->snsdb->query('select '.$key[0].' from '.$table.' where '.$wherekey[0].' = '.$toid)->row_array();
            $this->snsdb->update('ebh_sns_billchecks', array('old_status' => $sta[$key[0]]), array('toid' => $toid, 'type' => $type));
        }
		if ($type == 8) {
			$this->snsdb->update('ebh_sns_classfeeds',$setarr,$where);

			$this->snsdb->update('ebh_sns_roomfeeds',$setarr,$where);

		}
        $status = $this->snsdb->query('select status from '.$table.' where '.$wherekey[0].'='.$toid)->row_array();
        if($status['status'] == $setarr['status']){
            return true;
        }
		$this->snsdb->update($table,$setarr,$where);
	}
	/**
	 * 删除处理
	 */
	public function del($param){
		$setArr['del'] = 1;
		$setArr['delline'] = time();
		$whereArr['toid'] = $param['toid'];
		$whereArr['type'] = $param['type'];
		//(逻辑删除)
		if($param['type']==8){//新鲜事
			$this->snsdb->update("ebh_sns_feeds",array('status'=>1),array('fid'=>$param['toid']));
			$this->snsdb->update("ebh_sns_classfeeds",array('status'=>1),array('fid'=>$param['toid']));
			$this->snsdb->update("ebh_sns_roomfeeds",array('status'=>1),array('fid'=>$param['toid']));
			$this->snsdb->insert("ebh_sns_dels",array('toid'=>$param['toid'],'uid' =>0,'type'=>1,'dateline'=>SYSTIME));
		}
		elseif($param['type']==9){//日志
			$this->snsdb->update("ebh_sns_blogs",array('status'=>1),array('bid'=>$param['toid']));
			$this->snsdb->insert("ebh_sns_dels",array('toid'=>$param['toid'],'uid' =>0,'type'=>2,'dateline'=>SYSTIME));
		}
		$this->snsdb->update("ebh_sns_billchecks",$setArr,$whereArr);
	}

}
?>