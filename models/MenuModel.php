<?php
/**
 * 菜单Model类
 */
class MenuModel extends CModel{
	/**
	 * 获得菜单列表
	 * @return array 返回菜单列表
	 */
	public function getMenuList() {
		$menu_list = array();
		$sql = 'SELECT menuid,title,codepath,parentid,code,del,displayorder FROM kf_menu WHERE `parentid`=0 AND `del`=0 ORDER BY `displayorder` DESC,`menuid` ASC';
		$list = $this->db->query($sql)->list_array();
		if (empty($list)) {
		    return false;
        }
		$sql = 'SELECT menuid,title,codepath,parentid,code,del,displayorder FROM kf_menu WHERE `parentid`>0 AND `del`=0 ORDER BY `displayorder` DESC,`menuid` ASC';
		$sub_list = $this->db->query($sql)->list_array();
		if (!empty($sub_list)) {
		    $list = array_merge($list, $sub_list);
        }
		foreach ($list as $value) {
			if ($value['parentid'] == 0) {
				$menu_list[$value['menuid']] = array(
					'title' => $value['title'],
					'codepath' => $value['codepath'],
                    'code' => !empty($value['code']) ? $value['code'] : $value['menuid'],
                    'displayorder' => $value['displayorder'],
                    'del' => $value['del'],
					'menuid'=> $value['menuid'],
					'parentid'=> $value['parentid'],
					'child' => array()
				);
			} else {
			    if (!isset($menu_list[$value['parentid']])) {
			        continue;
                }
				$menu_list[$value['parentid']]['child'][$value['menuid']] = array(
					'title' => $value['title'],
					'codepath' => $value['codepath'],
                    'code' => !empty($value['code']) ? $value['code'] : $value['menuid'],
                    'displayorder' => $value['displayorder'],
                    'del' => $value['del'],
					'menuid'=> $value['menuid'],
					'parentid'=> $value['parentid']
				);
			}
		}
		return $menu_list;
	}
	
	/**
	 * 获得功能模块列表
	 * @return array 返回功能模块列表
	 */
	public function getModuleList() {
		$module_list = array();
		$sql = 'SELECT title,codepath FROM kf_menu WHERE parentid=0 AND `del`=0';
		$list = $this->db->query($sql)->list_array();
		foreach ($list as $key => $value) {
			$module_list['admin' . $value['codepath']] = $value['title'];
		}
		return $module_list;
	}

    /**
     * 添加菜单
     * @param $title
     * @param $codepath
     * @param $code
     * @param int $parentid
     * @param int $displayorder
     * @return bool
     */
	public function add($title, $codepath, $code, $parentid = 0, $displayorder = 0) {
	    if ($parentid > 0) {
            $parent = $this->db->query(
                "SELECT 1 AS `p` FROM `kf_menu` WHERE `menuid`=$parentid AND `parentid`=0 AND `del`=0")
                ->row_array();
            if (empty($parent)) {
                return false;
            }
        }
	    $title = trim($title);
	    $codepath = trim($codepath);
	    $code = trim($code);
	    if (empty($title) || empty($codepath) || empty($code)) {
	        return false;
        }
        $displayorder = max(0, $displayorder);
	    $parentid = max(0, $parentid);
        return $this->db->insert('kf_menu', array(
            'title' => $title,
            'codepath' => $codepath,
            'parentid' => $parentid,
            'code' => $code,
            'displayorder' => $displayorder
        ));
    }

    /**
     * 修改菜单
     * @param $menuid
     * @param $title
     * @param $codepath
     * @param $code
     * @param int $displayorder
     * @return bool
     */
    public function update($menuid, $parentid, $title, $codepath, $code, $displayorder = 0) {
        $menuid = (int) $menuid;
        $parentid = (int) $parentid;
        $title = trim($title);
        $codepath = trim($codepath);
        $code = trim($code);
        if ($menuid < 1 || empty($title) || empty($codepath) || empty($code)) {
            return false;
        }
        $displayorder = max(0, $displayorder);
        return $this->db->update('kf_menu', array(
            'parentid' => $parentid,
            'title' => $title,
            'codepath' => $codepath,
            'code' => $code,
            'displayorder' => $displayorder
        ), "`menuid`=$menuid");
    }

    /**
     * 逻辑删除菜单
     * @param $menuid
     * @return bool
     */
    public function remove($menuid) {
        $menuid = (int) $menuid;
        if ($menuid < 0) {
            return false;
        }
        return $this->db->update('kf_menu', array('del' => 1,'code' => '','codepath' => '','parentid' => ''), "`menuid`=$menuid");
    }

    /**
     * 根据name字段取得数据
     * @param $name
     * @param $namevalue
     * @return array 返回对应行数据
     */
    public function getModuleByName($name,$namevalue) {
        $name_list = array();
        $sql = "SELECT menuid,title,codepath,parentid,code,del,displayorder FROM kf_menu WHERE $name='$namevalue'";
        $name_list = $this->db->query($sql)->row_array();
        return $name_list;
    }
}