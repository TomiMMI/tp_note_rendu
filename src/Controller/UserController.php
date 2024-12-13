<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'api_user_list', methods: ['GET'])]
    public function listUsers(UserRepository $userRepository): Response
    {
        if (!$authChecker->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Vous devez être admin'], JsonResponse::HTTP_FORBIDDEN);
        }
        $users = $userRepository->findAll();

        return $this->render('user/admin_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/api/users', name: 'api_user_create', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em): Response
    {
        if (!$authChecker->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Vous devez être admin'], JsonResponse::HTTP_FORBIDDEN);
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User successfully created.');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('user/admin_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/users/{id}', name: 'api_admin_user_update', methods: ['PUT'])]
    public function editUser(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if (!$authChecker->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Vous devez être admin'], JsonResponse::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'User successfully updated.');

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('user/admin_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/api/users/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        if (!$authChecker->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Vous devez être admin'], JsonResponse::HTTP_FORBIDDEN);
        }
        
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'User successfully deleted.');

        return $this->redirectToRoute('admin_user_list');
    }

    #[Route('/api/users/profile', name: 'api_user_update', methods: ['PUT'])]
    public function userProfile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profile successfully updated.');

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}