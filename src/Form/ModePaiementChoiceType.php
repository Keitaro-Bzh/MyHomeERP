<?php
namespace App\Form;

use App\Repository\MyFinances\ModePaiementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModePaiementChoiceType extends AbstractType
{
    private $ModePaiementList;

    public function __construct(ModePaiementRepository $modePaiementRepo)
    {
        $this->ModePaiementList = $modePaiementRepo->findAll();
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $ModePaiement = array();
        $ModePaiement["--- Sélectionnez un mode de paiement ---"] = '-1';
        $ModePaiement['Virement'] = 'VIR';
        $ModePaiement['Prélèvement'] = 'PRE';
        foreach($this->ModePaiementList as $modePaiement) {
            if ($modePaiement->getModePaiement() == 'CB') {
                $libelle = "Carte bancaire (" . $modePaiement->getTitulaire()->getPrenom() . " " . substr($modePaiement->getTitulaire()->getNom(),0,1) . ")";
            }
            if ($modePaiement->getModePaiement() == 'PAY') {
                $libelle = "PayPal/Paylib (" . $modePaiement->getTitulaire()->getPrenom() . " " . substr($modePaiement->getTitulaire()->getNom(),0,1) . ")";
            }
            if ($modePaiement->getModePaiement() == 'CHQ') {
                $libelle = "Chèque (" . $modePaiement->getTitulaire()->getPrenom() . " " . substr($modePaiement->getTitulaire()->getNom(),0,1) . ")";
            }
            $ModePaiement[$libelle] = $modePaiement->getId();
        }
        $resolver->setDefaults([
            'choices' =>$ModePaiement,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}