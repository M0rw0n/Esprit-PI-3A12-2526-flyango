<?php

namespace App\Service;

use App\Entity\ForumPost;

class ForumPostManager
{
    public function validate(ForumPost $post): bool
    {
        if (empty($post->getTitle())) {
            throw new \InvalidArgumentException('Le titre est obligatoire');
        }

        if (empty($post->getContent())) {
            throw new \InvalidArgumentException('Le contenu est obligatoire');
        }

        if (empty($post->getAuthor())) {
            throw new \InvalidArgumentException('L\'auteur est obligatoire');
        }

        return true;
    }
}
