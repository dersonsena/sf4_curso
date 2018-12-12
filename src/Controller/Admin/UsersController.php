<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UsersController
 * @package App\Controller\Admin
 *
 * @Route("admin/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/", name="admin_users")
     */
    public function index()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('admin/users/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/create", name="admin_user_create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setCreatedAt(new DateTime);
            $data->setUpdatedAt(new DateTime);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();

            $this->addFlash('success', 'User criado com sucesso');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="admin_user_update")
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function update(Request $request, User $user)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setUpdatedAt(new DateTime);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->merge($data);
            $entityManager->flush();

            $this->addFlash('success', 'Usuário atualizado com sucesso');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_user_delete")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Usuário removido com sucesso.');
        return $this->redirectToRoute('admin_users');
    }
}
