<?php
$configs = [
	'admin_pagination_limit'=>10,
	'admin_pagination_links_each_side'=>4,
	'status'=>['1'=>'Active','2'=>'Inactive'],
];

if(!function_exists('pr')){
	function pr($array){
		echo '<pre>';print_r($array);echo '</pre>';
	}
}

if(!function_exists('constantForIn')){
	function constantForIn($comingArray= array()){
		if(!empty($comingArray)){
			$array = array_keys($comingArray);
		}
		return $array;
	}
}

if(!function_exists('getfileSize')){
	function getfileSize($to,$size){
		if($to == 'kb'){
			$nsize = $size*1000;
		}elseif($to == 'mb'){
			$nsize = $size/1000;
		}else{
			$nsize = '';
		}
		return $nsize;
	}
}

if(!function_exists('constantForComparison')){
	function constantForComparison($comparison,$comingArray){
		if(!empty($comingArray) && trim($comparison) != ''){
			$key = trim(strtolower($comparison));
			$array = array_flip($comingArray);
			$array = array_change_key_case($array,CASE_LOWER);
			if(isset($array[$key])){
				return $array[$key];
			}
		}
		return '-0';
	}
}








return $configs;


