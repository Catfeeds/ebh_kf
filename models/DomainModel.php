<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/9
 * Time: 10:58
 */
class DomainModel extends CEbhModel{
            public function getDomainList($param){
                $wherearr = array();
                $sql = "select c.fulldomain ,c.crname,c.crid,c.domain_time,ck.admin_status,ck.teach_status,ck.del,ck.admin_uid from ebh_domainchecks c  left join ebh_billchecks ck on ck.toid = c.crid and ck.type=13";
                if (!empty($param['q'])){
					$q = $this->ebhdb->escape_str($param['q']);
                    $wherearr[] = '(c.crname like \'%' . $q . '%\' or c.fulldomain like \'%'.$q.'%\')';
				}
                   $wherearr[]='c.fulldomain != \'\' ';

                if(!empty($param['access'])){
                    $wherearr[]='c.crid in ('.$this->ebhdb->escape_str($param['access']).')';
                    $wherearr[]='c.fulldomain != \'\' ';
                }
                if(!empty($param['crid'])){
                    if(is_array($param['crid'])){
                        $wherearr[] = 'c.crid in( '.implode(',', $param['crid']).')';

                    }else{
                        $wherearr[] = 'c.crid ='.$param['crid'];
                    }
                }
                //管理员
                if($param['role']=='admin'){
                    if($param['admin_status']>0){
                        $wherearr[] = 'ck.admin_status ='.$param['admin_status'];
                        $wherearr[]='c.fulldomain != \'\' ';
                    }
                    if($param['cat']==0){
                        $wherearr[] = '(ck.admin_status is null or ck.admin_status = 0 or ck.admin_status = 3)';
                         $wherearr[]='c.fulldomain != \'\' ';
                    }
                    if($param['cat']==1){
                        $wherearr[] = 'ck.admin_status in(1,2) and ck.del=0';
                         $wherearr[]='c.fulldomain != \'\' ';
                    }
                    if($param['cat']==2){
                        $wherearr[] = 'ck.del=1';
                         $wherearr[]='c.fulldomain != \'\' ';
                    }
                    //教师
                }elseif($param['role']=='teach'){
                    if($param['teach_status']>0){
                        $wherearr[] = 'ck.teach_status ='.$param['teach_status'];
                         $wherearr[]='c.fulldomain != \'\' ';
                    }
                    if($param['cat']==0){
                        $wherearr[] = 'ck.teach_status is null or ck.teach_status = 0';
                         $wherearr[]='c.fulldomain != \'\' ';
                    }
                    if($param['cat']==1){
                        $wherearr[] = 'ck.teach_status>0 and ck.del=0';
                         $wherearr[]='c.fulldomain != \'\' ';
                    }
                    if($param['cat']==2){
                        $wherearr[] = 'ck.del=1';
                         $wherearr[]='c.fulldomain != \'\' ';
                    }
                }
                if (!empty($wherearr)){
                    $sql.= ' WHERE ' . implode(' AND ', $wherearr);
                }
                 if (empty($wherearr)){
                    $sql.= ' WHERE ' . 'c.fulldomain != \'\'';
                }
                if (!empty($param['order'])) {
                    $sql .= ' order by ' . $param['order'];
                } else {
                    $sql .= ' order by c.domain_time desc ';
                }
                if(!empty($param['limit'])) {
                    $sql .= ' limit '. $param['limit'];
                }              

                $rows =  $this->ebhdb->query($sql)->list_array();
              
                return $rows;
            }

            public function getdomaincount($param) {
            $wherearr = array();
            $sql = 'select count(*) count from ebh_domainchecks c  '.
                'left join ebh_billchecks ck on ck.toid = c.crid and ck.type=13' ;
            if (!empty($param['q']))
                $wherearr[] = '(c.crname like \'%' . $this->ebhdb->escape_str($param['q']) . '%\' )';
                $wherearr[]='c.fulldomain != \'\' ';
            if(!empty($param['access'])){
              $wherearr[]='c.crid in ('.$this->ebhdb->escape_str($param['access']).')';
                $wherearr[]='c.fulldomain != \'\' ';
            }

            if(!empty($param['crid'])){
                if(is_array($param['crid'])){
                    $wherearr[] = 'c.crid in( '.implode(',', $param['crid']).')';

                }else{
                    $wherearr[] = 'c.crid ='.$param['crid'];

                }
            }
            //管理员
            if($param['role']=='admin'){
                if($param['admin_status']>0){
                    $wherearr[] = 'ck.admin_status ='.$param['admin_status'];
                    $wherearr[]='c.fulldomain != \'\' ';

                }
                if($param['cat']==0){
                    $wherearr[] = '(ck.admin_status is null or ck.admin_status = 0 or ck.admin_status = 3)';
                    $wherearr[]='c.fulldomain != \'\' ';
                }
                if($param['cat']==1){
                    $wherearr[] = 'ck.admin_status in(1,2) and ck.del=0';
                    $wherearr[]='c.fulldomain != \'\' ';

                }
                if($param['cat']==2){
                    $wherearr[] = 'ck.del=1';
                    $wherearr[]='c.fulldomain != \'\' ';

                }
                //教师
            }elseif($param['role']=='teach'){
                if($param['teach_status']>0){
                    $wherearr[] = 'ck.teach_status ='.$param['teach_status'];
                    $wherearr[]='c.fulldomain != \'\' ';

                }
                if($param['cat']==0){
                    $wherearr[] = 'ck.teach_status is null or ck.teach_status = 0';
                    $wherearr[]='c.fulldomain != \'\' ';

                }
                if($param['cat']==1){
                    $wherearr[] = 'ck.teach_status>0 and ck.del=0';
                    $wherearr[]='c.fulldomain != \'\' ';

                }
                if($param['cat']==2){
                    $wherearr[] = 'ck.del=1';
                    $wherearr[]='c.fulldomain != \'\' ';

                }
            }        

            if (!empty($wherearr)){
                $sql.= ' WHERE ' . implode(' AND ', $wherearr);
            }else{
                $sql.=' WHERE'.' fulldomain!=\' \' ';
            }
                //var_dump($sql);die;

            $count = $this->ebhdb->query($sql)->row_array();
            return $count['count'];
        }


        public function getdomainbycrid($crid){  //查询一条记录
            //print_r($crid);die;
            $sql='select c.crid,c.fulldomain,c.crname,c.domain_time,c.icp,'.
                    'ck.admin_status,ck.admin_remark,ck.teach_status,ck.teach_remark,ck.del,ck.admin_dateline,ck.teach_dateline,ck.delline,ck.admin_ip,ck.teach_ip,ck.admin_uid '.
                    'from ebh_domainchecks c '.
                    'left join ebh_billchecks ck on ck.toid = c.crid and ck.type=13 ' .
                    'where c.crid=' . $crid; 
                    //  print_r($sql);die;
                    return $this->ebhdb->query($sql)->row_array();
        }


        public function getdomaininfo($crid){//查询域名和备案信息

            $sql='select fulldomain,icp from ebh_domainchecks  where crid='.$crid;

            return   $res= $this->ebhdb->query($sql)->row_array();

         }

         public function editclassromm($param){
             if(empty($param)){
                 return false;
             }else{
                 $crid=$param['crid'];
                 $setArr['fulldomain'] = $param['fulldomain'];
                 $setArr['icp'] = $param['icp'];
                $row=$this->ebhdb->update("ebh_classrooms",$setArr,array('crid' =>$crid));
                 return $row;
             }
         }

}