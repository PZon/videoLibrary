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
     * @Route("/upload-video-by-vimeo", name="upload_video_by_vimeo")
    */
    public function uploadVideoByVimeo(Request $request)
    {
        $vimeo_id = preg_replace('/^\/.+\//','',$request->get('video_uri'));
        if($request->get('videoName') && $vimeo_id)
        {
            $em = $this->getDoctrine()->getManager();
            $video = new Video();
            $video->setTitle($request->get('videoName'));
            $video->setPath(Video::VimeoPath.$vimeo_id);

            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('videos');
        }
        return $this->render('admin/uploadVideoVimeo.html.twig');
    }

    /**
     * @Route("/set-video-duration/{video}/{vimeo_id}", name="set_video_duration", requirements={"vimeo_id"=".+"})
    */
    public function setVideoDuration(Videos $video, $vimeo_id)
    {
        if( !is_numeric($vimeo_id) )
        {
            return $this->redirectToRoute('videos');
        }

        $user_vimeo_token = $this->getUser()->getVimeoApiKey();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.vimeo.com/videos/{$vimeo_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/vnd.vimeo.*+json;version=3.4",
                "Authorization: Bearer $user_vimeo_token",
                "Cache-Control: no-cache",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
        {
            throw new ServiceUnavailableHttpException('Error. Try again later. Message: '.$err);
        } 
        else
        {
            $duration =  json_decode($response, true)['duration'] / 60;

            if($duration)
            {
                $video->setDuration($duration);
                $em = $this->getDoctrine()->getManager();
                $em->persist($video);
                $em->flush();
            }
            else
            {
                $this->addFlash(
                    'danger',
                    'Not able to update duration. Check the video.'
                );
            }

            return $this->redirectToRoute('videos');
        }

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

    /**
     * @Route("/updateVideoCat/{video}",methods={"POST"}, name="updateVideoCat")
     */
    public function updateVideoCats(Request $request, Videos $video)
    {
       $manager = $this->getDoctrine()->getManager();
       $category=$this->getDoctrine()->getRepository(Category::class)->find($request->request->get('videoCategory'));
       $video->setCategory($category);
       $manager->persist($video);
       $manager->flush();
        return $this->redirectToRoute('videos');
    }
}