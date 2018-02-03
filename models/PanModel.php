<?php
/**
 * 用户信息云盘使用，及分配情况model
 */
class PanModel extends CModel{
	public function __construct() {
		parent::__construct();
		$this->pandb = Ebh::app()->getOtherDb('pandb');
	}

	/**
	 *获取某个网校下所有用户云盘使用的信息
	 */
	public function getCridPanUserinfos($uidstr,$crid){
		if(empty($crid))
			return false;
		$sql = 'SELECT filesize,uid FROM pan_userinfos WHERE uid IN(' . $uidstr . ') AND crid=' . intval($crid);
		$row = $this->pandb->query($sql)->list_array();
		return $row ;
	}

    /*
    获取云盘详情，给教师后台输出的
    @param int $crid
    @param int $isFromAdmin,是否后台，直接返回
    @return array 
    */
    public function getClassroomPaninfo($crid,$isFromAdmin=0){
        $sql = 'select c.totalpansize,c.usepansize,c.defaultpansize from pan_giveinfos c where c.crid='.$crid;
        $info = $this->pandb->query($sql)->row_array();
        if ($isFromAdmin || $info) {
        	return $info;
        }
    	$info['totalpansize'] = 0;
    	$info['noinfo'] = 1;
    	$info['defaultpansize'] = 1024;
    	$info['totalpansize'] += $info['defautsize'];//每个人默认1G
    	$res = $this->getCridPanUsesize($crid);
		$info['usepansize'] = empty($res['sum']) ? 0 : ceil($res['sum']/1048576);//byte换成兆
        return $info;
    }

    /*
    获取云盘详情，给教师后台输出的
    @param int $crid
    @param int $isAdmin,是否后台，直接返回
    @return array 
    */
    public function setClassroomPaninfo($param){
    	$crid = intval($param['crid']);
        $sql = 'select c.totalpansize,c.usepansize,c.defaultpansize from pan_giveinfos c where c.crid='.$crid;
        $info = $this->pandb->query($sql)->row_array();
        if (!$info) {
        	return 0;
        } else {
        	$size = $this->getMaxCridPanUsesize($crid);//所有用户最大的使用量
        	if ($param['defaultpansize']*1024*1024*1024 == $info['defaultpansize']) {
        		return 1;
        	}
        	if ($param['defaultpansize']*1024*1024*1024 < $size['maxsize']) {
        		return -1;
        	}
        	if ($param['defaultpansize'] > $info['totalpansize']) {
        		return -3;
        	}
        	$wherearr['crid'] = $crid;
			$setarr['defaultpansize'] = $param['defaultpansize']*1024*1024*1024;//G换算成比特
			if ($this->pandb->update('pan_giveinfos', $setarr, $wherearr)) {
				return 1;
			} else {
				return -2;
			}
        }
    }

    /*
    获取云盘使用详情
    @param int $crid
    @return array 
    */
    public function getCridPanUsesize($crid){
        $sql = 'select sum(filesize) as sum from  pan_userinfos where crid ='.$crid;
        return $this->pandb->query($sql)->row_array();
    }

    /*
    获取云盘使用详情
    @param int $crid
    @return array 
    */
    public function getMaxCridPanUsesize($crid){
        $sql = 'select max(filesize) as maxsize from  pan_userinfos where crid ='.$crid;
        return $this->pandb->query($sql)->row_array();
    }

	/**
	 *获取某个网校下单个用户云盘使用的信息
	 */
	public function getOnePanUserinfo($uid,$crid){
		if(empty($crid))
			return false;
		$sql = 'SELECT filesize,uid FROM pan_userinfos WHERE uid =' . intval($uid) . ' AND crid=' . intval($crid);
		$row = $this->pandb->query($sql)->row_array();
		return $row ;
	}

	/**
     *删除某学生记录后更新网校和用户的云盘信息
     */
    public function delUserpanModel($wherearr = array()) {
        if (empty($wherearr['uid']) || empty($wherearr['crid']))
            return FALSE;
        $sql = 'SELECT filesize FROM pan_userinfos WHERE uid =' . intval($wherearr['uid']) . ' AND crid=' . intval($wherearr['crid']);
		$row = $this->pandb->query($sql)->row_array();
		if (!empty($row)) {//有记录，可能有分配，也可能没有
			//$filesize = floor($row['filesize']/1048576);//单位转换，比特转换成M,2.4则为2
			$filesize = $row['filesize'];
			$setarr = array('usepansize' => 'usepansize-' . $filesize);
			$this->pandb->begin_trans();
			$this->pandb->delete('pan_userinfos', $wherearr);
			unset($wherearr['uid']);
			$this->pandb->update('pan_giveinfos', array(), $wherearr, $setarr);
			return $this->transStatus();
		} else {//没有记录，表示没有分配过,也没有上传过文件
			return TRUE;
		}
		
	}

	/**
	 *第一次分配云盘对应操作，插入已使用的（统计select  sum(filesize) from  pan_userinfos where crid =10194），
	 *已分配中的 网校总人数*1G
	 *@param $seq int 0为第一次分配，执行插入
     */
    public function doPanGive($param,$seq=0) {
    	$crid = intval($param['crid']);
    	if (!$crid) {
    		return FALSE;
    	}
    	if (!$seq) {//第一次插入
    		$sql = 'select sum(filesize) as sum from  pan_userinfos where crid ='.$crid;
    		$res = $this->getCridPanUsesize($crid);
    		$setarr['usepansize'] = empty($res['sum']) ? 0 : $res['sum'];//byte
    		$setarr['givepansize'] = $usercount['count']*1024;//换算成比特单位*/
    		$setarr['totalpansize'] = $param['totalpansize']*1048576*1024;
    		if ($setarr['totalpansize'] < $setarr['usepansize']) {
    			return -1;
    		}
    		$setarr['crid'] = $crid;
    		$this->pandb->insert('pan_giveinfos', $setarr);

    	} else {//更新操作
    		if (empty($param['defaultpansize'])) {
    			$param['defaultpansize'] = 0;
    		}
    		$totalpansize = $param['totalpansize']*1048576*1024;
    		if ($totalpansize >= $param['defaultpansize']) {
    			$wherearr['crid'] = $crid;
    			$setarr['totalpansize'] = $totalpansize;//T换算成比特
    			$this->pandb->update('pan_giveinfos', $setarr, $wherearr);
    		} else {
    			return -1;
    		}
    	}

    }

	/**
	 *判断事务是否成功，成功就提交返回True
	 */
	public function transStatus() {
		if ($this->pandb->trans_status() === FALSE) {
            $this->pandb->rollback_trans();
            return FALSE;
        } else {
            $this->pandb->commit_trans();
        }
        return TRUE;
	}

}