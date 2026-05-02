<?php

namespace App\Entity;

use App\Repository\FAQRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FAQRepository::class)]
class FAQ
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $question = null;

    #[ORM\Column(type: 'text')]
    private ?string $answer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $keywords = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $embedding = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $feedbackUp = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $feedbackDown = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getEmbedding(): ?array
    {
        return $this->embedding;
    }

    public function setEmbedding(?array $embedding): static
    {
        $this->embedding = $embedding;
        return $this;
    }

    public function getFeedbackUp(): int
    {
        return $this->feedbackUp;
    }

    public function setFeedbackUp(int $feedbackUp): static
    {
        $this->feedbackUp = $feedbackUp;
        return $this;
    }

    public function getFeedbackDown(): int
    {
        return $this->feedbackDown;
    }

    public function setFeedbackDown(int $feedbackDown): static
    {
        $this->feedbackDown = $feedbackDown;
        return $this;
    }
}
