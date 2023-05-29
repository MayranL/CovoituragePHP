# Cocovoit

Ceci est le README pour l'application Cocovoit.

## Configuration requise

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- PHP (version 7.4 ou supérieure)
- Composer (https://getcomposer.org/download/)
- MySQL (version 8.0.31 ou supérieure)

De notre côté, nous utilisons WAMP 3.0.0 qui propose ces versions.

## Installation

Suivez les étapes ci-dessous pour installer et configurer l'application :

1. Clonez ce référentiel sur votre machine locale :
<pre>
git clone https://github.com/MayranL/CovoituragePHP.git
</pre>
2. Accédez au répertoire du projet :
<pre>
cd cocovoit
</pre>
3. Installez les dépendances à l'aide de Composer :
<pre>
composer install
</pre>
4. Modifier le fichier `.env` à la racine du projet en vous assurant de configurer les variables d'environnement appropriées, telles que la connexion à la base de données : <pre>DATABASE_URL="mysql://root:@127.0.0.1:3306/cocovoit?serverVersion=8&charset=utf8mb4"</pre>
5. Créez la base de données en utilisant la commande suivante :
<pre>
php bin/console doctrine:database:create
</pre>
6. Appliquez les migrations pour créer les tables de base de données :
<pre>
php bin/console doctrine:migrations:migrate
</pre>
7. (Facultatif) Chargez les données de démonstration si nécessaire :
<pre>
php bin/console doctrine:fixtures:load
</pre>
8. Démarrez le serveur de développement :
<pre>
php -S localhost:8000 -t public
</pre>
L'application devrait maintenant être accessible à l'adresse `http://localhost:8000`.

## Utilisation

Voici quelques points importants à connaître pour utiliser l'application :

- Accédez à `http://localhost:8000` dans votre navigateur pour accéder à l'application.
- Connectez-vous à l'aide des informations d'identification appropriées.
- Explorez les différentes fonctionnalités de l'application.
- Pour arrêter le serveur de développement faites CTRL+C

Vous avez un utilisateur admin : 
- Adresse email de l'admin : <pre>admin@admin.fr</pre>
- Mot de passe de l'admin : <pre>toto</pre>

Vous avez un utilisateur :
- Adresse email de l'admin : <pre>utilisateur@utilisateur.fr</pre>
- Mot de passe de l'admin : <pre>toto</pre>
  















