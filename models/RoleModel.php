<?php
/**
 *角色Model类
 */
class RoleModel extends CModel
{	
	/**
	 * 获取角色列表
	 * @param array $param 参数
	 * @return array 返回角色列表
	 */
	public function getRoleList($param = array())
	{
		$wherearr = array();
		$sql = 'SELECT * FROM kf_role';		
		if (!empty($param['q']))
		{
    		$wherearr[] = "rolename like '%" . $this->db->escape_str($param['q']) . "%'";
    	}
		if (!empty($param['roleid']))
		{
			$wherearr[] = 'roleid in (' . $this->db->escape_str($param['roleid']) . ')';
		}
		if (!empty($wherearr))
    	{
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
    	}
    	
    	if (!empty($param['limit']))
		{
    		$sql.= ' limit ' . $param['limit'];
    	}
		//echo $sql;
    	return $this->db->query($sql)->list_array();
	}
	
	/*
     * 角色总数
     * @param array $param
     * @return int 返回角色总数
    */
    public function getRoleCount($param)
	{
    	$wherearr = array();
    	$sql = 'select count(*) as count from kf_role';
		if (!empty($param['q']))
		{
    		$wherearr[] =  "rolename like '%" . $this->db->escape_str($param['q']) . "%'";
    	}
		if (!empty($param['roleid']))
		{
			$wherearr[] = 'roleid in (' . $this->db->escape_str($param['roleid']) . ')';
		}
    	if (!empty($wherearr))
		{
    		$sql.= ' WHERE '.implode(' AND ',$wherearr);
    	}
		//echo $sql;
    	$row = $this->db->query($sql)->row_array();
    	return $row['count'];
    }
	
	/**
	 * 获取角色数组
	 * @boolean $include_superadmin 是否包含超级管理员,默认包含
	 * @return 返回角色数组
	 */
	public function getRoleArray($include_superadmin = true)
	{
		$role_array = array();
		$sql = 'SELECT roleid,rolename FROM kf_role';
		if ($include_superadmin === false)
		{
			$sql .= ' WHERE roleid>1';
		}
		$list = $this->db->query($sql)->list_array();
		foreach ($list as $value)
		{
			$role_array[$value['roleid']] = $value['rolename'];
		}
		return $role_array;
	}
	
	/**
	 * 获取单个用户的角色名称
	 * @param string $roleid 角色id字符串
	 * @param array $role_array 角色数组
	 * @return 角色名称字符串
	 */
	public function getRoleName($roleid, $role_array = array())
	{
		if (empty($role_array))
		{
			$role_array = $this->getRoleArray();
		}
		if (empty($roleid))
		{
			return '未分配角色';
		}
		else
		{
			$rolename_array = array();
			$roleid_array = explode(',', $roleid);
			foreach ($roleid_array as $value)
			{
				$rolename_array[] = $role_array[$value];
			}
			return implode(',', $rolename_array);
		}		
	}
	//添加角色
	public function add($param)
	{		
		$setarr = array();
		if (isset($param['rolename']))
			$setarr['rolename'] = $param['rolename'];
		if (isset($param['access']))
			$setarr['access']	= $param['access'];
		if (isset($param['classroom']))
			$setarr['classroom'] = $param['classroom'];
		if (isset($param['status']))
			$setarr['status']	= $param['status'];
		if (isset($param['remark']))
			$setarr['remark'] = $param['remark'];
		if (isset($param['dateline']))
			$setarr['dateline']	= $param['dateline'];
		
		return $this->db->insert('kf_role',$setarr);
	}
	
	//更新角色
	public function update($param, $roleid)
	{
    	$afrows = false;    //影响行数
		$setarr = array();
		if (isset($param['rolename']))
			$setarr['rolename'] = $param['rolename'];
		if (isset($param['access']))
			$setarr['access']	= $param['access'];
		if (isset($param['classroom']))
			$setarr['classroom'] = $param['classroom'];
		if (isset($param['remark']))
			$setarr['remark'] = $param['remark'];			
    	$wherearr = array('roleid' => $roleid);
    	$sarr = array();
    	if (!empty($setarr)) {
    		$afrows = $this->db->update('kf_role', $setarr, $wherearr, $sarr);
    	}
    	return $afrows;
	}
	
	//删除角色
	public function delete($roleid)
	{
		return $this->db->delete('kf_role',array('roleid' => intval($roleid)));
	}
	
	//根据roleid获取一条记录
	public function getOneByRoleid($roleid)
	{
		$sql = 'SELECT * FROM kf_role WHERE roleid=' . intval($roleid);
		return $this->db->query($sql)->row_array();
	}
	
}