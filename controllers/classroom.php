<?php
/**
 * 网校控制器
 */
class ClassroomController extends CAdminControl
{	
	/**
	 * 学校列表
	 */
	public function getlist()
	{
		$htmlstr = '';
		//分页
		$param['page'] = $this->input->post('page');
		$param['page'] = (empty($param['page']) || intval($param['page']) <= 0)? 1 : intval($param['page']);
		$param['q'] = $this->input->post('keyword');
		$param['isschool'] = $this->input->post('isschool');
		
		//仅限有权限的学校
		$checkaccess = $this->input->post('checkaccess');
		if ($checkaccess)
		{
			$classroom_access = Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
			if ($classroom_access == '')
			{
				$htmlstr = '<tr><td colspan="4"><font color="red">未找到符合条件的学校！</font></td></tr>';
				echo $htmlstr;
				exit;
			}
			elseif ($classroom_access != 'ALL')
			{
				$param['access'] = $classroom_access;
			}
		}
		
		$param['page_size'] = 8;
		$param['record_count'] = $this->model('classroom')->getClassroomCount($param);
		$param = page_and_size($param);
		
		if (!empty($param['record_count']))
		{
			$classroomlist = $this->model('classroom')->getClassroomList($param);
			
			$pagestr = show_page_ajax($param['record_count'], $param['page_size']);
			
			foreach($classroomlist as $value)
			{
				$htmlstr .= '<tr style="cursor:pointer" onclick="checkCrItem(\'' . $value['crid'] . '\', \'' . $value['crname'] . '\')" ><td><input type="checkbox" name="crid" id="crid_' . $value['crid'] . '" value="' . $value['crid'] . '"  onclick="reversecheck(\'' . $value['crid'] . '\')" /></td><td>' . $value['domain'] . '</td><td>' . $value['crname'] . '</td><td>' . date("Y-m-d", $value['dateline']) . '</td></tr>';
			}
			
			$htmlstr .= '<tr><td colspan="4">'. $pagestr . '</td></tr>';
		}
		else
		{
			$htmlstr = '<tr><td colspan="4"><font color="red">未找到符合条件的学校！</font></td></tr>';
		}
		  
		echo $htmlstr;
	}
	
	//学校列表FOR开通网校
	public function getlistajax()
	{
		$htmlstr = '';
		//分页
		$param['page'] = $this->input->post('page');
		$param['page'] = (empty($param['page']) || intval($param['page']) <= 0)? 1 : intval($param['page']);
		$param['q'] = $this->input->post('keyword');
		$param['notfree'] = 1;
		
		//仅限有权限的学校
		$classroom_access = Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
		if ($classroom_access == '')
		{
			$htmlstr = '<tr><td colspan="6"><font color="red">未找到符合条件的学校！</font></td></tr>';
			echo $htmlstr;
			exit;
		}
		elseif ($classroom_access != 'ALL')
		{
			$param['access'] = $classroom_access;
		}
		
		$param['page_size'] = 8;
		$param['record_count'] = $this->model('classroom')->getClassroomCount($param);
		$param = page_and_size($param);
		
		if (!empty($param['record_count']))
		{
			$classroomlist = $this->model('classroom')->getClassroomList($param);
			
			$pagestr = show_page_ajax($param['record_count'], $param['page_size']);
			
			foreach($classroomlist as $value)
			{
				$htmlstr .= '<tr><td><input type="checkbox" name="crid" tag="' . $value['crid'] . '" id="crid_' . $value['crid'] . '" value="' . $value['crid'] . '" onclick="renderForm(\'' . $value['crid'] . '\', \'' . $value['crname'] . '\', \'' . $value['crprice'] . '\')" /></td><td>' . $value['domain'] . '</td><td>' . $value['crname'] . '</td><td>' . $value['crprice'] . '</td><td>' . date("Y-m-d", $value['begindate']) . '</td><td>' . date("Y-m-d", $value['enddate']) . '</td></tr>';
			}
			
			$htmlstr .= '<tr><td colspan="6">'. $pagestr . '</td></tr>';
		}
		else
		{
			$htmlstr = '<tr><td colspan="6"><font color="red">未找到符合条件的学校！</font></td></tr>';
		}
		  
		echo $htmlstr;
	}
}