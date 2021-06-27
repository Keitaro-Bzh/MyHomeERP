<?php
namespace App\Form;

use App\Repository\MyContacts\PersonneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonneChoiceType extends AbstractType
{
    private $PersonnesList;

    public function __construct(PersonneRepository $PersonneRepo)
    {
        $this->PersonnesList = $PersonneRepo->findActif();
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $Personnes = array();
        $Personnes["--- Sélectionnez une personne ---"] = '-1';
        foreach($this->PersonnesList as $Personne) {
            $Personnes[$Personne->getNom() . ' ' . $Personne->getPrenom()] = $Personne->getId();
        }
        $resolver->setDefaults([
            'choices' =>$Personnes,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}