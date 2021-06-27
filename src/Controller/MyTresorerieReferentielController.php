<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\BanqueChoiceType;
use App\Form\PersonneChoiceType;
use App\Form\SocieteBanqueChoiceType;
use App\Entity\MyFinances\Banque;
use App\Entity\MyFinances\Categorie;
use App\Entity\MyFinances\Compte;
use App\Entity\MyFinances\ModePaiement;
use App\Entity\MyFinances\SousCategorie;
use App\Entity\MyFinances\TypeCompte;
use App\Repository\MyContacts\SocieteRepository;
use App\Repository\MyContacts\PersonneRepository;
use App\Repository\MyFinances\BanqueRepository;
use App\Repository\MyFinances\CategorieRepository;
use App\Repository\MyFinances\CompteRepository;
use App\Repository\MyFinances\ModePaiementRepository;
use App\Repository\MyFinances\SousCategorieRepository;
use App\Repository\MyFinances\TypeCompteRepository;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Form\CategorieChoiceType;
use App\Form\CompteChoiceType;
use App\Form\TypeCompteChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class MyTresorerieReferentielController extends AbstractController
{
    /**
     * @Route("/tresorerie/referentiel", name="app_myTresorerie_referentiel")
     */
    public function mytresorerie_referentiel(BanqueRepository $banqueRepo, CompteRepository $compteRepo,ModePaiementRepository $modePaiement,TypeCompteRepository $typeCompteRepo, CategorieRepository $categorieRepo): Response
    {
        $banques = $banqueRepo->findAll();
        $comptes = $compteRepo->findAll();
        $categories = $categorieRepo->findAll();
        $modesPaiement = $modePaiement->findAll();
        $typesCompte = $typeCompteRepo->findAll();

        return $this->render('default/backend/myTresorerie/referentiel.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'banques' => $banques,
            'comptes' => $comptes,
            'categories' => $categories,
            'modesPaiement' => $modesPaiement,
            'typesCompte' => $typesCompte,
        ]);
    }

    /**
     * @Route("/tresorerie/referentiel/banque/add", name="app_myTresorerie_referentiel_banque_add")
     * @Route("/tresorerie/referentiel/banque/edit/{id>}", name="app_myTresorerie_referentiel_banque_edit")
     */
    public function mytresorerie_referentiel_banque_form(?int $id,BanqueRepository $banqueRepo, SocieteRepository $societeRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $banque = new Banque();
        if (isset($id)) {
            $banque = $banqueRepo->find($id);
        }
        $form = $this->createFormBuilder($banque)
            ->add('societeID', SocieteBanqueChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => ($banque->getSociete()) ? $banque->getSociete()->getId() : ''
            ])
            ->add('guichet', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('codeBanque',IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('codeGuichet',IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            // On transforme l'id du select en société
            $banque->setSociete($societeRepo->find($requete->request->get('form')['societeID']));
            // ***************************************
            $em->persist($banque);
            $em->flush();

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }

        return $this->render('default/backend/myTresorerie/referentiel_banquesForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/referentiel/compte/add", name="app_myTresorerie_referentiel_compte_add")
     * @Route("/tresorerie/referentiel/compte/edit/{id}", name="app_myTresorerie_referentiel_compte_edit")
     */
    public function mytresorerie_referentiel_compte_form(?int $id,PersonneRepository $personneRepo, CompteRepository $compteRepo, BanqueRepository $banqueRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $compte = new Compte();
        if (isset($id)) {
            $compte = $compteRepo->find($id);
        }
        $form = $this->createFormBuilder($compte)
            ->add('banqueID', BanqueChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => ($compte->getBanque()) ? $compte->getBanque()->getId() : ''
            ])
            ->add('typeCompte', TypeCompteChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                //'data' => ($compte->getTypeCompte()) ? $compte->getTypeCompte()->getId() : ''
            ])
            ->add('titulaireID', PersonneChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => ($compte->getTitulaire()) ? $compte->getTitulaire()->getId() : ''
            ])
            ->add('cotitulaireID', PersonneChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => ($compte->getCotitulaire()) ? $compte->getCotitulaire()->getId() : ''
            ])
            ->add('libelle', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('numero',IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('soldeInitial', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => $compte->getSoldeInitial() > 0 ? $compte->getSoldeInitial() : 0.00
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            // On transforme les ID des différents <select> pour les associer à un objet que l'on enregistrera dans la base
                $compte->setBanque($banqueRepo->find($requete->request->get('form')['banqueID']));
                if ($requete->request->get('form')['titulaireID'] > 0) {
                    $compte->setTitulaire($personneRepo->find($requete->request->get('form')['titulaireID']));
                }
                if ($requete->request->get('form')['cotitulaireID'] > 0) {
                    $compte->setCotitulaire($personneRepo->find($requete->request->get('form')['cotitulaireID']));
                }
            // ***************************************
            $em->persist($compte);
            $em->flush();

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }

        return $this->render('default/backend/myTresorerie/referentiel_comptesForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/referentiel/categorie/add", name="app_myTresorerie_referentiel_categorie_add")
     * @Route("/tresorerie/referentiel/categorie/edit/{id}", name="app_myTresorerie_referentiel_categorie_edit")
     */
    public function mytresorerie_referentiel_categorie_form(?int $id,CategorieRepository $categorieRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        if (isset($id)) {
            $categorie = $categorieRepo->find($id);
        }
        $form = $this->createFormBuilder($categorie)
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }

        return $this->render('default/backend/myTresorerie/referentiel_categoriesForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/referentiel/sous_categorie/add", name="app_myTresorerie_referentiel_sous_categorie_add")
     * @Route("/tresorerie/referentiel/sous_categorie/edit/{id}", name="app_myTresorerie_referentiel_sous_categorie_edit")
     */
    public function mytresorerie_referentiel_souscategorie_form(?int $id,SousCategorieRepository $sousCategorieRepo, CategorieRepository $categorieRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $sousCategorie = new SousCategorie();
        if (isset($id)) {
            $sousCategorie = $sousCategorieRepo->find($id);
        }
        $form = $this->createFormBuilder($sousCategorie)
            ->add('categorieID', CategorieChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorie = $categorieRepo->findBy(['id' => $requete->request->get('form')['categorieID'] ]);
            $sousCategorie->setCategorie($categorie[0]);

            $em->persist($sousCategorie);
            $em->flush();

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }

        return $this->render('default/backend/myTresorerie/referentiel_souscategoriesForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/referentiel/type_compte/add", name="app_myTresorerie_referentiel_type_compte_add")
     * @Route("/tresorerie/referentiel/type_compte/edit/{id}", name="app_myTresorerie_referentiel_type_compte_edit")
     */
    public function mytresorerie_referentiel_typeCompte_form(?int $id, TypeCompteRepository $typeCompteRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $typeCompte = new TypeCompte();
        if (isset($id)) {
            $typeCompte = $typeCompteRepo->find($id);
        }
        $form = $this->createFormBuilder($typeCompte)
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('retraitOK', CheckboxType::class, [
                'required' => false,
            ])
            ->add('depotChequeOK', CheckboxType::class, [
                'required' => false,
            ])
            ->add('carteOK', CheckboxType::class, [
                'required' => false,
            ])
            ->add('autreModePaiementOK', CheckboxType::class, [
                'required' => false,
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($typeCompte);
            $em->flush();

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }

        return $this->render('default/backend/myTresorerie/referentiel_typeCompteForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/referentiel/mode_paiement/add", name="app_myTresorerie_referentiel_mode_paiement_add")
     * @Route("/tresorerie/referentiel/mode_paiement/edit/{id}", name="app_myTresorerie_referentiel_mode_paiement_edit")
     */
    public function mytresorerie_referentiel_modePaiement_form(?int $id,CompteRepository $compteRepo, PersonneRepository $personneRepo, ModePaiementRepository $modePaiementeRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $modePaiement = new ModePaiement();
        if (isset($id)) {
            $modePaiement = $modePaiementeRepo->find($id);
        }
        $form = $this->createFormBuilder($modePaiement)
            ->add('ModePaiement', ChoiceType::class, [
                'choices'  => [
                    'Carte bancaire' => 'CB',
                    'Chéquier' => 'CHQ',
                    'Paypal' => 'PAY',
                    ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('compteID',CompteChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
            ])
            ->add('titulaireID',PersonneChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $modePaiement->setTitulaire($personneRepo->find($requete->request->get('form')['titulaireID']));
            $modePaiement->setCompte($compteRepo->find($requete->request->get('form')['compteID']));

            $em->persist($modePaiement);
            $em->flush();

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }

        return $this->render('default/backend/myTresorerie/referentiel_modePaiementForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/referentiel/banque/del/{id<[0-9+]>}", name="app_myTresorerie_referentiel_banques_del", methods ="DELETE")
     */
    public function mytresorerie_referentiel_banqueDelete(Banque $banque, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('banque_supprime_' . $banque->getId(), $requete->request->get('csrf_token'))) {
            try {
                $em->remove($banque);
                $em->flush();           
            
                $this->addFlash("successMSG", "Enregistrement supprimé");
            } catch (\Exception $e) {
                $this->addFlash("errorMSG", "Suppression impossible - La banque est référencée dans un autre module. Procédez à un archivage à la place");
            }

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }
}
