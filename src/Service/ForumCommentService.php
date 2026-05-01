<?php

namespace App\Service;

use App\Entity\ForumComment;
use App\Entity\ForumPost;
use App\Entity\LikeDislike;
use App\Entity\User;
use App\Repository\ForumCommentRepository;
use App\Repository\LikeDislikeRepository;
use Doctrine\ORM\EntityManagerInterface;

class ForumCommentService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ForumCommentRepository $commentRepo,
        private LikeDislikeRepository $likeRepo,
    ) {}

    public function getCommentsData(ForumPost $post, string $sort = 'top', int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $comments = $this->commentRepo->getRootComments($post, $sort, $perPage, $offset);
        $allComments = $this->commentRepo->getAllCommentsForPost($post);
        $tree = $this->commentRepo->buildCommentTree($allComments);
        $total = $this->commentRepo->countRootComments($post);

        return [
            'comments' => $this->prepareCommentsForDisplay($tree),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    public function prepareCommentsForDisplay(array $tree, ?User $user = null): array
    {
        $result = [];
        foreach ($tree as $item) {
            $comment = $item['comment'];
            $isEntity = is_object($comment) && method_exists($comment, 'getId');
            
            $id = $isEntity ? $comment->getId() : ($comment['id'] ?? 0);
            $userVote = 0;
            
            if ($user) {
                $vote = $this->likeRepo->findOneBy([
                    'user' => $user,
                    'targetType' => LikeDislike::TYPE_COMMENT,
                    'targetId' => $id,
                ]);
                if ($vote) {
                    $userVote = $vote->getVote();
                }
            }

            $result[] = [
                'id' => $id,
                'comment' => $comment,
                'userVote' => $userVote,
                'author' => $isEntity ? $comment->getAuthor() : ($comment['author'] ?? ''),
                'content' => $isEntity ? $comment->getContent() : ($comment['content'] ?? ''),
                'score' => $isEntity ? $comment->getScore() : ($comment['score'] ?? 0),
                'likes' => $isEntity ? $comment->getLikes() : ($comment['likes'] ?? 0),
                'dislikes' => $isEntity ? $comment->getDislikes() : ($comment['dislikes'] ?? 0),
                'isPinned' => $isEntity ? $comment->isIsPinned() : ($comment['isPinned'] ?? false),
                'createdAt' => $isEntity ? $comment->getCreatedAt() : ($comment['createdAt'] ?? null),
                'depth' => 0,
                'replyCount' => count($item['replies']),
                'replies' => $this->prepareCommentsForDisplay($item['replies'], $user),
            ];
        }
        return $result;
    }

    public function createComment(ForumPost $post, string $content, string $author, ?int $parentId = null, ?string $imagePath = null): ForumComment
    {
        $comment = new ForumComment();
        $comment->setPost($post)
                ->setContent($content)
                ->setAuthor($author)
                ->setParentId($parentId);
        
        if ($imagePath) {
            $comment->setImage($imagePath);
        }

        $this->em->persist($comment);
        $this->em->flush();

        return $comment;
    }

    public function vote(ForumComment $comment, User $user, int $vote): array
    {
        $existing = $this->likeRepo->findOneBy([
            'user' => $user,
            'targetType' => LikeDislike::TYPE_COMMENT,
            'targetId' => $comment->getId(),
        ]);

        $previousVote = 0;
        if ($existing) {
            $previousVote = $existing->getVote();
            if ($previousVote === $vote) {
                $this->em->remove($existing);
                $this->updateCommentVotes($comment, $previousVote, -1);
                $this->em->flush();
                return $this->getVoteResult($comment, 0);
            }
            $this->updateCommentVotes($comment, $previousVote, -1);
            $existing->setVote($vote);
        } else {
            $like = new LikeDislike();
            $like->setUser($user)
                 ->setTargetType(LikeDislike::TYPE_COMMENT)
                 ->setTargetId($comment->getId())
                 ->setVote($vote);
            $this->em->persist($like);
        }

        $this->updateCommentVotes($comment, $vote, 1);
        $this->em->flush();

        return $this->getVoteResult($comment, $vote);
    }

    private function updateCommentVotes(ForumComment $comment, int $vote, int $direction): void
    {
        $likes = $comment->getLikes();
        $dislikes = $comment->getDislikes();
        
        if ($vote === 1) {
            $comment->setLikes($likes + $direction);
        } else {
            $comment->setDislikes($dislikes + $direction);
        }
        $comment->setScore($comment->getLikes() - $comment->getDislikes());
    }

    private function getVoteResult(ForumComment $comment, int $userVote): array
    {
        return [
            'success' => true,
            'userVote' => $userVote,
            'likes' => $comment->getLikes(),
            'dislikes' => $comment->getDislikes(),
            'score' => $comment->getScore(),
        ];
    }

    public function deleteComment(ForumComment $comment): void
    {
        $this->em->remove($comment);
        $this->em->flush();
    }

    public function pinComment(ForumComment $comment): bool
    {
        $comment->setIsPinned(!$comment->isIsPinned());
        $this->em->flush();
        return $comment->isIsPinned();
    }

    public function calculateHotScore(ForumComment $comment): float
    {
        $score = $comment->getScore();
        $ageInHours = (time() - $comment->getCreatedAt()->getTimestamp()) / 3600;
        $gravity = 1.8;
        return $score / pow($ageInHours + 2, $gravity);
    }
}
