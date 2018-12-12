<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostsController
 * @package App\Controller\Admin
 *
 * @Route("admin/posts")
 */
class PostsController extends AbstractController
{
    /**
     * @Route("/", name="admin_posts")
     */
    public function index()
    {
        $posts = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findAll();

        return $this->render('admin/posts/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/create", name="admin_post_create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $form = $this->createForm(PostType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setCreatedAt(new DateTime);
            $data->setUpdatedAt(new DateTime);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();

            $this->addFlash('success', 'Post criado com sucesso');

            return $this->redirectToRoute('admin_posts');
        }

        return $this->render('admin/posts/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="admin_post_update")
     * @param Request $request
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function update(Request $request, Post $post)
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setUpdatedAt(new DateTime);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->merge($data);
            $entityManager->flush();

            $this->addFlash('success', 'Post atualizado com sucesso');
            return $this->redirectToRoute('admin_posts');
        }

        return $this->render('admin/posts/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_post_delete")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Post $post)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Post Removido com sucesso.');
        return $this->redirectToRoute('admin_posts');
    }
}
