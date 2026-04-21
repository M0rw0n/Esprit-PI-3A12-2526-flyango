<?php

namespace App\Controller;

use App\Entity\ForumComment;
use App\Entity\ForumPost;
use App\Entity\LikeDislike;
use App\Repository\FavoritePostRepository;
use App\Repository\ForumCommentRepository;
use App\Repository\ForumPostRepository;
use App\Repository\LikeDislikeRepository;
use App\Service\Api\ModerationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class ForumController extends AbstractController
{
    public function __construct(
        private ModerationService $moderationService,
    ) {}

    #[Route('', name: 'forum_index', methods: ['GET'])]
    public function index(Request $request, ForumPostRepository $repo, PaginatorInterface $paginator, ForumCommentRepository $commentRepo): Response
    {
        $q = $request->query->get('q');
        $categorie = $request->query->get('categorie');
        $tri = $request->query->get('tri', 'recent');

        $qb = $repo->createQueryBuilder('p')->where('p.status = :status')->setParameter('status', 'APPROVED');
        
        if ($q) {
            $qb->andWhere('p.title LIKE :q OR p.content LIKE :q')->setParameter('q', '%' . $q . '%');
        }
        if ($categorie) {
            $qb->andWhere('p.categorie = :cat')->setParameter('cat', $categorie);
        }

        switch ($tri) {
            case 'popular':
                $qb->orderBy('p.vues', 'DESC');
                break;
            case 'discussed':
                $qb->leftJoin('p.comments', 'c')->addSelect('COUNT(c.id) as HIDDEN commentCount')->groupBy('p.id')->orderBy('commentCount', 'DESC');
                break;
            case 'oldest':
                $qb->orderBy('p.id', 'ASC');
                break;
            default:
                $qb->orderBy('p.id', 'DESC');
                break;
        }

        $posts = $paginator->paginate($qb->getQuery(), $request->query->getInt('page', 1), 10, ['sort' => '']);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'html' => $this->renderView('forum/_posts_list.html.twig', [
                    'posts' => $posts,
                ]),
                'page' => $posts->currentPageNumber,
                'totalPages' => $posts->pageCount,
            ]);
        }

        return $this->render('forum/index.html.twig', [
            'posts' => $posts,
            'q' => $q,
            'categorie' => $categorie,
            'tri' => $tri,
        ]);
    }

    #[Route('/nouveau', name: 'forum_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $title = trim($request->request->get('title', ''));
            $content = trim($request->request->get('content', ''));
            $author = trim($request->request->get('author', 'Anonyme'));
            $categorie = $request->request->get('categorie');

            if (!$title || !$content) {
                $this->addFlash('error', 'Titre et contenu requis.');
                return $this->redirectToRoute('forum_new');
            }

            $fullContent = $title . ' ' . $content;
            $moderation = $this->moderationService->checkToxicity($fullContent);
            if ($moderation['is_toxic']) {
                $this->addFlash('error', 'Votre publication contient du contenu inapproprié. Veuillez le modifier.');
                return $this->redirectToRoute('forum_new');
            }

            $imagePath = null;
            $imageFile = $request->files->get('image');
            if ($imageFile) {
                try {
                    $uploadDir = dirname(__DIR__, 2) . '/public/uploads/forum/posts';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $extension = $imageFile->getClientOriginalExtension() ?: 'jpg';
                    $filename = uniqid('post_') . '.' . $extension;
                    $imageFile->move($uploadDir, $filename);
                    $imagePath = '/uploads/forum/posts/' . $filename;
                } catch (\Exception $e) {
                    error_log('Image upload error: ' . $e->getMessage());
                }
            }

            $currentUser = $this->getUser();
            $authorId = ($currentUser instanceof \App\Entity\User) ? $currentUser->getId() : null;
            
            $post = new ForumPost();
            $post->setTitle($title)
                 ->setContent($content)
                 ->setAuthor($author)
                 ->setAuthorId($authorId)
                 ->setCategorie($categorie ?: null)
                 ->setStatus('APPROVED');
            
            if ($imagePath) {
                $post->setImage($imagePath);
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
            'authorId' => $post->getAuthorId(),
            'authorName' => $post->getAuthor(),
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
