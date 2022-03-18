<?php

namespace App\Controller\Admin;

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
 * @Route("/admin")
 */

class MainController extends AbstractController{
	
	/**
     * @Route("/", name="adminPage")
     */
    public function index()
    {
        return $this->render('admin/my_profile.html.twig');
    }
	
	public function getAllCategories(CatTreeAdminOptionList $cats, $editedCat = null){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $cats->getCategoryList($cats->buidTree());
        return $this->render('admin/allCategories.html.twig', ['cats'=>$cats, 'editedCat'=>$editedCat]);
    }
	
	/**
     * @Route("/videos", name="videos")
     */
    public function videos()
    {
		if($this->isGranted('ROLE_ADMIN')){
			$videos = $this->getDoctrine()->getRepository(Videos::class)->findAll();
		}else{
			$videos = $this->getUser()->getLikedVideos();
		}
		
        return $this->render('admin/videos.html.twig',['videos'=>$videos]);
    }
	
}