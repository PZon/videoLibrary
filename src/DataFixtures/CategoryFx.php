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
       $this->loadToys($manager);
       $this->loadBooks($manager);
       $this->loadMovies($manager); 
       $this->loadVideoGames($manager);   
    }

    private function loadMainCategories($manager){

        foreach($this->getMainCategoriesData() as [$name]){ 
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
        }

        $manager->flush();
    }

    private function loadMovies($manager){
       $this->loadSubCategories($manager, 'Movies',1); 
    }

    private function loadBooks($manager){
       $this->loadSubCategories($manager, 'Books',2);
    }

    private function loadToys($manager){
       $this->loadSubCategories($manager, 'Toys',3); 
    }

    private function loadVideoGames($manager){
       $this->loadSubCategories($manager, 'VideoGames',7); 
    }

    private function loadSubCategories($manager, $category, $parentId){

        $parent = $manager->getRepository(Category::class)->find($parentId);
        $methodName = "get{$category}Data";

        foreach($this->$methodName() as [$name]){ 
            $category = new Category();
            $category->setName($name);
            $category->setParent($parent);
            $manager->persist($category);
        }

        $manager->flush();
    }

    private function getMainCategoriesData(){
        return[
            ['Movies',1],
            ['Books',2],
            ['Toys',3],
        ];
    }

    private function getToysData(){
        return[
            ['Board Game',4],
            ['Tedy Bear',5],
            ['Ball',6],
            ['Video Game',7],
        ];
    }

    private function getBooksData(){
        return[
            ['SF',8],
            ['Romance',9],
            ['Kids',10],
            ['Comics',11],
        ];
    }

    private function getMoviesData(){
        return[
            ['Horror',12],
            ['Comedy',13],
            ['Drama',14],
            ['Docs',15],
        ];
    }

        private function getVideoGamesData(){
        return[
            ['Action',16],
            ['VR',17],
        ];
    }
}
