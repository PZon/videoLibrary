<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Videos;
use App\Entity\Category;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
       foreach($this->VideoData() as [$title, $path, $category_id]){
            $duration = random_int(10, 300);
            $category = $manager->getRepository(Category::class)->find($category_id);
            $video = new Videos();
            $video->setTitle($title);
            $video->setPath('https://player.vimeo.com/video/'.$path);
            $video->setCategory($category);
            $video->setDuration($duration);
            $manager->persist($video);
       }

        $manager->flush();
    }

    private function VideoData()
    {
        return [

            ['Movies 1',289729765,1],
            ['Movies 2',238902809,1],
            ['Movies 3',150870038,1],
            ['Movies 4',219727723,1],
            ['Movies 5',289879647,1],
            ['Movies 6',261379936,1],
            ['Movies 7',289029793,1],
            ['Movies 8',60594348,1],
            ['Movies 9',290253648,1],

            ['Family 1',289729765,12],
            ['Family 2',289729765,12],
            ['Family 3',289729765,12],

            ['Romantic comedy 1',289729765,13],
            ['Romantic comedy 2',289729765,13],

            ['Romantic drama 1',289729765,14],

            ['Toys  1',289729765,3],
            ['Toys  2',289729765,3],
            ['Toys  3',289729765,3],
            ['Toys  4',289729765,3],
            ['Toys  5',289729765,3],
            ['Toys  6',289729765,3]

        ];
    }
}
