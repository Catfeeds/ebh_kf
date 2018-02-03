<?php

/**
 * 教室教师管理关系Model类 RoomteacherModel
 */
class RoomteacherModel extends CEbhModel {

    public function insert($param) {
        if (empty($param['crid']) || empty($param['tid']))
            return FALSE;
        $setarr = array();
        $setarr['crid'] = intval($param['crid']);
        $setarr['tid'] = $param['tid'];
        if (isset($param['status']))
            $setarr['status'] = $param['status'];
        if (!empty($param['role'])) {
            $setarr['role'] = $param['role'];
        }
        if (!empty($param ['cdateline'])) {
            $setarr['cdateline'] = $param['cdateline'];
        } else {
            $setarr['cdateline'] = SYSTIME;
        }
        $afrows = $this->ebhdb->insert('ebh_roomteachers', $setarr);
		$this->ebhdb->update('ebh_classrooms', array(), array('crid' => $param['crid']), array('teanum' => 'teanum+1'));
        return $afrows;
    }

    /**
     * 更新教室内的教师信息，需要带上$crid和$tid
     * @param type $param
     */
    public function update($param) {
        if (empty($param['crid']) || (empty($param['tid'])&&empty($param['role'])))
            return FALSE;
        $wherearr = array('crid' => $param['crid']);
		if(!empty($param['tid']))
			$wherearr['tid'] = $param['tid'];
		if(!empty($param['role']))
			$wherearr['role'] = $param['role'];
        $setarr = array();
        if (isset($param['status'])) { //状态，1正常 0 锁定
            $setarr ['status'] = $param['status'];
        }
		if(!empty($param['changetid']))
			$setarr['tid'] = $param['changetid'];
		if(!empty($param['changerole']))
			$setarr['role'] = $param['changerole'];
        if (empty($setarr))
            return FALSE;
        $afrows = $this->ebhdb->update('ebh_roomteachers', $setarr, $wherearr);
		return $afrows;
    }

    /**
     * 删除教室内的教师并更新教室教师数
     * @param type $param
     * @return boolean
     */
    public function del($param) {
        if (empty($param['crid']) || (empty($param['tid'])&&empty($param['role'])))
            return FALSE;
        $wherearr = array('crid' => $param['crid']);
		if(!empty($param['tid']))
			$wherearr['tid'] = $param['tid'];
		if(!empty($param['role']))
			$wherearr['role'] = $param['role'];
        $this->ebhdb->begin_trans();
        $afrows = $this->ebhdb->delete('ebh_roomteachers', $wherearr);
        if ($afrows > 0) {
            $this->ebhdb->update('ebh_classrooms', array(), array('crid' => $param['crid']), array('teanum' => 'teanum-1'));
        }
        if ($this->ebhdb->trans_status() === FALSE) {
            $this->ebhdb->rollback_trans();
            return FALSE;
        } else {
            $this->ebhdb->commit_trans();
        }
        return TRUE;
    }

    /**
     * 根据教室编号获取教师列表，一般适合于教师网校的教师列表
     * @param type $param
     * @param boolean $showcoursenum 是否显示教师在该平台的课件数
     * @return boolean
     */
    public function getroomteacherlist($param, $showcoursenum = FALSE) {
       
        if (empty($param['page']) || $param['page'] < 1)
            $page = 1;
        else
            $page = $param['page'];
        $pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
        $start = ($page - 1) * $pagesize;
        $sql = 'select u.uid,u.username,u.realname,u.sex,u.face,u.email,u.mobile,rt.status as tstatus,rt.cdateline,rt.role from ebh_roomteachers rt ' .
                'join ebh_users u on (rt.tid = u.uid) ';
        $wherearr = array();
         if (!empty($param['crid'])){
             $wherearr[] = 'rt.crid=' . $param['crid'];
         }
       
        if (isset($param['status']))
            $wherearr[] = 'rt.status=' . $param['status'];
        if (!empty($param['q'])) {
            $q = $this->ebhdb->escape_str($param['q']);
            $wherearr[] = '(u.username like \'%' . $q . '%\' OR u.realname like \'%' . $q . '%\')';
        }
        if (!empty($wherearr)){
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        }
        $sql.='group by u.uid';//去掉重复记录
        if (!empty($param['order']))
            $sql .= ' ORDER BY ' . $param['order'];
        else
            $sql .= ' ORDER BY rt.cdateline DESC';
        $sql .= ' limit ' . $start . ',' . $pagesize;
        $list = $this->ebhdb->query($sql)->list_array();
        if ($showcoursenum && !empty($list)) {    //显示课件数
            $newlist = array();
            $tids = '';
            foreach ($list as $teacher) {
                if (empty($tids))
                    $tids = $teacher['uid'];
                else
                    $tids .= ',' . $teacher['uid'];
                $teacher['coursenum'] = 0;
                $newlist[$teacher['uid']] = $teacher;
            }
            $numsql = 'select c.uid,count(*) count from ebh_roomcourses rc ' .
                    'join ebh_coursewares c on (rc.cwid = c.cwid) ' .
                    'where rc.crid=' . $param['crid'] . ' and c.uid in (' . $tids . ') ' .
                    'group by c.uid';
            $numlist = $this->ebhdb->query($numsql)->list_array();
            foreach ($numlist as $numitem) {
                $newlist[$numitem['uid']]['coursenum'] = $numitem['count'];
            }
            $list = $newlist;
        }
        return $list;
    }

    /**
     * 根据教室编号获取教师列表记录数，一般适合于教师网校的教师列表
     * @param type $param
     * @return boolean
     */
    public function getroomteachercount($param) {
        $count = 0;

        $sql = 'select count(*) count from ebh_roomteachers rt ' .
                'join ebh_users u on (rt.tid = u.uid)';
        $wherearr = array();
        if (!empty($param['crid'])){
            $wherearr[] = 'rt.crid=' . $param['crid'];
        } 

        if (isset($param['status']))
            $wherearr[] = 'rt.status=' . $param['status'];
        if (!empty($param['q'])) {
            $q = $this->ebhdb->escape_str($param['q']);
            $wherearr[] = '(u.username like \'%' . $q . '%\' OR u.realname like \'%' . $q . '%\')';
        }
        if (!empty($wherearr)){
            $sql .= ' WHERE ' . implode(' AND ', $wherearr);
        }
        $row = $this->ebhdb->query($sql)->row_array();
        if (!empty($row)){
            $count = $row['count'];
        }
        return $count;
    }


}
