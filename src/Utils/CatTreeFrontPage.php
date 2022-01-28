<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CatTreeAbstract;
use App\Twig\AppExtension;

class CatTreeFrontPage extends CatTreeAbstract{

	public $html_1='<ul>';
	public $html_2='<li>';
	public $html_3='<a href="';
	public $html_4='">';
	public $html_5='</a>';
	public $html_6='</li>';
	public $html_7='</ul>';

	public function getCategoryList(array $catArray){
			//to do;
			
			$this->catList .= $this->html_1;
				foreach($catArray as $val){
					$catName = $this->slugger->slugify($val['name']);
					$url= $this->urlGI->generate('videoList',['catName'=>$catName, 'id'=>$val['id']]);
					$this->catList .= $this->html_2. $this->html_3 .$url . $this->html_4. $catName .$this->html_5 ;
					
					if(!empty($val['children'])){
						$this->getCategoryList($val['children']);
					}
					$this->catList .= $this->html_6;
				}
			
			$this->catList .= $this->html_7;
			return $this->catList;
	}

	public function getMainParent(int $id): array{
		$key = array_search($id, array_column($this->catArrayFromDB, 'id'));

		if($this->catArrayFromDB[$key]['parent_id'] != null){
			return $this->getMainParent($this->catArrayFromDB[$key]['parent_id']);
		}else{
			return ['id'=>$this->catArrayFromDB[$key]['id'],
					'name'=>$this->catArrayFromDB[$key]['name']
					];		
		}
	}

	public function getCategoryListAndParent(int $id): string{
		$this->slugger = new AppExtension;
		$parentData = $this->getMainParent($id);
		$this->mainParentName = $parentData['name'];
		$this->mainParentId = $parentData['id'];
		$key = array_search($id, array_column($this->catArrayFromDB,'id'));
		$this->currentCategoryName = $this->catArrayFromDB[$key]['name'];

		$catArray = $this->buidTree($parentData['id']);
		return $this->getCategoryList($catArray);
	}
}
