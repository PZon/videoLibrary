<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Utils\CatTreeFrontPage;


class FrontController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index()
    {
        return $this->render('front/index.html.twig');
    }

     /**
     * @Route("/videoList/cat/{catName},{id}", name="videoList")
     */
    public function videoList($id, CatTreeFrontPage $cats)
    {
         $subCats=$cats->buidTree($id);
         dump($subCats); //composer require symfony/var-dumper --dev
        return $this->render('front/videolist.html.twig', ['subCats'=>$cats->getCategoryList($subCats)]);
    }

    /**
     * @Route("/videoDetails", name="videoDetails")
     */
    public function videoDetails()
    {
        return $this->render('front/video_details.html.twig');
    }

    /**
     * @Route("/searchResult",methods={"POST"}, name="searchResult")
     */
    public function searchResult()
    {
        return $this->render('front/search_results.html.twig');
    }

    /**
     * @Route("/pricing", name="pricing")
     */
    public function pricing()
    {
        return $this->render('front/pricing.html.twig');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register()
    {
        return $this->render('front/register.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        return $this->render('front/login.html.twig');
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function payment()
    {
        return $this->render('front/payment.html.twig');
    }

    public function mainCategories(){

        $cats = $this->getDoctrine()->getRepository(Category::class)->findBy(['parent'=>null], ['name'=>'ASC']);
        return $this->render('front/mainCategories.html.twig', ['cats'=>$cats]);
    }

}
