<?php

namespace App\Entity;

class RepondreQuestion
{
    private $question;
    private $answer;

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer()
    {
        return $this->answer;
    }

    public function setAnswer($answer): self
    {
        $this->answer = $answer;

        return $this;
    }


}
