<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Entity\RepondreQuestion;
use App\Form\QuestionType;
use App\Form\RepondreQuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/question')]
class QuestionController extends AbstractController
{
    #[Route('/', name: 'app_question_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $questions = $entityManager
            ->getRepository(Question::class)
            ->findAll();

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
        ]);
    }

    #[Route('/new{questionnaireid}', name: 'app_question_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $questionnaireid): Response
    {
        $questionnaireid = intval($questionnaireid);
        $questionnaire = $entityManager->getRepository(Questionnaire::class)->find($questionnaireid);
        if (!$questionnaire) {
          throw $this->createNotFoundException('No questionnaire found for id '.$questionnaireid);
        }

        $question = new Question();
        $question->setQuestionnaire($questionnaire);
        #On recupere la derniere question du questionnaire et on met l'indince de notre nouvelle question à l'ancien +1
        $max_questionnaire_order = $questionnaire->getMaxQuestionOrder();
        if($max_questionnaire_order === null){
            $question->setQuestionorder(0);
        }
        else{
            $question->setQuestionorder($max_questionnaire_order+1);
        }
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($question);
            $entityManager->flush();

            // Ajouter la question au questionnaire
            $questionnaire->addQuestion($question);
            $entityManager->persist($questionnaire);
            $entityManager->flush();

            //return $this->redirectToRoute('app_questionnaire_show', ['questionnaireid' => $questionnaire->getQuestionnaireid()]);
            return $this->redirectToRoute('app_question_edit', ['questionid' => $question->getQuestionid()]);
            //return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('question/new.html.twig', [
            'question' => $question,
            'form' => $form,
        ]);
    }

    #[Route('/{questionid}', name: 'app_question_show', methods: ['GET'])]
    public function show(Question $question): Response
    {
        
        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }

    #[Route('/{questionid}/edit', name: 'app_question_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_questionnaire_show', ['questionnaireid' => $question->getQuestionnaire()->getQuestionnaireid()]);

            #return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        $reponses = $question->getReponses();
        return $this->renderForm('question/edit.html.twig', [
            'question' => $question,
            'form' => $form,
            'reponses' => $reponses,
        ]);
    }


    #[Route('/{questionid}/repondre', name: 'app_question_repondre', methods: ['GET', 'POST'])]
    public function repondre(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        $answer = new RepondreQuestion();
        $answer->setQuestion($question);
        $form = $this->createForm(RepondreQuestionType::class, $answer, ['answer' => $question]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_questionnaire_show', ['questionnaireid' => $question->getQuestionnaire()->getQuestionnaireid()]);

            #return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
        }

        $reponses = $question->getReponses();
        return $this->renderForm('question/repondre.html.twig', [
            'question' => $question,
            'form' => $form,
            'reponses' => $reponses,
        ]);
    }

    #[Route('/{questionid}', name: 'app_question_delete', methods: ['POST'])]
    public function delete(Request $request, Question $question, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$question->getQuestionid(), $request->request->get('_token'))) {
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_question_index', [], Response::HTTP_SEE_OTHER);
    }
}
