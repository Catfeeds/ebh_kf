<?php
/**
* 课件附件相关AttachmentModel类 
*/
class AttachmentModel extends CEbhModel {
	/**
	* 根据课件编号等信息获取附件列表
	* @param array $queryarr 
	* @return array 附件列表数组
	*/
	public function getAttachmentListByCwid($queryarr = array()) {
		$sql = 'SELECT a.attid,a.title,a.filename,a.source,a.url,a.suffix,a.size,a.`status`,a.dateline,a.ispreview from ebh_attachments a';
		$wherearr = array();
		$wherearr[] ='a.cwid=' . $queryarr['cwid'];
		if(isset($queryarr['status']))
			$wherearr[] = 'a.status='.$queryarr['status'];
		$sql.= ' where '.implode(' AND ',$wherearr);
		$sql .= ' ORDER BY  a.attid desc ';
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	*根据cwid删除附件
	*/
	public function deletebycwid($cwid){
		$where = array('cwid'=>intval($cwid));
		return $this->ebhdb->delete('ebh_attachments',$where);
	}
	/**
	* 根据课件编号等信息获取附件总数
	* @param array $queryarr 
	* @return int
	*/
	public function getAttachmentCountByCwid($queryarr = array()) {
		$count = 0;
		$sql = 'SELECT count(*) count from ebh_attachments a ';
		$wherearr = array();
		$wherearr[] ='a.cwid=' . $queryarr['cwid'];
		if(isset($queryarr['status']))
			$wherearr[] = 'a.status='.$queryarr['status'];
		$sql.= ' where '.implode(' AND ',$wherearr);
		$countrow = $this->ebhdb->query($sql)->row_array();
		if (!empty($countrow))
			$count = $countrow['count'];
		return $count;
		}
	/*
	附件总数量
	@param array $param
	@return int
	*/
	public function getattachmentcount($param) {
		$sql = 'select count(*) count from ebh_attachments a '
		.' left join ebh_coursewares cw on cw.cwid = a.cwid  '
		.' left join ebh_billchecks ck on ck.toid = a.attid and ck.type=2 ';
		if (isset($param['q'])){
		$qstr = $this->ebhdb->escape_str($param['q']);
		$wherearr[] = ' ( a.title like \'%' . $qstr . '%\' or a.suffix like \'%' . $qstr . '%\')';
		}
		if(!empty($param['access'])){
			$wherearr[]='a.crid in ('.$this->ebhdb->escape_str($param['access']).')';
		}
		//管理员
		if($param['role']=='admin'){
			if($param['admin_status']>0){
                $wherearr[] = '(ck.teach_status ='.$param['admin_status']. ') or (ck.admin_status='.$param['admin_status']. ')';
            }
            if($param['cat']==0){
                $wherearr[] = '(ck.teach_status is null or ck.teach_status = 0 ) and (ck.admin_status is null or ck.admin_status = 3)';
            }
            if($param['cat']==1){
                $wherearr[] = '(ck.teach_status>0 or ck.admin_status>0) and ck.del=0';
            }
			if($param['cat']==2){
				$wherearr[] = 'ck.del=1';
			}
		//教师
		}elseif($param['role']=='teach'){
			if($param['teach_status']>0){
				$wherearr[] = 'ck.teach_status ='.$param['teach_status'];
			}
			if($param['cat']==0){
				$wherearr[] = 'ck.teach_status is null or ck.teach_status';
			}
			if($param['cat']==1){
				$wherearr[] = 'ck.teach_status>0 and ck.del=0';
			}
			if($param['cat']==2){
				$wherearr[] = 'ck.del=1';
			}
		}
		if(!empty($param['crid'])){
			if(is_array($param['crid'])){
				$wherearr[] = 'a.crid in( '.implode(',', $param['crid']).')';
				}else{
					$wherearr[] = 'a.crid ='.$param['crid'];
				}
			}
			if (!empty($wherearr))
				$sql.=' where ' . implode(' AND ', $wherearr);
			//var_dump($sql);
			$count = $this->ebhdb->query($sql)->row_array();
			return $count['count'];
			}
	/*
	所有附件列表
	@param array $param
	@return array 列表数组
	*/
	public function getattachmentlist($param) {
		$sql = 'select rc.folderid,a.uid,a.title,a.suffix,a.source,a.size,a.status,a.dateline,a.attid,a.crid,a.message,a.url,cw.title as cwtitle,cw.islive,cw.catid,ck.admin_status,ck.teach_status,ck.del, ck.admin_uid from ebh_attachments a '
		.' left join ebh_coursewares cw on cw.cwid = a.cwid  '
		.' left join ebh_roomcourses rc on rc.cwid = cw.cwid  '
		.' left join ebh_billchecks ck on ck.toid = a.attid and ck.type=2 ';        
		if (isset($param['q'])){
			$qstr = $this->ebhdb->escape_str($param['q']);
			$wherearr[] = ' ( a.title like \'%' . $qstr . '%\' or a.suffix like \'%' . $qstr . '%\')';
		}
		if(!empty($param['access'])){
			$wherearr[]='a.crid in ('.$this->ebhdb->escape_str($param['access']).')';
        }
			//管理员
			if($param['role']=='admin'){
				if($param['admin_status']>0){
                	$wherearr[] = '(ck.teach_status ='.$param['admin_status']. ') or (ck.admin_status='.$param['admin_status']. ')';
	            }
	            if($param['cat']==0){
	                $wherearr[] = '(ck.teach_status is null or ck.teach_status = 0 ) and (ck.admin_status is null or ck.admin_status = 3)';
	            }
	            if($param['cat']==1){
	                $wherearr[] = '(ck.teach_status>0 or ck.admin_status>0) and ck.del=0';
	            }
				if($param['cat']==2){
					$wherearr[] = 'ck.del=1';
				}
				//教师
            }elseif($param['role']=='teach'){
                if($param['teach_status']>0){
                    $wherearr[] = 'ck.teach_status ='.$param['teach_status'];
                }
                if($param['cat']==0){
                    $wherearr[] = 'ck.teach_status is null or ck.teach_status = 0';
                }
                if($param['cat']==1){
                    $wherearr[] = 'ck.teach_status>0 and ck.del=0';
                }
                if($param['cat']==2){
                    $wherearr[] = 'ck.del=1';
                }
            }
            if(!empty($param['crid'])){
                if(is_array($param['crid'])){
                    $wherearr[] = 'a.crid in( '.implode(',', $param['crid']).')';
                }else{
                        $wherearr[] = 'a.crid ='.$param['crid'];
                }
            }
		if (!empty($wherearr))
			$sql.=' where ' . implode(' AND ', $wherearr);
		$sql.=' order by a.dateline DESC';
		if (!empty($param['limit']))
			$sql.= ' limit ' . $param['limit'];
		$rows =  $this->ebhdb->query($sql)->list_array();
		//下面是对应优化代码
		$uidstr = '';
		$cridstr = '';
		$folderidstr = '';
		$checkstr = '';
		$uidrows=array();
		$cridrows=array();
		$folderidrows=array();
        $checkrows=array();
		foreach($rows as $key=>$row){
			if(!empty($row['uid'])){
				$uidstr.=$row['uid'].',';
			}
			if(!empty($row['crid'])){
				$cridstr.= $row['crid'].',';
			}
            if(!empty($row['folderid'])){
                $folderidstr .= $row['folderid'].',';
            }
            if(!empty($row['admin_uid'])){
                $checkstr .= $row['admin_uid'].',';
            }
		}
		$uidstr = implode(',',array_unique(explode(',',rtrim($uidstr, ','))));
		$cridstr = implode(',',array_unique(explode(',',rtrim($cridstr, ','))));
		$folderidstr = implode(',',array_unique(explode(',',rtrim($folderidstr, ','))));
        $checkstr = implode(',',array_unique(explode(',',rtrim($checkstr, ','))));
		    //用户信息
            if($uidstr!=''){
                $usql = 'select uid,username,realname from ebh_users where uid in('.$uidstr.')';
                $uidrows =  $this->ebhdb->query($usql)->list_array();
                $uidrows = $this->_arraycoltokey($uidrows,'uid');
                }
            //学校名称
            if($cridstr!=''){
                $ssql = 'select crid,crname from ebh_classrooms where crid in('.$cridstr.')';
                $cridrows =  $this->ebhdb->query($ssql)->list_array();
                $cridrows = $this->_arraycoltokey($cridrows,'crid');
            }
            //分类名称
            if($folderidstr!=''){
                $fsql =  'select folderid,foldername from ebh_folders where folderid in('.$folderidstr.')';
                $folderidrows =  $this->ebhdb->query($fsql)->list_array();
                $folderidrows = $this->_arraycoltokey($folderidrows,'folderid');
            }
            //附件审核人名称
            if($checkstr!=''){
                $asql =  'select uid,username from kf_user where uid in('.$checkstr.')';
                $checkrows =  $this->db->query($asql)->list_array();
                $checkrows = $this->_arraycoltokey($checkrows,'uid');
            }
            foreach($rows as &$row){
                $row['username'] = $uidrows[$row['uid']]['username'];
                $row['realname'] = $uidrows[$row['uid']]['realname'];
                $row['crname'] = $cridrows[$row['crid']]['crname'];
                $row['foldername'] = $folderidrows[$row['folderid']]['foldername'];
                $row['checkname'] = $checkrows[$row['admin_uid']]['username'];
            }
                return $rows;
        }
	/*
	编辑
	@param array $param
	@return int 影响行数
	*/
	public function editattachment($param) {
		if (isset($param['status']))
			$setarr['status'] = $param['status'];
		if (!empty($param['title']))
			$setarr['title'] = $param['title'];
		if (!empty($param['message']))
			$setarr['message'] = $param['message'];
		$wherearr = array('attid' => $param['attid']);
		$row = $this->ebhdb->update('ebh_attachments', $setarr, $wherearr);
		return $row;
		}
	/*
	删除附件
	@param int $attid
	@return int
	*/
	public function deleteattachment($attid) {
		return $this->ebhdb->delete('ebh_attachments', 'attid=' . $attid);
	}
	
	public function getCridById($id){
		$sql="select crid from ebh_attachments where attid=".$this->ebhdb->escape($id);
		$row=$this->ebhdb->query($sql)->row_array();
		return $row['crid'];
	}
	/**
	* 二维数组某个列的值作为索引键
	* @param unknown $data
	* @param string $key
	*
	*/
	protected  function _arraycoltokey($array, $key = '') {
		if(empty($key)) return ;
		$newarray = array();
		foreach ($array as $row){
			$newarray[$row[$key]] = $row;
			}
			return $newarray;
		}
	/*
	根据id获取附件
	*/
	public function getAttachById($attid){
		$sql = "SELECT a.attid,a.uid,a.crid,a.cwid,a.title,a.message,a.source,a.url,a.filename,a.suffix,a.size,a.ispreview,a.`status`,a.dateline,u.realname,cw.title as ctitle,cr.crname,
		ck.admin_status,ck.admin_remark,ck.teach_status,ck.teach_remark,ck.del,ck.admin_dateline,ck.teach_dateline,ck.delline,ck.admin_ip,ck.teach_ip, ck.admin_uid "
		." FROM  ebh_attachments a "
		." left join ebh_users u on u.uid =  a.uid"
		." left join ebh_coursewares cw on cw.cwid = a.cwid "
		." left join ebh_billchecks ck on ck.toid = a.attid"
		."  left join ebh_classrooms cr on cr.crid = a.crid"            
		." WHERE a.attid = $attid";
		return $this->ebhdb->query($sql)->row_array();
	}
}
