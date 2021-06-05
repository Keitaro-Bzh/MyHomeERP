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
        $this->banquesList = $banqueRepo->findAll();
        $this->comptesList = $CompteRepo->findAll();
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
                $tabCompte[$compte->getLibelle() . ' (' . $compte->getTitulaire()->getNom() . ' ' . $compte->getTitulaire()->getPrenom() . ')' ] = $compte->getId(); 
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