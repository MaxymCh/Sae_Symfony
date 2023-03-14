<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Entity\RepondreQuestion;
use App\Form\QuestionnaireType;
use App\Entity\Reponse;


use App\Form\RepondreQuestionType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;

#[Route('/questionnaire')]
class QuestionnaireController extends AbstractController
{
    #[Route('/', name: 'app_questionnaire_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $questionnaires = $entityManager
            ->getRepository(Questionnaire::class)
            ->findAll();

        return $this->render('questionnaire/index.html.twig', [
            'questionnaires' => $questionnaires,
        ]);
    }



    #[Route('/export', name: 'app_questionnaire_export', methods: ['GET'])]
    public function export(EntityManagerInterface $entityManager): Response
    {

        $questionnaires = $entityManager
            ->getRepository(Questionnaire::class)
            ->findAll();


        foreach ($questionnaires as $questionnaire) {
            $questions = [];
            foreach ($questionnaire->getQuestions() as $question) {
                $reponses = [];
                foreach ($question->getReponses() as $reponse) {
                    $reponse = [
                        //'id' => $reponse->getReponseid(),
                        'texte' => $reponse->getReponsetext(),
                        'correcte' => $reponse->getCorrect(),
                    ];
                    array_push($reponses, $reponse);
                }
                $question= [
                    //'id' => $question->getQuestionid(),
                    'texte' => $question->getQuestiontext(),
                    'order' => $question->getQuestionorder(),
                    'type' => $question->getQuestiontype(),
                    'reponses'=> $reponses,
                ];
                array_push($questions, $question);
            }
            $data['questionnaire_'.$questionnaire->getQuestionnaireid()] = [
                //'id' => $questionnaire->getQuestionnaireid(),
                'name' => $questionnaire->getQuestionnairename(),
                'description' => $questionnaire->getQuestionnairedescription(),
                'questions' => $questions,
            ];
    
            
            
        }
        
        

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // Écriture du JSON dans un fichier
        $filename = 'json/questionnaire_donnee.json';
        file_put_contents($filename, $json);

        // Création d'une réponse JSON pour indiquer que l'exportation s'est bien passée
        $response = new JsonResponse([
            'message' => 'Les résultats ont été exportés avec succès.'
        ]);

        return $response;
    }

    #[Route('/import', name: 'app_questionnaire_import', methods: ['GET'])]
    public function import(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        $filePath = new File($this->getParameter('kernel.project_dir') . '/public/json/file.json');
        $realPath = $filePath->getRealPath();


        // Récupération du fichier JSON depuis la requête
        //$file = $request->files->get('file.json');
        //if (!$file) {
        //    return new JsonResponse(['error' => 'Fichier manquant'], Response::HTTP_BAD_REQUEST);
        //}
        //$filename = $file->getPathname();

        // Lecture du fichier JSON
        //$json = file_get_contents($filename);
        $jsonData = file_get_contents($realPath);
        if (!$jsonData) {
            return new JsonResponse(['error' => 'Impossible de lire le fichier'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Décodage des données JSON
        //$data = json_decode($json, true);
        $data = json_decode($jsonData, true);

        if (!$data) {
            return new JsonResponse(['error' => 'Le fichier JSON est invalide'], Response::HTTP_BAD_REQUEST);
        }

        $questionnaireRepository = $entityManager->getRepository(Questionnaire::class);

        foreach ($data as $questionnaireData) {
            $questionnaire = $questionnaireRepository->findOneBy(['questionnairename' => $questionnaireData['name']]);
            if (!$questionnaire) {
                // Insertion d'un nouveau questionnaire
                $questionnaire = new Questionnaire();
                $questionnaire->setQuestionnairename($questionnaireData['name']);
                $questionnaire->setQuestionnairedescription($questionnaireData['description']);
                $entityManager->persist($questionnaire);
            }

            foreach ($questionnaireData['questions'] as $questionData) {
                // Insertion d'une nouvelle question
                $question = new Question();
                $question->setQuestiontext($questionData['texte']);
                $question->setQuestionorder($questionData['order']);
                $question->setQuestiontype($questionData['type']);

                $questionnaire->addQuestion($question);
                $entityManager->persist($question);

                foreach ($questionData['reponses'] as $reponseData) {
                    // Insertion d'une nouvelle réponse
                    $reponse = new Reponse();
                    $reponse->setReponsetext($reponseData['texte']);
                    $reponse->setCorrect($reponseData['correcte']);

                    $question->addReponse($reponse);
                    $entityManager->persist($reponse);
                }
            }
            
    }

    // Exécution des requêtes SQL pour insérer les nouvelles données
    $entityManager->flush();

    return new JsonResponse(['message' => 'Les données ont été importées avec succès.']);
    }


    #[Route('/new', name: 'app_questionnaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $questionnaire = new Questionnaire();
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($questionnaire);
            $entityManager->flush();

            return $this->redirectToRoute('app_questionnaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('questionnaire/new.html.twig', [
            'questionnaire' => $questionnaire,
            'form' => $form,
        ]);
    }

    
    #[Route('/{questionnaireid}', name: 'app_questionnaire_show', methods: ['GET'])]
    public function show(Questionnaire $questionnaire): Response
    {
        

        return $this->render('questionnaire/show.html.twig', [
            'questionnaire' => $questionnaire,
        ]);
    }

    #[Route('/{questionnaireid}/edit', name: 'app_questionnaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Questionnaire $questionnaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuestionnaireType::class, $questionnaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_questionnaire_index', [], Response::HTTP_SEE_OTHER);
        }

        $questions = $questionnaire->getQuestions();
        return $this->renderForm('questionnaire/edit.html.twig', [
            'questionnaire' => $questionnaire,
            'form' => $form,
            'questions' => $questions,
        ]);
    }

    #[Route('/{questionnaireid}', name: 'app_questionnaire_delete', methods: ['POST'])]
    public function delete(Request $request, Questionnaire $questionnaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$questionnaire->getQuestionnaireid(), $request->request->get('_token'))) {
            $entityManager->remove($questionnaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_questionnaire_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{questionnaireid}/repondre/{indiceQuestion}', name: 'app_questionnaire_repondre', methods: ['GET', 'POST'])]
    public function repondre(Questionnaire $questionnaire, Request $request, int $indiceQuestion): Response
    {



        $session = $request->getSession();
        // Récupération des questions du questionnaire
        $questions = $questionnaire->getQuestions();

        if ($indiceQuestion >= count($questions)) {
            // TODO : Calcul du score
            return $this->calculerScore($questionnaire, $request);

        }

       
        // Si l'indice de la question est supérieur ou égal au nombre de questions,
        // alors on a répondu à toutes les questions, on peut calculer le score
        
        // Récupération de la question correspondant à l'indice donné
        $question = $questions[$indiceQuestion];

        // Création du formulaire pour répondre à la question
        $answer = new RepondreQuestion();
        $answer->setQuestion($question);
        $form = $this->createForm(RepondreQuestionType::class, $answer, ['answer' => $question]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //$ancienne_reponse_questionnaire = $session->get('questionnaire_'.$questionnaire->getQuestionnaireid());
            //if($ancienne_reponse_questionnaire == null){
            //    $session->set('questionnaire_'.$questionnaire->getQuestionnaireid(), []);
            //}

            $listeReponseQuestionnaire = $session->get('questionnaire_'.$questionnaire->getQuestionnaireid(), []);
            $listeReponseQuestionnaire['question_'.$question->getQuestionid()] = $answer->getAnswer();
            $session->set('questionnaire_'.$questionnaire->getQuestionnaireid(), $listeReponseQuestionnaire);
            // $session->set('questionnaire_'.$questionnaire->getQuestionnaireid(), $score_questionnaire+1);


            // Sauvegarde de la réponse à la question
            // Redirection vers la question suivante
            $indiceQuestion += 1;
            return $this->redirectToRoute('app_questionnaire_repondre', [
            'questionnaireid' => $questionnaire->getQuestionnaireid(),
            'indiceQuestion' => $indiceQuestion,
            ]);
        }

        return $this->render('questionnaire/repondre.html.twig', [
            'questionnaire' => $questionnaire,
            'question' => $question,
            'questions' => $questions,
            'questionIndex' => $indiceQuestion,
            'form' => $form->createView(),
        ]);
    }


    public function calculerScore(Questionnaire $questionnaire, Request $request): Response
    {
    $session = $request->getSession();
    $reponsesUtilisateur = $session->get('questionnaire_'.$questionnaire->getQuestionnaireid());

    $questions = $questionnaire->getQuestions();
    $score = 0;
    
    // Comparaison des réponses de l'utilisateur avec les réponses correctes
    foreach ($questions as $question) {
        $reponseCorrectes = $question->getReponsesCorrecteString();
        
        // Si la question n'a pas de réponse correcte, on passe à la suivante
        if (empty($reponseCorrectes)) {
            continue;
        }
        //foreach ($reponseCorrectes as $reponseCorrecte)
        if ($question->getQuestionType() === 'checkbox' ) {
            $nb_bonne_reponses = 0;
            $nb_mauvaise_reponses = 0;
            foreach ($reponsesUtilisateur['question_'.$question->getQuestionid()] as $reponseUtilisateur) {
                if(in_array($reponseUtilisateur, $reponseCorrectes)){
                    $nb_bonne_reponses += 1;
                }
                else{
                    $nb_mauvaise_reponses +=1;
                }
            }

            if($nb_mauvaise_reponses == 0){
                if($nb_bonne_reponses == count($reponseCorrectes)){
                    $score += 1; 
                }
                else{
                    $score += $nb_bonne_reponses/count($reponseCorrectes); 
                }
            }

        }
        else{
            if (strtolower($reponsesUtilisateur['question_'.$question->getQuestionid()]) === strtolower($reponseCorrectes[0])) {
                $score += 1;
            }
            
        } 
    }
    
    
    return $this->render('questionnaire/resultat.html.twig', [
        'questionnaire' => $questionnaire,
        'score' => $score,
        'questions' => $questions,
        'rep_user' => $reponsesUtilisateur

    ]);
    }





}