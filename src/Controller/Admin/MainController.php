<?php

namespace App\Controller\Admin;

use App\Utils\CatTreeAdminPage;
use App\Utils\CatTreeAdminOptionList;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Videos;
use App\Form\UserType;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @Route("/admin")
 */

class MainController extends AbstractController{
	
	/**
     * @Route("/", name="adminPage")
     */
    public function index(Request $request, UserPasswordEncoderInterface $password_encoder, TranslatorInterface $translate)
    {
        $user = $this->getUser();
        $form=$this->createForm(UserType::class, $user, ['user'=>$user]);
        //$form=$this->createForm(UserType::class);
        $form->handleRequest($request);
        $is_invalid = null;

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $user->setName($request->request->get('user')['name']);
            $user->setLastName($request->request->get('user')['last_name']);
            $user->setEmail($request->request->get('user')['email']);
            $password=$password_encoder->encodePassword($user, $request->request->get('user')['password']['first']);
            $user->setPassword($password);
            $em->persist($user);
            $em->flush();

            //$translated = $translate->trans('your changes were saved');

            $this->addFlash(
                'success',
                'your changes were saved'
                //$translated - również działa
            );
            return $this->redirectToRoute('adminPage');
        }elseif($request->isMethod('post')){
            $is_invalid = 'is_invalid';
        }

        return $this->render('admin/my_profile.html.twig', [
			'subscription'=>$this->getUser()->getSubscription(),
            'form'=>$form->createView(),
            'is_invalid'=> $is_invalid
		]);
    }
	
	
	/**
     * @Route("/videos", name="videos")
     */
    public function videos(CatTreeAdminOptionList $cats)
    {
		if($this->isGranted('ROLE_ADMIN')){
			//$videos = $this->getDoctrine()->getRepository(Videos::class)->findAll();
            $cats->getCategoryList($cats->buidTree());
            $videos = $this->getDoctrine()->getRepository(Videos::class)->findBy([],['title'=>'ASC']);
		}else{
            $cats = null;
			$videos = $this->getUser()->getLikedVideos();
		}
		
        return $this->render('admin/videos.html.twig',[
                            'videos'=>$videos,
                            'cats'=>$cats ]);
    }

    
	/**
     * @Route("/cancel-plan", name="cancel_plan")
     */
    public function cancelPlan(){
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $subscription = $user->getSubscription();
        $subscription->setValidTo(new \Datetime());
        $subscription->setPaymentStatus(null);
        //$user->setSubscription(null);
        $subscription->setPlan('canceled');

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->persist($subscription);
        $em->flush();

        return $this->redirectToRoute('adminPage');
    }

    /**
     * @Route("/deleteAccount", name="deleteAccount")
     */
    public function deleteAccount(){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($this->getUser());
        $em->remove($user);
        $em->flush();

        session_destroy();

        return $this->redirectToRoute('main_page');
    }
}