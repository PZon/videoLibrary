<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\CatTreeAdminPage;
use App\Utils\CatTreeAdminOptionList;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;


    /**
     * @Route("/admin")
     */

class AdminController extends AbstractController
{
    /**
     * @Route("/", name="adminPage")
     */
    public function index()
    {
        return $this->render('admin/my_profile.html.twig');
    }

    /**
     * @Route("/categories", name="categories")
     */
    public function categories(CatTreeAdminPage $cats, Request $request)
    {
        $cats->getCategoryList($cats->buidTree());
        dump($cats);
        $cat = new Category();
        $form=$this->createForm(CategoryType::class, $cat);
        $isInvalid = null;
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $cat->setName($request->request->get('category')['name']);

            $repository=$this->getDoctrine()->getRepository(Category::class);
            $parent=$repository->find($request->request->get('category')['parent']);
            $cat->setParent($parent);

            $em = $this->getDoctrine()->getManager();
            $em->persist($cat);
            $em->flush();
            return $this->redirectToRoute('categories');

        }elseif ($request->isMethod('post')) {
           $isInvalid = ' is-invalid';
        }

        return $this->render('admin/categories.html.twig', ['cats'=>$cats->catList,
                                                            'form'=>$form->createView(),
                                                            'isInvalid'=>$isInvalid]);
    }

    /**
     * @Route("/editCategory/{id}", name="editCategory")
     */
    public function editCategory(Category $cat)
    {
        return $this->render('admin/edit_category.html.twig',['cat'=>$cat]);
    }

    /**
     * @Route("/deleteCategory/{id}", name="deleteCategory")
     */
    public function deleteCategory(Category $cat)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($cat);
        $em->flush();
        return $this->redirectToRoute('categories');
    }

    /**
     * @Route("/videos", name="videos")
     */
    public function videos()
    {
        return $this->render('admin/videos.html.twig');
    }

    /**
     * @Route("/uploadVideo", name="uploadVideo")
     */
    public function uploadVideo()
    {
        return $this->render('admin/upload_video.html.twig');
    }

    /**
     * @Route("/users", name="users")
     */
    public function users()
    {
        return $this->render('admin/users.html.twig');
    }

    public function getAllCategories(CatTreeAdminOptionList $cats, $editedCat = null){
        $cats->getCategoryList($cats->buidTree());
        return $this->render('admin/allCategories.html.twig', ['cats'=>$cats, 'editedCat'=>$editedCat]);
    }
}
