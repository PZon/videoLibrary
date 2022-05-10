<?php

namespace App\Controller;


use App\Entity\Category;
use App\Entity\Videos;
use App\Utils\CatTreeFrontPage;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\VideosRepository;
use App\Entity\Comment;
use App\Controller\Traits\Likes;
use App\Controller\SecurityController;
use App\Utils\VideoForNoValidSubscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class FrontController extends AbstractController
{
	use Likes;
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
    //public function videoList($id, $page ,CatTreeFrontPage $cats, Request $request)
    public function videoList($id, $page ,CatTreeFrontPage $cats, Request $request, VideoForNoValidSubscription $videoNoMembers)
    {
        $cats->getCategoryListAndParent($id);
        // dump($cats); //composer require symfony/var-dumper --dev
        $ids = $cats->getChildIds($id);
        array_push($ids, $id);

        $videos = $this->getDoctrine()->getRepository(Videos::class)->findByChildIds($ids, $page, $request->get('sortby'));
        return $this->render('front/videolist.html.twig', ['subCats'=>$cats,
		'videos'=>$videos,
		'videoNoMembers'=>$videoNoMembers->check()
		]);
    }

    /**
     * @Route("/videoDetails/{video}", name="videoDetails")
     */
    //public function videoDetails($video, VideosRepository $repo)
    public function videoDetails($video, VideosRepository $repo, VideoForNoValidSubscription $videoNoMembers)
    {
            //dump($repo->videoDetails($video));

        return $this->render('front/video_details.html.twig',[
            'video'=>$repo->videoDetails($video),
			'videoNoMembers'=>$videoNoMembers->check()
        ]);
    }

    /**
     * @Route("/searchResult/{page}",methods={"GET"}, defaults={"page":"1"}, name="searchResult")
     */
    public function searchResult($page, Request $request, VideoForNoValidSubscription $videoNoMembers)
    {
        $query=null;
        $videos=null;

        if($query = $request->get('query'))
        {
        $videos = $this->getDoctrine()->getRepository(Videos::class)->findByTitle($query, $page, $request->get('sortby'));
        if(!$videos->getItems()) $videos=null;
        }
        return $this->render('front/search_results.html.twig', ['videos'=>$videos,
																'query'=>$query,
																'videoNoMembers'=>$videoNoMembers->check()
																]);
    }

    public function mainCategories(){

        $cats = $this->getDoctrine()->getRepository(Category::class)->findBy(['parent'=>null], ['name'=>'ASC']);
        return $this->render('front/mainCategories.html.twig', ['cats'=>$cats]);
    }


    /**
     * @Route("/newComment/{video}", methods={"POST"}, name="newComment")
     */
    public function newComment(Videos $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if(!empty(trim($request->request->get('comment')))){
           $comment = new Comment(); 
           $comment -> setContent($request->request->get('comment'));
           $comment-> setUser($this->getUser());
           $comment-> setVideo($video);
           $comment->  setCreatedAt();

           $em = $this->getDoctrine()->getManager();
           $em->persist($comment);
           $em->flush();
        }

        return $this->redirectToRoute('videoDetails',['video'=>$video->getId()]);
    }

      /**
    * @Route("/delete-comment/{comment}", name="delete_comment")
    * @Security("user.getId() == comment.getUser().getId()")
    */
    public function deleteComment(Comment $comment, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/video-list/{video}/like", name="like_video", methods={"POST"})
     * @Route("/video-list/{video}/dislike", name="dislike_video", methods={"POST"})
     * @Route("/video-list/{video}/unlike", name="undo_like_video", methods={"POST"})
     * @Route("/video-list/{video}/undodislike", name="undo_dislike_video", methods={"POST"})
     */
    public function toggleLikesAjax(Videos $video, Request $request){
        $this->$this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        switch ($request->get(_route)) {
            case 'like_video':
                $result = $this->likeVideo($video);
                break;
            case 'dislike_video':
                 $result = $this->dislikeVideo($video);
                break;
            case 'undo_like_video':
                 $result = $this->undolikeVideo($video);
                break;
            case 'undo_dislike_video':
                 $result = $this->undoDislikeVideo($video);
                break;
        }
    return $this->json(['action'=>$result, 'id'=>$video->getId()]);
    }

}
