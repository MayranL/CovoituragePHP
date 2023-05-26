<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Commentaire;
use App\Entity\Note;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\UserRepository;
use Faker\Factory;
use App\Form\UserUpdateFormType;
use App\Repository\AnnonceRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/profile", name="profil")
     */
    public function profil(Request $request,Security $security, AnnonceRepository $annonceRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserUpdateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('password')->getData() !== null){
                $user->setPassword(
                    $this->passwordencoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email
        }
        dump($user->getReservations());
        dump(count($user->getReservations()));

        $annonces = $annonceRepository->findExpiredByUser($security->getUser()->getId());

        return $this->render('utilisateur/profil.html.twig', [
            'user' => $security->getUser(),
            'annonces' => $annonces,
            'form' => $form->createView(),
        ]);
    }

}
