<?php

namespace App\Controller;

header('Access-Control-Allow-Origin: *');

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
        $response = new Response(
            'Content',
            Response::HTTP_OK,
            ['content-type' => 'application/json', 
            'Access-Control-Allow-Origin' => '*']
        );

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        $response->setContent(json_encode([
            'users' => $users,
        ]));

        return $this->json($users, 200);
    }

    /**
     * @Route("/{position}", name="position")
     */
    public function getPseudoByPosition($position)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                'position' => $position,
            ]);

        return $this->json($user, 200);
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
            $user->setPosition(137);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{pseudo}/edit", name="edit")
     */
    public function edit(Request $request, $pseudo): Response
    {
        $data = $request->getContent();

        preg_match('#[0-9]+#i', $data, $match);

        $position = intval($match[0]);

        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(
                ['pseudo' => $pseudo],
            );

        if ($user) {
            $user->setPosition($position);
      
            $entityManager->persist($user);
            $entityManager->flush();
        }

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

