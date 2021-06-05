<?php
namespace App\Form;

use App\Repository\MyFinances\BanqueRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BanqueChoiceType extends AbstractType
{
    private $banquesList;

    public function __construct(BanqueRepository $banqueRepo)
    {
        $this->banquesList = $banqueRepo->findAll();
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $banques = array();
        $banques["--- Sélectionnez une banque --"] = '-1';
        foreach($this->banquesList as $banque) {
            $banques[$banque->getSociete()->getNom() . ' (' . $banque->getGuichet() . ')'] = $banque->getId();
        }
        $resolver->setDefaults([
            'choices' =>$banques,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}