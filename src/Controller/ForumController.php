<?php

namespace App\Controller;

use App\Entity\ForumPost;
use App\Entity\ForumComment;
use App\Entity\ForumReaction;
use App\Repository\ForumPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class ForumController extends AbstractController
{
    /* ── LIST ── */
    #[Route('', name: 'forum_index', methods: ['GET'])]
    public function index(ForumPostRepository $repo): Response
    {
        $posts = $repo->findAllWithCommentsAndReactions();
        return $this->render('forum/index.html.twig', ['posts' => $posts]);
    }

    /* ── AJAX SEARCH ── */
    #[Route('/search', name: 'forum_search', methods: ['GET'])]
    public function search(Request $request, ForumPostRepository $repo): JsonResponse
    {
        $q = trim($request->query->get('q', ''));

        $posts = $q ? $repo->searchPosts($q) : $repo->findAllWithCommentsAndReactions();

        $data = array_map(fn(ForumPost $p) => [
            'id'            => $p->getId(),
            'title'         => $p->getTitle(),
            'content'       => $p->getContent(),
            'author'        => $p->getAuthor(),
            'status'        => $p->getStatus(),
            'createdAt'     => $p->getCreatedAt()->format('d/m/Y'),
            'commentsCount' => count($p->getComments()),
        ], $posts);

        return new JsonResponse(['success' => true, 'posts' => $data]);
    }

    /* ── SHOW ── */
    #[Route('/{id}', name: 'forum_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, ForumPostRepository $repo): Response
    {
        $post = $repo->find($id);
        if (!$post) throw $this->createNotFoundException('Discussion introuvable.');
        return $this->render('forum/show.html.twig', ['post' => $post]);
    }

    /* ── ADD POST (AJAX) ── */
    #[Route('/add', name: 'forum_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $author  = trim($data['author']  ?? $request->request->get('author', 'Anonyme'));
        $title   = trim($data['title']   ?? $request->request->get('title', ''));
        $content = trim($data['content'] ?? $request->request->get('content', ''));

        // Validation
        $errors = [];
        if (strlen($title) < 3)   $errors[] = 'Le titre doit contenir au moins 3 caractères.';
        if (strlen($content) < 10) $errors[] = 'Le contenu doit contenir au moins 10 caractères.';

        if ($errors) {
            return new JsonResponse(['success' => false, 'errors' => $errors, 'message' => implode(' ', $errors)], 422);
        }

        $post = new ForumPost();
        $post->setTitle($title);
        $post->setContent($content);
        $post->setAuthor($author ?: 'Voyageur');
        $post->setStatus('PENDING');
        $post->setCreatedAt(new \DateTime());

        $em->persist($post);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'post' => [
                'id'        => $post->getId(),
                'title'     => $post->getTitle(),
                'content'   => $post->getContent(),
                'author'    => $post->getAuthor(),
                'status'    => $post->getStatus(),
                'createdAt' => $post->getCreatedAt()->format('d/m/Y'),
            ],
        ], 201);
    }

    /* ── DELETE POST (AJAX) ── */
    #[Route('/delete/{id}', name: 'forum_delete', methods: ['DELETE', 'POST'])]
    public function delete(int $id, ForumPostRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $post = $repo->find($id);
        if (!$post) return new JsonResponse(['success' => false, 'message' => 'Post introuvable.'], 404);

        $em->remove($post);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    /* ── ADD COMMENT ── */
    #[Route('/{id}/comment', name: 'forum_add_comment', methods: ['POST'])]
    public function addComment(int $id, Request $request, ForumPostRepository $repo, EntityManagerInterface $em): Response
    {
        $post = $repo->find($id);
        if (!$post) throw $this->createNotFoundException();

        $comment = new ForumComment();
        $comment->setPostId($id);
        $comment->setAuthor($request->request->get('author', 'Anonyme'));
        $comment->setContent($request->request->get('content'));
        $comment->setCreatedAt(new \DateTime());

        $em->persist($comment);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'comment' => [
                    'id'        => $comment->getId(),
                    'author'    => $comment->getAuthor(),
                    'content'   => $comment->getContent(),
                    'createdAt' => $comment->getCreatedAt()->format('d/m/Y'),
                ],
            ]);
        }

        $this->addFlash('success', 'Commentaire ajouté !');
        return $this->redirectToRoute('forum_show', ['id' => $id]);
    }

    /* ── REACT TO POST (AJAX) ── */
    #[Route('/{id}/react', name: 'forum_react', methods: ['POST'])]
    public function react(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $type = $data['type'] ?? 'LIKE';

        $reaction = new ForumReaction();
        $reaction->setPostId($id);
        $reaction->setAuthor('Voyageur');
        $reaction->setType(in_array($type, ['LIKE','DISLIKE','LOVE','WOW']) ? $type : 'LIKE');

        $em->persist($reaction);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
