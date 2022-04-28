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
class CategoriesController extends AbstractController{
	
	/**
     * @Route("/su/users", name="users")
     */
	public function users()
    {
        return $this->render('admin/users.html.twig');
    }
		
	private function saveCategory($cat, $form, $request){
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $cat->setName($request->request->get('category')['name']);

            $repository=$this->getDoctrine()->getRepository(Category::class);
            $parent=$repository->find($request->request->get('category')['parent']);
            $cat->setParent($parent);

            $em = $this->getDoctrine()->getManager();
            $em->persist($cat);
            $em->flush();
            return true;

        }
        return false;
    }
	
	/**
     * @Route("/su/deleteCategory/{id}", name="deleteCategory")
     */
    public function deleteCategory(Category $cat)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($cat);
        $em->flush();
        return $this->redirectToRoute('categories');
    }
	
	/**
     * @Route("/su/editCategory/{id}", name="editCategory", methods={"GET", "POST"})
     */
    public function editCategory(Category $cat, Request $request)
    {
        $form=$this->createForm(CategoryType::class, $cat);
        $isInvalid = null;

        if($this->saveCategory($cat, $form, $request)){
            return $this->redirectToRoute('categories');
        }elseif ($request->isMethod('post')) {
           $isInvalid = ' is-invalid';
        }

        return $this->render('admin/edit_category.html.twig',['cat'=>$cat,
                                                            'form'=>$form->createView(),
                                                            'isInvalid'=>$isInvalid]);
    }
	
	  /**
     * @Route("/su/categories", name="categories", methods={"GET", "POST"})
     */
    public function categories(CatTreeAdminPage $cats, Request $request)
    {
        $cats->getCategoryList($cats->buidTree());
       // dump($cats);
        $cat = new Category();
        $form=$this->createForm(CategoryType::class, $cat);
        $isInvalid = null;

        if($this->saveCategory($cat, $form, $request)){
            return $this->redirectToRoute('categories');
        }elseif ($request->isMethod('post')) {
           $isInvalid = ' is-invalid';
        }

        return $this->render('admin/categories.html.twig', ['cats'=>$cats->catList,
                                                            'form'=>$form->createView(),
                                                            'isInvalid'=>$isInvalid]);
    }

    public function getAllCategories(CatTreeAdminOptionList $cats, $editedCat = null){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $cats->getCategoryList($cats->buidTree());
        return $this->render('admin/allCategories.html.twig', ['cats'=>$cats, 'editedCat'=>$editedCat]);
    }    
	
}