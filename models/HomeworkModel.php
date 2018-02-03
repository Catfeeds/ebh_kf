    <?php 
    	//作业模型
    class HomeworkModel extends CEbhModel{
        /**
         * 获取作业列表
         * @return [type] [description]
         */
        public function getHomeworkList($param){
            $sql="select e.eid,e.title,e.dateline,u.realname,c.crname,ck.admin_status,ck.del, ck.admin_uid from ebh_schexams e left join ebh_classrooms c on c.crid=e.crid left join ebh_users u on u.uid=e.uid left join ebh_billchecks ck on ck.toid=e.eid and ck.type=7";
            if(!empty($param['q']))
               $wherearr[] = ' (e.title like \'%'. $this->ebhdb->escape_str($param['q']) .'%\' or c.crname like \'%'.$this->ebhdb->escape_str($param['q']).'%\' or u.realname like \'%'.$this->ebhdb->escape_str($param['q']).'%\')';
           if (!empty($param['access']))
            $wherearr[] = 'e.crid in ('.$param['access'].') ';   
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
        if(!empty($param['crid'])){
            if(is_array($param['crid'])){
                $wherearr[] = 'e.crid in( '.implode(',', $param['crid']).')';
            }else{
                $wherearr[] = 'e.crid ='.$param['crid'];
            }
        }
        if(!empty($wherearr))
            $sql.= ' WHERE '.implode(' AND ',$wherearr);     
        if (!empty($param['access']))
            $wherearr[] = 'a.crid in ('.$param['access'].') ';
        if(!empty($param['order'])){
            $sql.= ' order by ' .$param['order'];
        }
        else{
            $sql.= ' order by e.eid desc';   
        }
        if(!empty($param['limit'])){
            $sql.= ' limit ' . $param['limit']; 
        }else{
         if (empty($param['page']) || $param['page'] < 1)
            $page = 1;
        else
            $page = $param['page'];
        $pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
        $start = ($page - 1) * $pagesize;
        $sql .= ' limit ' . $start . ',' . $pagesize;               
    }
    // echo $sql;
    return $this->ebhdb->query($sql)->list_array();
    }
        //获取作业数量
    public function getHomeworkCount($param){
        $sql="select count(*) count from ebh_schexams e left join ebh_classrooms c on c.crid=e.crid left join ebh_users u on u.uid=e.uid left join ebh_billchecks ck on ck.toid=e.eid and ck.type=7";
        if(!empty($param['q']))
            $wherearr[] = ' (e.title like \'%'. $this->ebhdb->escape_str($param['q']) .'%\' or c.crname like \'%'.$this->ebhdb->escape_str($param['q']).'%\' or u.realname like \'%'.$this->ebhdb->escape_str($param['q']).'%\')';
        if (!empty($param['access']))
            $wherearr[] = 'e.crid in ('.$param['access'].') ';
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
        if(!empty($param['crid'])){
            if(is_array($param['crid'])){
                $wherearr[] = 'e.crid in( '.implode(',', $param['crid']).')';
            }else{
                $wherearr[] = 'e.crid ='.$param['crid'];
            }
        }
        if(!empty($wherearr))
            $sql.= ' WHERE '.implode(' AND ',$wherearr);     
        if (!empty($param['access']))
            $wherearr[] = 'a.crid in ('.$param['access'].') ';
        if(!empty($param['order'])){
            $sql.= ' order by ' .$param['order'];
        }
        else{
            $sql.= ' order by e.eid desc';   
        }
        // echo $sql;
        $count = $this->ebhdb->query($sql)->row_array();
        return $count['count'];
    }

    public function deleteHomework($eid){
        return $this->ebhdb->delete('ebh_schexams','eid='.$eid);
            // $sql = 'delete r.* from ebh_reviews r where r.logid='.$logid;
            // return $this->db->simple_query($sql);
    }
    public function getCridById($id){
      $sql="select crid from ebh_schexams where eid=".$this->ebhdb->escape($id);
      $row=$this->ebhdb->query($sql)->row_array();
      return $row['crid'];
    }
    /*
    通过作业id查找作业信息
    */
    public function getHomeworkById($id){
        $sql="select e.eid,e.title,e.dateline,e.uid,u.realname,c.crname,ck.admin_status,ck.admin_remark,ck.admin_ip,ck.admin_dateline,ck.admin_uid from ebh_schexams e left join ebh_classrooms c on c.crid=e.crid left join ebh_users u on u.uid=e.uid left join ebh_billchecks ck on ck.toid=e.eid where e.eid=".$this->ebhdb->escape($id);
        return $this->ebhdb->query($sql)->row_array();
    }
}
