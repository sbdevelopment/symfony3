<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class Users implements OrderedFixtureInterface, FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $users = [
            [
                'username' => 'bulbash',
                'email' => 'test1@test.ru',
                'password' => 'test123q',
                'location' => 'Belarus, Babruysk'
            ],
            [
                'username' => 'kazakh',
                'email' => 'test2@test.ru',
                'password' => 'test123a',
                'location' => 'Kazakhstan, Karaganda'
            ],
        ];

        foreach ($users as $data) {

            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordHash($data['password']));
            $user->setLocation($data['location']);

            $em->persist($user);
            $em->flush();
        }
    }
    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }

    private function passwordHash(string $password) {
        return md5(base64_encode(md5(base64_encode(md5($password.'0Wqlf$elr4sorE#k')))));
    }
}