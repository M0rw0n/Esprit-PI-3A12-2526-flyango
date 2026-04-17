<?php

namespace App\Controller;

use App\Entity\ForumComment;
use App\Entity\LikeDislike;
use App\Entity\User;
use App\Repository\ForumCommentRepository;
use App\Repository\ForumPostRepository;
use App\Repository\LikeDislikeRepository;
use App\Service\ForumCommentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/ajax/forum')]
class ForumAjaxController extends AbstractController
{
    public function __construct(
        private ForumCommentService $commentService,
        private ForumCommentRepository $commentRepo,
        private ForumPostRepository $postRepo,
        private LikeDislikeRepository $likeRepo,
        private EntityManagerInterface $em,
        private ?CsrfTokenManagerInterface $csrfTokenManager = null,
    ) {}

    #[Route('/comments/{postId}', name: 'ajax_forum_comments', methods: ['GET'])]
    public function getComments(int $postId, Request $request): JsonResponse
    {
        try {
            $post = $this->postRepo->find($postId);
            if (!$post) {
                return new JsonResponse(['success' => false, 'message' => 'Post non trouvé'], 404);
            }

            $sort = $request->query->get('sort', 'top');
            $page = max(1, (int) $request->query->get('page', 1));

            /** @var User|null $user */
            $user = $this->getUser();

            $data = $this->commentService->getCommentsData($post, $sort, $page, 20);

            $html = $this->renderView('forum/_comments_tree.html.twig', [
                'commentsTree' => $data['comments'],
                'user' => $user,
            ]);

            return new JsonResponse([
                'success' => true,
                'html' => $html,
                'total' => $data['total'],
                'page' => $page,
                'totalPages' => $data['totalPages'],
                'sort' => $sort,
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    #[Route('/comment/create', name: 'ajax_forum_comment_create', methods: ['POST'])]
    public function createComment(Request $request): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted('ROLE_USER');

            $postId = (int) $request->request->get('post_id');
            $content = trim($request->request->get('content', ''));
            $parentId = $request->request->get('parent_id') ? (int) $request->request->get('parent_id') : null;

            if (!$content) {
                return new JsonResponse(['success' => false, 'message' => 'Contenu requis'], 400);
            }

            $post = $this->postRepo->find($postId);
            if (!$post) {
                return new JsonResponse(['success' => false, 'message' => 'Post non trouvé'], 404);
            }

            if ($parentId) {
                $parent = $this->commentRepo->find($parentId);
                if (!$parent || $parent->getPost()->getId() !== $postId) {
                    return new JsonResponse(['success' => false, 'message' => 'Commentaire parent invalide'], 400);
                }
            }

            /** @var User $user */
            $user = $this->getUser();
            $authorName = $user->getPrenom() . ' ' . $user->getNom();

            $comment = $this->commentService->createComment($post, $content, $authorName, $parentId);

            $commentHtml = $this->renderView('forum/_comments_tree.html.twig', [
                'commentsTree' => [[
                    'id' => $comment->getId(),
                    'comment' => $comment,
                    'userVote' => 0,
                    'author' => $comment->getAuthor(),
                    'content' => $comment->getContent(),
                    'score' => 0,
                    'likes' => 0,
                    'dislikes' => 0,
                    'isPinned' => false,
                    'createdAt' => $comment->getCreatedAt(),
                    'depth' => 0,
                    'replyCount' => 0,
                    'replies' => [],
                ]],
                'user' => $user,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Commentaire ajouté',
                'commentId' => $comment->getId(),
                'commentHtml' => $commentHtml,
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/comment/{id}/vote', name: 'ajax_forum_comment_vote', methods: ['POST'])]
    public function voteComment(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $comment = $this->commentRepo->find($id);
        if (!$comment) {
            return new JsonResponse(['success' => false, 'message' => 'Commentaire non trouvé'], 404);
        }

        $vote = (int) $request->request->get('vote', LikeDislike::LIKE);
        if ($vote !== LikeDislike::LIKE && $vote !== LikeDislike::DISLIKE) {
            return new JsonResponse(['success' => false, 'message' => 'Vote invalide'], 400);
        }

        /** @var User $user */
        $user = $this->getUser();
        $result = $this->commentService->vote($comment, $user, $vote);

        return new JsonResponse($result);
    }

    #[Route('/comment/{id}/pin', name: 'ajax_forum_comment_pin', methods: ['POST'])]
    public function pinComment(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $comment = $this->commentRepo->find($id);
        if (!$comment) {
            return new JsonResponse(['success' => false, 'message' => 'Commentaire non trouvé'], 404);
        }

        $pinned = $this->commentService->pinComment($comment);

        return new JsonResponse([
            'success' => true,
            'pinned' => $pinned,
            'message' => $pinned ? 'Commentaire épinglé' : 'Commentaire désépinglé',
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'ajax_forum_comment_delete', methods: ['POST'])]
    public function deleteComment(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $comment = $this->commentRepo->find($id);
        if (!$comment) {
            return new JsonResponse(['success' => false, 'message' => 'Commentaire non trouvé'], 404);
        }

        /** @var User $user */
        $user = $this->getUser();
        if (!$this->isGranted('ROLE_ADMIN') && $comment->getAuthor() !== $user->getEmail() && 
            $comment->getAuthor() !== ($user->getPrenom() . ' ' . $user->getNom())) {
            return new JsonResponse(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $this->commentService->deleteComment($comment);

        return new JsonResponse([
            'success' => true,
            'message' => 'Commentaire supprimé',
        ]);
    }
}
