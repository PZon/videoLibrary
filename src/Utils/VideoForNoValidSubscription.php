<?php

namespace App\Utils;

use Symfony\Component\Security\Core\Security;
use App\Entity\Videos;

class VideoForNoValidSubscription{
	public $isSubscriptionValid = false;
	
	public function __construct(Security $security){
		$user = $security->getUser();
		if($user && $user->getSubscription() != null){
			$paymentStatus = $user->getSubscription()->getPaymentStatus();
			$valid = new \Datetime() < $user->getSubscription()->getValidTo();
			
			if($paymentStatus != null && $valid){
				$this->isSubscriptionValid = true;
			}
		}
	}
	
	public function check(){
		if($this->isSubscriptionValid){
			return null;
		}else{
			static $video = Videos::videoForNotLoggedInOrNoMember;
			return $video;
		}
		
	}

}
