<?php
/*
服务包内项目
*/
class PayitemModel extends CEbhModel{
	/**
	*获取服务包内项目列表
	*/
	public function getItemList($param) {
		$sql = 'select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.dateline,i.providercrid,i.comfee,i.roomfee,i.providerfee,r.crname,r.summary,r.cface,r.domain,r.coursenum,r.examcount,r.ispublic,p.pname,s.sname from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid left join ebh_pay_sorts s on i.sid = s.sid';
		$wherearr = array();
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['pidlist'])) {	//根据pid的列表获取数据，如 1,2形式
			$wherearr[] = 'i.pid in('.$param['pidlist'].')';
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}
		if(!empty($param['tid'])){
			$wherearr[] = 'p.tid='.$param['tid'];
		}
		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(!empty($param['q'])){
			$q = $this->ebhdb->escape_str($param['q']);
			$wherearr[] = '(i.iname like \'%'.$q.'%\' or p.pname like \'%'.$q.'%\' )';
		}			
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY itemid desc';
        }
		if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	*获取服务包内项目列表数量
	*/
	public function getItemListCount($param) {
		$count = 0;
		$sql = 'select count(*) count from ebh_pay_items i join ebh_classrooms r on (i.crid = r.crid) join ebh_pay_packages p on p.pid=i.pid';
		$wherearr = array();
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}
		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		$row = $this->ebhdb->query($sql)->row_array();
		if(!empty($row))
			$count = $row['count'];
		return $count;
	}
	/**
	*获取服务包内项目列表(针对课程)
	*/
	public function getItemFolderList($param) {
		$sql = 'select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.grade,i.sid,s.sname,f.foldername,f.summary,f.img,f.coursewarenum,f.viewnum,f.ispublic,f.fprice,f.speaker,s.showbysort,s.ishide,s.imgurl simg,s.content,f.credit,i.cannotpay,f.showmode,f.creditmode,f.credittime,s.showaslongblock,i.longblockimg from ebh_pay_items i '.
				'join ebh_folders f on (i.folderid = f.folderid) '.
				'left join ebh_pay_sorts s on (s.sid=i.sid)';
		$wherearr = array();
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['pidlist'])) {	//根据pid的列表获取数据，如 1,2形式
			$wherearr[] = 'i.pid in('.$param['pidlist'].')';
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}

		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(!empty($param['needsid'])){
			$wherearr[] = 'i.sid<>0';
		}
		if(isset($param['power']))
			$wherearr[] = 'f.power in ('.$param['power'].')';
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY pid desc';
        }
		if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
		return $this->ebhdb->query($sql)->list_array();
	}
	/**
	*获取服务包内项目列表数量
	*/
	public function getItemListFolderCount($param) {
		$count = 0;
		$sql = 'select count(*) count from ebh_pay_items i join ebh_folder f on (i.folderid = f.folderid)';
		$wherearr = array();
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}
		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		$row = $this->ebhdb->query($sql)->row_array();
		if(!empty($row))
			$count = $row['count'];
		return $count;
	}
	/**
	*根据itemid获取服务明细项详情
	*/
	public function getItemByItemid($itemid) {
		$sql = "select i.itemid,i.pid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.folderid,i.sid,cr.crid,cr.crname,p.pname,p.crid pcrid,f.fprice,cr.domain,f.speaker,f.detail,i.providercrid,i.comfee,i.roomfee,i.providerfee,i.cannotpay ,i.longblockimg from ebh_pay_items i join ebh_classrooms cr on i.crid=cr.crid join ebh_pay_packages p on p.pid=i.pid join ebh_folders f on i.folderid=f.folderid where i.itemid=$itemid"; 
		return $this->ebhdb->query($sql)->row_array();
	}
	/**
	*根据sid获取服务明细项列表
	*/
	public function getItemBySidOrItemid($param = array()) {
		if(empty($param['sid']) && empty($param['itemid']))
			return FALSE;
		$sql = "select i.itemid,i.pid,i.iname,i.isummary,i.iprice,i.imonth,i.iday,i.folderid,i.sid,cr.crid,cr.crname,p.pname,p.crid pcrid,f.fprice,cr.domain,f.speaker,f.detail,i.cannotpay from ebh_pay_items i join ebh_classrooms cr on i.crid=cr.crid join ebh_pay_packages p on p.pid=i.pid join ebh_folders f on i.folderid=f.folderid"; 
		$wherearr = array();
		if(!empty($param['sid']))
			$wherearr[] = 'i.sid='.$param['sid'];
		if(!empty($param['itemid']))
			$wherearr[] = 'i.itemid='.$param['itemid'];
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		return $this->ebhdb->query($sql)->list_array();
	}

	
	
	public function add($param){
		$spiarr['iname'] = $param['iname'];
		$spiarr['pid'] = $param['pid'];
		$spiarr['crid'] = $param['crid'];
		$spiarr['iprice'] = $param['iprice'];
		if(!empty($param['isummary']))
			$spiarr['isummary'] = $param['isummary'];
		if(!empty($param['folderid']))
			$spiarr['folderid'] = $param['folderid'];
		if(!empty($param['sid']))
			$spiarr['sid'] = $param['sid'];
		if(!empty($param['iday']))
			$spiarr['iday'] = $param['iday'];
		elseif(!empty($param['imonth']))
			$spiarr['imonth'] = $param['imonth'];
		if(!empty($param['providercrid']))
			$spiarr['providercrid'] = $param['providercrid'];
		if(!empty($param['comfee']))
			$spiarr['comfee'] = $param['comfee'];
		if(!empty($param['roomfee']))
			$spiarr['roomfee'] = $param['roomfee'];
		if(!empty($param['providerfee']))
			$spiarr['providerfee'] = $param['providerfee'];
		if(!empty($param['longblockimg']))
			$spiarr['longblockimg'] = $param['longblockimg'];
		$spiarr['dateline'] = SYSTIME;
		
		return $this->ebhdb->insert('ebh_pay_items',$spiarr);
	}
	
	public function edit($param){
		if(empty($param['itemid']))
			exit;
		$spiarr['iname'] = $param['iname'];
		$spiarr['pid'] = $param['pid'];
		$spiarr['crid'] = $param['crid'];
		$spiarr['isummary'] = $param['isummary'];
		$spiarr['iprice'] = $param['iprice'];
		$spiarr['folderid'] = $param['folderid'];
		$spiarr['sid'] = $param['sid'];
		if(isset($param['providercrid']))
			$spiarr['providercrid'] = $param['providercrid'];
		if(isset($param['comfee']))
			$spiarr['comfee'] = $param['comfee'];
		if(isset($param['roomfee']))
			$spiarr['roomfee'] = $param['roomfee'];
		if(isset($param['providerfee']))
			$spiarr['providerfee'] = $param['providerfee'];
		if(!empty($param['iday'])){
			$spiarr['iday'] = $param['iday'];
			$spiarr['imonth'] = 0;
		}elseif(!empty($param['imonth'])){
			$spiarr['imonth'] = $param['imonth'];
			$spiarr['iday'] = 0;
		}
		$spiarr['cannotpay'] = $param['cannotpay'];
		$spiarr['longblockimg'] = $param['longblockimg'];
		return $this->ebhdb->update('ebh_pay_items',$spiarr,'itemid='.$param['itemid']);
	}
	public function deleteitem($itemid){
		return $this->ebhdb->delete('ebh_pay_items','itemid='.$itemid);
	}
	
	/*
	无权限的服务项
	*/
	public function getItemFolderListNotPaid($param) {
		$sql = 'select i.itemid,i.pid,i.crid,i.folderid,i.iname,i.iprice,i.imonth,i.iday,f.foldername,f.img,f.ispublic,f.fprice,f.coursewarenum,i.sid,i.cannotpay from ebh_pay_items i '.
				'join ebh_folders f on (i.folderid = f.folderid) ';
		$wherearr = array();
		if(!empty($param['pid'])) {
			$wherearr[] = 'i.pid='.$param['pid'];
		}
		if(!empty($param['pidlist'])) {	//根据pid的列表获取数据，如 1,2形式
			$wherearr[] = 'i.pid in('.$param['pidlist'].')';
		}
		if(!empty($param['itemidlist'])) {	//根据itemid组合获取详情列表，如1,2形式
			$wherearr[] = 'i.itemid in('.$param['itemidlist'].')';
		}

		if(!empty($param['crid'])) {
			$wherearr[] = 'i.crid='.$param['crid'];
		}
		if(!empty($param['folderid'])) {
			$wherearr[] = 'i.folderid='.$param['folderid'];
		}
		if(isset($param['power']))
			$wherearr[] = 'f.power in ('.$param['power'].')';
		//不在用户权限表,并且课程不免费
		$wherearr[] = 'i.itemid not in (select itemid from ebh_userpermisions where uid='.$param['uid'].' and crid='.$param['crid'].' and enddate>='.(SYSTIME-86400).')';
		$wherearr[] = 'f.fprice>0 and i.iprice>0';
		if(!empty($wherearr)) {
			$sql .= ' WHERE ' . implode(' AND ', $wherearr);
		}
		if(!empty($param['displayorder'])) {
            $sql .= ' ORDER BY '.$param['displayorder'];
        } else {
            $sql .= ' ORDER BY pid desc';
        }
		if(!empty($param['limit'])) {
            $sql .= ' limit '. $param['limit'];
        } else {
			if (empty($param['page']) || $param['page'] < 1)
				$page = 1;
			else
				$page = $param['page'];
			$pagesize = empty($param['pagesize']) ? 10 : $param['pagesize'];
			$start = ($page - 1) * $pagesize;
            $sql .= ' limit ' . $start . ',' . $pagesize;
        }
		return $this->ebhdb->query($sql)->list_array();
	}
}

?>