<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;

use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JwtManager
{

    private $userId;

    private $jwtKey;

    /**
     * JwtManager constructor.
     * @param string $jwtKey
     */
    public function __construct(string $jwtKey)
    {
        $this->jwtKey = $jwtKey;
    }

    /**
     * @param User $user
     * @return bool|string
     */
    public function createAccessToken(User $user)
    {
        $this->userId = $user->getId();

        if(empty($this->jwtKey)) {
            return false;
        }

        $jwt = \Firebase\JWT\JWT::encode([
            'user_id' => $this->userId,
            'user_name' => $user->getUsername(),
            'exp' => strtotime('+90 minutes'),
        ],$this->jwtKey);

        return $jwt;
    }

    public function getUserIdFromPayload() : int
    {
        return $this->userId;
    }

    public function getExpiringTimestampForToken(string $jwt) : ?int
    {
        try {
            $payload = \Firebase\JWT\JWT::decode($jwt,$this->jwtKey,['HS256']);
            $this->userId = $payload->user_id;
        } catch (BeforeValidException $beforeValidException) {
            return false;
        } catch (ExpiredException $expiredException) {
            return false;
        } catch (SignatureInvalidException $signatureInvalidException) {
            return false;
        } catch (\UnexpectedValueException $unexpectedValueException) {
            return false;
        }

        return (int) $payload->exp;
    }
}
