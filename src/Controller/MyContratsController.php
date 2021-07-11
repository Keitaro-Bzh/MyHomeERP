<?php

namespace App\Controller;

use App\Entity\MyContrats\Contrat;
use App\Entity\MyContrats\ContratFacturation;
use App\Entity\MyFinances\Echeance;
use App\Entity\MyFinances\Operation;
use App\Entity\MyFinances\EcheanceOperation;
use App\Form\CompteChoiceType;
use App\Form\SocieteChoiceType;
use App\Form\SousCategorieChoiceType;
use App\Repository\MyContacts\SocieteRepository;
use App\Repository\MyContrats\ContratFacturationRepository;
use App\Repository\MyContrats\ContratRepository;
use App\Repository\MyFinances\CompteRepository;
use App\Repository\MyFinances\SousCategorieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyContratsController extends AbstractController
{
    /**
     * @Route("/contrats", name="app_myContrats")
     */
    public function app_myContrats(ContratRepository $contratRepo): Response
    {
        $contrats = $contratRepo->findActif();
        return $this->render('default/backend/myContrats/_home.html.twig', [
            'controller_name' => 'MyContratsController',
            'contrats' => $contrats
        ]);
    }

    /**
     * @Route("/contrats/add", name="app_myContrats_contrat_add")
     * @Route("/contrats/edit/{id}", name="app_myContrats_edit")
     */
    public function app_myContrats_contrat_add(?int $id, SocieteRepository $societeRepo, SousCategorieRepository $sousCategorieRepo, Request $requete, EntityManagerInterface $em, ContratRepository $contratRepo): Response
    {

        $contrat = new Contrat();
        if (isset($id)) {
            $contrat = $contratRepo->find($id);
           
        }

        $form = $this->createFormBuilder($contrat)      
            ->add('SocieteID', SocieteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $contrat->getSociete() ? $contrat->getSociete()->getId() : '-1'
            ])
            ->add('date_signature', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ]
            ])
            ->add('date_fin_contrat', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'required' => false
            ])
            ->add('CategorieID', SousCategorieChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'data' => $contrat->getCategorie() ? $contrat->getCategorie()->getId() : '-1'
            ])
            ->add('reference_contrat', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('facturable', CheckboxType::class, [
                'required' => false
            ])
            ->add('est_archive', CheckboxType::class, [
                'required' => false
            ])
            ->getform()
        ;
        
        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            if ((int)['id' => $requete->request->get('form')['CategorieID'] ] > 0) {
                $contrat->setCategorie($sousCategorieRepo->find($requete->request->get('form')['CategorieID']));
            }
            if ((int)['id' => $requete->request->get('form')['SocieteID'] ] > 0) {
                $contrat->setSociete($societeRepo->find($requete->request->get('form')['SocieteID']));
            }
            if (!isset($requete->request->get('form')['facturable'])) {
                $contrat->setFacturable(0);
            }
            $em->persist($contrat);
            $em->flush();

            $this->addFlash("contactFlashMSG", "Enregistrement effectué");
            return $this->redirectToRoute('app_myContrats');
        }

        return $this->render('default/backend/myContrats/contratsForm.html.twig', [
            'controller_name' => 'MyContratsController',
            'form' => $form->createView(),
        ]);
    }

        /**
     * @Route("/contrats/{id}/facturation/add", name="app_myContrats_facturation_form")
     * @Route("/contrats/{id}/facturation/edit/{idFacturation}", name="app_myContrats_facturation_edit")
     */
    public function app_myContrats_facturation_form(int $id, ?int $idFacturation,CompteRepository $compteRepo, ContratFacturationRepository $contratFacturationRepo, SocieteRepository $societeRepo, SousCategorieRepository $sousCategorieRepo, Request $requete, EntityManagerInterface $em, ContratRepository $contratRepo): Response
    {
        $contrat = $contratRepo->find($id);
        $contratFacturations = $contratFacturationRepo->findBy(['Contrat' => $contrat ]);

        $contratFacturation = new ContratFacturation;
        if (isset($idFacturation)) {
            $contratFacturations = $contratFacturationRepo->find($idFacturation);
        }

        $form = $this->createFormBuilder($contratFacturation)
            ->add('type_mouvement', ChoiceType::class, [
                'choices' => array(
                    'Débit' => 'D',
                    'Crédit' => 'C',
                ),
                'attr' => ['class' => 'form-control'],
                'data' => $contratFacturation->getFrequencePaiement()
            ])
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-plugin-masked' => 'data-plugin-masked-input',
                    'data-input-mask' => '99-99-9999',
                    'placeholder' => '__/__/____'
                ],
                'data' => $contratFacturation->getDateDebut() ? $contratFacturation->getDateDebut() : new DateTime()
            ])
            ->add('date_fin', DateType::class, [
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
                'data' => $contratFacturation->getDateDebut() ? $contratFacturation->getDateDebut() : new DateTime()
            ])
            ->add('frequence_nombre_paiement', NumberType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('frequence_paiement', ChoiceType::class, [
                'choices' => array(
                    '--- Tous les ---' => '-1',
                    '--- x Jours ---' => 'D',
                    '--- x Semaines ---' => 'W',
                    '--- x Mois ---' => 'M',
                    '--- x Ans ---' => 'Y'
                ),
                'attr' => ['class' => 'form-control'],
                'data' => $contratFacturation->getFrequencePaiement()
            ])
            ->add('CompteID', CompteChoiceType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('montant', NumberType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('montant_fraction', CheckboxType::class, [
                'required' => false,
            ])
            ->add('recalcul_operation_anterieur', CheckboxType::class, [
                'required' => false,
                'data' => true
            ])
            ->getform()
        ;
        
        $form->handleRequest($requete);

        if ($form->isSubmitted() && $form->isValid()) {
            if ((int) $requete->request->get('form')['CompteID'] > 0) {
                $contratFacturation->setCompte($compteRepo->find($requete->request->get('form')['CompteID']));
                $contratFacturation->setContrat($contrat);

                // Il faut créer une échéance correspond à notre facturation pour satisfaire
                // les relations entre les tables avant l'enregistrement de notre entité
                $echeance = new Echeance;
                $echeance->setEcheanceFromContratOperation($contratFacturation);

                $em->persist($echeance);
                $em->flush();
                
                // On va générer les opérations d'échéance associées 
                if (count($echeance->getTabEcheanceOperations()) > 0) {
                    for ($i = 1; $i <= count($echeance->getTabEcheanceOperations()); $i++) {
                        $echeanceOperation = new EcheanceOperation;
                        $echeanceOperation = $echeance->getTabEcheanceOperations()[$i];
                        $echeanceOperation->setEcheance($echeance);
                        $em->persist($echeanceOperation);
                        $em->flush();

                            // On va créer les opérations jusqu'à la fin du mois
                            // ou celle du mois si c'est elle
                            if ($echeanceOperation->getDateEcheance() < New DateTime('last day of this month')) {
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
                                if ($contratFacturation->getTypeMouvement() == 'C') {
                                    $operation->setCredit($echeanceOperation->getMontantEcheance());
                                }
                                else{
                                    $operation->setDebit($echeanceOperation->getMontantEcheance());
                                }
                                $operation->setEstPointe(0);

                                $em->persist($operation);
                                $em->flush();
                            }
                    }
                }

                $contratFacturation->setEcheance($echeance);

                // On enregistre notre entité
                $em->persist($contratFacturation);
                $em->flush();

                $this->addFlash("flashMSG", "Enregistrement effectué");
                return $this->redirectToRoute('app_myContrats');
            }
            else {
                $this->addFlash("flashMSG", "Merci de sélectionner un compte");
            }
        }

        return $this->render('default/backend/myContrats/contrats_facturationForm.html.twig', [
            'controller_name' => 'MyContratsController',
            'form' => $form->createView(),
            'contrat' => $contrat,
            'contratFacturations' => $contratFacturations
        ]);
    }

    /**
     * @Route("/contrats/del/{id}", name="app_myContrats_del", methods ="DELETE")
     */
    public function utilisateurDelete(int $id,Contrat $contrat, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('contrat_supprime_' . $contrat->getId(), $requete->request->get('csrf_token'))) {
            $em->remove($contrat);
            $em->flush();

            $this->addFlash("flashMSG", "Enregistrement supprimé");
            return $this->redirectToRoute('app_myContrats');
        }
        else {
            return $this->redirectToRoute('app_hacking');
        }
    }
}
