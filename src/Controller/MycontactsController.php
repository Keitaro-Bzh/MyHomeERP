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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MyContactsController extends AbstractController
{
    /**
     * @Route("/contacts", name="app_mycontacts")
     */
    public function app_mycontacts(PersonneRepository $personneRepo,SocieteRepository $societeRepo): Response
    {
        $personnes = $personneRepo->findActif();
        $societes = $societeRepo->findActif();
        return $this->render('default/backend/myContacts/myContacts.html.twig', [
            'controller_name' => 'MyHomeERPController',
            'tablePersonnes' => $personnes,
            'tableSocietes' => $societes
        ]);
    }


    /**
     * @Route("/personnes/ajout", name="app_personnes_ajout")
     * @Route("/personnes/edit/{id<[0-9+]>}", name="app_personnes_edit")
     */
    public function personneForm(?int $id,PersonneRepository $personneRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $personne = new Personne();
        if (isset($id)) {
            $personne = $personneRepo->find($id);
        }

        $form = $this->createFormBuilder($personne)      
            ->add('imagePhoto', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Effacer',
                'imagine_pattern' => 'personne_image_photo',
                'download_uri' => false,
            ])
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
            ->add('libelle_adresse', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('code_postal', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('ville', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'required' => false,
            ])
            ->add('archive', CheckboxType::class, [
                'required' => false
            ])
            ->getform()
        ;
        
        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($personne);
            $em->flush();

            $this->addFlash("successMSG", "Enregistrement effectué");
            return $this->redirectToRoute('app_mycontacts');
        }

        return $this->render('default/backend/myContacts/personnesForm.html.twig', [
            'controller_name' => 'MyHomeERPController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/societes/ajout", name="app_societes_ajout")
     * @Route("/societes/{id<[0-9+]>}/edit", name="app_societes_edit", methods={"GET", "POST"})
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
            ->add('libelle_adresse', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('code_postal', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('ville', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('archive', CheckboxType::class, [
                'attr' => ['data-plugin-ios-switch'],
                'required' => false,
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($societe);
            $em->flush();

            $this->addFlash("successMSG", "Enregistrement effectué");
            return $this->redirectToRoute('app_mycontacts');
        }

        return $this->render('default/backend/myContacts/societesForm.html.twig', [
            'controller_name' => 'MyHomeERPController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/personnes/del/{id<[0-9+]>}", name="app_personnes_del", methods ="DELETE")
     */
    public function personneDelete(Personne $personne, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('personne_supprime_' . $personne->getId(), $requete->request->get('csrf_token'))) {
            try {
                $em->remove($personne);
                $em->flush();           
            
                $this->addFlash("successMSG", "Enregistrement supprimé");
            } catch (\Exception $e) {
                $this->addFlash("errorMSG", "Suppression impossible - La personne est référencée dans un autre module. Procédez à un archivage à la place");
            }

            return $this->redirectToRoute('app_mycontacts');
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }

    /**
     * @Route("/societes/{id<[0-9+]>}/del", name="app_societes_del", methods ="DELETE")
     */
    public function societeDelete(Societe $societe, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('societe_supprime_' . $societe->getId(), $requete->request->get('csrf_token'))) {
            try {
                $em->remove($societe);
                $em->flush();           
            
                $this->addFlash("successMSG", "Enregistrement supprimé");
            } catch (\Exception $e) {
                $this->addFlash("errorMSG", "Suppression impossible - La société est référencée dans un autre module. Procédez à un archivage à la place");
            }
            
            return $this->redirectToRoute('app_mycontacts');
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }

}
