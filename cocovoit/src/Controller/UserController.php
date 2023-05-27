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
        $numberofresa = 0;
        if($this->getUser()){
            $user = $this->getUser();

            foreach ($user->getReservations() as $reservation){
                if (!$reservation->getOld()){
                    $numberofresa += 1;
                }
            }
        }


        return $this->render('index.html.twig', [
            'nombredereservation'=>$numberofresa,
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

        $numberofresa = 0;
        foreach ($user->getReservations() as $reservation){
            if (!$reservation->getOld()){
                $numberofresa += 1;
            }
        }

        $annonces = $annonceRepository->findExpiredByUser($security->getUser()->getId());

        return $this->render('utilisateur/profil.html.twig', [
            'user' => $security->getUser(),
            'annonces' => $annonces,
            'form' => $form->createView(),
            'nombredereservation'=>$numberofresa,
        ]);
    }

    /**
     * @Route("/language/{locale}", name="set_language")
     */
    public function changeLocale($locale, Request $request)
    {
        $request->getSession()->set('_locale', $locale);

        return $this->redirect($request->headers->get('referer'));
    }

}
