<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\MyContacts\PersonneRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class MyerpController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function app_index(): Response
    {
        if ($this->getUser()) {
            return $this->render('default/backend/index.html.twig', [
                'controller_name' => 'MyerpController',
            ]);
        }
        else {  
            return $this->redirectToRoute('app_login');        
        }
    }

    /**
     * @Route("/construction", name="app_construction")
     */
    public function app_construction(): Response
    {
        return $this->render('default/backend/construction.html.twig', [
            'controller_name' => 'MyerpController',
        ]);
    }

    /**
     * @Route("/configuration", name="app_configuration")
     */
    public function app_configuration(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('default/backend/configuration.html.twig', [
            'controller_name' => 'MyerpController',
            'tableUsers' => $users,
        ]);
    }

    /**
     * @Route("/configuration/profil", name="app_configuration_utilisateur_edit")
     */
    public function app_utilisateurEdit(PersonneRepository $personRepo, UserRepository $userRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $personnes = $personRepo->findAll();
        $listPersonnes = array();
        foreach ($personnes as $personne) {
            $listPersonnes[$personne->getNom() . ' ' . $personne->getPrenom()] = $personne->getId(); 
        }
        $form = $this->createFormBuilder($user)      
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('personne', ChoiceType::class, [
               'choices'  => $listPersonnes,
               'mapped'=>false,
               'attr' => [ 'class' => 'form-control']
           ])
            ->add('estActif', CheckboxType::class)
            ->getform()
        ;

        $form->handleRequest($requete);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_configuration');
        }

        return $this->render('default/backend/profil.html.twig', [
            'controller_name' => 'MyerpController',
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/configuration/utilisateur/del/{id<[0-9+]>}", name="app_configuration_utilisateur_del", methods ="DELETE")
     */
    public function utilisateurDelete(User $user, Request $requete, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_configuration');
    }


    /**
     * @Route("/calendrier", name="app_calendrier")
     */
    public function calendrier(): Response
    {
        return $this->render('default/backend/calendrier.html.twig', [
            'controller_name' => 'MyerpController',
        ]);
    }

}
