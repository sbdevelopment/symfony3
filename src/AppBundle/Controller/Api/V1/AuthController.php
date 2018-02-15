<?php

namespace AppBundle\Controller\Api\V1;

use AppBundle\Entity\User;

use AppBundle\Service\JwtManager;
use AppBundle\Service\UserManager;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Auth controller
 * @Route("api/v1")
 */
class AuthController extends Controller
{
    /**
     * @Route("/auth", name="Auth")
     * @Method("POST")
     */
    public function indexAction(Request $request, JwtManager $jwt)
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if (empty($username)) {
            $this->returnJsonAndExit('Имя пользователя не передано','error',400);
        }

        if (empty($password)) {
            $this->returnJsonAndExit('Пароль не передан','error',400);
        }

        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'username' => $username,
            'password' => UserManager::passwordHash($password)
        ]);

        if (!($user instanceof User)) {
            $this->returnJsonAndExit('Ошибка авторизации','error',400);
        }

        $token = $jwt->createAccessToken($user);

        if (!$token) {
            $this->returnJsonAndExit('Ошибка создания токена','error',400);
        }

        $this->returnJsonAndExit([
            'accessToken' => $token
        ],'success');
    }
}