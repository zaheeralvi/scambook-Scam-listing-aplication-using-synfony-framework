<?php

namespace App\Controller\WsbBundle;

use App\Entity\Comment;
use App\Entity\ScamDetails;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    public function listAction(Request $request)
    {
        echo 'ds';
        die;
    }

    public function localeAction(Request $request)
    {
        $locale = $locale = $request->getLocale();
        $request->getSession()->set('_locale', $locale);
//        $request->getSession()->set('_locale', $locale);

        $referer = $request->headers->get('referer');
        if (empty($referer)) {
            $router = $this->container->get('router');
            $referer = new RedirectResponse($router->generate('base_homepage'));
        }

        return $this->redirect($referer);
    }

    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->getMethod()=='POST')
        {

            $postId = $request->get('post_id');
            if($postId) {
                $scam = $em->getRepository('App:ScamDetails')->find($postId);
                $user = $this->getUser();

                $comment = new Comment();
                $comment->setCommentDetail($request->get('comment'));
                $comment->setPost($scam);
                $comment->setUser($user);
                $comment->setDate(new \DateTime('now'));
                $em->persist($comment);
                $em->flush();
                $this->redirectToRoute('/');
            }

        }


        $comments = $em->getRepository('App:Comment')->findAll();
        $commentsByPostId = [];
        foreach($comments as $comment){
            $commentsByPostId[$comment->getPost()->getId()][] = $comment;
        }

        $scamPost = $em->getRepository('App:ScamDetails')->findAll();

        $scamsByCompany = [];

        foreach ($scamPost as $val){
            $scamsByCompany[$val->getCompany()->getName()][] = $val;
        }
//        var_dump($scamsByCompany);die;

        $data = [];
        $data['scamsByCompany'] = $scamsByCompany;
        $data['commentsByPostId'] = $commentsByPostId;
        return $this->render('App:Default:index.html.twig',$data);
    }

    public function scamPostAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST'){
//            $targetPath = '';
//            $fname = '';
//            if(isset($_FILES['proof']['tmp_name']) and $_FILES['proof']['tmp_name']){
//
//                $filecontent = file_get_contents($_FILES['proof']['tmp_name']);
//
//                $path = $this->get('kernel')->getRootDir() . '/../public';
//                $fname = time().'.'.pathinfo($_FILES['proof']['name'])['extension'];
//                $targetPath = $path.'/uploads/'.$fname;
//
//                file_put_contents($targetPath,$filecontent);
//            }

            $company = null;
            $status = 'new';
            if($request->get('company')) {
                $company = $em->getRepository('App:Company')->find($request->get('company'));
            }
            $post = new ScamDetails();
            $post->setDamagePrice($request->get('Damages'));
            $post->setDateOccurance(new \DateTime('now'));
//            $post->setProofFile($fname);
            $post->setStatus($status);
            $post->setCompany($company);
            if($this->getUser()){
                $post->setUser($this->getUser());
            }
            $post->setDescription($request->get('detail'));
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('base_homepage');

        }

        $companies = $em->getRepository('App:Company')->findAll();
        $data = [];
        $data['companies']=$companies;
        return $this->render('App:Default:scamPost.html.twig',$data);
    }

    public function checkAccessAction()
    {

        $router = $this->container->get('router');

        if (
            $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')
            ||
            $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
        ) {

            if (
                $this->get('security.authorization_checker')->isGranted('ROLE_USER')
                ||
                $this->get('security.authorization_checker')->isGranted('ROLE_BUSINESS_ADMIN')
            ) {

                return new RedirectResponse($router->generate('sonata_admin_redirect'));
            }
        }

        return new RedirectResponse($router->generate('base_homepage'), 307);

    }

    public function userProfileAction($id, Request $request)
    {

        $admin_pool = $this->get('sonata.admin.pool');

        $user = $this->getDoctrine()->getRepository('App:User')->findOneBy(['id' => $id]);

        return $this->render('App:Default:user_profile.html.twig', [
            'admin_pool' => $admin_pool,
            'user' => $user
        ]);
    }

    public function mypageAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        if($request->getMethod()=='POST'){
            $postId = $request->get('post_id');
//            var_dump($postId);die;
            if($postId) {
                $scam = $em->getRepository('App:ScamDetails')->find($postId);
                $user = $this->getUser();

                $comment = new Comment();
                $comment->setCommentDetail($request->get('comment'));
                $comment->setPost($scam);
                $comment->setUser($user);
                $comment->setDate(new \DateTime('now'));
                $em->persist($comment);
                $em->flush();

            }


        }


        $scams = $em->getRepository('App:ScamDetails')->findBy(array('user'=>$this->getUser()));
        $comments = $em->getRepository('App:Comment')->findBy(array('post'=>$scams));
        $commentsByPostId = [];
        foreach($comments as $comment){
            $commentsByPostId[$comment->getPost()->getId()][] = $comment;
        }

        $data = [];
        $data['scamsByCompany'] = $scams;
        $data['commentsByPostId'] = $commentsByPostId;

        $investigation = $request->get('investigation');
        if(isset($investigation)){
            
            $scam = $em->getRepository('App:ScamDetails')->find($investigation);
//            $scam = $em->getRepository('App:ScamDetails')->findBy(array('id'=>$investigation));
            $scam->setInvestigation(1);
            $scam->setStatus('investigation requested');
            $em->persist($scam);
            $em->flush();
        }


//        return $this->render('App:Default:detailpage.html.twig',$data);
        return $this->render('App:Default:mypage.html.twig',$data);
    }
    
    public function scam_detailsAction(Request $request){
        $em = $this->getDoctrine()->getManager();



        if($request->getMethod()=='POST'){
            $postId = $request->get('post_id');
//            var_dump($postId);die;
            if($postId) {
                $scam = $em->getRepository('App:ScamDetails')->find($postId);
                $user = $this->getUser();

                $comment = new Comment();
                $comment->setCommentDetail($request->get('comment'));
                $comment->setPost($scam);
                $comment->setUser($user);
                $comment->setDate(new \DateTime('now'));
                $em->persist($comment);
                $em->flush();

            }


        }
        $comments = $em->getRepository('App:Comment')->findAll();
        $commentsByPostId = [];
        foreach($comments as $comment){
            $commentsByPostId[$comment->getPost()->getId()][] = $comment;
        }
        if(1/*$request->getMethod()=='GET'*/) {

            $id = $request->get('name');

            if($id) {
                $company = $em->getRepository('App:Company')->findOneBy(array('name'=>$id));
                $scams = $em->getRepository('App:ScamDetails')->findBy(array('company'=>$company));
//                $comments = $em->getRepository('App:Comment')->findBy(array('post'=>$scams));
//                $commentsByPostId = [];
//                foreach($comments as $comment){
//                    $commentsByPostId[$comment->getPost()->getId()][] = $comment;
//                }

                $allScams = $em->getRepository('App:ScamDetails')->findAll();
                $scamsByCompany = [];
                foreach($allScams as $scam){
                    $scamsByCompany[$scam->getCompany()->getName()][] = $scam;
                }
                
                $data = [];
                $data['scamsByCompany'] = $scams;
                $data['commentsByPostId'] = $commentsByPostId;
                $data['scamsByCompanys'] = $scamsByCompany;
                $data['company'] = $company;

                return $this->render('App:Default:detailpage.html.twig',$data);
            }

        }
        return 'id not found';
    }
}

