<?php
/**
* 城市控制器，主要用来获取城市三级目录(三个select控件)和城市三级文本数据,如 "浙江 杭州 西湖";
  
*/
class CitiesController extends CControl{
        public $allShengHtml=null;
        public $allShiHtml=null;
        public $allQuHtml=null;
        public $addrText = '';
	   /**
         * 获取城市列表，
         * @param type $type
            用法:getCities(6,$citycode);getCities(6,$citycode);getCities(6,$citycode);返回对应citycode下级的列表数据;例如
            <option value="0001">北京</option>
                getCities(5);返回城市三级列表;
        * @return 
         */

        public function getCities($type=null,$citycode=NULL){
                $type = isset($type)?$type:$this->input->post('type');
                $citycode = is_null($citycode)?$this->input->post('citycode'):$citycode;
                $cities = $this->model('Cities')->getCitiesByCode($type,$citycode);

                if($type==6||$type==7||$type==8){
                        return $cities;
                }else{  
                        echo $this->returnCitiesHtml($cities);
                }
                
        }
        //传入citycode初始化省市区列表，citycode默认选中
        /*      根据$citycode初始化控制器属性,初始化后效果
                        $this->$allShengHtml='<select><option value="02">省名</option></select>';
                        $this->$allShiHtml='<select><option value="0202">市名</option></select>';;
                        $this->$allQuHtml='<select>
                                            <option value="020202">区名1</option>
                                            <option value="020203" selected = selected>区名2</option>
                                            </select>';
                        $this->$addrText = '省名 市名 区名2';

        */
        public function iniCities($citycode){
                if(substr($citycode,0,2)==0){
                        $sheng = substr($citycode, 0,4);
                }else{
                        $sheng = substr($citycode,0,2);
                }
                $shi = substr($citycode, 0,4);
                $qu = $citycode;

                $allSheng = $this->getCities(6);
                $allShi = $this->getCities(8,$sheng);
                $allQu = $this->getCities(7,$shi);
                $allShengHtml = $this->returnCitiesHtml($allSheng,$sheng);
                $allShiHtml = $this->returnCitiesHtml($allShi,$shi);
                $allQuHtml = $this->returnCitiesHtml($allQu,$qu);
                $this->allShengHtml = '<select name="address_sheng" id="address_sheng" onchange="select_address(this,1)">'.$allShengHtml.'</select>';
                $this->allShiHtml = '<select name="address_shi" id="address_shi" onchange="select_address(this,2)">'.$allShiHtml.'</select>';
                $this->allQuHtml = ' <select name="address_qu" id="address_qu">'.$allQuHtml.'</select>';
              
               
        }
        //控制器私有方法,传入城市列表和默认选中项，返回初始化后的一级城市列表,传入cities列表Array
        /*  $this->returnCitiesHtml($cities,0002);
            返回结果:
            <select>
                <option value="0001">北京</option>
                <option value="0002" selected = selected>A地区</option>
                <option value="0003">B地区</option>
            </select>
        */
        private function returnCitiesHtml($cities,$checkedTag=0){
                $_html = '<option value="">请选择</option>';
                foreach ($cities as $v) {
                        if($v['citycode']==$checkedTag){
                            if($this->addrText!=$v['cityname'].' '){
                                $this->addrText.= $v['cityname'].' ';
                            }
                            $_html.='<option value="'.$v['citycode'].'" selected=selected>'.$v['cityname'].'</option>';    
                        }else{
                            $_html.='<option value="'.$v['citycode'].'">'.$v['cityname'].'</option>';
                        }   
                        
                }
                return $_html;
        }

        /*获取城市列表,post传入citycode;返回城市三级列表,返回结果如，一般结合挂件使用cities_widget
            <select><option value="0001">北京</option></select>
            <select><option value="0001">北京</option></select>
            <select><option value="000102">北京某区</option></select>
        */
        public function getAddr(){
                $key = md5(serialize($this->input->post('citycode')));
                if($this->cache->get($key)){
                    echo $this->cache->get($key);exit;
                }
                $citycode = $this->input->post('citycode');
                if($citycode){
                        $this->iniCities($citycode);   
                }
                $city = $this->allShengHtml.$this->allShiHtml.$this->allQuHtml;
                $this->cache->set($key,$city,60);
                echo $city;
        }
        //获取城市文本信息
        //post传入citycode;返回结果如："浙江 杭州 西湖";
        public function getAddrText(){
            $citycode = $this->input->post('citycode');
            if(empty($citycode)){
                $citycode = $this->input->get('citycode');
            }
            $cacheName = 'citycode_'.$citycode;
            if($this->cache->get($cacheName)){
                echo $this->cache->get($cacheName);
                exit;
            }
            if($citycode){
                        $this->iniCities($citycode);   
            }
            $this->cache->set($cacheName,$this->addrText,3600*24);
            echo $this->addrText;
        }


}