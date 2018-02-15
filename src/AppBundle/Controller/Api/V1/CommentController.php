<?php

namespace AppBundle\Controller\Api\V1;

use AppBundle\Entity\Comment;
use AppBundle\Service\UserManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;

/**
 * Comment controller.
 *
 * @Route("api/v1")
 */
class CommentController extends Controller
{

    /**
     * Lists all comment entities.
     *
     * @Route("/comments", name="comment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Comment[] $comments */
        $comments = $em->getRepository('AppBundle:Comment')->findAll();

        $data = [];

        foreach ($comments as $comment) {
            $data[] = [
                'id' => $comment->getId(),
                'authorId' => $comment->getAuthorId(),
                'body' => $comment->getBody(),
                'createdAt' => $comment->getCreatedAt()->getTimestamp()
            ];
        }

        $this->returnJsonAndExit($data,'success');
    }

    /**
     * Creates a new comment entity.
     *
     * @Route("/comments", name="comment_new")
     * @Method("POST")
     */
    public function newAction(Request $request, UserManager $userManager)
    {
        $authorized = $userManager->checkAuthFromRequest($request);

        if($authorized === null) {
            $this->returnJsonAndExit('Вы должны авторизоваться','error',401);
        }

        $data = [
            'authorId' => $authorized->getUserIdFromPayload(),
            'body' => $request->request->get('body'),
            'createdAt' => \DateTime::createFromFormat(DATE_ISO8601, date(DATE_ISO8601))
        ];

        if($data['body'] === null) {
            $this->returnJsonAndExit('Тело сообщения не может быть пустым','error',401);
        }

        $comment = new Comment();
        $comment->setBody($data['body']);
        $comment->setAuthorId($data['authorId']);
        $comment->setCreatedAt($data['createdAt']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();

        $data['id'] = $comment->getId();

        $this->returnJsonAndExit($data,'success');
    }

    /**
     * Deletes a comment entity.
     *
     * @Route("/comments/{id}", name="comment_delete")
     * @Method("DELETE")
     */
    public function deleteAction($id, Request $request, UserManager $userManager)
    {
        $authorized = $userManager->checkAuthFromRequest($request);

        if($authorized === null) {
            $this->returnJsonAndExit('Вы должны авторизоваться','error',401);
        }

        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);

        if(!($comment instanceof Comment)) {
            $this->returnJsonAndExit('Удаляемый комментарий не найден','error', 404);
        }

        if($comment->getAuthorId() !== $authorized->getUserIdFromPayload()) {
            $this->returnJsonAndExit('Вы не можете удалить чужой комментарий','error', 403);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();
        $this->returnJsonAndExit('','success');
    }
}
