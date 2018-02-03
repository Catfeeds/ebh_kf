<?php
/**
 * 后台默认控制器
 */
class DefaultController extends CAdminControl
{
	/**
	 * 框架页面
	 */
	public function index()
	{
		$menulist = $this->model('menu')->getMenuList();
		//format menu list,remove no priviledge menu item
		$menulist = Ebh::app()->lib('Access')->checkMenuAccess($menulist);
		
		$user = Ebh::app()->user->getloginuser();
		$user['role'] = $this->model('role')->getRoleName($user['roleid']);
		$this->assign('user',$user);
		$this->assign('menulist',$menulist);
		$this->display('default/index');
	}
	
	//默认首页
	public function main()
	{
		$user = Ebh::app()->user->getloginuser();
		$user['role'] = $this->model('role')->getRoleName($user['roleid']);
		$this->assign('user',$user);
		
		$uri = Ebh::app()->getUri();
		$page= $uri->uri_page();
		$page = !empty($page) ?  intval($page) : 1;
		$pagesize = 20;
		$limit = (max(($page-1),0)*$pagesize+1) .' , '.$pagesize;
		
		//查询即将到期的网校列表
		$dtdate = 30;//默认查询时间临近30天
		$param = array(
		    'status'=>1,//网校平台正常
		    'dt'=>1,
		    'orderby'=>' c.enddate ASC ',
		    'limit'=>$limit
		);
		$classroommodel = $this->model('Classroom');
		$count = $classroommodel->getClassroomCount($param);
		$list =$classroommodel->getClassroomList($param);
		
		$pagestr = show_page($count,$pagesize);
		$this->assign("count", $count);
		$this->assign('roomlist', $list);
		$this->assign('pagestr', $pagestr);

		
		$this->display('default/main');
	}
	
	//登录页
	public function login()
	{
		$this->display('default/login');
	}
	
	//登录
	public function check()
	{
		$username = $this->input->post('admin_username');
		$password = $this->input->post('admin_password');
		$password = md5($password);
		$status = array('code' => 0, 'message' => '', 'returnurl' => '');
		$user_info = $this->model('user')->getLoginInfo($username);
		if (Ebh::app()->lib('VerifyCode')->checkCode($this->input->post('seccode')) === false)
		{
			$status['message'] = '验证码错误';
		}
		else
		{
			if (!empty($user_info))
			{
				if ($password == $user_info['password'] && $user_info['status'] == 1)
				{
					$uid = $user_info['uid'];
					$pwd = $user_info['password'];
					$auth = authcode("$pwd\t$uid",'ENCODE');
					
					
					//cookie在会话结束时过期			
					$this->input->setcookie('auth',$auth);
					$this->input->setcookie('lasttime',$user_info['lastlogintime']);
					$this->input->setcookie('lastip',$user_info['lastloginip']);
					//保存访问权限列表
					Ebh::app()->lib('Access')->saveAccessList($uid);
					
					//更新上次登录时间、IP和登录次数
					$update = array(
						'lastlogintime' => SYSTIME,
						'lastloginip' => $this->input->getip(),
						'logincount' => 'logincount+1'
					);
					$this->model('user')->updateLoginInfo($update, $uid);					
					
					//记录系统日志
					admin_log('系统登录', '系统登录', '', 0, '', $user_info);
					
					$status['code'] = 1;
					$status['message'] = '登录成功';
					$status['returnurl'] = '/';			
				}
				elseif ($user_info['status'] == 0)
				{
					$status['message'] = '账号被锁定。';
				}
				else
				{
					$status['message'] = '账号或密码不正确。';
				}
			}
			else
			{
				$status['message'] = '账号或密码不正确。';
			}
		}
		echo json_encode($status);
	}
	
	//登出
	public function logout()
	{
		$cookietime = -3600;
		$this->input->setcookie('auth','',$cookietime);
		$this->input->setcookie('accesslist','',$cookietime);
		$this->input->setcookie('lasttime','',$cookietime);
		$this->input->setcookie('lastip','',$cookietime);		
		header("Location: /default/login.html");
	}
	
	/**
	 * 导出所有即将过期的网校列表
	 */
	public function exportexpiredschool(){
	    //error_reporting(0);
	    //查询即将到期的网校列表
	    $dtdate = 30;//默认查询时间临近30天
	    $param = array(
	        'status'=>1,//网校平台正常
	        'dt'=>2,
	        'orderby'=>' c.enddate ASC ',
	    );
	    $classroommodel = $this->model('Classroom');
	    $count = $classroommodel->getClassroomCount($param);
	    $list =$classroommodel->getClassroomList($param);
	    $dataArr = array();
	    $titleArr = array('序号','网校名称','网校域名','开始日期','结束日期','是否已过期','结束倒计时');
	    foreach ($list as $key=>$room){
	        if(!empty($room['enddate'])){
	            $data = array(
	                ($key+1),
	                $room['crname'],
	                !empty($room['fulldomain']) ?'http://'.$room['fulldomain'] : 'http://'.$room['domain'].'.ebh.net',
	                date('Y-m-d',$room['begindate']),
	                date('Y-m-d',$room['enddate']),
	                ($room['enddate'] < SYSTIME) ?  "已过期":"未过期",
	                $this->getdtdate($room['enddate'],SYSTIME)
	            );
	            $dataArr[] = $data;
	        }
	    }
	    
	  //  echo '<pre>';var_dump($dataArr);die;
	    
	    $name='最近一个月即将过期和已经过期的网校列表';
	    $manuallywidth = array(10,40,25,15,15,15,20);
	    $this->_exportExcel($titleArr,$dataArr,'FF808080', $name,$manuallywidth);
	}
	
	/**
	 * 获取时间间隔
	 */
	function  getdtdate($enddata,$nowdata){
	    $fuhao = '';//负
	    if($enddata < $nowdata){
	        $fuhao = '-';//负
	    }else{
	        $temp = $nowdata;
	        $nowdata = $enddata;
	        $enddata = $temp;
	    }
	    list($Y1,$m1,$d1)=explode('-',date(('Y-m-d'),$enddata));
	    list($Y2,$m2,$d2)=explode('-',date("Y-m-d",$nowdata));
	    $y=$Y2-$Y1;
	    $m=$m2-$m1;
	    $d=$d2-$d1;
	    if($d<0){
	        $d+=(int)date('t',strtotime("-1 month ".date("Y-m-d",$nowdata)));
	        $m--;
	    }
	    if($m<0){
	        $m+=12;
	        $y--;
	    }
	    $str = ($y>0) ? $y.'年'  : '';
	    $str .= ($m>0) ? $m.'月' : '';
	    $str .= ($d>=0) ? $d.'天' : '';
	    
	    return $fuhao.$str;
	    //return $fuhao.$y.'年'.$m.'月'.$d.'天';
	    //return round((($enddata)-strtotime(date("Y-m-d",$nowdata)))/3600/24).'天';
	}
	
	
	/**
	 * 导出excel
	 * @param Array array("编号",'用户名','性别'....)
	 * @param Array array('1','李华','男'...)
	 * @param String rgbColor
	 * @param String execl文件名称
	 *
	 */
	protected  function _exportExcel($titleArr,$dataArr,$titleColor="FF808080",$name,$manuallywidth=array()){
	    
	    set_time_limit(0);
	    $objPHPExcel = Ebh::app()->lib('PHPExcel');
	    
	    // 以下是一些设置 ，什么作者  标题啊之类的
	    $objPHPExcel->getProperties()
	    ->setTitle("数据EXCEL导出")
	    ->setSubject("数据EXCEL导出")
	    ->setDescription("备份数据")
	    ->setKeywords("excel")
	    ->setCategory("result file");
	    
	    // 设置列表标题
	    if(is_array($titleArr)){
	        $str = "A";
	        foreach($titleArr as $k=>$v){
	            $p = $str++.'1';//列A1,B1,C1,D1
	            if(empty($manuallywidth))
	                $objPHPExcel->getActiveSheet()->getColumnDimension($str)->setAutoSize(true);//设置列宽_自适应
	                $pt = $objPHPExcel->getActiveSheet()->getStyle($p);
	                
	                $pt->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
	                $pt->getFont()->setSize(14);
	                $pt->getFont()->setBold(true);
	                
	                //$pt->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置列填充模式 solid
	                $pt->getFill()->getStartColor()->setARGB($titleColor);//设置列填充颜色
	                //$pt->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);//设置列边宽
	                $objPHPExcel->getActiveSheet()->setCellValue($p, $v);//设置列名称
	        }
	    }
	    //传值
	    if(is_array($dataArr)){
	        foreach ($dataArr as $k=>$v) {
	            $str = "A";
	            foreach($titleArr as $kt=>$vt){
	                $p = $str.($k+2);//从第二列填充内容 A22,B22...A33 B33
	                $pt = $objPHPExcel->getActiveSheet();
	                if(empty($manuallywidth))
	                    $pt->getColumnDimension($str)->setAutoSize(true);//单元格每项内容自适应
	                    if(is_numeric($v[$kt])){
	                        if(empty($manuallywidth))
	                            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);//A列头标题自适应
	                            $pt->getStyle($p)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);//设置单元格文本存储类型
	                            $pt->getColumnDimension($str)->setWidth(20);//设置单元格宽度
	                            $pt->setCellValue($p, $v[$kt].' ');//填充内容
	                    }else{
	                        $pt->setCellValue($p, ' '.$v[$kt]);
	                    }
	                    
	                    $str++;
	            }
	        }
	    }
	    if(!empty($manuallywidth)){
	        $str = 'A';
	        foreach($manuallywidth as $width){
	            $objPHPExcel->getActiveSheet()->getColumnDimension($str)->setWidth($width);
	            $str++;
	        }
	    }
	    //exit(0);
	    // 输出下载文件 到浏览器
	    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
	    
	    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') || stripos($_SERVER['HTTP_USER_AGENT'], 'trident')) {
	        $name = urlencode($name);
	    } else {
	        $name = str_replace(' ', '', $name);
	    }
	    
	    $filename  = $name.".xls";//文件名,带格式
	    header("Content-type: text/csv");//重要 屏蔽ie等安全提醒
	    header('Content-Type:application/x-msexecl;name="'.$name.'"');
	    header('Content-Disposition: attachment;filename="'.$filename.'"');
	    header('Cache-Control: must-revalidate, post-check=0,pre-check=0');
	    header('Expires:0');
	    header('Pragma:public');
	    $objWriter->save('php://output');
	}
}
?>