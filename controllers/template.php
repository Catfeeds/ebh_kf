<?php
/*
模板
*/
class TemplateController extends CControl{
	public function getlist(){
		//原先:$tpldir = 'D:/ebh2/views/shop/';
		//modified by zkq in 2014-04-26
		$tpldir = realpath('views/shop/');
		$tplarray = array();
		if(is_dir($tpldir)) {
			$dhandler = opendir($tpldir);
			while(($tpl = readdir($dhandler))!==false) {
				if(strpos($tpl,'.')!==false)
					continue;
				$tplarray[] = array('tplname'=>$tpl);
			}
			closedir($dhandler);
		}
		
		echo json_encode($tplarray);
	}
}
?>