<?php

/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2016/10/12
 * Time: 10:40
 *充值记录类对应ebh_chargel表
 */
class ChargeModel extends CEbhModel
{
    /**
     * 插入一条记录
     *
     */
    public function add($param=array()){
        if(empty($param)){
            return false;
        }
        $data = array();
        if(!empty($param['rid'])){
            $data['rid'] = $param['rid'];
        }
        if(!empty($param['uid'])){
            $data['uid'] = $param['uid'];
        }
        if(!empty($param['useuid'])){
            $data['useuid'] = $param['useuid'];
        }
        if(!empty($param['cardno'])){
            $data['cardno'] = $param['cardno'];
        }
        if(!empty($param['type'])){
            $data['type'] = $param['type'];
        }
        if(!empty($param['value'])){
            $data['value'] = $param['value'];
        }
        if(!empty($param['curvalue'])){
            $data['curvalue'] = $param['curvalue'];
        }
        if(!empty($param['status'])){
            $data['status'] = $param['status'];
        }
        if(!empty($param['fromip'])){
            $data['fromip'] = $param['fromip'];
        }
        if(!empty($param['dateline'])){
            $data['dateline'] = $param['dateline'];
        }
        return $this->ebhdb->insert('ebh_charges',$data);
    }

    /**
     * 查询一条记录
     *
     */
    public function getOneByChargeid($chargeid){
        $this->ebhdb->set_con(0);
        $sql = "select rid,chargeid,useuid,uid,type,value,curvalue,status,dateline,fromip,paytime,buyer_id,buyer_info  from ebh_charges where chargeid = ".$chargeid;
        return $this->ebhdb->query($sql)->row_array();
    }
    /**
     * 更新记录
     *
     */
    public function update($param,$chargeid){
        $setarr = array();
        if(!empty($param['status'])){
            $setarr['status'] = $param['status'];
        }
        if(!empty($param['ordernumber'])){
            $setarr['ordernumber'] = $param['ordernumber'];
        }
        if(!empty($param['paytime'])){
            $setarr['paytime'] = $param['paytime'];
        }
        if(!empty($param['buyer_id'])){
            $setarr['buyer_id'] = $param['buyer_id'];
        }
        if(!empty($param['buyer_info'])){
            $setarr['buyer_info'] = $param['buyer_info'];
        }
        return $this->ebhdb->update("ebh_charges",$setarr,array('chargeid'=>$chargeid));
    }
}