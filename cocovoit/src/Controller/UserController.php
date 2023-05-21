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


    /**
     * @Route("/faker", name="faker")
     */
    public function faker(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, UserRepository $userRepository, AnnonceRepository $annonceRepository): Response
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstName);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $userPasswordEncoder->encodePassword(
                    $user,
                    $faker->password
                )
            );
            $user->setCreatedAt($faker->dateTimeThisYear);
            $user->setNote($faker->randomFloat(2, 0, 5));
            $entityManager->persist($user);
        }
        $entityManager->flush();
            for ($j = 0; $j < 10; $j++) {
                $annonce = new Annonce();
                $annonce->setConducteur($user);
                $annonce->setVilleDepart($faker->city);
                $annonce->setVilleArrive($faker->city);
                $annonce->setPrix($faker->randomFloat(2, 10, 100));
                $annonce->setModeleV($faker->word);
                $annonce->setNbplace($faker->numberBetween(1, 4));
                $annonce->setDate($faker->dateTimeBetween('now', '+1 year'));

                // Enregistrez l'annonce dans la base de données
                $entityManager->persist($annonce);

                $reservationCount = $j < 5 ? 5 : $faker->numberBetween(5, 10);
                for ($k = 0; $k < $reservationCount; $k++) {
                    $reservation = new Reservation();
                    $reservation->setPassager($user);

                    // Sélectionner une annonce aléatoire qui ne leur appartient pas
                    $randomUser = $userRepository->findByRandomUserExcept($user);
                    shuffle($randomUser);
                    dump($randomUser);
                    $randomAnnonce = $annonceRepository->findbyRandomAnnonceExceptUser($randomUser[0]);
                    shuffle($randomAnnonce);
                    if($randomAnnonce[0]->getNbplace()>0){
                        $reservation->setAnnonce($randomAnnonce[0]);
                        $randomAnnonce[0]->setNbplace( $randomAnnonce[0]->getNbplace()-1);
                        // Enregistrez la réservation dans la base de données
                        $entityManager->persist($reservation);
                    }
                }
            }
            $currentDate = new DateTime();
            $reservations = $entityManager->getRepository(Reservation::class)->findBy(['passager' => $user]);

            foreach ($reservations as $reservation) {
                $annonce = $reservation->getAnnonce();
                $date = $annonce->getDate();

                // Vérifier si la date de l'annonce est antérieure à celle d'aujourd'hui
                if ($date < $currentDate) {
                    // Vérifier si un commentaire existe déjà pour cette annonce et cet utilisateu
                        // Créer un nouveau commentaire
                    $commentaire = new Commentaire();
                    $commentaire->setAuteur($user);
                    $commentaire->setAnnonce($annonce);
                    $commentaire->setContenu("Votre commentaire ici.");

                    // Enregistrez le commentaire dans la base de données
                    $entityManager->persist($commentaire);

                    $existingNote = $entityManager->getRepository(Note::class)->findOneBy([
                        'auteur' => $user,
                        'annonce' => $annonce,
                    ]);

                    if (!$existingNote) {
                        // Créer une nouvelle note
                        $note = new Note();
                        $note->setAuteur($user);
                        $note->setConducteur($annonce->getConducteur());
                        $note->setAnnonce($annonce);
                        $note->setNote(0); // Note par défaut, à remplacer par la note réelle donnée par l'utilisateur

                        // Enregistrez la note dans la base de données
                        $entityManager->persist($note);
                    }

                }

            // Enregistrez l'utilisateur dans la base de données

        }
        $entityManager->flush();
        return $this->redirectToRoute('annonces');
    }


    // Autres actions pour la gestion des utilisateurs (modification du profil, suppression du compte, etc.)
}
