<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use AppBundle\Entity\Comment;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class Comments implements OrderedFixtureInterface, FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $em
     */
    public function load(ObjectManager $em)
    {
        $comments = [
            [
                'authorId' => $this->getUserIdByUsername('Bulbash', $em),
                'body' => 'Пламя угасло, и он глядел в окно на звезды.',
            ],
            [
                'authorId' => $this->getUserIdByUsername('Kazakh', $em),
                'body' => 'Едва осознав, что происходит, мы оторвались от земли.',
            ],
            [
                'authorId' => $this->getUserIdByUsername('Bulbash' , $em),
                'body' => 'Туман окутал корабль через три часа после выхода из порта',
            ],

        ];

        foreach ($comments as $data) {

            $comment = new Comment();
            $comment->setBody($data['body']);
            $comment->setAuthorId($data['authorId']);
            $comment->setCreatedAt(\DateTime::createFromFormat(DATE_ISO8601,date(DATE_ISO8601)));

            $em->persist($comment);
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
        return 2;
    }

    private function getUserIdByUsername(string $username, ObjectManager $em) {
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        return $user->getId();
    }
}