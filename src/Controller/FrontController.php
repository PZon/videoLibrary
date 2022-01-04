<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/videoList", name="videoList")
     */
    public function videoList()
    {
        return $this->render('front/videolist.html.twig');
    }

         /**
     * @Route("/videoDetails", name="videoDetails")
     */
    public function videoDetails()
    {
        return $this->render('front/video_details.html.twig');
    }
}
