<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Entity\Question;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reponse')]
class ReponseController extends AbstractController
{
    #[Route('/', name: 'app_reponse_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reponses = $entityManager
            ->getRepository(Reponse::class)
            ->findAll();

        return $this->render('reponse/index.html.twig', [
            'reponses' => $reponses,
        ]);
    }

    #[Route('/new{questionid}', name: 'app_reponse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $questionid): Response
    {
        $questionid = intval($questionid);
        $question = $entityManager->getRepository(Question::class)->find($questionid);
        if (!$question) {
          throw $this->createNotFoundException('No question found for id '.$questionid);
        }
        $reponse = new Reponse();
        $reponse->setQuestion($question);
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reponse);
            $entityManager->flush();

            // Ajouter la question au questionnaire
            $question->addReponse($reponse);
            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_question_edit', ['questionid' => $question->getQuestionid()]);
            //return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reponse/new.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{reponseid}', name: 'app_reponse_show', methods: ['GET'])]
    public function show(Reponse $reponse): Response
    {
        return $this->render('reponse/show.html.twig', [
            'reponse' => $reponse,
        ]);
    }

    #[Route('/{reponseid}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_question_show', ['questionid' => $reponse->getQuestion()->getQuestionid()]);
        }

        return $this->renderForm('reponse/edit.html.twig', [
            'reponse' => $reponse,
            'form' => $form,
        ]);
    }

    #[Route('/{reponseid}', name: 'app_reponse_delete', methods: ['POST'])]
    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reponse->getReponseid(), $request->request->get('_token'))) {
            $entityManager->remove($reponse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
    }
}
