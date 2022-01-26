<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CatTreeAbstract;

class CatTreeFrontPage extends CatTreeAbstract{
	public function getCategoryList(array $catArray){
			//to do;
			
			$this->catList .= '<ul>';
				foreach($catArray as $val){
					$catName = $val['name'];
					$url= $this->urlGI->generate('videoList',['catName'=>$catName, 'id'=>$val['id']]);
					$this->catList .= '<li>' . '<a href="'.$url .'">' . $catName . '</a>';
					
					if(!empty($val['children'])){
						$this->getCategoryList($val['children']);
					}
				}
			
			$this->catList .= '</ul>';
			return $this->catList;
	}
}
