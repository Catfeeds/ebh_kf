<?php
/**
 *sphinx工具库
 *@author zyp
 */
class SphinxUtil{
    public function __construct(){
        $this->sphinxClient = Ebh::app()->lib('SphinxClient');
        $this->sphinx_config = Ebh::app()->getConfig()->load('sphinx');
    }

    /**
     * 查询今天之前最后一条uid
     * @return int 返回用户uid
    */
    function getLastUid(){
        $lastuid = 0;
        $this->sphinxClient->setServer($this->sphinx_config['host'], $this->sphinx_config['port']);
        $this->sphinxClient->setMatchMode(SPH_MATCH_EXTENDED);
        //设置超时时间（毫秒）
        $this->sphinxClient->setMaxQueryTime(30000);
        //设置分页
        $this->sphinxClient->SetLimits(0,1);
        $this->sphinxClient->SetSortMode(SPH_MATCH_EXTENDED,'@id DESC,@weight DESC');
        $res = $this->sphinxClient->query('','user');
        if(($res !== false) && !empty($res['matches'])) {
            $uidarr = array_keys($res['matches']);
            $lastuid = !empty($uidarr['0']) ? $uidarr['0'] : 0;
        }
        return $lastuid;
    }
}
?>