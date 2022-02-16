<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use App\Entity\Videos;
use App\Utils\CatTreeFrontPage;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/videoList/cat/{catName},{id}/{page}", defaults={"page":"1"}, name="videoList")
     */
    public function videoList($id, $page ,CatTreeFrontPage $cats, Request $request)
    {
        $cats->getCategoryListAndParent($id);
        // dump($cats); //composer require symfony/var-dumper --dev
        $ids = $cats->getChildIds($id);
        array_push($ids, $id);

        $videos = $this->getDoctrine()->getRepository(Videos::class)->findByChildIds($ids, $page, $request->get('sortby'));
        return $this->render('front/videolist.html.twig', ['subCats'=>$cats, 'videos'=>$videos]);
    }

    /**
     * @Route("/videoDetails", name="videoDetails")
     */
    public function videoDetails()
    {
        return $this->render('front/video_details.html.twig');
    }

    /**
     * @Route("/searchResult/{page}",methods={"GET"}, defaults={"page":"1"}, name="searchResult")
     */
    public function searchResult($page, Request $request)
    {
        $query=null;
        $videos=null;

        if($query = $request->get('query'))
        {
        $videos = $this->getDoctrine()->getRepository(Videos::class)->findByTitle($query, $page, $request->get('sortby'));
        if(!$videos->getItems()) $videos=null;
        }
        return $this->render('front/search_results.html.twig', ['videos'=>$videos, 'query'=>$query]);
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
