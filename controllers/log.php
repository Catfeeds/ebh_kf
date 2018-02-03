<?php
/**
 * 日志控制器
 */
class LogController extends CAdminControl
{
	public function __construct()
	{
		parent::__construct();
		//检查权限
		Ebh::app()->lib('Access')->checkModuleAccess('adminlog');
	}
	
	public function index()
	{
		//分页
		$param['q'] = $this->input->get('q');
		$param['module'] = $this->input->get('module');
		$startdate = $this->input->get('startdate');
		$enddate = $this->input->get('enddate');
		if ($param['q'] == '请输入用户名')
		{
			$param['q'] = '';
		}
		if (!empty($startdate))
		{
			$param['startdate'] = strtotime($startdate);
		}
		if (!empty($enddate))
		{
			$param['enddate'] = strtotime($enddate) + 86400;
		}
			
		$param['username'] = $this->input->get('username');
		$param['module'] = $this->input->get('module');
		$param['page_size'] = 20;
		$param['record_count'] = $this->model('log')->getLogCount($param);
		$param = page_and_size($param);//分页信息
		$loglist = $this->model('log')->getLogList($param);
		$pagestr = show_page($param['record_count'], $param['page_size']);

		$this->assign('pagestr', $pagestr);
		$this->assign('page', $param['page']);
		$this->assign('pagesize', $param['page_size']);
		$this->assign('q', $param['q']);
		$this->assign('module', empty($param['module']) ? '全部' : $param['module']);
		$this->assign('startdate', $startdate);
		$this->assign('enddate', $enddate);
		$this->assign('loglist', $loglist);
		$this->display('log/index');
	}
}