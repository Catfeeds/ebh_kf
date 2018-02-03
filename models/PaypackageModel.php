<?php
/*
服务包
*/
class PaypackageModel extends CEbhModel{
	/**
	*获取服务包列表
	*/
	public function getlist($param){
		$temp = array();
		$sql = 'select p.pid,p.pname from ebh_pay_packages p';
		$wherearr = array();
		if(isset($param['crid'])) {	//所属crid
			$wherearr[] = 'p.crid=' . intval($param['crid']);
		}
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY pid desc';
        }
		$list = $this->ebhdb->query($sql)->list_array();
		foreach ($list as $value)
		{
			$temp[$value['pid']] = $value['pname'];
		}
		return $temp;
	}
	
	/**
	*根据itemid获取服务明细项详情
	*/
	public function getPackByPid($pid) {
		$sql = "select p.pid,p.pname,p.summary,p.crid,cr.crname,p.displayorder,t.tname,t.tid,p.limitdate from ebh_pay_packages p join ebh_classrooms cr on p.crid=cr.crid left join ebh_pay_terms t on t.tid=p.tid where pid=".$pid;
		return $this->ebhdb->query($sql)->row_array();
	}
}
?>
