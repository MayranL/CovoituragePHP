<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonces", name="annonces")
     */
    public function index(): Response
    {
        // Logique de récupération et d'affichage des annonces

        return $this->render('annonce/index.html.twig');
    }

    /**
     * @Route("/annonce/creer", name="creer_annonce")
     */
    public function creerAnnonce(Request $request): Response
    {
        // Logique de création d'une nouvelle annonce

        return $this->render('annonce/creer.html.twig');
    }

    /**
     * @Route("/annonce/{id}", name="afficher_annonce")
     */
    public function afficherAnnonce($id): Response
    {
        // Logique de récupération et d'affichage d'une annonce spécifique

        return $this->render('annonce/afficher.html.twig', [
            'id' => $id,
        ]);
    }

    // Autres actions pour la gestion des annonces (modification, suppression, réservation, etc.)
}
