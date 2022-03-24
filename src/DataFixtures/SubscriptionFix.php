<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Subscription;

class SubscriptionFix extends Fixture
{
    public function load(ObjectManager $manager)
    {
       foreach($this->getSubsriptionData() as [$user_id, $plan, $valid_to, $payment_status, $free_plan_used]){
		   $substriction = new Subscription();
		   $substriction->setPlan($plan);
		   $substriction->setValitTo($valid_to);
		   $substriction->setPaymentStatus($payment_status);
		   $substriction->setFreePlanUsed($free_plan_used);
		   
		   $user = $manager->getRepository(User::class)->find($user_id);
		   $user->setSubscription($substriction);
		   
		   $manager->persist($user);
		   
	   }

        $manager->flush();
    }
	
	private function getSubsriptionData(){
		return [
			[1, Subscription::getPlanNameByIndex(2), (new \DateTime())->modify('+77 year'), 'paid',false],
			[2, Subscription::getPlanNameByIndex(0), (new \DateTime())->modify('+1 month'), 'paid',true],
			[3, Subscription::getPlanNameByIndex(1), (new \DateTime())->modify('+1 minute'), 'paid',false]
		];
	}
}
