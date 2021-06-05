<?php

namespace App\Controller;

use App\Entity\MyFinances\Echeance;
use App\Entity\MyFinances\EcheanceOperation;
use App\Entity\MyFinances\Operation;
use App\Form\CompteChoiceType;
use App\Form\ModePaiementChoiceType;
use App\Form\PersonneChoiceType;
use App\Form\SocieteBanqueChoiceType;
use App\Form\SousCategorieChoiceType;
use App\Repository\MyContacts\PersonneRepository;
use App\Repository\MyContacts\SocieteRepository;
use App\Repository\MyFinances\CompteRepository;
use App\Repository\MyFinances\EcheanceRepository;
use App\Repository\MyFinances\ModePaiementRepository;
use App\Repository\MyFinances\SousCategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MyTresorerieEcheanceController extends AbstractController
{
    /**
     * @Route("/mytresorerie/echeance", name="app_myTresorerie_echeance")
     */
    public function index(EcheanceRepository $echeanceRepo): Response
    {
        return $this->render('/default/backend/myTresorerie//echeances.html.twig', [
            'controller_name' => 'MyTresorerieEcheanceController',
            'echeances' => $echeanceRepo->findAll()
        ]);
    }

    /**
     * @Route("/tresorerie/echeance/debit/add", name="app_myTresorerie_echeance_debit_add")
     * @Route("/tresorerie/echeance/debit/edit/{id}", name="app_myTresorerie_echeance_debit_edit", methods ={"GET","POST"})
     */
    public function mytresorerie_echeancier_debit_form(?int $id,SousCategorieRepository $sousCategorieRepo, CompteRepository $compteRepo, ModePaiementRepository $modePaiementRepo, SocieteRepository $societeRepo, PersonneRepository $personneRepo, EcheanceRepository $echeanceRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $echeance = new Echeance;
        if (isset($id)) {
            $echeance = $echeanceRepo->find($id);
        }
        
        $form = $this->createFormBuilder($echeance)
            ->add('dateEcheanceOne',DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $echeance->getDateEcheanceOne() ? $echeance->getDateEcheanceOne() : new DateTime()
            ])
            ->add('type_operation', HiddenType::class, [
                'data' => 'DEB',
            ])
            ->add('compteID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getCompte() ? $echeance->getCompte()->getId() : -1
            ])
            ->add('tiers_libelle',TextType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getTiersLibelle() ? $echeance->getTiersLibelle() : null,
                'required' => false,
            ])
            ->add('personneID',PersonneChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getTiersPersonne() ? $echeance->getTiersPersonne()->getId() : -1
            ])
            ->add('societeID', SocieteBanqueChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getTiersSociete() ? $echeance->getTiersSociete()->getId() : -1
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => ($echeance->getDescription()) ? $echeance->getDescription() : null
            ])
            ->add('montant_total', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => ($echeance->getMontantTotal()) ? $echeance->getMontantTotal() : null
            ])
            ->add('modePaiementID', ModePaiementChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                //'data' => $operation->getModePaiement() ?  $operation->getModePaiement()->getId() : ($operation->getModePaiementTrigramme() ?  : null)
            ])
            ->add('nombre_echeances', ChoiceType::class, array(
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
                'choices' => array(
                    '--- Sélectionnez la fréquence ' => '-1',
                    'Paiement 3x' => '3',
                    'Paiement 4x' => '4',
                    'Paiement 10x' => '10',
                    'Permament' => '0',
                ),
                'data' => $echeance->getTypeTiers() ? $echeance->getTypeTiers() : 'L'
            ))
            ->add('sousCategorieID', SousCategorieChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
                'data' => $echeance->getSousCategorie() ?  $echeance->getSousCategorie()->getId() : null
            ])
            ->add('est_solde', CheckboxType::class, [
                'data' => $echeance->getEstSolde() ? true : false,
                'required' => false
            ])
            ->add('type_tiers', ChoiceType::class, array(
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
                'choices' => array(
                    'Saisie libre ' => 'L',
                    '[CA] Société' => 'S',
                    '[CA] Personne' => 'P'
                ),
                'data' => $echeance->getTypeTiers() ? $echeance->getTypeTiers() : 'L'
            ))
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            if ( $echeance->getMontantTotal() > 0) {
                $echeance->setCompte($compteRepo->find($requete->request->get('form')['compteID']));
                $echeance->setSousCategorie($sousCategorieRepo->find($requete->request->get('form')['sousCategorieID']));
                switch ($requete->request->get('form')['type_tiers']) {
                    case 'L':
                        $echeance->setTiersPersonne(null);
                        $echeance->setTiersSociete(null);
                        $echeance->setTiersLibelle($requete->request->get('form')['tiers_libelle']);
                        break;
                    case 'S':
                        $echeance->setTiersSociete($societeRepo->find($requete->request->get('form')['societeID']));
                        $echeance->setTiersPersonne(null);
                        $echeance->setTiersLibelle(null);
                        break;
                    case 'P':
                        $echeance->setTiersPersonne($personneRepo->find($requete->request->get('form')['personneID']));
                        $echeance->setTiersSociete(null);
                        $echeance->setTiersLibelle(null);
                        break;
                }

                switch ($requete->request->get('form')['modePaiementID']){
                    case 'VIR':
                    case 'PRE':
                        $echeance->setModePaiementTrigramme($requete->request->get('form')['modePaiementID']);
                        break;
                    default:
                        $echeance->setModePaiement($modePaiementRepo->find($requete->request->get('form')['modePaiementID']));
                        $echeance->setModePaiementTrigramme($echeance->getModePaiement()->getModePaiement());
                        break;
                }

                $em->persist($echeance);
                $em->flush();
                
                // Dans le cas ou l'on a plusieurs paiements, on va créer une entrée par paiement
                if ($echeance->getNombreEcheances() > 0) {
                    $montantEcheance = round($echeance->getMontantTotal() / $echeance->getNombreEcheances(),2,PHP_ROUND_HALF_UP);
                }
                else {
                    $montantEcheance = 0;
                }
                $dateEcheance = $echeance->getDateEcheanceOne();
                for ($i =0; $i <= $echeance->getNombreEcheances(); $i++ ) {
                    $echeanceOperation = new EcheanceOperation;
                    $echeanceOperation->setMontantEcheance($montantEcheance);
                    $echeanceOperation->setDateEcheance($dateEcheance);
                    
                    if ($i == $echeance->getNombreEcheances()) {
                        $echeanceOperation->setMontantEcheance($echeance->getMontantTotal() - ($montantEcheance * $i));
                    }
                    $echeanceOperation->setEcheance($echeance);
                    
                    $em->persist($echeanceOperation);
                    $em->flush();

                    // On va créer les opérations antérieurs à la fin du mois
                    $date = New DateTime('now');
                    if ($echeanceOperation->getDateEcheance() < $date->modify('last day of this month')) {
                        $operation = new Operation;
                        $operation->setCompte($echeance->getCompte());
                        $operation->setDescription($echeance->getDescription());
                        $operation->setTypeOperation($echeance->getTypeOperation());
                        $operation->setTypeTiers($echeance->getTypeTiers());
                        $operation->setTiersLibelle($echeance->getTiersLibelle());
                        $operation->setPersonne($echeance->getTiersPersonne());
                        $operation->setSociete($echeance->getTiersSociete());
                        $operation->setCategorie($echeance->getSousCategorie());
                        $operation->setEcheanceOperation($echeanceOperation);
                        $operation->setModePaiementTrigramme($echeance->getModePaiementTrigramme());
                        $operation->setModePaiement($echeance->getModePaiement());
                        $operation->setDate($echeanceOperation->getDateEcheance());
                        $operation->setDebit($echeanceOperation->getMontantEcheance());
                        $operation->setEstPointe(0);

                        $em->persist($operation);
                        $em->flush();
                    }
                    
                    $dateEcheance = date_modify($dateEcheance, '+1 month');
                }
            }
            else {
                $this->addFlash("errorMSG", "Merci de saisir un montant positif");
            }
            return $this->redirectToRoute('app_myTresorerie_echeance');
        }

        return $this->render('default/backend/myTresorerie/echeancesDebitForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/echeance/credit/add", name="app_myTresorerie_echeance_credit_add")
     * @Route("/tresorerie/echeance/credit/edit/{id}", name="app_myTresorerie_echeance_credit_edit", methods ={"GET","POST"})
     */
    public function mytresorerie_echeancier_credit_form(?int $id,CompteRepository $compteRepo,SousCategorieRepository $sousCategorieRepo, SocieteRepository $societeRepo, PersonneRepository $personneRepo, EcheanceRepository $echeanceRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $echeance = new Echeance;
        if (isset($id)) {
            $echeance = $echeanceRepo->find($id);
        }
        
        $form = $this->createFormBuilder($echeance)
            ->add('dateEcheanceOne',DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $echeance->getDateEcheanceOne() ? $echeance->getDateEcheanceOne() : new DateTime()
            ])
            ->add('type_operation', HiddenType::class, [
                'data' => 'CRE',
            ])
            ->add('compteID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getCompte() ? $echeance->getCompte()->getId() : -1
            ])
            ->add('tiers_libelle',TextType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getTiersLibelle() ? $echeance->getTiersLibelle() : null,
                'required' => false,
            ])
            ->add('personneID',PersonneChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getTiersPersonne() ? $echeance->getTiersPersonne()->getId() : -1
            ])
            ->add('societeID', SocieteBanqueChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getTiersSociete() ? $echeance->getTiersSociete()->getId() : -1
            ])
            ->add('montant_total', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => ($echeance->getMontantTotal()) ? $echeance->getMontantTotal() : null
            ])
            ->add('est_solde', CheckboxType::class, [
                'data' => $echeance->getEstSolde() ? true : false,
                'required' => false
            ])
            ->add('sousCategorieID', SousCategorieChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'mapped' => false,
                'data' => $echeance->getSousCategorie() ?  $echeance->getSousCategorie()->getId() : null
            ])
            ->add('type_tiers', ChoiceType::class, array(
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
                'choices' => array(
                    'Saisie libre ' => 'L',
                    '[CA] Société' => 'S',
                    '[CA] Personne' => 'P'
                ),
                'data' => $echeance->getTypeTiers() ? $echeance->getTypeTiers() : 'L'
            ))
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            if ( $echeance->getMontantTotal() > 0) {
                $echeance->setCompte($compteRepo->find($requete->request->get('form')['compteID']));
                $echeance->setSousCategorie($sousCategorieRepo->find($requete->request->get('form')['sousCategorieID']));
                switch ($requete->request->get('form')['type_tiers']) {
                    case 'L':
                        $echeance->setTiersPersonne(null);
                        $echeance->setTiersSociete(null);
                        $echeance->setTiersLibelle($requete->request->get('form')['tiers_libelle']);
                        break;
                    case 'S':
                        $echeance->setTiersSociete($societeRepo->find($requete->request->get('form')['societeID']));
                        $echeance->setTiersPersonne(null);
                        $echeance->setTiersLibelle(null);
                        break;
                    case 'P':
                        $echeance->setTiersPersonne($personneRepo->find($requete->request->get('form')['personneID']));
                        $echeance->setTiersSociete(null);
                        $echeance->setTiersLibelle(null);
                        break;
                }
                $echeance->setModePaiementTrigramme('VIR');
                $echeance->setNombreEcheances(0);
                $em->persist($echeance);
                $em->flush();

                //On va créer les echeancesOperations
                $dateEcheance = $echeance->getDateEcheanceOne();
                $echeanceOperation = new EcheanceOperation;
                $echeanceOperation->setMontantEcheance($echeance->getMontantTotal());
                $echeanceOperation->setDateEcheance($echeance->getDateEcheanceOne());
                $echeanceOperation->setMontantEcheance($echeance->getMontantTotal());

                $em->persist($echeanceOperation);
                $em->flush();

                 // On va créer les opérations antérieurs à la fin du mois
                 $date = New DateTime('now');
                 if ($echeance->getDateEcheanceOne() < $date->modify('last day of this month')) {
                     $operation = new Operation;
                     $operation->setCompte($echeance->getCompte());
                     $operation->setDescription($echeance->getDescription());
                     $operation->setTypeOperation($echeance->getTypeOperation());
                     $operation->setTypeTiers($echeance->getTypeTiers());
                     $operation->setTiersLibelle($echeance->getTiersLibelle());
                     $operation->setPersonne($echeance->getTiersPersonne());
                     $operation->setSociete($echeance->getTiersSociete());
                     $operation->setCategorie($echeance->getSousCategorie());
                     $operation->setEcheanceOperation($echeanceOperation);
                     $operation->setModePaiementTrigramme($echeance->getModePaiementTrigramme());
                     $operation->setModePaiement($echeance->getModePaiement());
                     $operation->setDate($echeanceOperation->getDateEcheance());
                     $operation->setCredit($echeanceOperation->getMontantEcheance());
                     $operation->setEstPointe(0);

                     $em->persist($operation);
                     $em->flush();
                 }
            }
            else {
                $this->addFlash("errorMSG", "Merci de saisir un montant positif");
            }
            return $this->redirectToRoute('app_myTresorerie_echeance');
        }

        return $this->render('default/backend/myTresorerie/echeancesCreditForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/echeance/virement/add", name="app_myTresorerie_echeance_virement_add")
     * @Route("/tresorerie/echeance/virement/edit/{id}", name="app_myTresorerie_echeance_virement_edit", methods ={"GET","POST"})
     */
    public function mytresorerie_echeancier_virement_form(?int $id,CompteRepository $compteRepo, ModePaiementRepository $modePaiementRepo, SocieteRepository $societeRepo, PersonneRepository $personneRepo, EcheanceRepository $echeanceRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $echeance = new Echeance;
        if (isset($id)) {
            $echeance = $echeanceRepo->find($id);
        }
        
        $form = $this->createFormBuilder($echeance)
            ->add('dateEcheanceOne',DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $echeance->getDateEcheanceOne() ? $echeance->getDateEcheanceOne() : new DateTime()
            ])
            ->add('type_operation', HiddenType::class, [
                'data' => 'VII',
            ])
            ->add('compteID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getCompte() ? $echeance->getCompte()->getId() : -1
            ])
            ->add('compteDestinataireID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $echeance->getCompteDestinataireVirement() ? $echeance->getCompteDestinataireVirement()->getId() : -1
            ])
            ->add('montant_total', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => ($echeance->getMontantTotal()) ? $echeance->getMontantTotal() : null
            ])
            ->add('est_solde', CheckboxType::class, [
                'data' => $echeance->getEstSolde() ? true : false,
                'required' => false
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            if ( $echeance->getMontantTotal() > 0) {
                $echeance->setCompte($compteRepo->find($requete->request->get('form')['compteID']));
                $echeance->setCompteDestinataireVirement($compteRepo->find($requete->request->get('form')['compteDestinataireID']));

                $echeance->setModePaiementTrigramme('VIR');
                $echeance->setNombreEcheances(0);
                $em->persist($echeance);
                $em->flush();

                //On va créer les echeancesOperations
                // Etant dans le cadre d'un virement, il faut procéder
                // -----------------------------------------
                // A la création d'une operation de credit
                $timestamp = time();
                $echeanceOperation = new EcheanceOperation;
                $echeanceOperation->setMontantEcheance($echeance->getMontantTotal());
                $echeanceOperation->setDateEcheance($echeance->getDateEcheanceOne());
                $echeanceOperation->setMontantEcheance($echeance->getMontantTotal());

                $em->persist($echeanceOperation);
                $em->flush();

                // On va créer les opérations antérieurs à la fin du mois
                $date = New DateTime('now');
                if ($echeance->getDateEcheanceOne() < $date->modify('last day of this month')) {
                    $operation = new Operation;
                    $operation->setCompte($echeance->getCompte());
                    $operation->setDescription($echeance->getDescription());
                    $operation->setTypeOperation('VII');
                    $operation->setVirementID($timestamp);
                    $operation->setEcheanceOperation($echeanceOperation);
                    $operation->setModePaiementTrigramme($echeance->getModePaiementTrigramme());
                    $operation->setModePaiement($echeance->getModePaiement());
                    $operation->setDate($echeanceOperation->getDateEcheance());
                    $operation->setDebit($echeanceOperation->getMontantEcheance());
                    $operation->setEstPointe(0);

                    $em->persist($operation);
                    $em->flush();
                }

                // -----------------------------------------
                // A la création d'une operation de debit
                $echeanceOperation = new EcheanceOperation;
                $echeanceOperation->setMontantEcheance($echeance->getMontantTotal());
                $echeanceOperation->setDateEcheance($echeance->getDateEcheanceOne());
                $echeanceOperation->setMontantEcheance($echeance->getMontantTotal());

                $em->persist($echeanceOperation);
                $em->flush();

                // On va créer les opérations antérieurs à la fin du mois
                $date = New DateTime('now');
                if ($echeance->getDateEcheanceOne() < $date->modify('last day of this month')) {
                    $operation = new Operation;
                    $operation->setCompte($echeance->getCompteDestinataireVirement());
                    $operation->setTypeOperation('VII');
                    $operation->setEcheanceOperation($echeanceOperation);
                    $operation->setVirementID($timestamp);
                    $operation->setModePaiementTrigramme($echeance->getModePaiementTrigramme());
                    $operation->setModePaiement($echeance->getModePaiement());
                    $operation->setDate($echeanceOperation->getDateEcheance());
                    $operation->setCredit($echeanceOperation->getMontantEcheance());
                    $operation->setEstPointe(0);

                    $em->persist($operation);
                    $em->flush();
                }
            }
            else {
                $this->addFlash("errorMSG", "Merci de saisir un montant positif");
            }
            return $this->redirectToRoute('app_myTresorerie_echeance');
        }

        return $this->render('default/backend/myTresorerie/echeancesVirementForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/echeance/delete/{id}", name="app_myTresorerie_echeance_del", methods ="DELETE")
     */
    public function mytresorerie_echeancier_delete(Echeance $echeance, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('echeance_supprime_' . $echeance->getId(), $requete->request->get('csrf_token'))) {
            $em->remove($echeance);
            $em->flush();            
            $this->addFlash("successMSG", "Echéance supprimé");
            return $this->redirectToRoute('app_myTresorerie_echeance');
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }
}
