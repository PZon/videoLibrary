<?php

namespace App\Listeners;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\Videos;
use App\Entity\User;

class NewVideoListener{
	public function __construct(\Twig_Environment $templating, \Swift_Mailer $mailer){
         $this->templating = $templating;
         $this->mailer = $mailer;
   		}

	public function postPersist(LifecycleEventArgs $args){
		$entity = $args->getObject();

		if($entity instanceof Videos){
			return;
		}

		$em = $args->getObjectManager();
		//to do

		$users = $em->getRepository(User::class)->findAll();

		foreach($users as $user){
			$message = (new \Swift_Message('Hello Email'))
                ->setFrom('send@example.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->templating->render(
                        'emails/new_video.html.twig',
                        [
                            'name' => $user->getName(),
                            'video' => $entity
                        ]
                    ),
                    'text/html'
                );

            $this->mailer->send($message);
		}
	}
}