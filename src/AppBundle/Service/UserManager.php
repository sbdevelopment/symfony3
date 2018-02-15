<?php


namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class UserManager
{
    /** @var JwtManager $jwt */
    private $jwt;

    public function __construct(JwtManager $jwt)
    {
        $this->jwt = $jwt;
    }

    public function checkAuthFromRequest(Request $request) : JwtManager
    {
        $auth = trim(str_replace('Bearer ','',$request->headers->get('Authorization')));

        return ($this->jwt->getExpiringTimestampForToken($auth) > time()) ? $this->jwt : null;
    }

    public static function passwordHash(string $password) {
        return md5(base64_encode(md5(base64_encode(md5($password.'0Wqlf$elr4sorE#k')))));
    }
}