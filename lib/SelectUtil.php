<?php
class SelectUtil{
	/**
	 *获取教室的select控件
	 *@author zkq
	 *@param String $userattr 用户自定义节点
	 *@param int $selected 默认选中的教室
	 *@return String (教室的select控件)
	 */
	public function getCrSelect($userattr = '',$selected=-1){
		$crlist = EBH::app()->model('classroom')->getsimpleclassroomlist();
		$crlistSelect = '<select '.$userattr.'>';
		$crlistSelect.='<option value=0 selected=selected>所有学校</option>';
		foreach ($crlist as $crkey => $crvalue) {
			if($crvalue['crid']==$selected){
				$crlistSelect.='<option selected=selected value='.$crvalue['crid'].' upid='.$crvalue['upid'].'>'.$crvalue['crname'].'</option>';
			}else{
				$crlistSelect.='<option value='.$crvalue['crid'].'>'.$crvalue['crname'].'</option>';
			}
		}
		$crlistSelect.='</select>';
		return $crlistSelect;
	}

	/**
     *获取答疑分类的select控件
     */
    public function getAskCatSelect($userattr = '',$hastopcat=true){
        $allCate = EBH::app()->model('category')->getCategoriesForAskquestion();
        $select = '<select '.$userattr.'>';
        if($hastopcat==true){
            $select.='<option name=catid value=0>全部分类</option>';
        }
        foreach ($allCate as $key => $value) {
            $select.='<option  value='.$value['catid'].' catid='.$value['catid'].' upid='.$value['upid'].'>'.$value['name'].'</option>';
            foreach ($value['subcat'] as $vk => $vv) {
                $select.='<option value='.$vv['catid'].'  catid='.$vv['catid'].' upid='.$vv['upid'].'>┣━'.$vv['name'].'</option>';
            }
        }
        $select.='</select>';
        return $select;
    }
}