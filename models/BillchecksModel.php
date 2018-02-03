<?php
	/**
	 *审核model类,针对ebh_billchecks表
	 *
	 */
	class BillchecksModel extends CEbhModel{
		/**
		 * 审核处理
		 *
		 */
		public function check($param){
			$toid = $param['toid'];
			$role = $param['role'];
			$type = $param['type'];
			if(!$toid){return false;}
			//检查是否持存在
			$sql = "select count(*) as count from ebh_billchecks where toid = {$toid} and type = {$type}";
			$row = $this->ebhdb->query($sql)->row_array();
            $sql2='select admin_status,teach_status from ebh_billchecks WHERE  toid='.$toid.' and type ='.$type;
			//var_dump($row);exit;
            $domainrow = $this->ebhdb->query($sql2)->row_array();
            //var_dump($param);die;
			if(!empty($domainrow['admin_status'])){
				//更新
				return false;

			}elseif ($row['count']>0) {
				if($role=='admin'){//管理员审核
					$setArr['admin_uid'] = $param['admin_uid'];
					$setArr['admin_status'] = $param['admin_status'];
					$setArr['admin_remark'] = htmlentities($param['admin_remark'],ENT_NOQUOTES,"utf-8");
					$setArr['admin_ip'] = $param['admin_ip'];
					$setArr['admin_dateline'] = time();
				}elseif($role=='teach'){//教师审核
					$setArr['teach_uid'] = $param['teach_uid'];
					$setArr['teach_status'] = $param['teach_status'];
					$setArr['teach_remark'] = $param['teach_remark'];
					$setArr['teach_ip'] = $param['teach_ip'];
					$setArr['teach_dateline'] = time();
				}
//                var_dump(1);
                $res = $this->ebhdb->update("ebh_billchecks",$setArr,array('toid'=>$toid,'type'=>$type));
				//网校对应修改课件等状态				
			}else{
				//添加
				if($role=='admin'){//管理员审核
					$data = array(
						'toid'=>$toid,
						'type'=>$type,
						'admin_uid'=>$param['admin_uid'],
						'admin_status'=>$param['admin_status'],
						'admin_remark'=>htmlentities($param['admin_remark'],ENT_NOQUOTES,"utf-8"),
                       					 'teach_remark'=>$param['teach_remark'] ? $param['teach_remark'] : '',
						'admin_ip'=>$param['admin_ip'],
						'admin_dateline'=>time(),
					);
				}elseif($role=='teach'){//教师审核
					$data = array(
						'toid'=>$toid,
						'type'=>$type,
						'teach_uid'=>$param['teach_uid'],
						'teach_status'=>$param['teach_status'],
						'teach_remark'=>$param['teach_remark'],
						'teach_ip'=>$param['teach_ip'],
						'teach_dateline'=>time(),
                        					'admin_remark'=>'',
					);
				}
                $res = $this->ebhdb->insert("ebh_billchecks",$data);

			}
			if($param['type']!=13){
                //更新课件/附件/评论/答疑/回答表
                if($param['teach_status']==2 || $param['admin_status']==2){
                    $this->updatestatus($toid,  $type);
                }
            }
            if($param['type'] == 2){
            	if($param['teach_status'] == 1 || $param['admin_status']==1){
            		$this->updatestatusattachment($toid,  $type);
            	}
            }
            if($param['type'] == 15){
            	$this->ebhdb->begin_trans();
            	$rsql = 'select l.number,l.lotcode,l.crid,l.name from ebh_redeem_lots l where l.lotid='.$toid;
            	$rdetail = $this->ebhdb->query($rsql)->row_array();
            	if (!empty($rdetail)) {
            		if($param['admin_status'] == 1){//审核通过，按照批次生成兑换码
	            		$rparam['resid'] = $toid;
	            		$rparam['number'] = $rdetail['number'];
						$rparam['lotcode'] = $rdetail['lotcode'];
						$rparam['crid'] = $rdetail['crid'];
						$this->doCards($rparam);
						//更新使得批次
	            		$setarr['status'] = 2;
        				$res = $this->ebhdb->update('ebh_redeem_lots',$setarr,array('lotid'=>$toid));
	            	} else {//不通过，增添一条不过的记录
	            		$paysql = 'select ordername,crid,dateline,paytime,payfrom,uid,ip,payip,paycode,paycode,totalfee,remark,status,refunded,invalid,buyer_id,buyer_info,out_trade_no,itype,isbatchrefund,batchid,ptype from ebh_pay_torders where ptype=1 and status=1 and itype=1 and batchid='.$toid;
	            		$pay_info = $this->ebhdb->query($paysql)->row_array();
	            		if (!empty($pay_info)) {
	            			$pay_info['ptype'] = 2;//审核不通过退押金
	            			$pay_info['dateline'] = time();
	            			$pay_info['isbatchrefund'] = 1;
	            			$pay_info['name'] = $rdetail['name'];
	            			$pay_info['name'] = $rdetail['name'];
							//记录
		            		$this->addOrder($pay_info);
		            		//退钱
		            		$money = $pay_info['totalfee'];
		            		$pay_info['redeemcode'] = $rdetail['lotcode']; 
		            		$sqltouser = "update ebh_users set balance = balance + $money where uid =".intval($pay_info['uid']);
		            		$this->ebhdb->simple_query($sqltouser);
		            		//更新使得批次弃用
		            		$setarr['status'] = -1;
		            		$pay_info['money'] = $money; 
		            		$this->addCharge($pay_info);
            				$res = $this->ebhdb->update('ebh_redeem_lots',$setarr,array('lotid'=>$toid));
	            		}
	            	}
            	}
            	if($this->ebhdb->trans_status() === FALSE){
	            	$this->ebhdb->rollback_trans();
	            	return false;
	            }else{
	            	$this->ebhdb->commit_trans();
	           		return true;
	            }	
            	
            }
			return  $res;

		}

		 /**
     * 插入一条记录
     * 
     */
    public function addRecorder($param=array()){
        if(empty($param)){
            return false;
        }
        $data = array();
        if(!empty($param['uid'])){
            $data['uid'] = $param['uid'];
        }
        $data['cate'] = 1;
        $data['dateline'] = time();
        $data['status'] = 1;
        return $this->ebhdb->insert('ebh_records',$data);
    }

    /**
    *生成充值记录，退钱就是充值
    */
    public function addCharge($param = array()) {
        if(empty($param))
            return false;
        $data = array();
        $param['rid'] = $this->addRecorder($param);
        if(!empty($param['rid'])){
            $data['rid'] = $param['rid'];
        }
        $data['uid'] = 0;
        if(!empty($param['uid'])){
            $data['useuid'] = $param['uid'];
        }
        if(!empty($param['name'])){
            $data['cardno'] = $param['name'];
        } else {
            $data['cardno'] = $param['ordername'];
        }
            
        $data['type'] = 11;//退款充值
        if(!empty($param['money'])){
            $data['value'] = $param['money'];
        }
        //余额
        $sqltouser = "select balance from ebh_users where uid =".intval($param['uid']);
        $res = $this->ebhdb->query($sqltouser)->row_array();
        if(!empty($res['balance'])){
            $data['curvalue'] = $res['balance'];
        }
        $data['status'] = 1;
        if(!empty($param['payip'])){
            $data['fromip'] = $param['payip'];
        }
        $data['paytime'] = time();
        $data['dateline'] = time();
        return $this->ebhdb->insert('ebh_charges',$data);
    }

		/**
		 *成批按批次生成兑换码
		 */
		public function doCards($param) {
			if (empty($param['resid']) || empty($param['crid']) || empty($param['lotcode'])) {
				return false;
			}
	        $cardarr['redeemid'] = $param['resid'];
	        $cardarr['crid'] = $param['crid'];
	        $sql = ' insert into ebh_redeem_cards (redeemid,redeemnumber,usetime,status,crid,uid) values ';
	        for($i=0;$i<$param['number'];$i++){
	            $code = $param['lotcode'].$this->getUnicardNumber($param['crid']);
	            $sql .= "({$cardarr['redeemid']},'{$code}',0,0,{$param['crid']},0),";
	        }
	        $sql = substr($sql, 0, -1);
	        return $this->ebhdb->query($sql);
		}
		
		/**
	     *获取唯一的序列号
	     *激活码为:123456789012 一共12位激活码不允许重复
	     *激活码去除易混淆的字符,如: 1,l,L,0 ,Oo剔除
	     */
	    public function getUnicardNumber($crid) {
	        $pattern = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
	        $cardpass = '';
	        for ($i = 0; $i < 7; $i++) {
	            $cardpass .= $pattern{mt_rand(0, 30)};
	        }
	        $checksql = 'select redeemnumber from ebh_redeem_cards where redeemnumber=\''.$cardpass.'\' and crid='.$crid;
	        while ($this->ebhdb->query($checksql)->list_array()) {
	            $cardpass = '';
	            for ($i = 0; $i < 7; $i++) {
	                $cardpass .= $pattern{mt_rand(0, 30)};
	            }
	            $checksql = 'select redeemnumber from ebh_redeem_cards where  redeemnumber=\''.$cardpass.'\' and crid='.$crid;
	        }
	        return $cardpass;
	    }

		/**
		 * 批量审核
		 */
		public function multcheck($param){
			$idarr = explode(",", $param['ids']);
			if(!is_array($idarr)){
				return false;
			}
			foreach($idarr as $id){
				$param['toid'] = $id;
				$params = $param;
				$ck = $this->check($params);
				if($ck <= 0){
					break;
					return false;
				}
			}
			return true;
		}

        /**
         * 撤销审核操作,并还原相关信息
         */
        public function revoke($param){
            $toid = $param['toid'];
            $type = $param['type'];
            $status = $param['status'];
            if($status == 2){
                //获取不通过之前的状态
                $old_status = $this->ebhdb->query('select old_status from ebh_billchecks where toid = '.$toid.' and type = '.$type)->row_array();
                $old_status = $old_status['old_status'];
                switch ($type){
                    case 1 : $table = 'ebh_coursewares';
                        $setarr=array('status'=>$old_status);
                        $where= array('cwid'=>$toid);
                        $folderidsql = 'select folderid from `ebh_roomcourses` where cwid ='.intval($toid).' limit 1';
						$row = $this->ebhdb->query($folderidsql)->row_array();
						$folderid = $row['folderid'];
						$tableTo = 'ebh_folders';
						$minus = array('idcolumn'=>'folderid','folderid'=>$folderid,'column'=>'coursewarenum','coursewarenum'=>'coursewarenum+1');
                        break;//课件  status 为-2禁止
                    case 2 : $table = 'ebh_attachments';
                        $setarr=array('status'=>$old_status);
                        $where= array('attid'=>$toid);
                        $attidsql = 'select cwid from `ebh_attachments` where attid ='.intval($toid).' limit 1';
						$row = $this->ebhdb->query($attidsql)->row_array();
						$cwid = $row['cwid'];
						$tableTo = 'ebh_coursewares';
						$minus = array('idcolumn'=>'cwid','cwid'=>$cwid,'column'=>'attachmentnum','attachmentnum'=>'attachmentnum+1');
                        break;//附件 0未审核 1审核通过 -1审核未通过 默认1
                    case 3 : $table = 'ebh_reviews';
                        $setarr=array('shield'=>$old_status);
                        $where= array('logid'=>$toid);
                        break;//评论 shield为1屏蔽
                    case 4 : $table = 'ebh_askquestions';
                        $setarr=array('shield'=>$old_status);
                        $where= array('qid'=>$toid);
                        break;//答疑 shield为1屏蔽
                    case 5 : $table = 'ebh_askanswers';
                        $setarr=array('shield'=>$old_status);
                        $where= array('aid'=>$toid);
                        break;//回答 shield为1屏蔽
                    case 6:	 $table = 'portal.ebh_previews';
                        $setarr=array('status'=>$old_status);
                        $where= array('reviewid'=>$toid);
                        break;//主站评论 status为2锁定 1不锁定 默认1
                    case 7:	 $table = 'ebh_schexams';
                        $setarr=array('status'=>$old_status);
                        $where= array('eid'=>$toid);
                        break;//作业 status:0表示临时保存，1表示已提交（不能编辑），-1已删除
                }
                $this->ebhdb->update($table,$setarr,$where);
                if(isset($tableTo)){
		            $minussql = 'update '.$tableTo.' set '.$minus['column'].' = '.$minus[$minus['column']].' where '.$minus['idcolumn'].' = '.$minus[$minus['idcolumn']];
		            $this->ebhdb->query($minussql);	
	            }
                //评论加1
                if($type==3){
                    $dsql = " select r.toid,r.logid,cw.cwid from ebh_reviews r left join ebh_coursewares cw on cw.cwid = r.toid where logid = {$toid} ";
                    $drow = $this->ebhdb->query($dsql)->row_array();
                    if(!empty($drow)){
                        $upsql = "update ebh_coursewares set reviewnum = reviewnum+1 where cwid = {$drow['cwid']}";
                        $this->ebhdb->query($upsql);
                    }
                }

                //审核未通过 扣除1积分 包含有(答疑审核,回答审核) 写入日志
                if(in_array($type,array(4,5))){
                    $creditmodel = Ebh::app()->model("Credit");
                    $row =$this->getinforow($table,$where);
                    //获取用户积分
                    $credit = $this->getCredit($row['uid']);
                    //审核不通过撤销 加分
                    $dodec = 1;
                    if(!empty($credit)&&$credit['credit']>=$dodec){
                        $message = ($type == 4)?"您的问题,[".$row['title']."]":"您的回答,[".$row['title']."]";
                        //问答悬赏 暂时未处理
                        $reward = $row['reward'];
                        if($type==4){
                            $crid = Ebh::app()->model('askquestion')->getCridById($toid);
                        }elseif($type==5){
                            $crid = Ebh::app()->model('askquestion')->getCridByAid($toid);
                            $asql = 'select isbest from ebh_askanswers where aid='.$toid;
                            $ares = $this->ebhdb->query($asql)->row_array();
                            if($ares['isbest']){
                                $params = array(
                                    'ruleid'=>32,//系统撤销补偿
                                    'credit'=>2,
                                    'uid'=>$row['uid'],
                                    'detail'=>$message.'且为最佳回答',
                                    'crid'=>$crid,
									'theqid'=>$row['qid'],
									'thedateline'=>$row['dateline']
                                );
                                $creditmodel->addCreditlog($params);
                            }
                        }
                        $params = array(
                            'ruleid'=>32,//系统撤销补偿
                            'credit'=>$dodec,
                            'uid'=>$row['uid'],
                            'detail'=>$message,
                            'crid'=>$crid,
							'theqid'=>$row['qid'],
							'thedateline'=>$row['dateline']
                        );
                        $creditmodel->addCreditlog($params);
                    }

                    //回答数加1
                    if($type==5){
                        $qid = $row['qid'];
                        $upsql = "update ebh_askquestions set answercount = answercount+1 where qid = $qid ";
                        $this->ebhdb->query($upsql);
                    }
                }
            }
            if($status == 1 && $type == 2){
            	$this->ebhdb->update('ebh_attachments',array('status'=>0),array('attid'=>$toid));
            }
            $data = array(
                'admin_status' => $param['admin_status'],
                'admin_uid' => $param['admin_uid'],
                'teach_dateline' => 0,
                'teach_status' => 0,
                'teach_uid' => 0
            );
            $this->ebhdb->update("ebh_billchecks",$data,array('toid'=>$toid,'type'=>$type));
            return $this->ebhdb->affected_rows();
        }
		/**
		 * 更新网校课件,附件等状态
		 *
		 */
		public function updatestatus($toid,$type){
			switch ($type){
				case 1 : $table = 'ebh_coursewares';
						 $setarr=array('status'=>-2);
						 $where= array('cwid'=>$toid);
						 $folderidsql = 'select folderid from `ebh_roomcourses` where cwid ='.intval($toid).' limit 1';
						 $row = $this->ebhdb->query($folderidsql)->row_array();
						 $folderid = $row['folderid'];
						 $tableTo = 'ebh_folders';
						 $minus = array('idcolumn'=>'folderid','folderid'=>$folderid,'column'=>'coursewarenum','coursewarenum'=>'coursewarenum-1');
						 break;//课件  status 为-2禁止
				case 2 : $table = 'ebh_attachments';
						 $setarr=array('status'=>-1);
						 $where= array('attid'=>$toid);
						 $attidsql = 'select cwid from `ebh_attachments` where attid ='.intval($toid).' limit 1';
						 $row = $this->ebhdb->query($attidsql)->row_array();
						 $cwid = $row['cwid'];
						 $tableTo = 'ebh_coursewares';
						 $minus = array('idcolumn'=>'cwid','cwid'=>$cwid,'column'=>'attachmentnum','attachmentnum'=>'attachmentnum-1');
						 break;//附件
				case 3 : $table = 'ebh_reviews';
						 $setarr=array('shield'=>1);
						 $where= array('logid'=>$toid);
						 break;//评论 shield为1屏蔽
		       	case 4 : $table = 'ebh_askquestions';
						 $setarr=array('shield'=>1);
						 $where= array('qid'=>$toid);
						 break;//答疑 shield为1屏蔽
				case 5 : $table = 'ebh_askanswers';
						 $setarr=array('shield'=>1);
						 $where= array('aid'=>$toid);
						 break;//回答 shield为1屏蔽
				case 6:	 $table = 'portal.ebh_previews';
						 $setarr=array('status'=>2);
						 $where= array('reviewid'=>$toid);
						 break;//主站评论 status为2锁定
				case 7:	 $table = 'ebh_schexams';
						 $setarr=array('status'=>-1);
						 $where= array('eid'=>$toid);
						 break;//作业 status为-1删除
			}
            //记录审核不通过之前的状态
            if(in_array($type,array(1,2,3,4,5,6,7))){
                $key = array_keys($setarr);
                $wherekey = array_keys($where);
                $sta = $this->ebhdb->query('select '.$key[0].' from '.$table.' where '.$wherekey[0].' = '.$toid)->row_array();
                $updatesql = 'update ebh_billchecks set old_status = '.intval($sta[$key[0]]).' where toid ='.$toid.' and type ='.$type;
                $this->ebhdb->simple_query($updatesql);
            }
            if(isset($tableTo)){
	            $minussql = 'update '.$tableTo.' set '.$minus['column'].' = '.$minus[$minus['column']].' where '.$minus['idcolumn'].' = '.$minus[$minus['idcolumn']];
	            $this->ebhdb->query($minussql);	
            }
			$this->ebhdb->update($table,$setarr,$where);
			$dsql = " select r.toid,r.logid,cw.cwid from ebh_reviews r left join ebh_coursewares cw on cw.cwid = r.toid where logid = {$toid} ";
				$drow = $this->ebhdb->query($dsql)->row_array();
			if($type==3){
				
				if(!empty($drow)){
					$upsql = "update ebh_coursewares set reviewnum = reviewnum-1 where cwid = {$drow['cwid']}";
					$this->ebhdb->query($upsql);
				}
			}

			//审核未通过 扣除1积分 包含有(答疑审核,回答审核) 写入日志
			if(in_array($type,array(4,5))){
                    $creditmodel = Ebh::app()->model("Credit");
                    $row =$this->getinforow($table,$where);
                    //获取用户积分
                    $credit = $this->getCredit($row['uid']);
                    //审核不通过 扣分
                    $dodec = 1;
                    if(!empty($credit)&&$credit['credit']>=$dodec){
                        $message = ($type == 4)?"您的问题,[".$row['title']."], 不符合规范":"您的回答,[".$row['title']."],不符合规范";
                        //问答悬赏 暂时未处理
                        $reward = $row['reward'];
                        if($type==4){
                            $crid = Ebh::app()->model('askquestion')->getCridById($toid);
                        }elseif($type==5){
                            $crid = Ebh::app()->model('askquestion')->getCridByAid($toid);
                            $asql = 'select isbest from ebh_askanswers where aid='.$toid;
                            $ares = $this->ebhdb->query($asql)->row_array();
                            if($ares['isbest']){
                                $params = array(
                                    'ruleid'=>30,//系统惩罚
                                    'credit'=>2,
                                    'uid'=>$row['uid'],
                                    'detail'=>$message.'且为最佳回答',
                                    'crid'=>$crid
                                );
                                $creditmodel->addCreditlog($params);
                            }
                        }
                        $param = array(
                            'ruleid'=>30,
                            'credit'=>$dodec,
                            'uid'=>$row['uid'],
                            'detail'=>$message,
                            'crid'=>$crid,
							'theqid'=>$row['qid'],
							'thedateline'=>$row['dateline']
                        );
                        $creditmodel->addCreditlog($param);
			}

				//回答数减1
				if($type==5){
					$qid = $row['qid'];
					$upsql = "update ebh_askquestions set answercount = answercount-1 where qid = $qid ";
					$this->ebhdb->query($upsql);
				}
			}
		}
		/**
		 * 删除处理
		 */
		public function del($param){
			$setArr['del'] = 1;
			$setArr['delline'] = time();
			$whereArr['toid'] = $param['toid'];
			$whereArr['type'] = $param['type'];
			//课件附件表字段删除更新处理(逻辑删除)
			if($param['type']==1){//课件
				$sql = 'SELECT c.uid,rc.crid,f.folderid,f.folderlevel,f.upid FROM ebh_coursewares c LEFT JOIN ebh_roomcourses rc ON c.cwid = rc.cwid LEFT JOIN ebh_folders f ON f.folderid = rc.folderid WHERE c.cwid=' . $param['toid'];
				$course = $this->ebhdb->query($sql)->row_array();

				$folder = $course;
				$folderid = $folder['folderid'];
				$folderlevel = $folder['folderlevel'];
				while($folderlevel>1){
					$this->ebhdb->update(
						'ebh_folders',
						array(),
						'folderid='.$folderid,
						array('coursewarenum'=>'coursewarenum-1')
					);//课程对应课件数
					$folder = $this->ebhdb->query('select folderid,folderlevel,upid from ebh_folders where folderid='.$folder['upid'])->row_array();
					$folderlevel = $folder['folderlevel'];
					$folderid = $folder['folderid'];
				}

				//教室对应课件数
				$this->ebhdb->update(
					'ebh_classrooms',
					array(),
					'crid='.$course['crid'],
					array('coursenum'=>'coursenum-1')
				);
				//教师课件数
/*				$this->ebhdb->update(
					'ebh_teachers',
					array(),
					'teacherid='.$course['uid'],
					array('cwcount'=>'cwcount-1')
				);*/

				$this->ebhdb->update("ebh_coursewares",array('status'=>-3),array('cwid'=>$param['toid']));
			}
			elseif($param['type']==6){
				$this->ebhdb->delete('portal.ebh_previews',array('reviewid'=>$param['toid']));
			}
			$this->ebhdb->update("ebh_billchecks",$setArr,$whereArr);
		}

		/**
		 * 获取问答或者答疑标题与uid
		 */
		public function getinforow($table,$where){
			if($table=='ebh_askquestions'){//问题
				$sql = "select uid,title,reward,qid,dateline from ebh_askquestions where ".key($where)." = ".current($where);
			}elseif($table=='ebh_askanswers'){//回答
				$sql = "select uid,message as title,qid,dateline from ebh_askanswers where ".key($where)." = ".current($where);
			}
			//echo $sql;
			$row = $this->ebhdb->query($sql)->row_array();
			$row['title'] = shortstr(filterhtml($row['title']),20);
			if(empty($row['reward'])){
				$row['reward'] = 0;
			}
			return $row;
		}

		/**
		 * 获取用户积分
		 */
		public function getCredit($uid){
			$sql = "select uid,credit from ebh_users where uid = $uid" ;
			$row = $this->ebhdb->query($sql)->row_array();
			return $row;
		}
//
//        /**
//         * 敏感字替换
//         * $param:字符串或者一维数组
//         * $array:若是二维数组填true
//         */
//        public function replace($param, $istwo = false ){
//            if(!$istwo){
//                $bad = file(S_ROOT.'bad.txt');//获取敏感字库
//                $text = preg_replace("/(\r\n|\n|\r|\t)/i", '', $bad);//去数组中的空行
//                $m = "<b style='color: #ff0000'>*</b>";
//                return (str_replace($text, $m, $param));//替换敏感字
//            }
//            $array = array();
//            foreach($param as $value){
//                $array[] = $this->replace($value);
//            }
//            return $array;
//        }

        /**
         * @param $param
         * @istwo $istwo 是否为二维数组
         * @replace $replace 设置敏感字替换的符号
         * @return array
         * 修改敏感字的样式
         */
        public function str_change($param, $istwo = false, $replace = '')
        {
//            $bad = file(S_ROOT . 'bad.txt');//获取敏感字库
            $sql = 'select keyword from kf_sensitives';
            $text = $this->db->query($sql)->list_array();//获取敏感词列表
//            $text = preg_replace("/(\r\n|\n|\r|\t)/i", '', $bad);//去数组中的空行
            $array = array();
            if($istwo){
                foreach ($param as $value){
                    foreach ($text as $v) {
                        $bad = $replace ? $replace :"<b style='color: #ff0000'>" . $v['keyword'] . "</b>";
                        $value = (str_replace($v['keyword'], $bad, $value));
                    }
                    $array[] = $value;
                }
                return $array;
            }else{
                foreach($text as $v){
                    $bad = $replace ? $replace : "<b style='color: #ff0000'>" . $v['keyword'] . "</b>";
                    $param = (str_replace($v['keyword'],$bad,$param));
                }
                return $param;
            }
        }



        /**
        *域名审核时把备案信息写进domainchecks表
        */

   public function inserticp($param){
       $toid = $param['toid'];
	   $role = $param['role'];
	   $icp = $param['icp'];
       empty($icp)?'':$icp;
		if(!$toid){return false;}


       if ($role == 'admin') {//管理员审核
           $setArr['icp'] = $icp;

       } elseif ($role == 'teach') {//教师审核
           $setArr['icp'] = $icp;
       }
                //print_r($setArr);die;
       $res = $this->ebhdb->update("ebh_domainchecks", $setArr, array('crid' => $toid));
       return $res;

     }

        public function updatestatusattachment($toid,$type){
        	if(empty($toid) || empty($type)){
        		return false;
        	}
        	$this->ebhdb->update("ebh_attachments",array('status'=>1),array('attid'=>$toid));
        }

    /**
	*生成订单信息
	*/
	public function addOrder($param = array()) {
		if(empty($param))
			return false;
		$setarr = array();
		if(!empty($param['crid']))
			$setarr['crid'] = $param['crid'];
		if(!empty($param['ordername']))
			$setarr['ordername'] = $param['ordername'];
		if(!empty($param['uid']))
			$setarr['uid'] = $param['uid'];
		if(!empty($param['paytime']))
			$setarr['paytime'] = $param['paytime'];
		if(!empty($param['dateline']))
			$setarr['dateline'] = $param['dateline'];
		if(!empty($param['payfrom']))
			$setarr['payfrom'] = $param['payfrom'];
		if(!empty($param['totalfee']))
			$setarr['totalfee'] = $param['totalfee'];
		if(!empty($param['ip']))
			$setarr['ip'] = $param['ip'];
		if(!empty($param['payip']))
			$setarr['payip'] = $param['payip'];
		if(!empty($param['paycode']))
			$setarr['paycode'] = $param['paycode'];
		if(!empty($param['remark']))
			$setarr['remark'] = $param['remark'];
		if(!empty($param['ordernumber']))
			$setarr['ordernumber'] = $param['ordernumber'];
		if(!empty($param['buyer_id']))
			$setarr['buyer_id'] = $param['buyer_id'];
		if(!empty($param['buyer_info']))
			$setarr['buyer_info'] = $param['buyer_info'];
		if(!empty($param['status']))
			$setarr['status'] = $param['status'];
		if(!empty($param['dateline']))
			$setarr['dateline'] = $param['dateline'];
		if(!empty($param['refunded']))
			$setarr['refunded'] = $param['refunded'];
		if(!empty($param['out_trade_no']))
			$setarr['out_trade_no'] = $param['out_trade_no'];
		if(isset($param['invalid']))
			$setarr['invalid'] = $param['invalid'];
		if(isset($param['itype']))
			$setarr['itype'] = $param['itype'];
		if(isset($param['isbatchrefund']))
			$setarr['isbatchrefund'] = $param['isbatchrefund'];
		if(isset($param['batchid']))
			$setarr['batchid'] = $param['batchid'];
		if(isset($param['ptype']))
			$setarr['ptype'] = $param['ptype'];
		if(isset($param['redeemcode']))
			$setarr['redeemcode'] = $param['redeemcode'];
		$orderid = $this->ebhdb->insert('ebh_pay_torders',$setarr);
		return $orderid;
	}




 }

?>