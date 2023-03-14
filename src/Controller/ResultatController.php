<?php

namespace App\Controller;

use App\Entity\Resultat;
use App\Form\ResultatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/resultat')]
class ResultatController extends AbstractController
{
    #[Route('/', name: 'app_resultat_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $resultats = $entityManager
            ->getRepository(Resultat::class)
            ->findAll();

        return $this->render('resultat/index.html.twig', [
            'resultats' => $resultats,
        ]);
    }

    #[Route('/new', name: 'app_resultat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $resultat = new Resultat();
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($resultat);
            $entityManager->flush();

            return $this->redirectToRoute('app_resultat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resultat/new.html.twig', [
            'resultat' => $resultat,
            'form' => $form,
        ]);
    }

    #[Route('/{resultatid}', name: 'app_resultat_show', methods: ['GET'])]
    public function show(Resultat $resultat): Response
    {
        return $this->render('resultat/show.html.twig', [
            'resultat' => $resultat,
        ]);
    }

    #[Route('/{resultatid}/edit', name: 'app_resultat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Resultat $resultat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_resultat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resultat/edit.html.twig', [
            'resultat' => $resultat,
            'form' => $form,
        ]);
    }

    #[Route('/{resultatid}', name: 'app_resultat_delete', methods: ['POST'])]
    public function delete(Request $request, Resultat $resultat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resultat->getResultatid(), $request->request->get('_token'))) {
            $entityManager->remove($resultat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_resultat_index', [], Response::HTTP_SEE_OTHER);
    }
}
