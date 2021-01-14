<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

/**
 * @Route("/users", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->json($users);
    }

    /**
     * @Route("/add/{pseudo}", name="add")
     */
    public function add($pseudo): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        if (!empty($pseudo)) {
            $user = new User();
            $user->setPseudo($pseudo);
            $user->setPosition(rand(0,168));
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/edit/{playersInfo}", name="edit")
     */
    public function edit($playersInfo): Response
    {
        $arrayPlayersInfo = explode('|', $playersInfo);

        $entityManager = $this->getDoctrine()->getManager();

        foreach ($arrayPlayersInfo as $value) {
            $arrayValue = explode(',', $value);
            $idPlayer = $arrayValue[0];
            $positionPlayer = $arrayValue[1];

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(
                    ['id' => $idPlayer],
                );

            if ($user) {
                $user->setPosition($arrayValue[1]);

                $entityManager->persist($user);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(
                ['id' => $id],
            );

        if ($user) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}

