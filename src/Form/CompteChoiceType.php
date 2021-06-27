<?php
namespace App\Form;

use App\Repository\MyFinances\BanqueRepository;
use App\Repository\MyFinances\CompteRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompteChoiceType extends AbstractType
{
    private $comptesList;

    public function __construct(BanqueRepository $banqueRepo, CompteRepository $CompteRepo)
    {
        $this->banquesList = $banqueRepo->findActif();
        //$this->comptesList = $CompteRepo->findAll();
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $listeComptes = array();
        $banques = array();
        $listeComptes["--- Sélectionnez une compte --"] = '-1';
        foreach($this->banquesList as $banque) {
            $tabCompte = array();
            $comptes = $banque->getComptes()->toArray();
            foreach ($comptes as $compte) {
                $listeTitulaire = $compte->getTitulaire()->getPrenom() . ' ' . substr($compte->getTitulaire()->getNom(),0,1);
                if ($compte->getCoTitulaire()) {
                    $listeTitulaire = $listeTitulaire . '. / ' . $compte->getCoTitulaire()->getPrenom() . ' ' . substr($compte->getCoTitulaire()->getNom(),0,1);
                }
                $tabCompte[$compte->getLibelle() . ' (' . $listeTitulaire . '.)' ] = $compte->getId(); 
            }
            $listeComptes[$banque->getSociete()->getNom()] = $tabCompte;
        }
        $resolver->setDefaults([
            'choices' =>$listeComptes,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}