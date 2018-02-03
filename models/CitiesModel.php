<?php 
	class CitiesModel extends CEbhModel{
		/**
         * 获取城市列表数组，
         * @param type $type
			用法:getCitiesByCode(5)，getCitiesByCode(6) 返回直辖市或者省数据数组;
				getCitiesByCode(1,$citycode),getCitiesByCode(6,$citycode),getCitiesByCode(8,$citycode) 返回对应城市下面的市或者区的列表数据Array;
         * 
         */
		public function getCitiesByCode($type = null,$citycode=null,$isini=null){
			if($type==5||$type==6){
				//省，直辖市
				$where = " isdirect = 1 or length(citycode)=2";
			}else{
				if($citycode== '0001' ||$citycode== '0002' ||$citycode== '0009'||$citycode== '0022'){
					if($type==1||$type==6||$type==8){
						$where = " citycode = '$citycode'";
					}else{
						$where = " citycode like '{$citycode}__'";
					}
				}else{
					$where = " citycode like '{$citycode}__'";
				}
				
			}
			$sql = 'select c.* from ebh_cities c where' .$where;
			return $this->ebhdb->query($sql)->list_array();
			
			
		}

		/**
	     *判断城市是否存在
	     *@author zkq
	     *@param int $citycode
	     *@return  bool
	     */
	    public function isExits($citycode=0){
	    	$citycode = intval($citycode);
	    	if(empty($citycode)){
	    		return false;
	    	}
	    	$sql = 'select count(*) count from ebh_cities t where t.citycode = '.$citycode.' limit 1 ';
	    	$res = $this->ebhdb->query($sql)->row_array();
	    	if(empty($res['count'])){
	    		return false;
	    	}else{
	    		return true;
	    	}
	    }


	}