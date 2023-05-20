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
    public function index(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findallNotOld();

        return $this->render('annonce/annonces.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    /**
     * @Route("/annonce/new", name="creer_annonce")
     */
    public function creerAnnonce(Request $request,Security $security): Response
    {
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
        // Affichage du formulaire
        return $this->render('annonce/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/annonce/{id}", name="afficher_annonce")
     */
    public function afficherAnnonce($id,AnnonceRepository $annonceRepository, CommentaireRepository $commentaireRepository): Response
    {
        $annonce = $annonceRepository->findOneBy(['id'=> $id]);
        $commentaires = $commentaireRepository->findby(['annonce'=> $id]);
        $user = $this->getUser();
        dump($annonce->getOld());

        return $this->render('annonce/afficher.html.twig', [
            'id' => $id,
            'annonce' => $annonce,
            'date' => $annonce->getDate(),
            'commentaires' => $commentaires,
            'user' => $user,
            'old' => $annonce->getOld(),
        ]);
    }

    /**
     * @Route("/annonce/{id}/delete", name="supprimer_annonce")
     */
    public function supprimerAnnonce($id, Request $request, Security $security, AnnonceRepository $annonceRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $annonce = $annonceRepository->findOneBy(['id' => $id]);

        if ($user->getId() === $annonce->getConducteur()->getId()) {
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

            // Affichage du formulaire
            return $this->render('annonce/modifier.html.twig', [
                'id' => $id,
                'form' => $form->createView(),
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

        // Création d'une nouvelle note
        $note = new Note();
        $note->setAuteur($user);
        $note->setConducteur($annonce->getConducteur());
        $note->setNote($request->request->get('note'));
        $conducteur = $annonce->getConducteur();
        $notes = $noteRepository->findAllByConducteurId($conducteur->getId());

        $totalNotes = 0;
        $nbNotes = count($notes);

        foreach ($notes as $note) {
            $totalNotes += $note->getNote();
        }

        $averageNote = $nbNotes > 0 ? $totalNotes / $nbNotes : 0;
        $conducteur->setNote($averageNote);


        // Sauvegarde de la note en base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($note);
        $entityManager->flush();
        $entityManager->persist($conducteur);
        $entityManager->flush();

        $this->addFlash('success', 'Note ajoutée avec succès.');

        return $this->redirectToRoute('annonces');
    }

}
