<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Commentaire;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create();
        $admin = new User();
        $admin->setEmail('admin@admin.fr');
        $admin->setNom('admin');
        $admin->setPrenom('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('toto');
        $admin->hashPassword();
        $admin->setCreatedAt($faker->dateTimeThisYear);
        $admin->setNote(5);
        $manager->persist($admin);
        $manager->flush();
        $utilisateur = new User();
        $utilisateur->setEmail('utilisateur@utilisateur.fr');
        $utilisateur->setNom('utilisateur');
        $utilisateur->setPrenom('utilisateur');
        $utilisateur->setRoles(['ROLE_USER']);
        $utilisateur->setPassword('toto');
        $utilisateur->hashPassword();
        $utilisateur->setCreatedAt($faker->dateTimeThisYear);
        $utilisateur->setNote(5);
        $manager->persist($utilisateur);
        $manager->flush();
        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstName);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword('toto');
            $user->hashPassword();
            $user->setCreatedAt($faker->dateTimeThisYear);
            $user->setNote($faker->randomFloat(2, 0, 5));
            $manager->persist($user);
            $this->addReference('user-'.$i, $user);
        }
        $manager->flush();
        for ($j = 0; $j < 50; $j++) {
            for ($k = 0; $k < 8; $k++){
                $annonce = new Annonce();
                $annonce->setConducteur($this->getReference('user-'.$j));
                $annonce->setVilleDepart($faker->randomElement([
                    'Paris',
                    'Marseille',
                    'Lyon',
                    'Toulouse',
                    'Nice',
                    'Nantes',
                    'Strasbourg',
                    'Montpellier',
                    'Bordeaux',
                    'Lille',
                    'Rennes',
                    'Reims',
                    'Le Havre',
                    'Cergy-Pontoise',
                    'Saint-Étienne',
                    'Toulon',
                    'Angers',
                    'Grenoble',
                    'Dijon',
                    'Nîmes',
                    'Aix-en-Provence',
                    'Saint-Quentin-en-Yvelines',
                    'Brest',
                    'Le Mans',
                    'Amiens',
                    'Tours',
                    'Limoges',
                    'Clermont-Ferrand',
                    'Villeurbanne',
                    'Besançon',
                    'Orléans',
                    'Metz',
                    'Rouen',
                    'Mulhouse',
                    'Perpignan',
                    'Caen',
                    'Nancy',
                    'Saint-Denis',
                    'Argenteuil',
                    'Saint-Paul',
                    'Montreuil',
                    'Roubaix',
                    'Tourcoing',
                    'Nanterre',
                    'Avignon',
                    'Vitry-sur-Seine',
                    'Créteil',
                    'Dunkerque',
                    'Poitiers',
                    'Asnières-sur-Seine',
                    'Courbevoie',
                    'Versailles',
                    'Colombes',
                    'Fort-de-France',
                    'Aulnay-sous-Bois',
                    'Saint-Pierre',
                    'Rueil-Malmaison',
                    'Pau',
                    'Aubervilliers',
                    'Le Tampon',
                    'Champigny-sur-Marne',
                    'Antibes',
                    'Béziers',
                    'La Rochelle',
                    'Saint-Maur-des-Fossés',
                    'Cannes',
                    'Calais',
                    'Cergy',
                    'Ajaccio',
                    'Levallois-Perret',
                    'Bourg-en-Bresse',
                    'Toulon',
                    'Saint-Tropez',
                    'Grasse',
                    'Martigues',
                    'Les Sables-d\'Olonne',
                    'Saint-Malo',
                    'La Roche-sur-Yon',
                    'Albi',
                    'Saint-Nazaire',
                    'Mâcon',
                    'Chambéry',
                    'Vannes',
                    'Bayonne',
                    'Saint-Quentin',
                    'Épinay-sur-Seine',
                    'Bondy',
                    'Clichy',
                    'Fréjus',
                    'Arles',
                    'Sète',
                    'Pessac',
                    'Ivry-sur-Seine',
                    'Saint-Ouen',
                    'Livry-Gargan',
                    'Noisy-le-Grand',
                    'La Seyne-sur-Mer',
                    'Le Blanc-Mesnil',
                    'Valence',
                    'Cergy',
                    'Garges-lès-Gonesse',
                    'Montluçon',
                    'Mantes-la-Jolie',
                    'Vénissieux',
                    'Talence',
                    'Cholet',
                    'Clamart',
                    'Niort',
                    'Sarcelles',
                    'Saint-André',
                    'Fontenay-sous-Bois',
                    'La Courneuve',
                    'Brive-la-Gaillarde']));
                $annonce->setVilleArrive($faker->randomElement([
                    'Paris',
                    'Marseille',
                    'Lyon',
                    'Toulouse',
                    'Nice',
                    'Nantes',
                    'Strasbourg',
                    'Montpellier',
                    'Bordeaux',
                    'Lille',
                    'Rennes',
                    'Reims',
                    'Le Havre',
                    'Cergy-Pontoise',
                    'Saint-Étienne',
                    'Toulon',
                    'Angers',
                    'Grenoble',
                    'Dijon',
                    'Nîmes',
                    'Aix-en-Provence',
                    'Saint-Quentin-en-Yvelines',
                    'Brest',
                    'Le Mans',
                    'Amiens',
                    'Tours',
                    'Limoges',
                    'Clermont-Ferrand',
                    'Villeurbanne',
                    'Besançon',
                    'Orléans',
                    'Metz',
                    'Rouen',
                    'Mulhouse',
                    'Perpignan',
                    'Caen',
                    'Nancy',
                    'Saint-Denis',
                    'Argenteuil',
                    'Saint-Paul',
                    'Montreuil',
                    'Roubaix',
                    'Tourcoing',
                    'Nanterre',
                    'Avignon',
                    'Vitry-sur-Seine',
                    'Créteil',
                    'Dunkerque',
                    'Poitiers',
                    'Asnières-sur-Seine',
                    'Courbevoie',
                    'Versailles',
                    'Colombes',
                    'Fort-de-France',
                    'Aulnay-sous-Bois',
                    'Saint-Pierre',
                    'Rueil-Malmaison',
                    'Pau',
                    'Aubervilliers',
                    'Le Tampon',
                    'Champigny-sur-Marne',
                    'Antibes',
                    'Béziers',
                    'La Rochelle',
                    'Sevran',
                    'Saint-Maur-des-Fossés',
                    'Cannes',
                    'Calais',
                    'Cergy',
                    'Ajaccio',
                    'Levallois-Perret',
                    'Bourg-en-Bresse',
                    'Toulon',
                    'Saint-Tropez',
                    'Grasse',
                    'Martigues',
                    'Les Sables-d\'Olonne',
                    'Saint-Malo',
                    'La Roche-sur-Yon',
                    'Albi',
                    'Saint-Nazaire',
                    'Mâcon',
                    'Chambéry',
                    'Vannes',
                    'Bayonne',
                    'Saint-Quentin',
                    'Épinay-sur-Seine',
                    'Bondy',
                    'Clichy',
                    'Fréjus',
                    'Arles',
                    'Sète',
                    'Pessac',
                    'Ivry-sur-Seine',
                    'Saint-Ouen',
                    'Livry-Gargan',
                    'Noisy-le-Grand',
                    'La Seyne-sur-Mer',
                    'Le Blanc-Mesnil',
                    'Valence',
                    'Cergy',
                    'Garges-lès-Gonesse',
                    'Montluçon',
                    'Mantes-la-Jolie',
                    'Vénissieux',
                    'Talence',
                    'Cholet',
                    'Clamart',
                    'Niort',
                    'Sarcelles',
                    'Saint-André',
                    'Fontenay-sous-Bois',
                    'La Courneuve',
                    'Brive-la-Gaillarde']));
                $annonce->setPrix($faker->randomFloat(2, 10, 100));
                $annonce->setModeleV($faker->randomElement([
                    'Audi A4',
                    'BMW M5',
                    'Chevrolet Camaro',
                    'Dodge Challenger',
                    'Ford Mustang',
                    'Honda Civic',
                    'Hyundai Elantra',
                    'Jaguar F-Type',
                    'Kia Optima',
                    'Lamborghini Aventador',
                    'Mercedes-Benz C-Class',
                    'Nissan Altima',
                    'Porsche 911',
                    'Subaru Impreza',
                    'Toyota Camry',
                    'Volkswagen Golf',
                    'Aston Martin DB11',
                    'Bentley Continental GT',
                    'Cadillac Escalade',
                    'Ferrari 488 GTB',
                    'GMC Sierra',
                    'Infiniti Q50',
                    'Jeep Wrangler',
                    'Kia Sorento',
                    'Land Rover Range Rover',
                    'Maserati Ghibli',
                    'Nissan Rogue',
                    'Porsche Cayenne',
                    'Rolls-Royce Phantom',
                    'Tesla Model S',
                    'Audi Q7',
                    'BMW X5',
                    'Chevrolet Tahoe',
                    'Ford Explorer',
                    'Honda CR-V',
                    'Jaguar F-Pace',
                    'Kia Sportage',
                    'Lexus RX',
                    'Mercedes-Benz GLE',
                    'Nissan Pathfinder',
                    'Porsche Macan',
                    'Subaru Forester',
                    'Toyota RAV4',
                    'Volkswagen Tiguan',
                    'Alfa Romeo Giulia',
                    'BMW i3',
                    'Dodge Durango',
                    'Ford F-150',
                    'Honda Accord',
                    'Jeep Grand Cherokee'
                ]));
                $annonce->setNbplace($faker->numberBetween(1, 4));
                $annonce->setDate($faker->dateTimeBetween('-1 year', '+6 month'));
                $this->addReference('annonce-'.$j.'-'.$k, $annonce);

                // Enregistrez l'annonce dans la base de données
                $manager->persist($annonce);
                $manager->flush();
            }

        }

        for ($i = 0; $i < 50; $i++){
            for ($j = 0; $j < 5; $j++){
                for ($k = 0; $k < 3; $k++) {
                    if($this->getReference('annonce-' . $i . '-' . $j)->getNbplace()>0){
                        $reservation = new Reservation();
                        $reservation->setAnnonce($this->getReference('annonce-' . $i . '-' . $j));
                        $passenger = $this->getReference('user-' . $faker->numberBetween(0, 49));
                        $reservation->setPassager($passenger);
                        while ($reservation->getAnnonce()->getConducteur() == $passenger){
                            $reservation->setPassager($passenger);
                            $passenger = $this->getReference('user-' . $faker->numberBetween(0, 49));
                        }
                        $annonce = $this->getReference('annonce-' . $i . '-' . $j);
                        $annonce->setNbplace($annonce->getNbplace()-1);
                        $manager->persist($reservation);
                        $manager->flush();
                        if($this->getReference('annonce-' . $i . '-' . $j)->getDate()<new \DateTime()){
                            $annonce = $this->getReference('annonce-' . $i . '-' . $j);
                            $author = $reservation->getPassager();
                            $commentaire = New Commentaire();
                            $commentaire->setAnnonce($annonce);
                            $commentaire->setAuteur($author);
                            $commentaire->setContenu($faker->randomElement([
                                "Super trajet, le conducteur était très sympathique !",
                                "Très bonne expérience, je recommande vivement !",
                                "Le trajet s'est bien passé, le conducteur était ponctuel.",
                                "J'ai apprécié le confort du véhicule, merci au conducteur !",
                                "Excellent service, je réserverai à nouveau sans hésiter.",
                                "Le conducteur était très professionnel et courtois.",
                                "Très bon rapport qualité-prix, je suis satisfait de ma réservation.",
                                "Une expérience agréable, je recommanderai cette annonce à mes amis.",
                                "Le conducteur était très attentionné et a assuré un trajet sécurisé.",
                                "Je suis enchanté par ce trajet, tout s'est parfaitement déroulé."
                            ]));
                            $manager->persist($commentaire);
                            $manager->flush();
                        }
                    }


                }
            }
        }

    }
}
