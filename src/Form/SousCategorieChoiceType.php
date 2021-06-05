<?php
namespace App\Form;

use App\Repository\MyFinances\CategorieRepository;
use App\Repository\MyFinances\SousCategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SousCategorieChoiceType extends AbstractType
{
    private $categorieList;

    public function __construct(CategorieRepository $categorieRepo, SousCategorieRepository $SousCategorieRepo)
    {
        $this->categorieList = $categorieRepo->findAll();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $listeCategories = array();
        $listeCategories["--- Sélectionnez une categorie --"] = '-1';
        foreach($this->categorieList as $categorie) {
            $tabCategorie = array();
            $sousCategories = $categorie->getSousCategories()->toArray();
            foreach ($sousCategories as $sousCategorie) {
                $tabCategorie[$sousCategorie->getnom()] = $sousCategorie->getId(); 
            }
            $listeCategories[strtoupper($categorie->getNom())] = $tabCategorie;
        }
        $resolver->setDefaults([
            'choices' =>$listeCategories,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}