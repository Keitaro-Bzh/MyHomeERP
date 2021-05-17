<?php
namespace App\Form;

use App\Repository\MyFinances\CategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieChoiceType extends AbstractType
{
    private $CategoriesList;

    public function __construct(CategorieRepository $CategorieRepo)
    {
        $this->CategoriesList = $CategorieRepo->findAll();
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $Categories = array();
        foreach($this->CategoriesList as $Categorie) {
            $Categories[$Categorie->getNom()] = $Categorie->getId();
        }
        $resolver->setDefaults([
            'choices' =>$Categories,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}