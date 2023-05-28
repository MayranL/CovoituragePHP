# Mon Application

Ceci est le README pour l'application Mon Application.

## Configuration requise

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- PHP (version 7.4 ou supérieure)
- Composer
- MySQL (ou un autre système de gestion de base de données)

## Installation

Suivez les étapes ci-dessous pour installer et configurer l'application :

1. Clonez ce référentiel sur votre machine locale :
git clone https://github.com/MayranL/CovoituragePHP.git
2. Accédez au répertoire du projet :
cd cocovoit
3. Installez les dépendances à l'aide de Composer :
composer install
4. Modifier le fichier `.env` à la racine du projet en vous assurant de configurer les variables d'environnement appropriées, telles que la connexion à la base de données : <pre>DATABASE_URL="mysql://root:@127.0.0.1:3306/cocovoit?serverVersion=8&charset=utf8mb4"</pre>
5. Créez la base de données en utilisant la commande suivante :
php bin/console doctrine:database:create
6. Appliquez les migrations pour créer les tables de base de données :
php bin/console doctrine:migrations:migrate
7. (Facultatif) Chargez les données de démonstration si nécessaire :
php bin/console doctrine:fixtures:load
8. Démarrez le serveur de développement :
php -S localhost:8000 -t public

L'application devrait maintenant être accessible à l'adresse `http://localhost:8000`.

## Utilisation

Voici quelques points importants à connaître pour utiliser l'application :

- Accédez à `http://localhost:8000` dans votre navigateur pour accéder à l'application.
- Connectez-vous à l'aide des informations d'identification appropriées.
- Explorez les différentes fonctionnalités de l'application.
- Pour arrêter le serveur de développement faites CTRL+C














