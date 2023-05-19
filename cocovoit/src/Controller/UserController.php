<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConnexionType;
use App\Form\InscriptionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    private UserPasswordEncoderInterface $passwordencoder;

    /**
     * @param $passwordencoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordencoder)
    {
        $this->passwordencoder = $passwordencoder;
    }

    /**
     * @Route("/", name="dashboard")
     */
    public function dashboard(): Response
    {
        // Logique de récupération et d'affichage du profil de l'utilisateur

        return $this->render('index.html.twig', [
        ]);
    }


    /**
     * @Route("/profile}", name="profil")
     */
    public function profil(Security $security): Response
    {
        dump($security->getUser());

        return $this->render('utilisateur/profil.html.twig', [
            'user' => $security->getUser()->getNom(),
        ]);
    }


    // Autres actions pour la gestion des utilisateurs (modification du profil, suppression du compte, etc.)
}
