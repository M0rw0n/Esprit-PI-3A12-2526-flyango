<?php

namespace App\Controller;

<<<<<<< HEAD
use App\Entity\ForumComment;
use App\Entity\ForumPost;
use App\Entity\LikeDislike;
use App\Repository\FavoritePostRepository;
use App\Repository\ForumCommentRepository;
use App\Repository\ForumPostRepository;
use App\Repository\LikeDislikeRepository;
=======
use App\Entity\ForumPost;
use App\Entity\ForumComment;
use App\Entity\ForumReaction;
use App\Repository\ForumPostRepository;
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class ForumController extends AbstractController
{
<<<<<<< HEAD
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
=======
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
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
        $em->flush();

        return new JsonResponse([
            'success' => true,
<<<<<<< HEAD
            'pinned' => $comment->isIsPinned(),
            'message' => $comment->isIsPinned() ? 'Commentaire épinglé' : 'Commentaire désépinglé',
        ]);
    }

    #[Route('/{id}/comments/page/{page}', name: 'forum_post_comments', methods: ['GET'])]
    public function comments(int $id, int $page, ForumPostRepository $repo, EntityManagerInterface $em): Response
=======
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
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
    {
        $post = $repo->find($id);
        if (!$post) throw $this->createNotFoundException();

<<<<<<< HEAD
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
=======
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
>>>>>>> 3e12171c67102e38de2cde7e791a0d50ede41739
    }
}
