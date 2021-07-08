<?php
namespace App\Form;

use App\Repository\MyContacts\SocieteRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocieteChoiceType extends AbstractType
{
    private $societesList;

    public function __construct(SocieteRepository $societeRepo)
    {
        $this->societesList = $societeRepo->findActif();
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $societes = array();
        $societes["--- Sélectionnez une societé ---"] = '-1';
        foreach($this->societesList as $societe) {
            $societes[$societe->getNom()] = $societe->getId();
        }
        $resolver->setDefaults([
            'choices' =>$societes,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}