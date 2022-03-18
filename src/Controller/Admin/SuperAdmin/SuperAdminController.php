<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Utils\CatTreeAdminPage;
use App\Utils\CatTreeAdminOptionList;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Videos;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/admin/su")
     */

class SuperAdminController extends AbstractController{

	/**
     * @Route("/su/uploadVideo", name="uploadVideo")
     */
    public function uploadVideo()
    {
        return $this->render('admin/upload_video.html.twig');
    }
	
	/**
     * @Route("/su/users", name="users")
     */
	public function users()
    {
        return $this->render('admin/users.html.twig');
    }
}