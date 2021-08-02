<?php

namespace App\Controller;

use App\Entity\MyFinances\Credit;
use App\Entity\MyFinances\Echeance;
use App\Entity\MyFinances\EcheanceOperation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MyFinances\Operation;
use App\Entity\MyFinances\Position;
use App\Entity\MyFinances\PositionOrdre;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Form\CompteChoiceType;
use App\Form\ModePaiementChoiceType;
use App\Form\SousCategorieChoiceType;
use App\Form\PersonneChoiceType;
use App\Form\SocieteBanqueChoiceType;
use App\Form\SocieteChoiceType;
use App\Repository\MyContacts\PersonneRepository;
use App\Repository\MyContacts\SocieteRepository;
use App\Repository\MyContrats\ContratFacturationRepository;
use App\Repository\MyContrats\ContratRepository;
use App\Repository\MyFinances\CreditRepository;
use App\Repository\MyFinances\ModePaiementRepository;
use App\Repository\MyFinances\OperationRepository;
use App\Repository\MyFinances\SousCategorieRepository;
use App\Repository\MyFinances\CompteRepository;
use App\Repository\MyFinances\EcheanceOperationRepository;
use App\Repository\MyFinances\EcheanceRepository;
use App\Repository\MyFinances\PositionOrdreRepository;
use App\Repository\MyFinances\PositionRepository;
use App\Repository\MyFinances\TypeCompteRepository;
use DateTime;

class MyTresorerieController extends AbstractController
{
    /**
     * @Route("/tresorerie", name="app_myTresorerie_index")
     */
    public function mytresorerie_index(CompteRepository $compteRepo, TypeCompteRepository $typeCompteRepo): Response
    {
        $typesCompte = $typeCompteRepo->findAll();
        $comptes = $compteRepo->findActif();

        return $this->render('default/backend/myTresorerie/mytresorerie.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'comptes' => $comptes,
            'typesCompte' => $typesCompte
        ]);
    }

    /**
     * @Route("/tresorerie/moulinette", name="app_myTresorerie_moulinette")
     */
    public function mytresorerie_moulinette(EntityManagerInterface $em, EcheanceRepository $echeanceRepo, EcheanceOperationRepository $echeanceOperationRepo,OperationRepository $OperationRepo, ContratFacturationRepository $contratFacturationRepo): Response
    {
        // La période définie sera toujours le mois en cours (la date de fin est au 1er jour du mois suivantà 0h00 donc à exclure dans les requêtes)
        $dateDebutPeriode = new DateTime('midnight first day of this month');
        $dateFinPeriode = new DateTime('midnight first day of next month');
        
        // On va commencer dans un premier temps pour parcourir les écheances actives et archiver celles dont 
        // un nombre d'échéance est défini à l'avance ou une date de fin existe et est dépassée
        $echeanceDepassees = $echeanceRepo->findActifsByDateFin($dateDebutPeriode);
        
        if (count($echeanceDepassees) > 0) {
            for($i = 0 ; $i < count($echeanceDepassees); $i++) {
                $echeanceDepassees[$i]->setEstSolde(true);
                // On vérifie que l'échéance ne fait pas parti d'un contrat défini sinon, on va procéder à l'archivage de celui-ci également
                $contratFacturation = $contratFacturationRepo->findContratFacturationByEcheance($echeanceDepassees[$i]);
                if ($contratFacturation) {
                    $contratFacturation->setEstArchive(true);
                   $em->persist($contratFacturation);
                }
                $em->persist($echeanceDepassees[$i]);
                $em->flush();
            }
        }

        
        // On va maintenant gérer la particularité des échéances multiples sur paiement x2, x3 x4 par exemple ou il n'existe pas de dates de fin, mais qui sont échus
        $echeanceDepassees = $echeanceRepo->findActifsDateFinNull();
        if (count($echeanceDepassees) > 0) {
            for($i = 0 ; $i < count($echeanceDepassees); $i++) {
                if ($echeanceDepassees[$i]->getDateEcheanceOne() < $dateFinPeriode) {
                    $echeanceOperationCours = $echeanceOperationRepo->findOperationByEcheanceDateDebut($echeanceDepassees[$i],$dateDebutPeriode);
                    if (count($echeanceOperationCours) == 0) {
                        $echeanceDepassees[$i]->setEstSolde(true);
                        // On vérifie que l'échéance ne fait pas parti d'un contrat défini sinon, on va procéder à l'archivage de celui-ci également
                        $contratFacturation = $contratFacturationRepo->findContratFacturationByEcheance($echeanceDepassees[$i]);
                        if ($contratFacturation) {
                            $contratFacturation->setEstArchive(true);
                            $em->persist($contratFacturation);
                        }
                        $em->persist($echeanceDepassees[$i]);
                        $em->flush();
                    }
                }
            }
        }

        // On va calculer les échéances à cet écran pour les intégrer si besoin et ne pas avoir à les recharger ensuite durant la navigation, les nouveaux contrats/échéances étant calculés à la création
        // On va traiter dans un premier temps les échéances permanentes pour créer les EcheancesOperations/Operations correspondantes
        $echeancesCours = $echeanceRepo->findActifsPeriode($dateDebutPeriode,$dateFinPeriode);
        if (count($echeancesCours)) {
            for ($i = 0; $i < count($echeancesCours); $i++) {
                // On va d'abord gérer les échéanciers à nombres définis dont l'EcheanceOperation a déjà été créée lors de la création de l'échéance
                if ( $echeancesCours[$i]->getNombreEcheances() > 0 ) {
                    $echeanceOperation = $echeanceOperationRepo->findOperationByEcheancePeriode($echeancesCours[$i],$dateDebutPeriode,$dateFinPeriode);
                    // Attention, il peut y avoir plusieurs échéances sur un même mois, donc nous allons procéder à une analyse en boucle
                    if (count($echeanceOperation) > 0) {
                        for($y = 0 ; $y < count($echeanceOperation); $y++) {
                            // On va vérifier que l'EcheanceOperation n'a pas déjà été généré en tant qu'Operation
                            $operationCours = $OperationRepo->findOperationAllByEcheanceOperation($echeanceOperation[$y]);
                            if (count($operationCours) == 0) {
                                $operation = new Operation;
                                switch ($echeanceOperation[$y]->getEcheance()->getTypeOperation()) {
                                    case 'CRE':
                                        $operation->setCreditFromEcheanceOperation($echeanceOperation[$y]);
                                        $em->persist($operation);
                                        $em->flush();
                                        break;
                                    case 'DEB':
                                        $operation->setDebitFromEcheanceOperation($echeanceOperation[$y]);
                                        $em->persist($operation);
                                        $em->flush();
                                        break;
                                }
                            }
                        }
                    }
                    else {
                        // Cette portion de code va permettre de regénérer un tableau d'échéance dans le cas ou il n'aurait pas été généré correctement 
                        // lors de la génération d'une periode de facturation sur un contrat.
                        $echeancesCours[$i]->calculNombreEcheanceOperation();
                        $echeancesCours[$i]->calculTableEcheanceOperation();

                        for($y = 1 ; $y <= count($echeancesCours[$i]->getTabEcheanceOperations()) ; $y++) {
                            $em->persist($echeancesCours[$i]->getTabEcheanceOperations()[$y]);
                            $em->flush();

                            // On ne va créer une opération que pour la période en cours
                            if ($echeancesCours[$i]->getTabEcheanceOperations()[$y]->getDateEcheance() >= $dateDebutPeriode && $echeancesCours[$i]->getTabEcheanceOperations()[$y]->getDateEcheance() < $dateFinPeriode) {
                                $operation = new Operation;
                                switch ($echeancesCours[$i]->getTypeOperation()) {
                                    case 'CRE':
                                        $operation->setCreditFromEcheanceOperation($echeancesCours[$i]->getTabEcheanceOperations()[$y]);
                                        $em->persist($operation);
                                        $em->flush();
                                        break;
                                    case 'DEB':
                                        $operation->setDebitFromEcheanceOperation($echeancesCours[$i]->getTabEcheanceOperations()[$y]);
                                        $em->persist($operation);
                                        $em->flush();
                                        break;
                                }
                            }
                        }
                    }
                }
                // On va gérer les échéances permanentes ou il est nécessaire dans ce cas de générer une EcheanceOperation ainsi que l'opération correspondant
                else {
                    // On va s'assurer qu'une EcheanceOperation n'a été créée sur le mois
                    $echeanceOperation = $echeanceOperationRepo->findOperationByEcheancePeriode($echeancesCours[$i],$dateDebutPeriode,$dateFinPeriode);
                    if (count($echeanceOperation) == 0) {
                        $echeancesCours[$i]->calculNombreEcheanceOperation();
                        $echeancesCours[$i]->calculTableEcheanceOperation();
                        // On va pouvoir créer les entrées EcheanceOperation et Operation
                        if (count($echeancesCours[$i]->getTabEcheanceOperations()) > 0) {
                            for ($y = 1 ; $y <= count($echeancesCours[$i]->getTabEcheanceOperations()) ; $y++) {
                                if ($echeancesCours[$i]->getTabEcheanceOperations()[$y]->getDateEcheance() >= $dateDebutPeriode && $echeancesCours[$i]->getTabEcheanceOperations()[$y]->getDateEcheance() <= $dateFinPeriode) {
                                    $operation = new Operation;
                                    switch ($echeancesCours[$i]->getTabEcheanceOperations()[$y]->getEcheance()->getTypeOperation()) {
                                        case 'CRE':
                                            $em->persist($echeancesCours[$i]->getTabEcheanceOperations()[$y]);
                                            $em->flush();

                                            $operation->setCreditFromEcheanceOperation($echeancesCours[$i]->getTabEcheanceOperations()[$y]);
                                            $em->persist($operation);
                                            $em->flush();
                                            break;
                                        case 'DEB':
                                            $em->persist($echeancesCours[$i]->getTabEcheanceOperations()[$y]);
                                            $em->flush();

                                            $operation->setDebitFromEcheanceOperation($echeancesCours[$i]->getTabEcheanceOperations()[$y]);
                                            $em->persist($operation);
                                            $em->flush();
                                            break;
                                        case 'VII':
                                            $timestamp = time() + $y;
                    
                                            $echeanceOperationDebit = $echeancesCours[$i]->getTabEcheanceOperations()[$y];

                                            $echeanceOperationCredit = clone($echeancesCours[$i]->getTabEcheanceOperations()[$y]);

                                            $em->persist($echeanceOperationCredit);
                                            $em->persist($echeanceOperationDebit);
                                            $em->flush();
    
                                            $operationDebit = new Operation;
                                            $operationDebit->setDebitFromEcheanceOperation($echeanceOperationDebit);
                                            $operationDebit->setTypeOperation('VII');
                                            $operationDebit->setVirementID($timestamp);
                                            $operationDebit->setCompteVirementInterne($echeancesCours[$i]->getCompteDestinataireVirement());
                                            
                                            $operationCredit = new Operation;
                                            $operationCredit->setCreditFromEcheanceOperation($echeanceOperationCredit);
                                            $operationCredit->setCompte($echeancesCours[$i]->getCompteDestinataireVirement());
                                            $operationCredit->setCompteVirementInterne($echeancesCours[$i]->getCompte());
                                            $operationCredit->setTypeOperation('VII');
                                            $operationCredit->setVirementID($timestamp);

                                            $em->persist($operationDebit);
                                            $em->persist($operationCredit);
                                            $em->flush();
                                            break;
                                    }
                                }                                
                            }
                        }
                    }
                }
            }
        }

        return $this->redirectToRoute('app_myTresorerie_index');        
    }

    /**
     * @Route("/tresorerie/comptes/{id}", name="app_myTresorerie_compte_releve")
     */
    public function mytresorerie_compte(int $id,OperationRepository $operationRepo, CompteRepository $compteRepo, PositionRepository $positionRepo): Response
    {
        $compte = $compteRepo->find($id);
        $compte = $compteRepo->calculSoldes($compte);
        
        if ($compte->getTypeCompte() != 3 ) {
            return $this->render('default/backend/myTresorerie/compte_releve.html.twig', [
                'controller_name' => 'MyTresorerieController',
                'compte' => $compte,
                'operationsEcheancesFuturs' => $operationRepo->findEcheances($compte),
                'operationsNonRapprochees' => $operationRepo->findOperationsNonRapprochees($compte),
                'operationsRapprochees' => $operationRepo->findOperationsRapprochees($compte),
            ]);
        } elseif ($compte->getTypeCompte() == 3) {
           // dd($positionRepo->findAll());
            return $this->render('default/backend/myTresorerie/compte_releve_titre.html.twig', [
                'controller_name' => 'MyTresorerieController',
                'compte' => $compte,
                'positions' => $positionRepo->findActifByCompte($compte),
                'operations' => $operationRepo->findOperationsRapprochees($compte),
            ]);
        } else {
            return $this->redirectToRoute('app_hacking');
        }

    }
    
    /**
     * @Route("/tresorerie/operation/credit", name="app_myTresorerie_operation_credit")
     * @Route("/tresorerie/operation/credit/{id}", name="app_myTresorerie_operation_credit_edit")
     * @Route("/tresorerie/compte/{idCompte}/operation/credit", name="app_myTresorerie_compte_operation_credit_add")
     */
    public function mytresorerie_operation_credit(?int $id,?int $idCompte, SocieteRepository $societeRepo, PersonneRepository $personnneRepo, SousCategorieRepository $sousCategorieRepo, OperationRepository $operationRepo,ModePaiementRepository $modePaiementRepo, CompteRepository $compteRepo, Request $requete,EntityManagerInterface $em): Response
    {
        $operation = new Operation();
 
        // On va gérer une particularité qui va sélectioner le compte uniquement s'il s'agit d'une opération en cours
        // ou si on déclenche l'enregistrement d'un mouvement depuis un relevé
        if (isset($id)) {
            $operation = $operationRepo->find($id);
            $idCompte = $operation->getCompte() ?  $operation->getCompte()->getId() : null;
        }
        else {
            if (!isset($idCompte)) {
                $idCompte = "-1";
            }
        }

        $form = $this->createFormBuilder($operation)
            ->add('type_operation', HiddenType::class, [
                'data' => 'CRE',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $operation->getDate() ? $operation->getDate() : new DateTime()
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('compteID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $idCompte
            ])
            ->add('modePaiementTrigramme', ChoiceType::class, [
                'choices' => array(
                    '--- Sélectionnez un mode de paiement ---' => '-1',
                    '--- Virement ---' => 'VIR',
                    '--- Dépot Chèque/Espèce ---' => 'DEP'
                ),
                'attr' => ['class' => 'form-control'],
                'data' => $operation->getModePaiement() ?  $operation->getModePaiement()->getId() : ($operation->getModePaiementTrigramme() ?  : null)
            ])
            ->add('typeTiers', ChoiceType::class, array(
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
                'choices' => array(
                    'Saisie libre ' => 'L',
                    '[CA] Société' => 'S',
                    '[CA] Personne' => 'P'
                ),
                'data' => $operation->getTypeTiers() ? $operation->getTypeTiers() : 'L'
            ))
            ->add('tiers_libelle', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('societeID',SocieteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => $operation->getSociete() ?  $operation->getSociete()->getId() : "-1"
            ])            
            ->add('personneID',PersonneChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => $operation->getPersonne() ?  $operation->getPersonne()->getId() : "-1"
            ])   
            ->add('categorieID',SousCategorieChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $operation->getCategorie() ?  $operation->getCategorie()->getId() : null
            ])
            ->add('credit',NumberType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('est_pointe', CheckboxType::class, [
                'attr' => ['data-plugin-ios-switch' => 'data-plugin-ios-switch'],
                'required' => false
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) { 
            if ($requete->request->get('form')['credit'] > 0) {
                $operation->setCompte($compteRepo->find($requete->request->get('form')['compteID']));
                $operation->setCategorie($sousCategorieRepo->find($requete->request->get('form')['categorieID']));
                switch ($operation->getTypeTiers()) {
                    case "L":
                        $operation->setPersonne(null);
                        $operation->setSociete(null);
                        break;
                    case "P":
                        $operation->setTiersLibelle(null);
                        $operation->setPersonne($personnneRepo->find($requete->request->get('form')['personneID']));
                        $operation->setSociete(null);
                        break;
                    case "S":
                        $operation->setTiersLibelle(null);
                        $operation->setPersonne(null);
                        $operation->setSociete($societeRepo->find($requete->request->get('form')['societeID']));
                        break;
                }

                $em->persist($operation);
                $em->flush();

                $this->addFlash("userUpdate", "Ajout opération effectué");
                return $this->redirectToRoute('app_myTresorerie_compte_releve', [ 'id' => $requete->request->get('form')['compteID']]);
            }
            else {
                $this->addFlash("errorMSG", "Merci de saisir un montant positif");
            }
        }

        return $this->render('default/backend/myTresorerie/operation_credit.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView(),
            'compte' => $idCompte
        ]);
    }

    /**
     * @Route("/tresorerie/compte/{idCompte}/titre", name="app_myTresorerie_compte_operation_titre_add")
     * @Route("/tresorerie/compte/{idCompte}/titre/{id}", name="app_myTresorerie_compte_operation_titre_edit")
     */
    public function mytresorerie_operation_titre(?int $idCompte,?int $id,PositionRepository $positionRepo, PositionOrdreRepository $PositionOrdreRepo,CompteRepository $compteRepo, SousCategorieRepository $sousCategorieRepo, SocieteRepository $societeRepo, Request $requete,EntityManagerInterface $em): Response
    {
        $PositionOrdre = new PositionOrdre();
        $compte = $compteRepo->find($idCompte);

        if ($id) {
            $PositionOrdrePere = $positionRepo->find($id);
        }
 
        $form = $this->createFormBuilder($PositionOrdre)
            ->add('societeID',SocieteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => isset($PositionOrdrePere) ? $PositionOrdrePere->getSociete()->getId() : "-1"
            ])
            ->add('PositionSocieteAbrege', TextType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => isset($PositionOrdrePere) ? $PositionOrdrePere->getAbregeSociete() : null
            ])
            ->add('typeMouvement', ChoiceType::class, array(
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
                'choices' => array(
                    'Achat' => 'A',
                    'Vente' => 'V'
                ),
            ))
            ->add('TypePosition', ChoiceType::class, array(
                'mapped' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
                'choices' => array(
                    'Comptant' => 'C',
                    'SRD' => 'S'
                ),
                'data' => isset($PositionOrdrePere) ? $PositionOrdrePere->getPosition() : 'C'
            ))
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $PositionOrdre->getDate() ? $PositionOrdre->getDate() : new DateTime()
            ])   
            ->add('categorieID',SousCategorieChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('nombre_titres', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('valeur_titre', NumberType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('taxe', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('frais', NumberType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            $saisieValide = false;

            if (isset($PositionOrdrePere)) {
                // On va procéder que la quantité saisie
                if ($requete->request->get('form')['typeMouvement'] == 'V') {
                    $nbActionsFormulaire = -1 * $requete->request->get('form')['nombre_titres'];
                }
                else {
                    $nbActionsFormulaire = $requete->request->get('form')['nombre_titres'];
                }
                if ($PositionOrdrePere->getNombreActions() + $nbActionsFormulaire >= 0) {
                    // On va solder la position si le nombre d'action est à 0
                    if ($PositionOrdrePere->getNombreActions() + $nbActionsFormulaire == 0) {
                        $PositionOrdrePere->setEstSolde(true);

                        $em->persist($PositionOrdrePere);
                        $em->flush();
                    }


                    $PositionOrdre->setPosition($PositionOrdrePere);
                    $position = $PositionOrdrePere;

                    $saisieValide = true;
                }
                else {
                    $messageErreur = "Le nombre d'actions de l'ordre ne peut dépasser celui de la position";
                }

            }
            else {
                if ($requete->request->get('form')['TypePosition'] == 'C' && $requete->request->get('form')['typeMouvement'] == 'V') {
                    $messageErreur = "Le passage d'un ordre en position comptant ne peut se faire en vente à découvert";
                }
                else {
                $saisieValide = true;

                    $position = new Position();
                    $position->setCompte($compte);
                    $position->setSociete($societeRepo->find($requete->request->get('form')['societeID']));
                    $position->setPosition($requete->request->get('form')['TypePosition']);
                    $position->setAbregeSociete($requete->request->get('form')['PositionSocieteAbrege']);
        
                    $em->persist($position);
                    $em->flush();

                    $PositionOrdre->setPosition($position);
                }
            }

            if ($saisieValide) {
                // On enregistre ensuite l'ordre
                $em->persist($PositionOrdre);
                $em->flush();

                // On crée enfin l'opération uniquement pour les opérations au comptant
                // Le réglement SRD sera fait en fin de mois
                if ($position->getPosition() == 'C') {
                    $sousCategorie = $sousCategorieRepo->find($requete->request->get('form')['categorieID']);
                    $operation = new Operation();
                    $operation->setOperationFromPositionOrdre($PositionOrdre, $sousCategorie);

                    $em->persist($operation);
                    $em->flush();
                }
                
                $this->addFlash("successMSG", "Ajout Ordre effectué");
            }
            else {
                $this->addFlash("errorMSG", "L'ordre n'a pu être ajouté : " . $messageErreur);
            }
            return $this->redirectToRoute('app_myTresorerie_compte_releve', [ 'id' => $compte->getId()]);
        
        }

        return $this->render('default/backend/myTresorerie/operation_titre.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView(),
            'compte' => $compte
        ]);
    }

    /**
     * @Route("/tresorerie/operation/debit", name="app_myTresorerie_operation_debit")
     * @Route("/tresorerie/operation/debit/{id}", name="app_myTresorerie_operation_debit_edit")
     * @Route("/tresorerie/compte/{idCompte}/operation/debit", name="app_myTresorerie_compte_operation_debit_add")
     */
    public function mytresorerie_debit(?int $id,?int $idCompte,SocieteRepository $societeRepo, PersonneRepository $personnneRepo, SousCategorieRepository $sousCategorieRepo, OperationRepository $operationRepo,ModePaiementRepository $modePaiementRepo, CompteRepository $compteRepo, Request $requete,EntityManagerInterface $em): Response
    {
        $operation = new Operation();

        // On va gérer une particularité qui va sélectioner le compte uniquement s'il s'agit d'une opération en cours
        // ou si on déclenche l'enregistrement d'un mouvement depuis un relevé
        if (isset($id)) {
            $operation = $operationRepo->find($id);
            $idCompte = $operation->getCompte() ?  $operation->getCompte()->getId() : null;
        }
        else {
            if (!isset($idCompte)) {
                $idCompte = "-1";
            }
        }

        $form = $this->createFormBuilder($operation)
            ->add('type_operation', HiddenType::class, [
                'data' => 'DEB',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $operation->getDate() ? $operation->getDate() : new DateTime()
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('compteID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $idCompte
            ])
            ->add('modePaiementID', ModePaiementChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $operation->getModePaiement() ?  $operation->getModePaiement()->getId() : ($operation->getModePaiementTrigramme() ?  : null)
            ])
            ->add('typeTiers', ChoiceType::class, array(
                'multiple' => false,
                'attr' => ['class' => 'form-control'],
                'choices' => array(
                    'Saisie libre ' => 'L',
                    '[CA] Société' => 'S',
                    '[CA] Personne' => 'P'
                ),
                'data' => $operation->getTypeTiers() ? $operation->getTypeTiers() : 'L'
            ))
            ->add('tiers_libelle', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('societeID',SocieteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => $operation->getSociete() ?  $operation->getSociete()->getId() : "-1"
            ])            
            ->add('personneID',PersonneChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'data' => $operation->getPersonne() ?  $operation->getPersonne()->getId() : "-1"
            ])   
            ->add('categorieID',SousCategorieChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $operation->getCategorie() ?  $operation->getCategorie()->getId() : null
            ])
            ->add('debit',NumberType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('numeroCheque', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('est_pointe', CheckboxType::class, [
                'attr' => ['data-plugin-ios-switch' => 'data-plugin-ios-switch'],
                'required' => false
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) { 
            if ($requete->request->get('form')['debit'] > 0) {
                $operation->setCompte($compteRepo->find($requete->request->get('form')['compteID']));

                // On va gérer le mode de paiement
                if ($requete->request->get('form')['modePaiementID']) {
                    switch ($requete->request->get('form')['modePaiementID']) {
                        case 'VIR':
                        case 'PRE':
                            $operation->setModePaiementTrigramme(substr($requete->request->get('form')['modePaiementID'],0,3));
                            break;
                        default:
                            $modePaiement = $modePaiementRepo->find($requete->request->get('form')['modePaiementID']);
                            $operation->setModePaiement($modePaiement);
                            $operation->setModePaiementTrigramme(substr($modePaiement->getModePaiement(),0,3));
                            break;
                    }

                }
                $operation->setCategorie($sousCategorieRepo->find($requete->request->get('form')['categorieID']));
                switch ($operation->getTypeTiers()) {
                    case "L":
                        $operation->setPersonne(null);
                        $operation->setSociete(null);
                        break;
                    case "P":
                        $operation->setTiersLibelle(null);
                        $operation->setPersonne($personnneRepo->find($requete->request->get('form')['personneID']));
                        $operation->setSociete(null);
                        break;
                    case "S":
                        $operation->setTiersLibelle(null);
                        $operation->setPersonne(null);
                        $operation->setSociete($societeRepo->find($requete->request->get('form')['societeID']));
                        break;
                }

                $em->persist($operation);
                $em->flush();

                $this->addFlash("userUpdate", "Ajout opération effectué");
                return $this->redirectToRoute('app_myTresorerie_compte_releve', [ 'id' => $requete->request->get('form')['compteID']]);
            }
            else {
                $this->addFlash("errorMSG", "Merci de saisir un montant positif");
            }
        }

        return $this->render('default/backend/myTresorerie/operation_debit.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView(),
            'compte' => $idCompte
        ]);
    }

    /**
     * @Route("/tresorerie/operation/virement", name="app_myTresorerie_virement")
     * @Route("/tresorerie/operation/virement/{id}", name="app_myTresorerie_virement_edit", methods={"GET", "POST"})
     * @Route("/tresorerie/compte/{idCompte}/virement", name="app_myTresorerie_compte_virement_add")
     */
    public function mytresorerie_virement(?int $id,?int $idCompte, CompteRepository $compteRepo, OperationRepository $operationRepo, Request $requete,EntityManagerInterface $em): Response
    {
        // l'id correspond à l'id de l'operation du relevé. Il va donc falloir charger le formulaire
        // en fonction de l'id_virement enregistré lors de l'enregistrement initial.
        if (isset($id)) {
            $operationDebit = $operationRepo->find($id);
            $operationDebit = $operationRepo->findOperationVirementDebit($operationDebit->getVirementID());
            $operationCredit = $operationRepo->findOperationVirementCredit($operationDebit->getVirementID());
            $idCompte = $operationDebit->getCompte() ? $operationDebit->getCompte()->getId() : null;
        }
        // On va gérer une particularité qui va sélectioner le compte uniquement s'il s'agit d'une opération en cours
        // ou si on déclenche l'enregistrement d'un mouvement depuis un relevé
        else {
            if (!isset($idCompte)) {
                $idCompte = "-1";
            }
        }


        $form = $this->createFormBuilder()
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => isset($operationDebit) ? $operationDebit->getDate() : new DateTime()
            ])
            ->add('compteDebitID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $idCompte
            ])
            ->add('compteCreditID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => isset($operationCredit) ? $operationCredit->getCompte()->getId() : ''
            ])
            ->add('montant',NumberType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => isset($operationDebit) ? $operationDebit->getDebit() : 0.00
            ])
            ->add('est_pointe', CheckboxType::class, [
                'attr' => ['data-plugin-ios-switch' => 'data-plugin-ios-switch'],
                'required' => false,
                'data' => isset($operationDebit) && $operationDebit = '1' ? true : false
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {     
            if ($requete->request->get('form')['montant'] > 0) {
                // Il va falloir créer 2 opérations avec un identifiant commun
                $idVirement = time();
                // -> une pour le débit
                $operationDebit = new Operation;
                $operationDebit->setDate(\DateTime::createFromFormat('d/m/Y', $requete->request->get('form')['date']));
                $operationDebit->setDebit($requete->request->get('form')['montant']);
                $operationDebit->setTypeOperation('VII');
                $operationDebit->setVirementID($idVirement);
                $operationDebit->setEstPointe(isset($requete->request->get('form')['est_pointe']) ? $requete->request->get('form')['est_pointe'] : 0);
                $operationDebit->setCompte($compteRepo->find($requete->request->get('form')['compteDebitID']));
                $operationDebit->setCompteVirementInterne($compteRepo->find($requete->request->get('form')['compteCreditID']));

                $em->persist($operationDebit);
                $em->flush();

                // -> une pour le crédit
                $operationCredit = new Operation;
                $operationCredit->setDate(\DateTime::createFromFormat('d/m/Y', $requete->request->get('form')['date']));
                $operationCredit->setCredit($requete->request->get('form')['montant']);
                $operationCredit->setTypeOperation('VII');
                $operationCredit->setVirementID($idVirement);
                $operationCredit->setEstPointe(isset($requete->request->get('form')['est_pointe']) ? $requete->request->get('form')['est_pointe'] : 0);
                $operationCredit->setCompte($compteRepo->find($requete->request->get('form')['compteCreditID']));
                $operationCredit->setCompteVirementInterne($compteRepo->find($requete->request->get('form')['compteDebitID']));

                $em->persist($operationCredit);
                $em->flush();

                // On positionne le relevé sur l'opération débitrice
                return $this->redirectToRoute('app_myTresorerie_compte_releve', [ 'id' => $operationDebit->getCompte()->getId()]);
            }
            else {
                $this->addFlash("errorMSG", "Merci de saisir un montant positif");
            }
        }
        

        return $this->render('default/backend/myTresorerie/operation_virement.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView(),
            'compte' => $idCompte
        ]);
    }

    /**
     * @Route("/tresorerie/operation/retrait", name="app_myTresorerie_retrait")
     * @Route("/tresorerie/operation/retrait/{id}", name="app_myTresorerie_retrait_edit", methods={"GET", "POST"})
     * @Route("/tresorerie/compte/{idCompte}/retrait", name="app_myTresorerie_compte_retrait_add")
     */
    public function mytresorerie_retrait(?int $id,?int $idCompte,CompteRepository $compteRepo, OperationRepository $operationRepo, Request $requete,EntityManagerInterface $em): Response
    {
        $operation = new Operation;

        // On va gérer une particularité qui va sélectioner le compte uniquement s'il s'agit d'une opération en cours
        // ou si on déclenche l'enregistrement d'un mouvement depuis un relevé
        if (isset($id)) {
            $operation = $operationRepo->find($id);
            $idCompte = $operation->getCompte() ?  $operation->getCompte()->getId() : null;
        }
        else {
            if (!isset($idCompte)) {
                $idCompte = "-1";
            }
        }

        $form = $this->createFormBuilder($operation)
            ->add('type_operation', HiddenType::class, [
                'data' => 'RET',
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $operation->getDate() ? $operation->getDate() : new DateTime()
            ])
            ->add('compteID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $idCompte
            ])
            ->add('debit', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => $operation->getDebit() > 0 ? $operation->getCompte()->getId() : 0.00
            ])
            ->add('est_pointe', CheckboxType::class, [
                'attr' => ['data-plugin-ios-switch' => 'data-plugin-ios-switch'],
                'required' => false
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            if ( $operation->getDebit() > 0) {
                $operation->setCompte($compteRepo->find($requete->request->get('form')['compteID']));
                
                $em->persist($operation);
                $em->flush();

                $this->addFlash("userUpdate", "Ajout retrait effectué");
                $CompteID = $operation->getCompte()->getId();
                return $this->redirectToRoute('app_myTresorerie_compte_releve', [ 'id' => $CompteID]);
            }
            else {
                $this->addFlash("errorMSG", "Merci de saisir un montant positif");
            }
        }

        return $this->render('default/backend/myTresorerie/operation_retrait.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView(),
            'compte' => $idCompte
        ]);
    }

    /**
     * @Route("/tresorerie/operation/pointage/{id}", name="app_operation_pointage", methods ="UPDATE")
     */
    public function operationPointage(Operation $operation, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('operation_pointage_' . $operation->getId(), $requete->request->get('csrf_token'))) {
            if ($operation->getEstPointe()) {
                $operation->setEstPointe(false);
            }
            else {
                $operation->setEstPointe(true);
            }
            $em->persist($operation);
            $em->flush();            
            
            return $this->redirectToRoute('app_myTresorerie_compte_releve', [ 'id' => $operation->getCompte()->getId()]);
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }

    /**
     * @Route("/tresorerie/operation/delete/{id}", name="app_operation_del", methods ="DELETE")
     */
    public function operationDelete(Operation $operation,OperationRepository $operationRepo, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('operation_supprime_' . $operation->getId(), $requete->request->get('csrf_token'))) {
            $CompteID = $operation->getCompte()->getId();
            // Spécificité pour les virements ou il faut supprimer 2 opérations
            if ($operation->getTypeOperation() == 'VII') {
                $operationCredit = $operationRepo->findOperationVirementCredit($operation->getVirementID());
                $operation = $operationRepo->findOperationVirementDebit($operation->getVirementID());
                $em->remove($operationCredit);
                $em->remove($operation);
                $this->addFlash("successMSG", "Opérations supprimées");
            }
            else {
                $em->remove($operation);
                $this->addFlash("successMSG", "Opération supprimée");
            }
            $em->flush();            
            
            return $this->redirectToRoute('app_myTresorerie_compte_releve', [ 'id' => $CompteID]);
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }

    /**
     * @Route("/tresorerie/credit", name="app_myTresorerie_credit")
     */
    public function mytresorerie_credit(?int $id, CreditRepository $creditRepo, Request $requete,EntityManagerInterface $em): Response
    {
        return $this->render('default/backend/myTresorerie/credit.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'credits' => $creditRepo->findAll()
        ]);
    }

        /**
     * @Route("/tresorerie/credit/add", name="app_myTresorerie_credit_add")
     * @Route("/tresorerie/credit/edit/{id}", name="app_myTresorerie_credit_edit", methods={"GET", "POST"})
     */
    public function mytresorerie_credit_form(?int $id,CompteRepository $compteRepo, CreditRepository $creditRepo, Request $requete,EntityManagerInterface $em): Response
    {
        $credit = new Credit();
        if (isset($id)) {
            $credit = $creditRepo->find($id);
        }
        
        $form = $this->createFormBuilder($credit)
            ->add('dateEcheanceOne', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $credit->getDateEcheanceOne() ? $credit->getDateEcheanceOne() : new DateTime()
            ])
            ->add('dateSignature', DateType::class, [
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
                'data' => $credit->getDateSignature() ? $credit->getDateSignature() : new DateTime()
            ])
            ->add('compteID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => ($credit->getCompte()) ? $credit->getCompte()->getId() : -1
            ])
            ->add('libelle', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => ($credit->getLibelle()) ? $credit->getLibelle() : null
            ])
            ->add('estCloture', CheckboxType::class, [
                'data' => $credit->getEstCloture() ? true : false,
                'required' => false
            ])
            ->add('duree', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => $credit->getDuree() > 0 ? $credit->getDuree() : 0.00
            ])   
            ->add('categorieID',SousCategorieChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $credit->getSousCategorie() ?  $credit->getSousCategorie()->getId() : null
            ])
            ->add('montant', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => $credit->getMontant() > 0 ? $credit->getMontant() : 0.00
            ])
            ->add('taux', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => $credit->getTaux() > 0 ? $credit->getTaux() : 0.00
            ])
            ->add('montantAssurance', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'data' => $credit->getMontantAssurance() > 0 ? $credit->getMontantAssurance() : 0.00
            ])
            ->getform()
        ;

        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            if ( $credit->getMontant() > 0) {
                $credit->setCompte($compteRepo->find($requete->request->get('form')['compteID']));
                
                $em->persist($credit);
                $em->flush();

                // On va commencer par créer notre echéance
                $echeance = new Echeance;
                $echeance->setCompte($credit->getCompte());
                $echeance->setCredit($credit);
                $echeance->setTypeTiers('S');
                $echeance->setTiersSociete($credit->getCompte()->getBanque()->getSociete());
                $echeance->setTypeOperation('DEB');
                $echeance->setNombreEcheances($credit->getDuree());
                $echeance->setMontantTotal($credit->getMontant());
                $echeance->setModePaiementTrigramme('PRE');
                $echeance->setDescription($credit->getLibelle());
                $echeance->setDateEcheanceOne($credit->getDateEcheanceOne());

                $em->persist($echeance);
                $em->flush();
                // Puis définir le tableau d'amortissement
                // Formule calcul selon le site
                // https://www.inc-conso.fr/content/comment-sont-calculees-les-mensualites-de-votre-emprunt
                $tauxMensuel = ((1 + ($credit->getTaux()/100))**(1/12))-1; 
                $mensualite = round(($credit->getMontant()*$tauxMensuel*((1+$tauxMensuel)**$credit->getDuree()))/(((1+$tauxMensuel)**$credit->getDuree())-1),2) ;
                $mensualite = $mensualite + $credit->getMontantAssurance();
                $dateEcheance =$credit->getDateEcheanceOne();
                for ($i =0; $i <= $echeance->getNombreEcheances(); $i++ ) {
                    $echeanceOperation = new EcheanceOperation;
                    $echeanceOperation->setMontantEcheance($mensualite);
                    $echeanceOperation->setDateEcheance($dateEcheance);
                    
                    if ($i == $echeance->getNombreEcheances()) {
                        $echeanceOperation->setMontantEcheance($echeance->getMontantTotal() - ($mensualite * $i));
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
                $this->addFlash("userUpdate", "Prêt bancaire enregistré");
            }
            else {
                $this->addFlash("errorMSG", "Merci de saisir un montant positif");
            }
            return $this->redirectToRoute('app_myTresorerie_credit');
        }

        return $this->render('default/backend/myTresorerie/creditForm.html.twig', [
            'controller_name' => 'MyTresorerieController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/tresorerie/credit/delete/{id}", name="app_myTresorerie_credit_del", methods ="DELETE")
     */
    public function mytresorerie_credit_delete(Credit $credit,OperationRepository $operationRepo, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('credit_supprime_' . $credit->getId(), $requete->request->get('csrf_token'))) {
            $em->remove($credit);
            $em->flush();            
            $this->addFlash("successMSG", "Crédit supprimé");
            return $this->redirectToRoute('app_myTresorerie_credit');
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }

    /**
     * @Route("/tresorerie/position/delete/{id}", name="app_myTresorerie_position_del", methods ="DELETE")
     */
    public function mytresorerie_position_delete(Position $position, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('position_supprime_' . $position->getId(), $requete->request->get('csrf_token'))) {
            $CompteID = $position->getCompte()->getId();

            $em->remove($position);
            $em->flush();            
            $this->addFlash("successMSG", "Position supprimée");

            return $this->redirectToRoute('app_myTresorerie_compte_releve', [ 'id' => $CompteID]);
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }
}
