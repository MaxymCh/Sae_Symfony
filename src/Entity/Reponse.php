<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $reponseid = null;

    #[ORM\Column(length: 255)]
    private ?string $reponsetext = null;

    #[ORM\Column]
    private ?bool $correct = null;

    #[ORM\ManyToOne(inversedBy: 'reponses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponseid(): ?int
    {
        return $this->reponseid;
    }

    public function setReponseid(int $reponseid): self
    {
        $this->reponseid = $reponseid;

        return $this;
    }

    public function getReponsetext(): ?string
    {
        return $this->reponsetext;
    }

    public function setReponsetext(string $reponsetext): self
    {
        $this->reponsetext = $reponsetext;

        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->correct;
    }

    public function setCorrect(bool $correct): self
    {
        $this->correct = $correct;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
