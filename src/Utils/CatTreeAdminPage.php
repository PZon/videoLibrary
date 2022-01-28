<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CatTreeAbstract;
use App\Twig\AppExtension;

class CatTreeAdminPage extends CatTreeAbstract{

	public $html_1 = '<ul class="fa-ul text-left">';
    public $html_2 = '<li><i class="fa-li fa fa-arrow-right"></i>  ';
    public $html_3 = '<a href="';
    public $html_4 = '">';
    public $html_5 = '</a> <a onclick="return confirm(\'Are you sure?\');" href="';
    public $html_6 = '">';
    public $html_7 = '</a>';
    public $html_8 = '</li>';
    public $html_9 = '</ul>';
	
	public function getCategoryList(array $catArray){
		$this->catList .= $this->html_1;
        foreach ($catArray as $value){
        	$url_edit = $this->urlGI->generate('editCategory', ['id' => $value['id']]);
        	$url_delete = $this->urlGI->generate('deleteCategory', ['id' => $value['id']]);
        	$this->catList .= $this->html_2 . $value['name'] . $this->html_3 . $url_edit . $this->html_4 . ' Edit' . $this->html_5 . $url_delete . $this->html_6 . 'Delete' . $this->html_7;
           
            if (!empty($value['children'])){
            	$this->getCategoryList($value['children']);
            }
            $this->catList .= $this->html_8;
        }
        $this->catList .= $this->html_9;
        return $this->catList;

	}

}
