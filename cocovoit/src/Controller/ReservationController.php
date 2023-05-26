<?php
namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Annonce;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReservationController extends AbstractController
{
    /**
     * @Route("/annonce/{id}/reserver", name="faire_reservation", methods={"POST"})
     */
    public function faireReservation(Request $request, $id, FlashBagInterface $flashBag): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $flashBag->add('error', 'Vous devez être connecté pour effectuer une réservation.');
            return $this->redirectToRoute('login');
        }

        $annonce = $this->getDoctrine()->getRepository(Annonce::class)->find($id);

        if (!$annonce) {
            $flashBag->add('error', 'Annonce non trouvée.');
            return $this->redirectToRoute('annonces');
        }

        $user = $this->getUser();

        // Vérifier si le conducteur de l'annonce est différent de l'utilisateur connecté
        if ($annonce->getConducteur() === $user) {
            $flashBag->add('error', 'Vous ne pouvez pas réserver votre propre annonce.');
            return $this->redirectToRoute('annonces');
        }

        // Vérifier s'il y a au moins une place disponible dans l'annonce
        if ($annonce->getNbplace() > 0) {
            dump($annonce->getNbplace());
            $reservation = new Reservation();
            $reservation->setPassager($user);
            $reservation->setAnnonce($annonce);
            $annonce->setNbplace($annonce->getNbplace() - 1);

            // Sauvegarde de la réservation en base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            // Ajouter un message flash de succès
            $flashBag->add('success', 'Réservation effectuée avec succès.');

            // Redirection vers la page de succès ou autre action souhaitée
            return $this->redirectToRoute('annonces');
        }else{
            $flashBag->add('error', 'Aucune place disponible pour cette annonce.');
            return $this->redirectToRoute('annonces');
        }


    }

    /**
     * @Route("/reservation/{id}/supprimer", name="supprimer_reservation")
     */
    public function supprimerReservation($id,Request $request, Reservation $reservation, FlashBagInterface $flashBag): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $flashBag->add('error', 'Vous devez être connecté pour supprimer une réservation.');
            return $this->redirectToRoute('login');
        }

        $user = $this->getUser();
        $annonce = $reservation->getAnnonce();

        // Vérifier si l'utilisateur est le passager de la réservation
        if ($reservation->getPassager() !== $user) {
            $flashBag->add('error', 'Vous n\'êtes pas autorisé à supprimer cette réservation.');
            return $this->redirectToRoute('annonces');
        }

        // Rétablir la place retirée lors de la réservation
        $annonce->setNbplace($annonce->getNbplace() + 1);

        // Supprimer la réservation de la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($reservation);
        $entityManager->flush();

        // Ajouter un message flash de succès
        $flashBag->add('success', 'Réservation supprimée avec succès.');

        // Redirection vers la page de succès ou autre action souhaitée
        return $this->redirectToRoute('annonces');
    }
    /**
     * @Route("/reservations", name="reservations")
     */
    public function consulterReservation(Request $request, FlashBagInterface $flashBag): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            $flashBag->add('error', 'Vous devez être connecté pour supprimer une réservation.');
            return $this->redirectToRoute('login');
        }

        $user = $this->getUser();

        return $this->render('reservation/reservations.html.twig', [
            'test' => 'test',
        ]);
    }


}