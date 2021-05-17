<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\BanqueChoiceType;
use App\Form\PersonneChoiceType;
use App\Form\SocieteBanqueChoiceType;
use App\Entity\MyFinances\Banque;
use App\Entity\MyFinances\Categorie;
use App\Entity\MyFinances\Compte;
use App\Entity\MyFinances\SousCategorie;
use App\Entity\MyFinances\TypeCompte;
use App\Form\CategorieChoiceType;
use App\Repository\MyContacts\SocieteRepository;
use App\Repository\MyContacts\PersonneRepository;
use App\Repository\MyFinances\BanqueRepository;
use App\Repository\MyFinances\CategorieRepository;
use App\Repository\MyFinances\CompteRepository;
use App\Repository\MyFinances\ModePaiementRepository;
use App\Repository\MyFinances\SousCategorieRepository;
use App\Repository\MyFinances\TypeCompteRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class MytresorerieController extends AbstractController
{
    /**
     * @Route("/tresorerie", name="app_myTresorerie_index")
     */
    public function mytresorerie_index(): Response
    {
        return $this->render('default/backend/myTresorerie/mytresorerie.html.twig', [
            'controller_name' => 'MytresorerieController',
        ]);
    }

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
            'controller_name' => 'MytresorerieController',
            'banques' => $banques,
            'comptes' => $comptes,
            'categories' => $categories,
            'modesPaiement' => $modesPaiement,
            'typesCompte' => $typesCompte,
        ]);
    }

    /**
     * @Route("/tresorerie/comptes/liste", name="app_myTresorerie_compte_liste")
     */
    public function mytresorerie_compte(): Response
    {
        return $this->render('default/backend/myTresorerie/compte.html.twig', [
            'controller_name' => 'MytresorerieController',
        ]);
    }

    /**
     * @Route("/tresorerie/banque/add", name="app_myTresorerie_referentiel_banque_add")
     * @Route("/tresorerie/banque/edit/{id<[0-9+]>}", name="app_myTresorerie_referentiel_banque_edit")
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
            $societe = $societeRepo->findBy(['id' => $requete->request->get('form')['societeID'] ]);
            $banque->setSociete($societe[0]);
            // ***************************************
            $em->persist($banque);
            $em->flush();

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }

        return $this->render('default/backend/myTresorerie/banque.html.twig', [
            'controller_name' => 'MytresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/compte/add", name="app_myTresorerie_referentiel_compte_add")
     * @Route("/tresorerie/compte/edit/{id<[0-9+]>}", name="app_myTresorerie_referentiel_compte_edit")
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
            ])
            ->add('typeCompte', ChoiceType::class, [
                'choices'  => [
                    'Courant' => 'C',
                    'Epargne' => 'E',
                    'Titre' => 'T',
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('titulaireID', PersonneChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('cotitulaireID', PersonneChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('libelle', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('numero',IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('soldeInitial',IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            // On transforme les ID des différents <select> pour les associer à un objet que l'on enregistrera dans la base
                $banque = $banqueRepo->findBy(['id' => $requete->request->get('form')['banqueID'] ]);
                $compte->setBanque($banque[0]);
                if ((int)['id' => $requete->request->get('form')['titulaireID'] ]) {
                    $personne = $personneRepo->findBy(['id' => $requete->request->get('form')['titulaireID'] ]);
                    $compte->setTitulaire($personne[0]);
                }
                if ((int)['id' => $requete->request->get('form')['cotitulaireID'] ]) {
                    $personne = $personneRepo->findBy(['id' => $requete->request->get('form')['cotitulaireID'] ]);
                    $compte->setCotitulaire($personne[0]);
                }
            // ***************************************
            $em->persist($compte);
            $em->flush();

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }

        return $this->render('default/backend/myTresorerie/referentiel_comptesForm.html.twig', [
            'controller_name' => 'MytresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/categorie/add", name="app_myTresorerie_referentiel_categorie_add")
     * @Route("/tresorerie/categorie/edit/{id<[0-9+]>}", name="app_myTresorerie_referentiel_categorie_edit")
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
            'controller_name' => 'MytresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/sous_categorie/add", name="app_myTresorerie_referentiel_sous_categorie_add")
     * @Route("/tresorerie/sous_categorie/edit/{id<[0-9+]>}", name="app_myTresorerie_referentiel_sous_categorie_edit")
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
            // On transforme les ID des différents <select> pour les associer à un objet que l'on enregistrera dans la base
            $categorie = $categorieRepo->findBy(['id' => $requete->request->get('form')['categorieID'] ]);
            $sousCategorie->setCategorie($categorie[0]);
        // ***************************************
            $em->persist($sousCategorie);
            $em->flush();

            return $this->redirectToRoute('app_myTresorerie_referentiel');
        }

        return $this->render('default/backend/myTresorerie/referentiel_souscategoriesForm.html.twig', [
            'controller_name' => 'MytresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/type_compte/add", name="app_myTresorerie_referentiel_type_compte_add")
     * @Route("/tresorerie/type_compte/edit/{id<[0-9+]>}", name="app_myTresorerie_referentiel_type_compte_edit")
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
            'controller_name' => 'MytresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/operation", name="app_myTresorerie_operation")
     */
    public function mytresorerie_operation(): Response
    {
        return $this->render('default/backend/myTresorerie/operation.html.twig', [
            'controller_name' => 'MytresorerieController',
        ]);
    }

    /**
     * @Route("/tresorerie/virement", name="app_myTresorerie_virement")
     */
    public function mytresorerie_virement(): Response
    {
        return $this->render('default/backend/myTresorerie/virement.html.twig', [
            'controller_name' => 'MytresorerieController',
        ]);
    }

    /**
     * @Route("/tresorerie/retrait", name="app_myTresorerie_retrait")
     */
    public function mytresorerie_retrait(): Response
    {
        return $this->render('default/backend/myTresorerie/retrait.html.twig', [
            'controller_name' => 'MytresorerieController',
        ]);
    }
}
