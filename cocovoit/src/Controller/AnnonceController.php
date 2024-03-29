<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Commentaire;
use App\Entity\Note;
use App\Entity\Reservation;
use App\Form\AnnonceType;
use App\Form\CommentaireType;
use App\Repository\AnnonceRepository;
use App\Repository\CommentaireRepository;
use App\Repository\NoteRepository;
use App\Repository\ReservationRepository;
use Faker\Core\DateTime;
use PhpParser\Node\Expr\New_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonces", name="annonces")
     */
    public function index(Request $request, AnnonceRepository $annonceRepository,Security $security): Response
    {
        $departure = $request->query->get('departure');
        $arrival = $request->query->get('arrival');
        $date = $request->query->get('date');
        $annonces = [];
            if ($departure && $arrival && $date) {
                $annonces = $annonceRepository->findByDepartureArrivalAndHour($departure, $arrival, $date);
            } elseif ($departure && $date){
                $annonces = $annonceRepository->findByDepartureAndHour($departure, $date);
            } elseif ($arrival && $date){
                $annonces = $annonceRepository->findByArrivalAndHour($arrival, $date);
            } elseif ($departure && $arrival) {
                $annonces = $annonceRepository->findByDepartureAndArrival($departure, $arrival);
            } elseif ($departure) {
                $annonces = $annonceRepository->findByDeparture($departure);
            } elseif ($arrival) {
                $annonces = $annonceRepository->findByArrival($arrival);
            } elseif ($date) {
                $annonces = $annonceRepository->findByHour($date);
            } else {
                if ($security->isGranted('ROLE_ADMIN')){
                    $annonces = $annonceRepository->findAll();
                }else{
                    $annonces = $annonceRepository->findAllNotOld();
                }
            }

        $numberofresa = 0;
        if($this->getUser()){
            $user = $this->getUser();
            $numberofresa = 0;
            foreach ($user->getReservations() as $reservation){
                if (!$reservation->getOld()){
                    $numberofresa += 1;
                }
            }
        }

        return $this->render('annonce/annonces.html.twig', [
            'annonces' => $annonces,
            'nombredereservation'=>$numberofresa,
            'admin' => $security->isGranted('ROLE_ADMIN'),
        ]);
    }

    /**
     * @Route("/annonce/new", name="creer_annonce")
     */
    public function creerAnnonce(Request $request,Security $security): Response
    {
       if (!$this->getUser()) {
            $this->addflash('error', 'Vous devez être connecté pour effectuer une réservation.');
            return $this->redirectToRoute('login');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // Création du formulaire d'annonce
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);

        // Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'utilisateur connecté
            $user = $this->getUser();
            // Associer l'utilisateur à l'annonce
            $annonce->setConducteur($user);
            // Enregistrer l'annonce en base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($annonce);
            $entityManager->flush();

            // Rediriger vers la page d'affichage de l'annonce
            return $this->redirectToRoute('afficher_annonce', ['id' => $annonce->getId()]);
        }

        if($this->getUser()){
            $user = $this->getUser();
            $numberofresa = 0;
            foreach ($user->getReservations() as $reservation){
                if (!$reservation->getOld()){
                    $numberofresa += 1;
                }
            }
        }
        // Affichage du formulaire
        return $this->render('annonce/creer.html.twig', [
            'form' => $form->createView(),
            'nombredereservation'=>$numberofresa,
        ]);
    }

    /**
     * @Route("/annonce/{id}", name="afficher_annonce")
     */
    public function afficherAnnonce($id,ReservationRepository  $reservationRepository,AnnonceRepository $annonceRepository, CommentaireRepository $commentaireRepository): Response
    {
        if (!$this->getUser()) {
            $this->addflash('error', 'Vous devez être connecté pour voir ce trajet en détail.');
            return $this->redirectToRoute('login');
        }
        $annonce = $annonceRepository->findOneBy(['id'=> $id]);
        if (!$annonce){
            $this->addflash('error', 'L\'annonce que vous essayez de consulter n\'existe pas' );
            return $this->redirectToRoute('annonces');
        }
        // récuperer les com + resa
        $commentaires = $commentaireRepository->findby(['annonce'=> $id]);
        $user = $this->getUser();
        $reservations = $reservationRepository->findBy(['annonce' => $annonce]);

        $numberofresa = 0;
        foreach ($user->getReservations() as $reservation){
            if (!$reservation->getOld()){
                $numberofresa += 1;
            }
        }
        return $this->render('annonce/afficher.html.twig', [
            'id' => $id,
            'annonce' => $annonce,
            'date' => $annonce->getDate(),
            'commentaires' => $commentaires,
            'user' => $user,
            'old' => $annonce->getOld(),
            'reservations' => $reservations,
            'nombredereservation'=>$numberofresa,
        ]);
    }

    /**
     * @Route("/annonce/{id}/delete", name="supprimer_annonce")
     */
    public function supprimerAnnonce($id, Request $request, Security $security, AnnonceRepository $annonceRepository, ReservationRepository $reservationRepository): Response
    {
        if (!$this->getUser()) {
            $this->addflash('error', 'Vous devez être connecté pour effectuer une réservation.');
            return $this->redirectToRoute('login');
        }
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $annonce = $annonceRepository->findOneBy(['id' => $id]);

        if (($user->getId() === $annonce->getConducteur()->getId() and $annonce->getDate()->getTimestamp()<time()) or $security->isGranted('ROLE_ADMIN')) {
            // Supprimer l'annonce
            $entityManager->remove($annonce);
            $entityManager->flush();

            $this->addFlash('success', 'La suppression de ce trajet est effectuée');
            return $this->redirectToRoute('annonces');
        }

        $this->addFlash('error', 'Vous n\'avez pas accès à la suppression de ce trajet');
        return $this->redirectToRoute('afficher_annonce', ['id' => $annonce->getId()]);
    }

    /**
     * @Route("/annonce/{id}/edit", name="modifier_annonce")
     */
    public function modifierAnnonce(Request $request, Security $security, $id, AnnonceRepository $annonceRepository): Response
    {
        if (!$this->getUser()) {
            $this->addflash('error', 'Vous devez être connecté pour effectuer une réservation.');
            return $this->redirectToRoute('login');
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $annonce = $annonceRepository->findOneBy(['id' => $id]);

        if ($user->getId() === $annonce->getConducteur()->getId()) {
            // Création du formulaire d'annonce
            $form = $this->createForm(AnnonceType::class, $annonce);

            // Traitement du formulaire
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Fusionner l'entité modifiée avec celle récupérée depuis la base de données
                $annonce = $entityManager->merge($annonce);

                // Enregistrer les modifications de l'annonce en base de données
                $entityManager->flush();

                // Rediriger vers la page d'affichage de l'annonce
                return $this->redirectToRoute('afficher_annonce', ['id' => $annonce->getId()]);
            }

            $numberofresa = 0;
            foreach ($user->getReservations() as $reservation){
                if (!$reservation->getOld()){
                    $numberofresa += 1;
                }
            }

            // Affichage du formulaire
            return $this->render('annonce/modifier.html.twig', [
                'id' => $id,
                'form' => $form->createView(),
                'nombredereservation'=>$numberofresa,
            ]);
        }

        $this->addFlash('error', 'Vous n\'avez pas accès à la modification de ce trajet');
        return $this->redirectToRoute('afficher_annonce', ['id' => $annonce->getId()]);
    }

    /**
     * @Route("/annonce/{id}/rate", name="noter_annonce")
     */
    public function noterAnnonce(Request $request, $id, NoteRepository $noteRepository): Response
    {
        $annonce = $this->getDoctrine()->getRepository(Annonce::class)->find($id);

        if (!$annonce) {
            $this->addFlash('error', 'Annonce non trouvée.');
            return $this->redirectToRoute('annonces');
        }

        $user = $this->getUser();

        // Vérifier si le conducteur de l'annonce est différent de l'utilisateur connecté
        if ($annonce->getConducteur() === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas noter votre propre annonce.');
            return $this->redirectToRoute('annonces');
        }

        // Vérifier si l'utilisateur a déjà noté cette annonce
        $existingNote = $this->getDoctrine()->getRepository(Note::class)->findOneBy([
            'auteur' => $user,
            'annonce' => $annonce,
        ]);

        if ($existingNote) {
            $this->addFlash('error', 'Vous avez déjà noté cette annonce.');
            return $this->redirectToRoute('annonces');
        }
        $conducteur = $annonce->getConducteur();
        $notes = $noteRepository->findAllByAuteur($this->getUser(),$annonce,$conducteur->getId());
        dump(count($notes));
        // Création d'une nouvelle note
        if (count($notes)===0){
            $notes = $noteRepository->findAllByConducteurId($conducteur->getId());
            $note = new Note();
            $note->setAuteur($user);
            $note->setConducteur($annonce->getConducteur());
            $note->setAnnonce($annonce);
            $note->setNote($request->request->get('note'));

            $totalNotes = 0;
            $nbNotes = count($notes)+1;

            foreach ($notes as $note) {
                $totalNotes += $note->getNote();
            }

            $averageNote = $nbNotes > 0 ? $totalNotes / $nbNotes : 0;
            $notef = ($conducteur->GetNote()+$averageNote)/2;
            $conducteur->setNote($notef);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->persist($conducteur);
            $entityManager->flush();

            $this->addFlash('success', 'Note ajoutée avec succès.');
        }else{
            $this->addFlash('danger', 'Annonce déjà notée.');
            return $this->redirectToRoute('annonces');
        }


        return $this->redirectToRoute('annonces');
    }

    /**
     * @Route("/annonce/{id}/duplicate", name="dupliquer_annonce")
     */
    public function dubliquerAnnonce(Request $request, Security $security, $id, AnnonceRepository $annonceRepository): Response
    {
        if (!$this->getUser()) {
            $this->addflash('error', 'Vous devez être connecté pour dupliquer cette reservation.');
            return $this->redirectToRoute('login');
        }

        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $annonce = $annonceRepository->findOneBy(['id' => $id]);

        $duplicateannonce = New Annonce();
        $duplicateannonce->setConducteur($this->getUser());
        $duplicateannonce->setDate($annonce->getDate());
        $duplicateannonce->setPrix($annonce->getPrix());
        $duplicateannonce->setVilleDepart($annonce->getVilleDepart());
        $duplicateannonce->setVilleArrive($annonce->getVilleArrive());
        $duplicateannonce->setNbplace($annonce->getNbplace());
        $duplicateannonce->setModeleV($annonce->getModeleV());
        $form = $this->createForm(AnnonceType::class, $duplicateannonce);

        // Traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Fusionner l'entité modifiée avec celle récupérée depuis la base de données
            $entityManager->persist($duplicateannonce);
            $entityManager->flush();

            // Rediriger vers la page d'affichage de l'annonce
            return $this->redirectToRoute('afficher_annonce', ['id' => $duplicateannonce->getId()]);
        }

        $numberofresa = 0;
        foreach ($user->getReservations() as $reservation){
            if (!$reservation->getOld()){
                $numberofresa += 1;
            }
        }

        // Affichage du formulaire
        return $this->render('annonce/dupliquer.html.twig', [
            'id' => $id,
            'form' => $form->createView(),
            'nombredereservation'=>$numberofresa,
        ]);
    }

}
