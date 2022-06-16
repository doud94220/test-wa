<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PersonneController extends AbstractController
{
    ////// ********************************* Partie 3a) et 4a) *********************************
    /* L'API DOC ça ne marche que partiellement, je n'insiste pas, c'est pas un point essentiel */

    /**
     * @Route("api/sauvegarde_nouvelle/{prenom}/{nom}/{date_naissance}", name="personne_sauvegarde_nouvelle")
     */
    public function sauvegarde_personne($prenom, $nom, $date_naissance, PersonneRepository $personneRepository)
    {
        $NouvellePersonne = new Personne();
        $dateNaissanceFormatDate = DateTime::createFromFormat('dmY', $date_naissance);
        $dateNaissanceFormatDate->format('d/m/Y');

        //Règle de gestion : l'âge doit être inférieur à 150 ans
        $dateDuJour = new DateTime;
        $dateDuJour->format('d/m/Y');
        $intervalle = $dateDuJour->diff($dateNaissanceFormatDate);
        $agePersonne = $intervalle->y;

        if ($agePersonne >= 150) {
            $response401 = new Response("Vous ne pouvez pas vous inscrire si vous avez 150 ans ou plus. Désolé.", 401);
            return $response401;
        }

        $NouvellePersonne->setNom($nom)->setPrenom($prenom)->setDateNaissance($dateNaissanceFormatDate);

        try {
            $personneRepository->add($NouvellePersonne);
            $reponseOk1 = new Response('OK', 201);
            return $reponseOk1;
        } catch (Exception $e) {
            $response402 = new Response("Problème lors de l'insertion en base de la nouvelle personne : " . $e->getMessage(), 402);
            return $response402;
        }
    }

    /**
     * @Route("api/renvoie_tout", name="personne_renvoie_tout")
     */
    public function renvoie_tout(PersonneRepository $personneRepository)
    {
        $tableauAvecToutesLesPersonnes = $personneRepository->findAll();
        $tableauAvecToutesLesPersonnesReformate = [];

        foreach ($tableauAvecToutesLesPersonnes as $unePersonneDuTableau) {
            //calcul de l'âge de la personne
            $dateNaissance = $unePersonneDuTableau->getDateNaissance();
            $dateNaissance->format('d/m/Y');
            $dateDuJour = new DateTime;
            $dateDuJour->format('d/m/Y');
            $intervalle = $dateDuJour->diff($dateNaissance);
            $agePersonne = $intervalle->y;

            $informationsDunePersonne = $unePersonneDuTableau->getNom() . " " . $unePersonneDuTableau->getPrenom() . " " . $agePersonne . " ans";
            array_push($tableauAvecToutesLesPersonnesReformate, $informationsDunePersonne);
        }

        /*
            Le bloc dessous s'affiche parfaitement dans un navigateur (Chrome en l'occurence)
            Pour voir clairement la réponse dans Postman, il faut voir la réponse dans Body > Preview
        */
        sort($tableauAvecToutesLesPersonnesReformate);
        dump($tableauAvecToutesLesPersonnesReformate);

        echo '<br>';
        $reponseOk2 = new Response('OK', 202, $tableauAvecToutesLesPersonnesReformate);
        return $reponseOk2;
    }


    ////// ********************************* Partie 3b) et 4b) *********************************

    /**
     * @Route("enregistre_nouvelle", name="personne_enregistre_nouvelle")
     */
    public function enregistre_personne(Request $request, EntityManagerInterface $em)
    {
        $NouvellePersonne = new Personne;
        $form = $this->createForm(PersonneType::class, $NouvellePersonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($NouvellePersonne);
            $em->flush();
            $this->addFlash('success', 'La nouvelle personne a bien été enregistrée !');
            return $this->redirect('enregistre_nouvelle');
        }

        return $this->render('enregistre_personne.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("affiche_toutes", name="personne_affiche_toutes")
     */
    public function affiche_toutes_les_personnes(PersonneRepository $personneRepository)
    {
        $tableauAvecToutesLesPersonnes = $personneRepository->findAll();
        $tableauAvecToutesLesPersonnesReformate = [];

        foreach ($tableauAvecToutesLesPersonnes as $unePersonneDuTableau) {
            //calcul de l'âge de la personne
            $dateNaissance = $unePersonneDuTableau->getDateNaissance();
            $dateNaissance->format('d/m/Y');
            $dateDuJour = new DateTime;
            $dateDuJour->format('d/m/Y');
            $intervalle = $dateDuJour->diff($dateNaissance);
            $agePersonne = $intervalle->y;

            $informationsDunePersonne = $unePersonneDuTableau->getNom() . " " . $unePersonneDuTableau->getPrenom() . " " . $agePersonne . " ans";
            array_push($tableauAvecToutesLesPersonnesReformate, $informationsDunePersonne);
        }

        /*
            Le bloc dessous s'affiche parfaitement dans un navigateur (Chrome en l'occurence)
            Pour voir clairement la réponse dans Postman, il faut voir la réponse dans Body > Preview
        */
        sort($tableauAvecToutesLesPersonnesReformate);
        echo json_encode($tableauAvecToutesLesPersonnesReformate);

        $reponseOk3 = new Response('', 203, $tableauAvecToutesLesPersonnesReformate);
        return $reponseOk3;
    }
}
