<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Videos;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
//use App\DataFixtures\UserFix;

class CommentFix extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
       foreach($this->commentData() as [$content, $user, $video, $created_at]){
        $comment = new Comment;
        $user = $manager->getRepository(User::class)->find($user);
        $video= $manager->getRepository(Videos::class)->find($video);

        $comment->setContent($content);
        $comment->setUser($user);
        $comment->setVideo($video);
        $comment->setCreatedAtForFixtures(new \DateTime($created_at));

        $manager->persist($comment);
       }

        $manager->flush();
    }

    private function commentData(){
        return[
            ['Lorem ipsum dupsum', 1, 3, '1977-01-01 13:13:13'],
            [' Lorem ipsum dupsum', 2, 7, '1977-01-01 13:13:13'],
            [' Lorem ipsum dupsum', 3, 13, '1977-01-01 13:13:13'],
            [' Lorem ipsum dupsum', 4, 1, '1977-01-01 13:13:13'],
            [' Lorem ipsum dupsum', 1, 11, '1977-01-01 13:13:13'],
            [' Lorem ipsum dupsum', 1, 7, '1977-01-01 13:13:13'],
            [' Lorem ipsum dupsum', 1, 13, '1977-01-01 13:13:13']
        ];

    }

    public function getDependencies(){
        return array(
            UserFix::class
       );
    }
}
