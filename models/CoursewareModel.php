<?php

/**
 * CoursewareModel 课件Model类
 */
class CoursewareModel extends CEbhModel {
      /**
     * 获取课件详情
     * @param int $cwid
     * @return array
     */
    public function getcoursedetail($cwid) {
        $sql = 'select c.cwid,c.uid,c.catid,c.title,c.thumb,c.tag,c.logo,c.images,c.verifyprice,c.edition,c.summary,c.message,c.cwname,c.cwsource,c.cwurl,cwsize,c.dateline,c.ispreview,u.username,u.realname,rc.crid,rc.folderid,rc.sid,rc.isfree,rc.cdisplayorder,f.foldername,c.viewnum,c.ism3u8,c.isrtmp,c.islive,c.liveid,
                ck.admin_status,ck.admin_remark,ck.teach_uid,ck.teach_status,ck.teach_remark,ck.del,ck.admin_dateline,ck.teach_dateline,ck.delline,ck.admin_ip,ck.teach_ip ,ck.admin_uid ' .
                'from ebh_coursewares c ' .
                'join ebh_roomcourses rc on (c.cwid = rc.cwid) ' .
                'join ebh_users u on (u.uid = c.uid) ' .
                'left join ebh_folders f on (f.folderid = rc.folderid) ' .
                'left join ebh_billchecks ck on ck.toid = c.cwid '.
                'where c.cwid=' . $cwid;
        return $this->ebhdb->query($sql)->row_array();
    }

    /*
      后台获取课件数量
      @param array $param
      @return int
     */
    public function getcoursewarecount($param) {
        $sql = 'select count(*) count 
                from ebh_coursewares c 
                left join ebh_roomcourses rc on rc.cwid = c.cwid
                left join ebh_billchecks ck on ck.toid = c.cwid and ck.type=1';
        if (isset($param['q'])&&$param['q']!=''){
            $qstr = $this->ebhdb->escape_str($param['q']);
            $wherearr[] = ' (c.title like \'%' . $qstr. '%\' )';          
        }
        if(!empty($param['access'])){
            $wherearr[]='rc.crid in ('.$this->ebhdb->escape_str($param['access']).')';
        }
            //管理员
        if($param['role']=='admin'){
            if($param['admin_status']>0){
                $wherearr[] = '(ck.teach_status ='.$param['admin_status']. ') or (ck.admin_status='.$param['admin_status']. ')';
            }
            if($param['cat']==0){
                $wherearr[] = '(ck.teach_status is null or ck.teach_status = 0 ) and (ck.admin_status is null or ck.admin_status = 3)';
            }
            if($param['cat']==1){
                $wherearr[] = '(ck.teach_status>0 or ck.admin_status>0) and ck.del=0';
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
                $wherearr[] = 'ck.teach_status is null';
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
        if (!empty($wherearr))
            $sql.= ' where ' . implode(' AND ', $wherearr);
        //echo $sql;
        $count = $this->ebhdb->query($sql)->row_array();
        return $count['count'];
    }

   /*
      后台获取课件列表
      @param array $param
      @return array 列表数组
     */

    public function getcoursewarelist($param) {
        $sql = 'select c.uid,c.cwid,c.cwurl,c.islive,c.cwsource,c.title,c.dateline,c.sub_title,c.cwurl,c.cwsource,c.viewnum,c.status,c.price,ck.admin_status,ck.teach_uid,ck.teach_status,ck.del,ck.admin_uid,rc.crid,rc.folderid
             from ebh_coursewares c
            left join ebh_roomcourses rc on rc.cwid = c.cwid 
            left join ebh_billchecks ck on ck.toid = c.cwid and ck.type=1';
        if (isset($param['q'])&&$param['q']!=''){
            $qstr = $this->ebhdb->escape_str($param['q']);
            $wherearr[] = ' (c.title like \'%' . $qstr. '%\' )';
        }
        if(!empty($param['access'])){
            $wherearr[]='rc.crid in ('.$this->ebhdb->escape_str($param['access']).')';
        }
        //管理员
        if($param['role']=='admin'){
            if($param['admin_status']>0){
                $wherearr[] = '(ck.teach_status ='.$param['admin_status']. ') or (ck.admin_status='.$param['admin_status']. ')';
            }
            if($param['cat']==0){
                $wherearr[] = '(ck.teach_status is null or ck.teach_status = 0 ) and (ck.admin_status is null or ck.admin_status = 3)';
            }
            if($param['cat']==1){
                $wherearr[] = '(ck.teach_status>0 or ck.admin_status>0) and ck.del=0';
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
                $wherearr[] = 'ck.teach_status is null';
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
        if (!empty($wherearr))
            $sql.= ' where ' . implode(' AND ', $wherearr);
        if(!empty($param['orderby'])){
            $sql.=' order by '.$param['orderby'];
        }else{
            $sql.=' order by cwid desc';
        }
        if (!empty($param['limit']))
            $sql.= ' limit ' . $param['limit'];
        $rows =  $this->ebhdb->query($sql)->list_array();
        //下面是对应优化代码
        $uidstr = '';
        $cridstr = '';
        $folderidstr = '';
        foreach($rows as $key=>$row){
            if(!empty($row['uid'])){
                $uidstr.=$row['uid'].',';
            }
            if(!empty($row['crid'])){
                $cridstr.= $row['crid'].',';
            }
            if(!empty($row['folderid'])){
                $folderidstr .= $row['folderid'].',';
            }
        }
        $uidstr = rtrim($uidstr, ',');
        $cridstr = rtrim($cridstr, ',');
        $folderidstr = rtrim($folderidstr, ',');
        //用户信息
        if($uidstr!=''){
            $usql = 'select uid,username,realname from ebh_users where uid in('.$uidstr.')';
            $uidrows =  $this->ebhdb->query($usql)->list_array();
            $uidrows = $this->_arraycoltokey($uidrows,'uid');
        }
        //网校信息
        if($cridstr!=''){
            $ssql = 'select crid,crname from ebh_classrooms where crid in('.$cridstr.')';
            $cridrows =  $this->ebhdb->query($ssql)->list_array();
            $cridrows = $this->_arraycoltokey($cridrows,'crid');
        }
        //分类名称
        if($folderidstr!=''){
            $fsql =  'select folderid,foldername from ebh_folders where folderid in('.$folderidstr.')';
            $folderidrows =  $this->ebhdb->query($fsql)->list_array();
            $folderidrows = $this->_arraycoltokey($folderidrows,'folderid');
        }
        
        foreach($rows as &$row){
            $row['username'] = $uidrows[$row['uid']]['username'];
            $row['realname'] = $uidrows[$row['uid']]['realname'];
            $row['crname'] = $cridrows[$row['crid']]['crname'];
            $row['foldername'] = $folderidrows[$row['folderid']]['foldername'];
        } 
        /* echo '<pre>';
        var_dump($rows); */
        return $rows;
    }
    public function getSchoolName($id){
        $sql='select cr.crname from ebh_coursewares c left join ebh_roomcourses rc on rc.cwid = c.cwid left join ebh_classrooms cr on rc.crid=cr.crid where c.cwid='.intval($id);
        $info=$this->ebhdb->query($sql)->row_array();
        return $info['crname'];
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
     * 获取播放课件时用到的课件详情数据
     * @param int $cwid
     * @return array
     */
    public function getplaycoursedetail($cwid) {
        $sql = 'SELECT cw.cwurl,cw.cwsource,cw.m3u8url,cw.thumb,cw.title,cw.status,cw.ispreview,cw.apppreview,r.isfree,cr.isschool,cr.isshare,cr.ispublic,r.crid,cr.upid,f.fprice,f.folderid FROM ebh_coursewares cw JOIN '
                . 'ebh_roomcourses r ON cw.cwid=r.cwid  left JOIN ebh_folders f ON r.folderid = f.folderid JOIN '
                . 'ebh_classrooms cr ON cr.crid = r.crid where cw.cwid=' . $cwid;
        return $this->ebhdb->query($sql)->row_array();
    }
    public function getClassroomId($id){
        $sql='select cr.crid from ebh_coursewares c left join ebh_roomcourses rc on rc.cwid = c.cwid left join ebh_classrooms cr on rc.crid=cr.crid where c.cwid='.intval($id);
        $info=$this->ebhdb->query($sql)->row_array();
        return $info['crid'];
    }

  
}

