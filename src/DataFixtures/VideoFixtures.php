<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Videos;
use App\Entity\Category;
use App\Entity\User;

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
        $this->loadLikes($manager);
        $this->loadDislikes($manager);
    }

    public function loadLikes($manager){
        foreach($this->likesData() as [$video_id, $user_id]){
            $video = $manager->getRepository(Videos::class)->find($video_id);
            $user = $manager->getRepository(User::class)->find($user_id);
            $video->addUsersThatLike($user);
            $manager->persist($video);
        }
        $manager->flush();
    }

    public function loadDislikes($manager){
         foreach($this->dislikesData() as [$video_id, $user_id]){
            $video = $manager->getRepository(Videos::class)->find($video_id);
            $user = $manager->getRepository(User::class)->find($user_id);
            $video->addUsersThatDontLike($user);
            $manager->persist($video);
        }
        $manager->flush();
    }

    private function VideoData()
    {
        return [

            ['Movies 1',289729765,10],
            ['Movies 2',238902809,11],
            ['Movies 3',150870038,14],
            ['Movies 4',219727723,15],
            ['Movies 5',289879647,12],
            ['Movies 6',261379936,13],
            ['Movies 7',289029793,14],
            ['Movies 8',60594348,15],
            ['Movies 9',290253648,12],

            ['Family 1',289729765,12],
            ['Family 2',289729765,12],
            ['Family 3',289729765,12],

            ['Romantic comedy 1',289729765,9],
            ['Romantic comedy 2',289729765,13],

            ['Romantic drama 1',289729765,14],

            ['Toys  1',289729765,4],
            ['Toys  2',289729765,5],
            ['Toys  3',289729765,6],
            ['Toys  4',289729765,7],
            ['Toys  5',289729765,5],
            ['Toys  6',289729765,7]

        ];
    }

    private function dislikesData(){
        return[
            [7,3],
            [14,2],
            [21,3],
            [13,1],
            [17,3]
        ];
    }

        private function likesData(){
        return[
            [1,1],
            [5,2],
            [10,3],
            [15,2],
            [20,2]
        ];
    }
}
