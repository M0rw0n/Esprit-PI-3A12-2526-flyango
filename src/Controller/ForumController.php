<?php

namespace App\Controller;

use App\Entity\ForumComment;
use App\Entity\ForumPost;
use App\Entity\LikeDislike;
use App\Repository\FavoritePostRepository;
use App\Repository\ForumCommentRepository;
use App\Repository\ForumPostRepository;
use App\Repository\LikeDislikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class ForumController extends AbstractController
{
    #[Route('', name: 'forum_index', methods: ['GET'])]
    public function index(Request $request, ForumPostRepository $repo): Response
    {
        $q = $request->query->get('q');
        $categorie = $request->query->get('categorie');
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10;

        $posts = $repo->searchPaginated($q, $categorie, $page, $limit);
        $total = $repo->countFiltered($q, $categorie);
        $totalPages = (int) ceil($total / $limit);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'html' => $this->renderView('forum/_posts_list.html.twig', [
                    'posts' => $posts,
                    'page' => $page,
                    'totalPages' => $totalPages,
                ]),
                'page' => $page,
                'totalPages' => $totalPages,
            ]);
        }

        return $this->render('forum/index.html.twig', [
            'posts' => $posts,
            'q' => $q,
            'categorie' => $categorie,
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/nouveau', name: 'forum_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $post = new ForumPost();
            $post->setTitle(trim($request->request->get('title', '')))
                 ->setContent(trim($request->request->get('content', '')))
                 ->setAuthor(trim($request->request->get('author', 'Anonyme')))
                 ->setCategorie($request->request->get('categorie') ?: null)
                 ->setStatus('APPROVED');

            if (!$post->getTitle() || !$post->getContent()) {
                $this->addFlash('error', 'Titre et contenu requis.');
                return $this->redirectToRoute('forum_new');
            }

            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'Sujet publié avec succès !');
            return $this->redirectToRoute('forum_show', ['id' => $post->getId()]);
        }

        return $this->render('forum/new.html.twig');
    }

    #[Route('/{id}', name: 'forum_show', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function show(int $id, Request $request, ForumPostRepository $repo, FavoritePostRepository $favRepo, LikeDislikeRepository $likeRepo, EntityManagerInterface $em, ForumCommentRepository $commentRepo): Response
    {
        $post = $repo->find($id);
        if (!$post || $post->getStatus() !== 'APPROVED') throw $this->createNotFoundException();

        $post->setVues($post->getVues() + 1);
        $em->flush();

        $isFavorited = false;
        $likeData = $likeRepo->getCount(LikeDislike::TYPE_POST, $id);
        $likeCount = $likeData['score'] ?? 0;
        $userVote = 0;
        $totalComments = $commentRepo->countRootComments($post);

        if ($this->getUser()) {
            $isFavorited = $favRepo->isFavorited($this->getUser(), $post);
            $userVote = $likeRepo->getUserVote($this->getUser(), LikeDislike::TYPE_POST, $id) ?? 0;
        }

        return $this->render('forum/show.html.twig', [
            'post' => $post,
            'isFavorited' => $isFavorited,
            'likeCount' => $likeCount,
            'userVote' => $userVote,
            'totalComments' => $totalComments,
        ]);
    }

    #[Route('/comment/{id}/pin', name: 'forum_comment_pin', methods: ['POST'])]
    public function pinComment(int $id, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $comment = $em->find(ForumComment::class, $id);
        if (!$comment) {
            return new JsonResponse(['success' => false, 'message' => 'Commentaire non trouvé'], 404);
        }

        $comment->setIsPinned(!$comment->isIsPinned());
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'pinned' => $comment->isIsPinned(),
            'message' => $comment->isIsPinned() ? 'Commentaire épinglé' : 'Commentaire désépinglé',
        ]);
    }

    #[Route('/{id}/comments/page/{page}', name: 'forum_post_comments', methods: ['GET'])]
    public function comments(int $id, int $page, ForumPostRepository $repo, EntityManagerInterface $em): Response
    {
        $post = $repo->find($id);
        if (!$post) throw $this->createNotFoundException();

        $limit = 10;
        $offset = ($page - 1) * $limit;

        $comments = $em->createQueryBuilder()
            ->select('c')
            ->from(ForumComment::class, 'c')
            ->where('c.post = :post')
            ->setParameter('post', $post)
            ->orderBy('c.isPinned', 'DESC')
            ->addOrderBy('c.createdAt', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $total = $em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(ForumComment::class, 'c')
            ->where('c.post = :post')
            ->setParameter('post', $post)
            ->getQuery()
            ->getSingleScalarResult();

        return new JsonResponse([
            'html' => $this->renderView('forum/_comments_list.html.twig', ['comments' => $comments]),
            'page' => $page,
            'totalPages' => (int) ceil($total / $limit),
        ]);
    }
}
