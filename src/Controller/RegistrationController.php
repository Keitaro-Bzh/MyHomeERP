<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/configuration/utilisateur/add", name="app_configuration_utilisateur_add")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_configuration');
        }

        return $this->render('default/backend/utilisateur.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/configuration/password/reset", name="app_configuration_password_reset")
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
 
        $form = $this->createFormBuilder()
            ->add('ancienMDP', PasswordType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('nouveauMDP', PasswordType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
        ->getform()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($passwordEncoder->isPasswordValid($user,$form->get('ancienMDP')->getData())) {
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                $this->addFlash("userUpdate", "Votre mot de passe a été mis à jour");
                return $this->redirectToRoute('app_configuration_utilisateur_edit');
            }
            else {
                $this->addFlash("erreurPassword", "Votre ancien mot de passe ne correspond pas");
                return $this->redirectToRoute('app_configuration_password_reset');
            }
        }

        return $this->render('default/backend/password_reset.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
