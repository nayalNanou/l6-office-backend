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

        return $this->json($users, 200, [
            'Access-Control-Allow-Origin' => 'http://localhost:3000', 
            'Access-Control-Allow-Credentials' => 'true'
        ]);
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
            $user->setPosition(0);
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
        $position = $request->request->get('position');

        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(
                ['pseudo' => $pseudo],
            );

        $user->setPosition($position);

        return $this->json($user);
/*

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_index');
*/
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

