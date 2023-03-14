<?php

namespace App\Entity;

use App\Repository\QuestionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionnaireRepository::class)]
class Questionnaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $questionnairename = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $questionnairedescription = null;

    #[ORM\OneToMany(mappedBy: 'questionnaire', targetEntity: Question::class)]
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }


    public function getQuestionnaireid(): ?int
    {
        return $this->id;
    }

    public function setQuestionnaireid(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getQuestionnairename(): ?string
    {
        return $this->questionnairename;
    }

    public function setQuestionnairename(string $questionnairename): self
    {
        $this->questionnairename = $questionnairename;

        return $this;
    }

    public function getQuestionnairedescription(): ?string
    {
        return $this->questionnairedescription;
    }

    public function setQuestionnairedescription(string $questionnairedescription): self
    {
        $this->questionnairedescription = $questionnairedescription;

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuestionnaire($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuestionnaire() === $this) {
                $question->setQuestionnaire(null);
            }
        }

        return $this;
    }

    public function getMaxQuestionOrder() {
		$maxQuestionOrder = null;
		foreach($this->questions as $question) {
			if($maxQuestionOrder === null || $question->getQuestionorder() > $maxQuestionOrder) {
				$maxQuestionOrder = $question->getQuestionorder();
			}
		}
		return $maxQuestionOrder;
	}
}
