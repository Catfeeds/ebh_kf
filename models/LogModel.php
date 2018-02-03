<?php
/**
 * 日志Model类
 */
class LogModel extends CModel
{
	/**
	 * 获取日志列表
	 * @param array $param 参数
	 * @return array 返回日志列表
	 */
	public function getLogList($param)
	{
		$wherearr = array();
		$sql = 'SELECT * FROM kf_log';
		if (!empty($param['q']))
		{
    		$wherearr[] =  "username like '%" . $this->db->escape_str($param['q']) . "%'";
    	}
		if (!empty($param['module']))
		{
    		$wherearr[] =  'module=' . $this->db->escape($param['module']);
    	}
		if (!empty($param['startdate']))
		{
    		$wherearr[] =  'dateline>' . $param['startdate'];
    	}
		if (!empty($param['enddate']))
		{
    		$wherearr[] =  'dateline<' . $param['enddate'];
    	}
		if (!empty($wherearr))
    	{
			$sql.= ' WHERE '.implode(' AND ',$wherearr);
    	}
    	
		$sql .= ' ORDER BY logid desc';
    	if (!empty($param['limit']))
		{
    		$sql.= ' limit ' . $param['limit'];
    	}
		//echo $sql;
    	return $this->db->query($sql)->list_array();
	}
	
	/*
     * 日志总数
     * @param array $param
     * @return int 返回日志总数
    */
    public function getLogCount($param)
	{
    	$wherearr = array();
    	$sql = 'select count(*) as count from kf_log';
		if (!empty($param['q']))
		{
    		$wherearr[] =  "username like '%" . $this->db->escape_str($param['q']) . "%'";
    	}
		if (!empty($param['module']))
		{
    		$wherearr[] =  'module=' . $this->db->escape($param['module']);
    	}
		if (!empty($param['startdate']))
		{
    		$wherearr[] =  'dateline>' . $param['startdate'];
    	}
		if (!empty($param['enddate']))
		{
    		$wherearr[] =  'dateline<' . $param['enddate'];
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
     * 写日志
     * @param array $param
     * @return 返回insert
    */
    public function writeLog($param)
	{
		$setarr = array();
		if (isset($param['uid']))
			$setarr['uid'] = $param['uid'];
		if (isset($param['username']))
			$setarr['username'] = $param['username'];
		if (isset($param['realname']))
			$setarr['realname'] = $param['realname'];
		if (isset($param['module']))
			$setarr['module'] = $param['module'];
		if (isset($param['operation']))
			$setarr['operation'] = $param['operation'];
		if (isset($param['objectname']))
			$setarr['objectname'] = $param['objectname'];
		if (isset($param['objectid']))
			$setarr['objectid'] = $param['objectid'];
		if (isset($param['info']))
			$setarr['info'] = $param['info'];
		if (isset($param['ip']))
			$setarr['ip'] = $param['ip'];
		if (isset($param['dateline']))
			$setarr['dateline'] = $param['dateline'];
		return $this->db->insert('kf_log',$setarr);
	}
}