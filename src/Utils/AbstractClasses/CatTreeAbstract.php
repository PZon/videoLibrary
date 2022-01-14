<?php

namespace App\Utils\AbstractClasses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CatTreeAbstract{

	protected static $dbConnection;
	public $catArrayFromDB;
		
	public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $urlGI){
		$this->em = $em;
		$this->urlGI = $urlGI;
		$this->catArrayFromDB = $this->getCategories();
	}

	abstract public function getCategoryList(array $catArray);

	public function buidTree($parentId = null):array{

		$subCat=[];

		foreach($this->catArrayFromDB as $cat){
			if($cat['parent_id'] == $parentId){
				$children = $this->buidTree($cat['id']);

				if($children){
					$cat['children'] = $children;
				}
				$subCat[] = $cat;
			}
		}
		return $subCat;
	}

	private function getCategories():array{

		if(self::$dbConnection){
			return self::$dbConnection;
		}else{
				$conn = $this->em->getConnection();
				$sql = "SELECT * FROM categories";
				$stmt = $conn->prepare($sql);
				$stmt->execute();
				return self::$dbConnection = $stmt->fetchAll();
			}
	}
}

