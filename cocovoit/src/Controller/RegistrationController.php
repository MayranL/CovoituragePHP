<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConnexionType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword($form->get('password')->getData());
            $user->hashPassword();
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('utilisateur/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        UserPasswordEncoderInterface $userPasswordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        UserRepository $userRepository
    ): Response {
        $user = new User();
        $form = $this->createForm(ConnexionType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Récupérer l'utilisateur à partir de l'email
            $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);

            if ($existingUser) {
                if (password_verify($form->get('password')->getData(),$existingUser->getPassword())){
                    return $guardHandler->authenticateUserAndHandleSuccess(
                        $existingUser,
                        $request,
                        $authenticator,
                        'main'
                    );
                }else{
                    dump('coucou');
                }

            }

            // Si la connexion échoue, ajoutez un message d'erreur
            $this->addFlash('error', 'Identifiants de connexion invalides.');
        }

        return $this->render('utilisateur/connexion.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/deconnexion", name="deconnexion")
     */
    public function deconnexion(AuthenticationUtils $authenticationUtils): Response
    {
        $this->get('security.token_storage')->setToken(null);
        $this->get('session')->invalidate();

        return $this->redirectToRoute('connexion');
    }

}
