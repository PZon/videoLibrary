<?php

namespace App\Controller\Admin\SuperAdmin;

use App\Utils\CatTreeAdminPage;
use App\Utils\CatTreeAdminOptionList;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Videos;
use App\Form\VideoType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Interfaces\UploaderInterface;


    /**
     * @Route("/admin/su")
     */

class SuperAdminController extends AbstractController{

	/**
     * @Route("/su/uploadVideo_Old", name="uploadVideo_Old_PZ")
     */
    public function uploadVideo()
    {
        return $this->render('admin/upload_video.html.twig');
    }

        /**
     * @Route("/uploadVideoLocally", name="uploadVideoLocally")
     */
    public function uploadVideoLocally(Request $request, UploaderInterface $fileUploader)
    {
        $video = new Videos();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $em =$this->getDoctrine()->getManager();
            $file = $video->getUploadedVideo();
            $fileName = $fileUploader->upload($file);

            $base_path = Videos::uploadFolder;
            $video->setPath($base_path.$fileName[0]);
            $video->setTitle($fileName[1]);

            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('videos');
        }

        return $this->render('admin/uploadVideoLocally.html.twig',[
            'form'=>$form->createView()
        ]);
    }
	
	/**
     * @Route("/su/users", name="users")
     */
	public function users()
    {
        
        $rep =$this->getDoctrine()->getRepository(User::class);
        $users = $rep->findBy([],['name'=>'ASC']);
        return $this->render('admin/users.html.twig', ['users'=>$users]);
    }

    /**
     * @Route("/deleteUser/{user}", name="deleteUser")
     */
    public function deleteUser(User $user)
    {
       $manager = $this->getDoctrine()->getManager();
       $manager->remove($user);
       $manager->flush();
        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/deleteVideo/{video}/{path}", name="deleteVideo", requirements={"path"=".+"})
     */
    public function deleteVideo(Videos $video, $path, UploaderInterface $fileUploader)
    {
       $manager = $this->getDoctrine()->getManager();
       $manager->remove($video);
       $manager->flush();

       if($fileUploader->delete($path)){
        $this->addFlash(
            'success',
            'Video has been deleted'
        );
       }else{
        addFlash(
         'danger',
         'ERROR: Video is not deleted ?'
        );
       }

        return $this->redirectToRoute('videos');
    }
}