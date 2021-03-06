<?php

namespace App\Controller;

use App\Entity\MyFinances\Echeance;
use App\Entity\MyFinances\EcheanceOperation;
use App\Entity\MyFinances\Operation;
use App\Form\CompteChoiceType;
use App\Form\ModePaiementChoiceType;
use App\Form\PersonneChoiceType;
use App\Form\SocieteChoiceType;
use App\Form\SocieteBanqueChoiceType;
use App\Form\SousCategorieChoiceType;
use App\Repository\MyContacts\PersonneRepository;
use App\Repository\MyContacts\SocieteRepository;
use App\Repository\MyContrats\ContratFacturationRepository;
use App\Repository\MyFinances\CompteRepository;
use App\Repository\MyFinances\EcheanceOperationRepository;
use App\Repository\MyFinances\EcheanceRepository;
use App\Repository\MyFinances\ModePaiementRepository;
use App\Repository\MyFinances\OperationRepository;
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
            'echeances' => $echeanceRepo->findActifs()
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
            ->add('dateFin',DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $echeance->getDateFin() ? $echeance->getDateFin() : null
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
            ->add('societeID', SocieteChoiceType::class, [
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
                    '--- S??lectionnez la fr??quence ---' => '-1',
                    'Paiement 1x' => '1',
                    'Paiement 3x' => '3',
                    'Paiement 4x' => '4',
                    'Paiement 10x' => '10',
                    'Permament' => '0',
                ),
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
                    '[CA] Soci??t??' => 'S',
                    '[CA] Personne' => 'P'
                ),
                'data' => $echeance->getTypeTiers() ? $echeance->getTypeTiers() : 'L'
            ))
            ->add('recalcul_operation_anterieur', CheckboxType::class, [
                'required' => false,
                'data' => $echeance->getRecalculOperationAnterieur() ? $echeance->getRecalculOperationAnterieur() : true,
            ])
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
                        if ($echeance->getNombreEcheances() > 0) {
                            $echeance->setMontantFraction(true);
                        }
                        break;
                }
                // on force la fr??quence ?? 1 paiement par mois
                $echeance->setFrequenceNombrePaiement(1);
                $echeance->setFrequencePaiement('M');
                $em->persist($echeance);
                $em->flush();

                $echeance->calculNombreEcheanceOperation();
                $echeance->calculTableEcheanceOperation();
 
                // On va g??n??rer les op??rations d'??ch??ance associ??es 
                if (count($echeance->getTabEcheanceOperations()) > 0) {
                    for ($i = 1; $i <= count($echeance->getTabEcheanceOperations()); $i++) {
                        $echeanceOperation = new EcheanceOperation;
                        $echeanceOperation = $echeance->getTabEcheanceOperations()[$i];
                        $echeanceOperation->setEcheance($echeance);
                        $em->persist($echeanceOperation);
                        $em->flush();

                            // On va cr??er les op??rations jusqu'?? la fin du mois
                            // ou celle du mois si c'est elle
                            if ($echeanceOperation->getDateEcheance() < New DateTime('last day of this month')) {
                                $operation = new Operation;
                                $operation->setDebitFromEcheanceOperation($echeanceOperation);

                                $em->persist($operation);
                                $em->flush();
                            }
                    }
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
            ->add('dateFin',DateType::class, [
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
                'data' => $echeance->getDateFin() ? $echeance->getDateFin() : null
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
            ->add('societeID', SocieteChoiceType::class, [
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
                    '[CA] Soci??t??' => 'S',
                    '[CA] Personne' => 'P'
                ),
                'data' => $echeance->getTypeTiers() ? $echeance->getTypeTiers() : 'L'
            ))
            ->add('nombre_echeances', ChoiceType::class, array(
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
                'choices' => array(
                    '--- S??lectionnez la fr??quence ---' => '-1',
                    'Unique' => '1',
                    'Permament' => '0',
                ),
                'data' => $echeance->getTypeTiers() ? $echeance->getTypeTiers() : 'L'
            ))
            ->add('recalcul_operation_anterieur', CheckboxType::class, [
                'required' => false,
                'data' => $echeance->getRecalculOperationAnterieur() ? $echeance->getRecalculOperationAnterieur() : true,
            ])
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

                // on d??finie les valeurs par d??faut d'un cr??dit
                $echeance->setModePaiementTrigramme('VIR');
                $echeance->setTypeOperation('CRE');
                $echeance->setFrequenceNombrePaiement(1);
                $echeance->setFrequencePaiement('M');

                $em->persist($echeance);
                $em->flush();

                $echeance->calculNombreEcheanceOperation();
                $echeance->calculTableEcheanceOperation();

                // On va g??n??rer les op??rations d'??ch??ance associ??es 
                if (count($echeance->getTabEcheanceOperations()) > 0) {
                    for ($i = 1; $i <= count($echeance->getTabEcheanceOperations()); $i++) {
                        $echeanceOperation = new EcheanceOperation;
                        $echeanceOperation = $echeance->getTabEcheanceOperations()[$i];
                        $echeanceOperation->setEcheance($echeance);
                        $em->persist($echeanceOperation);
                        $em->flush();

                            // On va cr??er les op??rations jusqu'?? la fin du mois
                            // ou celle du mois si c'est elle
                            if ($echeanceOperation->getDateEcheance() < New DateTime('last day of this month')) {
                                $operation = new Operation;
                                $operation->setCreditFromEcheanceOperation($echeanceOperation);

                                $em->persist($operation);
                                $em->flush();
                            }
                    }
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
            ->add('nombre_echeances', ChoiceType::class, array(
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
                'choices' => array(
                    '--- S??lectionnez la fr??quence ---' => '-1',
                    'Unique' => '1',
                    'Permament' => '0',
                ),
                'data' => $echeance->getTypeTiers() ? $echeance->getTypeTiers() : 'L'
            ))
            ->add('recalcul_operation_anterieur', CheckboxType::class, [
                'required' => false,
                'data' => $echeance->getRecalculOperationAnterieur() ? $echeance->getRecalculOperationAnterieur() : true,
            ])
            ->add('dateFin',DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $echeance->getDateFin() ? $echeance->getDateFin() : null
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            if ( $echeance->getMontantTotal() > 0) {
                $echeance->setCompte($compteRepo->find($requete->request->get('form')['compteID']));
                $echeance->setCompteDestinataireVirement($compteRepo->find($requete->request->get('form')['compteDestinataireID']));

                $echeance->setFrequenceNombrePaiement(1);
                $echeance->setFrequencePaiement('M');
                $echeance->setTypeOperation('VII');
                $echeance->setModePaiementTrigramme('VIR');

                $em->persist($echeance);
                $em->flush();

                $echeance->calculNombreEcheanceOperation();
                $echeance->calculTableEcheanceOperation();

                // On va g??n??rer les op??rations d'??ch??ance associ??es 
                if (count($echeance->getTabEcheanceOperations()) > 0) {
                    for ($i = 1; $i <= count($echeance->getTabEcheanceOperations()); $i++) {
                        $timestamp = time() + $i;
                        $echeanceOperation = new EcheanceOperation;
                        $echeanceOperation = $echeance->getTabEcheanceOperations()[$i];
                        $echeanceOperation->setEcheance($echeance);

                        $em->persist($echeanceOperation);
                        $em->flush();

                        if ($echeanceOperation->getDateEcheance() < New DateTime('last day of this month')) {
                            $operationDebit = new Operation;
                            $operationDebit->setDebitFromEcheanceOperation($echeanceOperation);
                            $operationDebit->setTypeOperation('VII');
                            $operationDebit->setVirementID($timestamp);
                            $operationDebit->setCompteVirementInterne($echeance->getCompteDestinataireVirement());

                            $em->persist($operationDebit);
                            $em->flush();
                        }

                        $echeanceOperationCredit = new EcheanceOperation;
                        $echeanceOperationCredit = clone($echeance->getTabEcheanceOperations()[$i]);
                        $echeanceOperationCredit->setEcheance($echeance);

                        $em->persist($echeanceOperationCredit);
                        $em->flush();
                        if ($echeanceOperation->getDateEcheance() < New DateTime('last day of this month')) {
                            $operationCredit = new Operation;
                            $operationCredit->setCreditFromEcheanceOperation($echeanceOperationCredit);
                            $operationCredit->setCompte($echeance->getCompteDestinataireVirement());
                            $operationCredit->setCompteVirementInterne($echeance->getCompte());
                            $operationCredit->setTypeOperation('VII');
                            $operationCredit->setVirementID($timestamp);

                            $em->persist($operationCredit);
                            $em->flush();
                        }
                    }
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
     * @Route("/tresorerie/echeance/archive/{id}", name="app_myTresorerie_echeance_archive")
     */
    public function mytresorerie_echeancier_archive(Echeance $echeance, Request $requete,ContratFacturationRepository $contratFacturationRepo, OperationRepository $operationRepo, EcheanceOperationRepository $echeanceOperationRepo, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('echeance_archive_' . $echeance->getId(),$requete->request->get('csrf_token'))) {
            $listEcheanceOperations = $echeanceOperationRepo->findOperationByEcheance($echeance);
            // On va lister toutes les EcheanceOperations pour les supprimer car devenues inutiles
            if (count($listEcheanceOperations) > 0) {
                for ($i =0; $i < count($listEcheanceOperations); $i++) {
                    // on va supprimer l'EcheanceOperation et l'Operation uniquement si cette derni??re n'a pas ??t?? rapproch??e
                    $operationCours = $operationRepo->findOperationAllByEcheanceOperation ($listEcheanceOperations[$i]);
                    if (count($operationCours) > 0) {
                        if ($operationCours[0]->getEstPointe() != true) {
                            $em->remove($operationCours[0]);
                            $em->remove($listEcheanceOperations[$i]);
                            $em->flush();
                        }
                    }
                    else {
                        // Aucune op??ration n'a encore ??t?? cr????e donc on peut supprimer sans probl??me
                        $em->remove($listEcheanceOperations[$i]);
                        $em->flush();
                    }
                }
            }

            // On va v??rifier si une p??riode de facturation au niveau du contrat existe pour la solder ??galement
            $contratFacturation = $contratFacturationRepo->findContratFacturationByEcheance($echeance);
            if ($contratFacturation) {
                $contratFacturation->setEstArchive(true);
                $em->persist($contratFacturation);
                $em->flush();
            }

            $echeance->setEstSolde(true);
            $em->persist($echeance);
            $em->flush();

            $this->addFlash("successMSG", "Archivage effectu??");

            return $this->redirectToRoute('app_myTresorerie_echeance');
        }
        else {
            dd('teete');
            return $this->redirectToRoute('app_hacking');
        }
    }

    /**
     * @Route("/tresorerie/echeance/delete/{id}", name="app_myTresorerie_echeance_del", methods ="DELETE")
     */
    public function mytresorerie_echeancier_delete(Echeance $echeance, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('echeance_supprime_' . $echeance->getId(), $requete->request->get('csrf_token'))) {
            try {
                $em->remove($echeance);
                $em->flush();            
                $this->addFlash("successMSG", "Ech??ance supprim??");
            } catch (\Exception $e) {
                $this->addFlash("errorMSG", "Suppression impossible - L'??ch??ance est r??f??renc??e dans un autre module. Proc??dez ?? un archivage ?? la place");
            }

            return $this->redirectToRoute('app_myTresorerie_echeance');
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }
}
