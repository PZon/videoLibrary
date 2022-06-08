<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Subscription;
//use App\Controller\Traits\SaveSubscription;


class SubscriptionController extends AbstractController
{
    //use SaveSubscription;
    /**
     * @Route("/pricing", name="pricing")
     */
    public function pricing()
    {
        return $this->render('front/pricing.html.twig',[
            'name' => Subscription::getPlanDataNames(),
            'price' => Subscription::getPlanDataPrice(),
        ]);
    }

     /**
     * @Route("/payment/{paypal}", name="payment", defaults = {"paypal":false})
     */
    public function payment(/*$paypal,*/ SessionInterface $session){
      /*  $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if($paypal){
            $this->saveSubscription($session->get('planName'), $this->getUser());
            return $this->redirectToRoute('adminPage');
        }
        return $this->render('front/payment.html.twig');*/

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if($session->get('planName') == 'enterprise')
        {
            $subscribe = Subscription::EnterprisePlan;
        }
        else
         {
            $subscribe = Subscription::ProPlan;
         }
        return $this->render('front/payment.html.twig',['subscribe'=>$subscribe]);
    }

}
