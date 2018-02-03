<?php

/**
 * Created by PhpStorm.
 * User: wjf
 * Date: 2016/10/12
 * Time: 10:44
 * 充值记录主表 ebh_records表
 */
class RecordModel extends  CEbhModel
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
        if(!empty($param['uid'])){
            $data['uid'] = $param['uid'];
        }
        if(!empty($param['cate'])){
            $data['cate'] = $param['cate'];
        }
        if(!empty($param['dateline'])){
            $data['dateline'] = $param['dateline'];
        }
        if(!empty($param['status'])){
            $data['status'] = $param['status'];
        }
        return $this->ebhdb->insert('ebh_records',$data);
    }


    /**
     * 更新记录
     *
     */
    public function update($param,$rid){
        $setarr = array();
        if(isset($param['status'])){
            $setarr['status'] = $param['status'];
        }
        if(!empty($param['dateline'])){
            $setarr['dateline'] = $param['dateline'];
        }
        return $this->ebhdb->update("ebh_records",$setarr,array('rid'=>$rid));
    }
}