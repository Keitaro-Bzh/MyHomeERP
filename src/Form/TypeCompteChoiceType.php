<?php
namespace App\Form;

use App\Repository\MyFinances\TypeCompteRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeCompteChoiceType extends AbstractType
{
    private $TypeComptesList;

    public function __construct(TypeCompteRepository $TypeCompteRepo)
    {
        $this->TypeComptesList = $TypeCompteRepo->findAll();
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $TypeComptes = array();
        $TypeComptes["--- Sélectionner un type de compte ---"] = '-1';
        foreach($this->TypeComptesList as $TypeCompte) {
            $TypeComptes[$TypeCompte->getNom()] = $TypeCompte->getId();
        }
        $resolver->setDefaults([
            'choices' =>$TypeComptes,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}