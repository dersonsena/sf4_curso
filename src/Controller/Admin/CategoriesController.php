<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\CategoryType;
use App\Form\PostType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoriesController
 * @package App\Controller\Admin
 *
 * @Route("admin/categories")
 */
class CategoriesController extends AbstractController
{
    /**
     * @Route("/", name="admin_categories")
     */
    public function index()
    {
        $posts = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('admin/categories/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/create", name="admin_category_create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setCreatedAt(new DateTime);
            $data->setUpdatedAt(new DateTime);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();

            $this->addFlash('success', 'Category criado com sucesso');

            return $this->redirectToRoute('admin_posts');
        }

        return $this->render('admin/categories/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="admin_category_update")
     * @param Request $request
     * @param Category $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function update(Request $request, Category $post)
    {
        $form = $this->createForm(CategoryType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setUpdatedAt(new DateTime);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->merge($data);
            $entityManager->flush();

            $this->addFlash('success', 'Categoria atualizada com sucesso');
            return $this->redirectToRoute('admin_posts');
        }

        return $this->render('admin/categories/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_category_delete")
     * @param Category $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Category $post)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'Categoria removida com sucesso.');
        return $this->redirectToRoute('admin_posts');
    }
}
