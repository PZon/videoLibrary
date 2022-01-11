<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Category;

class CategoryFx extends Fixture
{
    public function load(ObjectManager $manager)
    {
       $this->loadMainCategories($manager);
       
    }

    private function loadMainCategories($manager){

        foreach($this->getMainCategoriesData() as [$name]){ 
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();
    }

    private function getMainCategoriesData(){
        return[
            ['Movies',1],
            ['Books',2],
            [ 'Toys',3],
        ];
    }
}
