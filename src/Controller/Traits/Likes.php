<?php

namespace App\Controller\Traits;

use App\Entity\User;

trait Likes{
	private function likeVideo($video){
        $user=$this->$this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->addLikedVideo($video);
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return 'liked';
    }

    private function dislikeVideo($video){
        $user=$this->$this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->addDislikedVideo($video);
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return 'disliked';
    }

    private function undolikeVideo($video){
        $user=$this->$this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->removeLikedVideo($video);
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return 'undo liked';
    }

    private function undoDislikeVideo($video){
        $user=$this->$this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->removeSislikedVideo($video);
        $em=$this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return 'undo disliked';
    }
}

