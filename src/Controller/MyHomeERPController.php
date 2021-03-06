<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Form\PersonneChoiceType;
use App\Repository\MyContacts\PersonneRepository;;
use App\Repository\MyFinances\BanqueRepository;
use App\Repository\MyFinances\OperationRepository;
use App\Repository\MyFinances\CategorieRepository;
use App\Repository\MyFinances\CompteRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class MyHomeERPController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function app_index(OperationRepository $operationRepo, CategorieRepository $categorieRepo,BanqueRepository $banqueRepo, CompteRepository $compteRepo): Response
    {
        if ($this->getUser()) {
            $listeOperationEcheance = $operationRepo->findOperationsNonRapprocheesAll();
            $statistiquesCategorie = $categorieRepo->sqlStatistiqueSoldeParCategorie();
            $banques = $banqueRepo->findActif();
            $comptes = $compteRepo->findActif();

            return $this->render('default/backend/tableauBord.html.twig', [
                'controller_name' => 'MyHomeERPController',
                'listeEcheances' => $listeOperationEcheance,
                'categories' => $statistiquesCategorie,
                'banques' => $banques,
                'comptes' => $comptes
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
            'controller_name' => 'MyHomeERPController',
        ]);
    }

    /**
     * @Route("/configuration", name="app_configuration")
     */
    public function app_configuration(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('default/backend/configuration.html.twig', [
            'controller_name' => 'MyHomeERPController',
            'tableUsers' => $users,
        ]);
    }

    /**
     * @Route("/configuration/profil", name="app_configuration_utilisateur_edit")
     */
    public function app_utilisateurEdit(PersonneRepository $personneRepo, UserRepository $userRepo, Request $requete, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $personnes = $personneRepo->findAll();
        $listPersonnes = array();
        foreach ($personnes as $personne) {
            $listPersonnes[$personne->getNom() . ' ' . $personne->getPrenom()] = $personne->getId(); 
        }
        $form = $this->createFormBuilder($user)  
            ->add('email', EmailType::class, [
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'disabled' => 'disabled',
                'data' => $user->getEmail()
            ])      
            ->add('pseudo', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('personneID', PersonneChoiceType::class, [
               'mapped'=>false,
               'attr' => [ 'class' => 'form-control'],
               'required' => false,
                'data' => ($this->getUser()->getPersonne()) ? $this->getUser()->getPersonne()->getID() : ''
           ])
            ->getform()
        ;

        $form->handleRequest($requete);
        if ($form->isSubmitted() && $form->isValid()) {
            if ((int)['id' => $requete->request->get('form')['personneID'] ] > 0) {
                $personne = $personneRepo->find($requete->request->get('form')['personneID']);
                $user->setPersonne($personne);
            }

            try {
                $em->persist($user);
                $em->flush();
                $this->addFlash("userUpdate", "Votre profil a ??t?? mis ?? jour");
            }
            catch (UniqueConstraintViolationException $e) {
                $this->addFlash("errorUpdate", "Ce pseudo est d??j?? utilis??. Merci d'en saisr un autre");
            }
            return $this->redirectToRoute('app_configuration_utilisateur_edit');
        }

        return $this->render('default/backend/profil.html.twig', [
            'controller_name' => 'MyHomeERPController',
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/configuration/utilisateur/del/{id<[0-9+]>}", name="app_configuration_utilisateur_del", methods ="DELETE")
     */
    public function utilisateurDelete(User $user, Request $requete, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('user_supprime_' . $user->getId(), $requete->request->get('csrf_token'))) {
            $em->remove($user);
            $em->flush();

            return $this->render('default/backend/configuration.html.twig', [
                'controller_name' => 'MyHomeERPController',
            ]);
        }
    }
        


    /**
     * @Route("/calendrier", name="app_calendrier")
     */
    public function calendrier(): Response
    {
        return $this->render('default/backend/calendrier.html.twig', [
            'controller_name' => 'MyHomeERPController',
        ]);
    }

    /**
     * @Route("/hacking", name="app_hacking")
     */
    public function hacking(): Response
    {
        return $this->redirect('https://www.google.fr/search?q=ouioui');
    }

}
