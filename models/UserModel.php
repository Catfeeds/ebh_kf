<?php
/**
 *客服Model类
 */
class UserModel extends CModel
{
	/**
	 * 获取用户登录信息
	 * @param string $username 用户名
	 * @return array 返回用户信息
	 */
	public function getLoginInfo($username)
	{
		$sql = 'SELECT uid,username,password,status,lastlogintime,lastloginip,realname FROM kf_user WHERE username=' . $this->db->escape($username);
		return $this->db->query($sql)->row_array();
	}
	
	/**
	 * 更新登录信息
	 * @param array $param 用户信息
	 * @param int $uid 用户ID
	 * @return boolean 返回影响行数
	 */
	public function updateLoginInfo($param, $uid)
	{
		$afrows = FALSE;    //影响行数
    	$userarr = array();
    	if(!empty($param['lastlogintime']))
    		$userarr['lastlogintime'] = $param['lastlogintime'];
    	if(!empty($param['lastloginip']))
    		$userarr['lastloginip'] = $param['lastloginip'];
    	$sarr = array();
    	if(isset($param['logincount']))
    		$sarr['logincount'] = 'logincount+1';
    	$wherearr = array('uid' => $uid);
    	if (!empty($userarr)) {
    		$afrows = $this->db->update('kf_user', $userarr, $wherearr, $sarr);
    	}
    	return $afrows;
	}
	
	/**
     * 用uid和password判断登录
     * @param int $uid
     * @return array 返回用户信息数组，无用户返回false
     */
    public function getUserInfo($uid)
	{
    	$sql = 'select uid,username,realname,roleid,logincount,password,lastloginip,status from kf_user where uid=' . intval($uid);
    	$user = $this->db->query($sql)->row_array();
    	return $user;
    }
	
	/**
	 * 获取客服列表
	 * @param array $param 参数
	 * @return array 返回用户列表
	 */
	public function getUserList($param)
	{
		$wherearr = array();
		$sql = 'SELECT uid,username,roleid,status,lastlogintime,logincount,lastloginip,realname FROM kf_user';
		if (!empty($param['q']))
		{
    		$wherearr[] =  "(realname like '%" . $this->db->escape_str($param['q']) . "%' or username like '%" . $this->db->escape_str($param['q']) . "%')";
    	}
		if (!empty($param['roleid']))
		{
    		$wherearr[] =  "(CONCAT(',',roleid,',') like '%," . $param['roleid'] . ",%')";
    	}
		if (!empty($wherearr))
    	{
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
    	}
    	
		$sql .= ' ORDER BY uid desc';
    	if (!empty($param['limit']))
		{
    		$sql.= ' limit ' . $param['limit'];
    	}
		//echo $sql;
    	return $this->db->query($sql)->list_array();
	}
	
	/*
     * 客服总数
     * @param array $param
     * @return int 返回用户总数
    */
    public function getUserCount($param)
	{
    	$wherearr = array();
		$sql = 'select count(*) as count from kf_user';
		if (!empty($param['q']))
		{
    		$wherearr[] =  "(realname like '%" . $this->db->escape_str($param['q']) . "%' or username like '%" . $this->db->escape_str($param['q']) . "%')";
    	}
		if (!empty($param['roleid']))
		{
    		$wherearr[] =  "(CONCAT(',',roleid,',') like '%," . $param['roleid'] . ",%')";
    	}
		if (!empty($wherearr))
    	{
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
    	}
		//echo $sql;
    	$row = $this->db->query($sql)->row_array();
    	return $row['count'];
    }
	
	/*
	 * 根据uid获取用户信息
	 *	@param intval $uid 用户ID
	 * @return 返回用户信息
	 */
	public function getOneByUid($uid)
	{
		if (empty($uid))
			return FALSE;
		$sql = 'SELECT * FROM kf_user WHERE uid=' . intval($uid);
		return $this->db->query($sql)->row_array();
	}
	/*
     * 添加客服
     * @param array $param
     * @return
     */
	public function add($param)
	{
		$setarr = array();
		if (!empty($param['username']))
			$setarr['username'] = $param['username'];
		if (!empty($param['password']))
			$setarr['password'] = md5($param['password']);	
		if (!empty($param['realname']))
			$setarr['realname'] = $param['realname'];	
		if (isset($param['sex']))
			$setarr['sex'] = $param['sex'];
		if (isset($param['roleid']))
			$setarr['roleid'] = $param['roleid'];	
		if (!empty($param['mobile']))
			$setarr['mobile'] = $param['mobile'];
		if (!empty($param['address']))
			$setarr['address'] = $param['address'];
		if (!empty($param['email']))
			$setarr['email'] = $param['email'];
		if (!empty($param['remark']))
			$setarr['remark'] = $param['remark'];
		if (!empty($param['dateline']))
			$setarr['dateline'] = $param['dateline'];
		
		return $this->db->insert('kf_user',$setarr);
	}
	
	//更新客服
	public function update($param, $uid)
	{
    	$afrows = false;    //影响行数
		$setarr = array();
		if (!empty($param['password']))
			$setarr['password'] = md5($param['password']);	
		if (!empty($param['realname']))
			$setarr['realname'] = $param['realname'];
		if (isset($param['sex']))
			$setarr['sex'] = $param['sex'];
		if (isset($param['roleid']))
			$setarr['roleid'] = $param['roleid'];	
		if (!empty($param['mobile']))
			$setarr['mobile'] = $param['mobile'];
		if (!empty($param['address']))
			$setarr['address'] = $param['address'];
		if (!empty($param['email']))
			$setarr['email'] = $param['email'];
		if (!empty($param['remark']))
			$setarr['remark'] = $param['remark'];		
    	$wherearr = array('uid' => $uid);
    	$sarr = array();
    	if (!empty($setarr)) {
    		$afrows = $this->db->update('kf_user', $setarr, $wherearr, $sarr);
    	}
    	return $afrows;
	}
	
	//删除客服
	public function delete($uid)
	{
		return $this->db->delete('kf_user',array('uid' => intval($uid)));
	}
	
	//锁定客服
	public function lock($uid)
	{
		return $this->db->update('kf_user', array('status' => 0), array('uid' => $uid));
	}
	
	//解锁客服
	public function unlock($uid)
	{
		return $this->db->update('kf_user', array('status' => 1), array('uid' => $uid));
	}
	    /**
     * 用uid和password判断登录
     * @param type $uid
     * @param type $userpass
     * @param boolean $iscoding 是否加密过密码
     * @return boolean 返回用户信息数组
     */
    public function getloginbyuid($uid,$userpass,$iscoding = FALSE) {
        $pwd = $iscoding ? $userpass : md5($userpass);
        $sql = "select u.uid,u.password,u.status,r.classroom from kf_user u left join kf_role r on u.roleid=r.roleid where uid=$uid";
        $user = $this->db->query($sql)->row_array();
        if(empty($user) || $user['password'] != $pwd || $user['status'] == 0) {
            return false;
        }
        return $user;
    }
    //获取客服名称列表
    public function getUsernameList(){
        $sql = 'select realname,username,uid from kf_user';
        return $this->db->query($sql)->list_array();
    }
    /**
     * 获取user信息
     */
    public  function  getUsernameByUidarr($arr){
        if(empty($arr)){
            return false;
        }
        $sql = 'select username,uid from `kf_user` where uid in('.implode(',',$arr).')';
        return $this->db->query($sql)->list_array();
    }
}