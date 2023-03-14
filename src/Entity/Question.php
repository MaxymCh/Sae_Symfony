<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $questiontext = null;

    #[ORM\Column(length: 255)]
    private ?string $questiontype = null;

    #[ORM\Column]
    private ?int $questionorder = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Questionnaire $questionnaire = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Reponse::class)]
    private Collection $reponses;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getQuestionid(): ?int
    {
        return $this->id;
    }

    public function setQuestionid(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getQuestiontext(): ?string
    {
        return $this->questiontext;
    }

    public function setQuestiontext(string $questiontext): self
    {
        $this->questiontext = $questiontext;

        return $this;
    }

    public function getQuestiontype(): ?string
    {
        return $this->questiontype;
    }

    public function setQuestiontype(string $questiontype): self
    {
        $this->questiontype = $questiontype;

        return $this;
    }

    public function getQuestionorder(): ?int
    {
        return $this->questionorder;
    }

    public function setQuestionorder(int $questionorder): self
    {
        $this->questionorder = $questionorder;

        return $this;
    }

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?Questionnaire $questionnaire): self
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function getReponseString()
	{
		$reponses = $this->reponses;
		$result = [];
		foreach ($reponses as $reponse) {
			$result[$reponse->getReponsetext()] = $reponse->getReponsetext();
		}
		return $result;
	}

    public function getReponsesCorrecteString(): array
    {
		$reponsesCorrecte = [];
		foreach ($this->reponses as $reponse) {
            if($reponse->getCorrect()){
				array_push($reponsesCorrecte, $reponse->getReponsetext());
			}
        }
        return $reponsesCorrecte;
    }

     /**
     * @return array
     */
    public function getReponsesCorrecte(): array
    {
		$reponsesCorrecte = [];
		foreach ($this->reponses as $reponse) {
            if($reponse->getCorrect()){
				array_push($reponsesCorrecte, $reponse);
			}
        }
        return $reponsesCorrecte;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setQuestion($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }

        return $this;
    }
}
