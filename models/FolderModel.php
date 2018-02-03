<?php
/**
 * 课程相关model类 FolderModel
 */
class FolderModel extends CEbhModel{

	    /**
     * 添加课程对应的课件数
     * @param int $folderid 课程编号
     * @param int $num 如为正数则添加，负数则为减少
     */
    public function addcoursenum($folderid,$num = 1) {
        $where = 'folderid='.$folderid;
        $setarr = array('coursewarenum'=>'coursewarenum+'.$num);
        $this->ebhdb->update('ebh_folders',array(),$where,$setarr);
    }
	/*
	添加folder
	@param array $param
	*/
	public function addfolder($param){
	//	print_r($param);exit;
		if(!empty($param['uid']))
			$farr['uid'] = $param['uid'];
		if(!empty($param['crid']))
			$farr['crid'] = $param['crid'];
		if(!empty($param['foldername']))
			$farr['foldername'] = $param['foldername'];
		if(!empty($param['upid']))
			$farr['upid'] = $param['upid'];
		if(!empty($param['folderlevel']))
			$farr['folderlevel'] = $param['folderlevel'];
		if(!empty($param['displayorder']))
			$farr['displayorder'] = $param['displayorder'];
		if(!empty($param['summary']))
			$farr['summary'] = $param['summary'];
		if(!empty($param['img']))
			$farr['img'] = $param['img'];
		if(!empty($param['grade']))
			$farr['grade'] = $param['grade'];
		if(isset($param['fprice']))
			$farr['fprice'] = $param['fprice'];
		if(isset($param['speaker']))
			$farr['speaker'] = $param['speaker'];
		if(isset($param['detail']))
			$farr['detail'] = $param['detail'];
		$folderid = $this->ebhdb->insert('ebh_folders',$farr);
		if(!empty($param['folderpath']))
		$setarr['folderpath'] = $param['folderpath'].$folderid.'/';
		$wherearr['folderid'] = $folderid;
		$this->ebhdb->update('ebh_folders',$setarr,$wherearr);
		return $folderid;
	}
	/**
	 * 通过网校crid获得课程信息
	 * @param  [type] $crid [description]
	 * @return [type]       [description]
	 */
	public function getFolderbyCrid($crid){
		$sql="select f.folderid,f.foldername,f.grade from ebh_folders f  where f.folderlevel<>1 and f.crid=".$this->ebhdb->escape($crid);
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	 * 通过课程id查找课程信息
	 * @param  [type] $folderid [description]
	 * @return [type]           [description]
	 */
	public  function getFolder($folderid){
		$sql="select f.folderid,f.crid,f.foldername,f.grade from ebh_folders f  where f.folderid=".$this->ebhdb->escape($folderid);
		// echo $sql;
		return $this->ebhdb->query($sql)->row_array();
	}
		/*
	选择课程任课教师
	@param array $param
	*/
	public function chooseteacher($param){
		if(!empty($param['folderid'])){
			$wherearr['folderid'] = $param['folderid'];
			//return $wherearr;
			$this->ebhdb->delete('ebh_teacherfolders',$wherearr);
		}
		$idarr = explode(',',$param['teacherids']);
		foreach($idarr as $id){
			$tfarr = array('tid'=>$id,'folderid'=>$param['folderid'],'crid'=>$param['crid']);
			$this->ebhdb->insert('ebh_teacherfolders',$tfarr);
		}
	}
	    /**
     * 根据课程编号获取课程详情信息
     * @param int $folderid 课程编号
     * @return array 课程信息数组 
     */
    public function getfolderbyid($folderid) {
    	if(empty($folderid))return false;
        $sql = 'select f.folderid,f.foldername,f.displayorder,f.img,f.coursewarenum,f.summary,f.grade,f.district,f.upid,f.folderlevel,f.folderpath,f.fprice,f.speaker,f.detail,f.viewnum from ebh_folders f where f.folderid='.$folderid;
		return $this->ebhdb->query($sql)->row_array();
    }
    /*
    设置年级
    */
    public function setGrade($param){
    	return $this->ebhdb->update('ebh_folders',array('grade'=>intval($param['grade'])),array('folderid'=>$param['folderid']));
    }

	
}
