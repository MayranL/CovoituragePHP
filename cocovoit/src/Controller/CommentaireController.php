<?php

namespace App\Controller;


use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\AnnonceRepository;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommentaireController extends AbstractController
{


    /**
     * @Route("/commentaire/{id}/delete", name="supprimer_commentaire")
     */
    public function supprimerCommentaire($id, CommentaireRepository $commentaireRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $commentaire = $commentaireRepository->findOneBy(['id' => $id]);

        if ($user->getId() === $commentaire->getAuteur()->getId()) {
            // Supprimer le commentaire de la base de données
            $entityManager->remove($commentaire);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');

            // Rediriger vers la page d'affichage de l'annonce liée au commentaire
            return $this->redirectToRoute('afficher_annonce', ['id' => $commentaire->getAnnonce()->getId()]);
        }

        $this->addFlash('error', 'Vous n\'avez pas accès à la suppression de ce commentaire.');

        // Rediriger vers la page d'affichage de l'annonce liée au commentaire
        return $this->redirectToRoute('afficher_annonce', ['id' => $commentaire->getAnnonce()->getId()]);
    }

    /**
     * @Route("/annonce/{id}/comment}", name="faire_commentaire")
     */
    public function posterCommentaire(Request $request, Security $security, $id, AnnonceRepository $annonceRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $annonce = $annonceRepository->findOneBy(['id' => $id]);

        // Vérifier si l'utilisateur est autorisé à poster un commentaire sur cette annonce
        // Vous pouvez ajouter vos propres conditions selon vos règles métier

        // Création du formulaire de commentaire
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);

        // Traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur et l'annonce au commentaire
            $commentaire->setAuteur($user);
            $commentaire->setAnnonce($annonce);

            // Enregistrer le commentaire en base de données
            $entityManager->persist($commentaire);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Votre commentaire a été posté avec succès.');

            // Rediriger vers la page d'affichage de l'annonce
            return $this->redirectToRoute('afficher_annonce', ['id' => $annonce->getId()]);
        }

        return $this->redirectToRoute('afficher_annonce', ['id' => $annonce->getId()]);
    }
}
