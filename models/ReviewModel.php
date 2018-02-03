<?php 
	//评论类
class ReviewModel extends CEbhModel{
    /*
    评论列表
    @param array $param
    @return array
    */
    public function getreviewlist($param){
        $sql = ' select r.uid,rc.crid,r.logid,r.subject,rc.cwid,r.good,r.useful,r.bad,r.dateline,r.fromip,r.type,r.score,r.subject,c.title as coursewaretitle,
                ck.admin_status,ck.teach_status,ck.del,ck.admin_uid '
            .' from ebh_reviews r '
            .' left join ebh_roomcourses rc on rc.cwid = r.toid '
            .' left join ebh_coursewares c on c.cwid = r.toid '
            .' left join ebh_billchecks ck on ck.toid = r.logid and ck.type=3 ';
        if(!empty($param['q']))
            $wherearr[] = ' (r.subject like \'%'. $this->ebhdb->escape_str($param['q']) .'%\')';
        if(!empty($param['access'])){
          $wherearr[]='rc.crid in ('.$this->ebhdb->escape_str($param['access']).')';
        }  
        //管理员
        if($param['role']=='admin'){
            if($param['admin_status']>0){
                $wherearr[] = 'ck.admin_status ='.$param['admin_status'];
            }
            if($param['cat']==0){
                $wherearr[] = '(ck.admin_status is null or ck.admin_status=0 or ck.admin_status = 3)';
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
        if(!empty($param['crid'])){
            if(is_array($param['crid'])){
                $wherearr[] = 'rc.crid in( '.implode(',', $param['crid']).')';
            }else{
                $wherearr[] = 'rc.crid ='.$param['crid'];
            }
        }
        if(!empty($wherearr))
            $sql.= ' WHERE '.implode(' AND ',$wherearr);
        if(!empty($param['order']))
            $sql.= ' order by ' .$param['order'];
        else
            $sql.= ' order by r.logid desc';
        if(!empty($param['limit']))
             $sql.= ' limit ' . $param['limit'];
        $rows=  $this->ebhdb->query($sql)->list_array();
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
    评论数量
    @param array $param
    @return int
    */
    public function getreviewcount($param){
        $sql = ' select count(*) count  from ebh_reviews r '
                .' left join ebh_roomcourses rc on rc.cwid = r.toid '
                .' left join ebh_billchecks ck on ck.toid = r.logid and ck.type=3 ';
        if(!empty($param['q']))
            $wherearr[] = ' (r.subject like \'%'. $this->ebhdb->escape_str($param['q']) .'%\' )';
        if(!empty($param['access'])){
          $wherearr[]='rc.crid in ('.$this->ebhdb->escape_str($param['access']).')';
        }    
        //管理员
        if($param['role']=='admin'){
            if($param['admin_status']>0){
                $wherearr[] = 'ck.admin_status ='.$param['admin_status'];
            }
            if($param['cat']==0){
                $wherearr[] = '(ck.admin_status is null or ck.admin_status=0 or ck.admin_status = 3)';
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
                $wherearr[] = 'ck.teach_status is null or ck.teach_status = 0';
            }
            if($param['cat']==1){
                $wherearr[] = 'ck.teach_status>0 and ck.del=0';
            }
            if($param['cat']==2){
                $wherearr[] = 'ck.del=1';
            }
        }       
        if(!empty($param['crid'])){
            if(is_array($param['crid'])){
                $wherearr[] = 'rc.crid in( '.implode(',', $param['crid']).')';
            }else{
                $wherearr[] = 'rc.crid ='.$param['crid'];
            }
        }
        if(!empty($wherearr))
            $sql.= ' WHERE '.implode(' AND ',$wherearr);
        //echo $sql;
        $count = $this->ebhdb->query($sql)->row_array();

        return $count['count'];
    }

        /*
    删除评论
    @param int $logid
    @return bool
    */
    public function deletereview($logid){
        return $this->ebhdb->delete('ebh_reviews','logid='.$logid);
        // $sql = 'delete r.* from ebh_reviews r where r.logid='.$logid;
        // return $this->db->simple_query($sql);
    }
     public function getCridById($id){
          $sql="select crid from ebh_reviews where qid=".$this->ebhdb->escape($id);
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
     * 获取评论详情
     * 
     */
    public function getReviewById($logid){
         $sql = 'select r.logid,r.subject,r.fromip,r.dateline,r.type,r.score,c.title,r.score,u.realname,u.username,c.cwurl,c.cwsource,
                ck.admin_status,ck.admin_remark,ck.teach_status,ck.teach_remark,ck.del,ck.admin_dateline,ck.teach_dateline,ck.delline,ck.admin_ip,ck.teach_ip,ck.admin_uid
                from ebh_reviews r    
                left join ebh_users u on (u.uid = r.uid)  
                left join ebh_coursewares c on (c.cwid = r.toid)   
                left join ebh_billchecks ck on ck.toid = r.logid 
                
                 where r.logid = '.$logid;
        return $this->ebhdb->query($sql)->row_array();
    }

    public function getSchoolName($id){
        $sql='select cr.crname from ebh_reviews r left join ebh_roomcourses rc on rc.cwid = r.toid left join ebh_classrooms cr on cr.crid=rc.crid where r.logid='.intval($id);
        $info=$this->ebhdb->query($sql)->row_array();
        return $info['crname'];
    }
}
