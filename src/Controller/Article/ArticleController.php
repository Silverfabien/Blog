<?php

namespace App\Controller\Article;

use App\ControllerHandler\Article\ArticleControllerHandler;
use App\Entity\Article\Article;
use App\Form\Article\ArticleType;
use App\Repository\Article\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/article', name: 'article_')]
final class ArticleController extends AbstractController
{
    public function __construct(
        private readonly ArticleControllerHandler $articleControllerHandler
    ) {}

    #[Route(name: 'index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, SessionInterface $session): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article)->handleRequest($request);

        if ($this->articleControllerHandler->new($form, $article, $session)) {
            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/edit', name: 'edit')]
    public function edit(Request $request, Article $article, SessionInterface $session): Response
    {
        $form = $this->createForm(ArticleType::class, $article)->handleRequest($request);

        if ($this->articleControllerHandler->edit($form, $article, $session)) {
            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{slug}/remove', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getSlug(), $request->getPayload()->getString('_token'))) {
            $this->articleControllerHandler->delete($article);
        }

        return $this->redirectToRoute('article_index');
    }
}
