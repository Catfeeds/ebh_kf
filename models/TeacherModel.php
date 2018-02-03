<?php

/*
  教师
 */

class TeacherModel extends CEbhModel {
    /*
      教师列表
      @param array $param
      @return array
     */

    public function getteacherlist($param) {
        $wherearr = array();
        $sql = 'select u.uid,u.realname,u.username,u.nickname,u.citycode,u.status,u.mobile,u.credit,u.logincount,t.tag,t.phone,t.agency,a.realname as agentname from ebh_teachers t left join ebh_users u on t.teacherid=u.uid left join ebh_agents a on t.agentid=a.agentid';
        if (!empty($param['q']))
            $wherearr[] = ' ( u.realname like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' or u.username like \'%' . $this->ebhdb->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        $sql.=' order by teacherid desc';
        if (!empty($param['limit']))
            $sql.= ' limit ' . $param['limit'];

        return $this->ebhdb->query($sql)->list_array();
    }

    /*
      教师总数
      @param array $param
      @return int
     */

    public function getteachercount($param) {
        $wherearr = array();
        $sql = 'select count(*) count from ebh_teachers t left join ebh_users u on t.teacherid=u.uid';
        if (!empty($param['q']))
            $wherearr[] = ' ( u.realname like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' or u.username like \'%' . $this->ebhdb->escape_str($param['q']) . '%\')';
        if (!empty($wherearr))
            $sql.= ' WHERE ' . implode(' AND ', $wherearr);
        //var_dump($sql);
        $count = $this->ebhdb->query($sql)->row_array();
        return $count['count'];
    }
        /**
     *判断教师是否存在
     *@author zkq
     *@param int $teacherid
     *@return  bool
     */
    public function isExists($teacherid=0){
      $teacherid = intval($teacherid);
      if(empty($teacherid)){
        return false;
      }
      $sql = 'select count(*) count from ebh_teachers t where t.teacherid = '.$teacherid.' limit 1 ';
      $res = $this->ebhdb->query($sql)->row_array();
      if(empty($res['count'])){
        return false;
      }else{
        return true;
      }
    }
  /*
  获取学校的教师列表
  @param int $crid
  @param array $param
  */
  public function getroomteacherlist($crid,$param=null){
    $sql = 'SELECT u.sex,u.face,u.mobile,u.uid,u.username,t.teacherid,t.realname,0 as folderid 
      from ebh_roomteachers rt 
      join ebh_users u on(rt.tid=u.uid) 
      join ebh_teachers t on(t.teacherid=u.uid)';
    
    $wherearr[] = 'rt.crid='.$crid;
    if (!empty($param['q']))
            $wherearr[] = ' (u.username like \'%' . $this->db->escape_str($param['q']) . '%\' or u.realname like \'%' . $this->db->escape_str($param['q']) . '%\')';
    if(isset($param['schoolname'])){
      $wherearr[] = 'u.schoolname = \''.$this->db->escape_str($param['schoolname']).'\'';
    }
    if(!empty($wherearr))
      $sql.= ' where '.implode(' AND ',$wherearr);
    if(!empty($param['order']))
      $sql.= ' order by '.$param['order'];
    if(!empty($param['limit']))
      $sql.= ' limit '.$param['limit'];
    else {
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

  public function getFolderTeacherList($fid){
    $sql = 'SELECT u.sex,u.face,u.mobile,u.uid,u.username,t.teacherid,t.realname,0 as folderid 
      from ebh_teacherfolders tf 
      join ebh_users u on(tf.tid=u.uid) 
      join ebh_teachers t on(t.teacherid=u.uid) where tf.folderid='.$this->ebhdb->escape($fid);
      return $this->ebhdb->query($sql)->list_array();
  }
  /**
   * 获取某个班级的教师列表
   * @param  [type] $classid [description]
   * @return [type]          [description]
   */
  public function getClassTeacherList($classid){
      $sql = 'SELECT u.sex,u.face,u.mobile,u.uid,u.username,t.teacherid,t.realname,0 as folderid 
      from ebh_classteachers ct 
      join ebh_users u on(ct.uid=u.uid) 
      join ebh_teachers t on(t.teacherid=u.uid) where ct.classid='.$this->ebhdb->escape($classid);
      // echo $sql;
      return $this->ebhdb->query($sql)->list_array();
  }
    /**
  * 添加教师的课件数
  * @param int uid 教师用户编号
  * @param int num 添加的课件数量
  */
  public function addcoursenum($uid,$num = 1) {
    $where = 'teacherid='.$uid;
        $setarr = array('cwcount'=>'cwcount+'.$num);
        $this->ebhdb->update('ebh_teachers',array(),$where,$setarr);
  }
	
}
