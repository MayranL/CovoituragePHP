<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(Request $request): Response
    {
        // Logique de gestion de l'inscription de l'utilisateur

        return $this->render('utilisateur/inscription.html.twig');
    }

    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function profil($id): Response
    {
        // Logique de récupération et d'affichage du profil de l'utilisateur

        return $this->render('utilisateur/profil.html.twig', [
            'id' => $id,
        ]);
    }

    // Autres actions pour la gestion des utilisateurs (modification du profil, suppression du compte, etc.)
}
