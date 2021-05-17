<?php

namespace App\Controller;

use App\Entity\MyContacts\Personne;
use App\Entity\MyContacts\Societe;
use App\Repository\MyContacts\PersonneRepository;
use App\Repository\MyContacts\SocieteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MycontactsController extends AbstractController
{
    /**
     * @Route("/contacts", name="app_mycontacts")
     */
    public function app_mycontacts(PersonneRepository $personneRepo,SocieteRepository $societeRepo): Response
    {
        $personnes = $personneRepo->findAll();
        $societes = $societeRepo->findAll();
        return $this->render('default/backend/myContacts/myContacts.html.twig', [
            'controller_name' => 'MyerpController',
            'tablePersonnes' => $personnes,
            'tableSocietes' => $societes
        ]);
    }


    /**
     * @Route("/personnes/ajout", name="app_personnes_ajout")
     * @Route("/personnes/edit/{id<[0-9+]>}", name="app_personnes_edit")
     * @Route("/personnes/del/{id<[0-9+]>}", name="app_personnes_del")
     */
    public function personneForm(?int $id,PersonneRepository $personneRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $personne = new Personne();
        if (isset($id)) {
            $personne = $personneRepo->find($id);
        }

        $form = $this->createFormBuilder($personne)
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('telephone', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('dateNaissance', DateType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('estActif', CheckboxType::class, [
                'required' => false,
                'data' => true
            ])
            ->getform()
        ;
        
        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($personne);
            $em->flush();

            return $this->redirectToRoute('app_mycontacts');
        }

        return $this->render('default/backend/myContacts/personnesForm.html.twig', [
            'controller_name' => 'MyerpController',
            'form' => $form->createView()
        ]);
    }

        /**
     * @Route("/societes/ajout", name="app_societes_ajout")
     * @Route("/societes/{id<[0-9+]>}/edit", name="app_societes_edit")
     */
    public function societeForm(?int $id,SocieteRepository $societeRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $societe = new Societe();
        if (isset($id)) {
            $societe = $societeRepo->find($id);
        }
        $form = $this->createFormBuilder($societe)      
            ->add('imageLogo', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Effacer',
                'imagine_pattern' => 'societe_image_logo'
            ])
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('telephone', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('url', UrlType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('estBanque', CheckboxType::class, [
                'attr' => ['data-plugin-ios-switch'],
                'required' => false
            ])
            ->add('estActif', CheckboxType::class, [
                'attr' => ['data-plugin-ios-switch'],
                'required' => false,
                'data' => true
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($societe);
            $em->flush();

            return $this->redirectToRoute('app_mycontacts');
        }

        return $this->render('default/backend/myContacts/societesForm.html.twig', [
            'controller_name' => 'MyerpController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/societes/{id<[0-9+]>}/del", name="app_societes_del", methods ="DELETE")
     */
    public function societeDelete(Societe $societe, Request $requete, EntityManagerInterface $em): Response
    {
        $em->remove($societe);
        $em->flush();

        return $this->redirectToRoute('app_mycontacts');
    }

}
