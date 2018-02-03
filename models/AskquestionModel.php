<?php

/*
  答疑
 */

class AskquestionModel extends CEbhModel {
   /*
      答疑列表
      @param array $param
      @return array 列表数组
     */
    public function getaskquestionlist($param) {
        $wherearr = array();
        $sql = 'select q.qid,q.catpath,q.dateline,q.catid,q.crid,q.uid,q.title,q.message,q.answercount,q.thankcount,q.hasbest,q.viewnum,ck.admin_status,ck.teach_status,ck.del,ck.admin_uid from ebh_askquestions q '
                .' left join ebh_billchecks ck on ck.toid = q.qid and ck.type=4 ';
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' or q.message like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' )';
        if(!empty($param['access'])){
          $wherearr[]='q.crid in ('.$this->ebhdb->escape_str($param['access']).')';
        }    
        if(!empty($param['crid'])){
            if(is_array($param['crid'])){
                $wherearr[] = 'q.crid in( '.implode(',', $param['crid']).')';
            }else{
                $wherearr[] = 'q.crid ='.$param['crid'];
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
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        if (!empty($param['order'])) {
            $sql .= ' order by ' . $param['order'];
        } else {
            $sql .= ' order by q.qid desc ';
        }
        if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        }
        $rows =  $this->ebhdb->query($sql)->list_array();
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
      答疑数量
      @param array $param
      @return int
     */
    public function getaskquestioncount($param) {
        $wherearr = array();
        $sql = 'select count(*) count from ebh_askquestions q  '
                .' left join ebh_billchecks ck on ck.toid = q.qid and ck.type=4 ';
        if (!empty($param['q']))
            $wherearr[] = '(q.title like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' or q.message like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' )';
        if(!empty($param['access'])){
          $wherearr[]='q.crid in ('.$this->ebhdb->escape_str($param['access']).')';
        }    
        if(!empty($param['crid'])){
            if(is_array($param['crid'])){
                $wherearr[] = 'q.crid in( '.implode(',', $param['crid']).')';
            }else{
                $wherearr[] = 'q.crid ='.$param['crid'];
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
                $wherearr[] = 'ck.admin_status in(1,2) and ck.del=0';
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
                $wherearr[] = 'ck.teach_status is null or ck.teach_status = 0';
            }
            if($param['cat']==1){
                $wherearr[] = 'ck.teach_status>0 and ck.del=0';
            }
            if($param['cat']==2){
                $wherearr[] = 'ck.del=1';
            }
        }        

        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        //var_dump($sql);
        $count = $this->ebhdb->query($sql)->row_array();
        return $count['count'];
    }
        /*
    批量删除
    */
    public function delAll($qidarr){
        $this->ebhdb->begin_trans();
        foreach($qidarr as $qid){
            if(!empty($qid))
                $this->ebhdb->delete('ebh_askquestions','qid='.$qid);
        }
        if ($this->ebhdb->trans_status() === FALSE) {
            $this->ebhdb->rollback_trans();
            return FALSE;
        } else {
            $this->ebhdb->commit_trans();
        }
        return TRUE;
    }
     public function getCridById($id){
          $sql="select crid from ebh_askquestions where qid=".$this->ebhdb->escape($id);
          $row=$this->ebhdb->query($sql)->row_array();
          return $row['crid'];
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
     * 根据问题编号获取问题信息
     * @param int $qid
     * @return array
     */
    public function getaskbyqid($qid) {
            $sql = 'select q.qid,q.crid,q.uid,q.title,q.message,cr.domain,cr.crname,u.username,u.realname,q.folderid,f.foldername,q.audioname,q.audiosrc,q.imagename,q.imagesrc,q.answercount,q.thankcount,q.hasbest,q.`status`,q.dateline,q.viewnum,q.attname,q.attsrc,q.catpath, ' .
                    'ck.admin_status,ck.admin_remark,ck.teach_status,ck.teach_remark,ck.del,ck.admin_dateline,ck.teach_dateline,ck.delline,ck.admin_ip,ck.teach_ip,ck.admin_uid '.
                    'from ebh_askquestions q '.
                    'join ebh_users u on (u.uid = q.uid) ' .
                    'left join ebh_folders f on (f.folderid = q.folderid) ' .
                    'left join ebh_billchecks ck on ck.toid = q.qid ' .
                    'left join ebh_classrooms cr on cr.crid = q.crid ' .
                    'where q.qid=' . $qid;
        
        return $this->ebhdb->query($sql)->row_array();
    }


    
    /**
     * 获取回答列表
     * 
     */
    public function getanswerlist($param){
    	$wherearr = array();
    	$sql = 'select a.uid,a.aid,a.answertype,a.message,a.message,a.dateline,q.crid,q.qid,q.title,q.catid,ck.admin_status,ck.teach_status,ck.del,ck.admin_uid from ebh_askanswers a '
    			.' left join ebh_askquestions q on q.qid = a.qid '
    			.' left join ebh_billchecks ck on ck.toid = a.aid and ck.type=5 ';
    	if (!empty($param['q']))
    		$wherearr[] = '(q.title like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' or a.message like \'%' . $this->ebhdb->escape_str($param['q']) . '%\')';
        if(!empty($param['access'])){
          $wherearr[]='q.crid in ('.$this->ebhdb->escape_str($param['access']).')';
        }
    	if(!empty($param['crid'])){
    		if(is_array($param['crid'])){
    			$wherearr[] = 'q.crid in( '.implode(',', $param['crid']).')';
    		}else{
    			$wherearr[] = 'q.crid ='.$param['crid'];
    		}
    	}
    	//管理员
    	if($param['role']=='admin'){
    		if($param['admin_status']>0){
    			$wherearr[] = 'ck.admin_status ='.$param['admin_status'];
    		}
    		if($param['cat']==0){
    			$wherearr[] = 'ck.admin_status is null or ck.admin_status=0 or ck.admin_status = 3';
    		}
    		if($param['cat']==1){
    			$wherearr[] = 'ck.admin_status in(1,2) and ck.del=0';
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

    	if (!empty($wherearr))
    		$sql.= ' WHERE ' . implode(' AND ', $wherearr);
    	if (!empty($param['order'])) {
    		$sql .= ' order by ' . $param['order'];
    	} else {
    		$sql .= ' order by a.aid desc ';
    	}
    	if(!empty($param['limit'])) {
    		$sql .= ' limit '. $param['limit'];
    	}
    	//echo $sql;
    	$rows =  $this->ebhdb->query($sql)->list_array();
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
    
    /**
     * 回答数量
     * 
     */
    public function getanswercount($param){
    	$wherearr = array();
    	$sql = 'select count(*) count  from ebh_askanswers a '
    			.' left join ebh_askquestions q on q.qid = a.qid '
    			.' left join ebh_users u on a.uid=u.uid '
    			.' left join ebh_classrooms cr on cr.crid = q.crid '
    			.' left join ebh_billchecks ck on ck.toid = a.aid and ck.type=5 ';
    	if (!empty($param['q']))
    		$wherearr[] = '(q.title like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' or a.message like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' or u.username like \'%' . $this->ebhdb->escape_str($param['q']) . '%\')';
        if(!empty($param['access'])){
            $wherearr[]='q.crid in ('.$this->ebhdb->escape_str($param['access']).')';
        }
        if(!empty($param['crid'])){
    		if(is_array($param['crid'])){
    			$wherearr[] = 'q.crid in( '.implode(',', $param['crid']).')';
    		}else{
    			$wherearr[] = 'q.crid ='.$param['crid'];
    		}
    	}
    	//管理员
    	if($param['role']=='admin'){
    		if($param['admin_status']>0){
    			$wherearr[] = 'ck.admin_status ='.$param['admin_status'];
    		}
    		if($param['cat']==0){
    			$wherearr[] = 'ck.admin_status is null or ck.admin_status=0 or ck.admin_status=3';
    		}
    		if($param['cat']==1){
    			$wherearr[] = 'ck.admin_status in(1,2) and ck.del=0';
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
        if (!empty($wherearr))
    		$sql.= ' WHERE ' . implode(' AND ', $wherearr);
//        p($sql);die;
        $count = $this->ebhdb->query($sql)->row_array();
        return $count['count'];
    }

    /**
     * 获取回答信息
     * 
     */
    public function getanswerbyaid($aid){
    	$sql = 'select a.aid,q.qid,a.answertype,a.dateline,a.message amessage,q.message qmessage,q.title ,u.realname,u.username,'.
	    	'ck.admin_status,ck.admin_remark,ck.teach_status,ck.teach_remark,ck.del,ck.admin_dateline,ck.teach_dateline,ck.delline,ck.admin_ip,ck.teach_ip, ck.admin_uid,cr.crname '.
	    	'from ebh_askanswers a '.
	    	' join ebh_askquestions as q on q.qid = a.qid ' .
	    	'left join ebh_users u on (u.uid = a.uid) ' .
	    	'left join ebh_billchecks ck on ck.toid = a.aid ' .
	    	'left join ebh_classrooms cr on cr.crid = q.crid ' .
	    	'where a.aid=' . $aid;
    	//echo $sql;
    	return $this->ebhdb->query($sql)->row_array();
    }

    /**
     * @param $aid
     * 根据aid获取学校id
     */
    public function getCridByAid($aid){
        $sql = 'select q.crid from ebh_askanswers a join ebh_askquestions as q on q.qid=a.qid where aid='.$aid;
        $res = $this->ebhdb->query($sql)->row_array();
        return $res['crid'];
    }
}
