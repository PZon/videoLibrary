<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CatTreeAbstract;
use App\Twig\AppExtension;

class CatTreeAdminOptionList extends CatTreeAbstract{

	public function getCategoryList(array $catArray, $repeat=0){

		foreach($catArray as $val){
			$this->catList[]=['name'=>str_repeat("-",$repeat).$val['name'], 'id'=>$val['id']];

			if(!empty($val['children'])){
				$repeat = $repeat+2;
				$this->getCategoryList($val['children'], $repeat);
				$repeat = $repeat - 2;
			}
		}
		return $this->catList;
	}
}