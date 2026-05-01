<?php

namespace App\Admin;

use App\Entity\Circuit;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CircuitAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'circuit';
    protected $baseRouteName = 'admin_circuit';

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'ID'])
            ->add('titre', null, ['label' => 'Titre'])
            ->add('destination', null, ['label' => 'Destination'])
            ->add('type', null, ['label' => 'Type'])
            ->add('status', null, ['label' => 'Statut'])
            ->add('difficulte', null, ['label' => 'Difficulté'])
            ->add('actif', null, ['label' => 'Actif'])
            ->add('isAiGenerated', null, ['label' => 'IA Généré'])
            ->add('sourceType', null, ['label' => 'Source'])
            ->add('prix', 'doctrine_orm_number', ['label' => 'Prix'])
            ->add('startDate', null, ['label' => 'Date début'])
            ->add('endDate', null, ['label' => 'Date fin']);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', null, ['label' => 'ID'])
            ->add('titre', null, ['label' => 'Titre'])
            ->add('destination', null, ['label' => 'Destination'])
            ->add('type', null, ['label' => 'Type'])
            ->add('status', null, ['label' => 'Statut'])
            ->add('duree', null, ['label' => 'Durée (jours)'])
            ->add('prix', null, ['label' => 'Prix (TND)'])
            ->add('noteMoyenne', null, ['label' => 'Note'])
            ->add('nbAvis', null, ['label' => 'Nb Avis'])
            ->add('actif', 'boolean', ['label' => 'Actif'])
            ->add('isAiGenerated', 'boolean', ['label' => 'IA'])
            ->add('startDate', null, ['label' => 'Début'])
            ->add('endDate', null, ['label' => 'Fin'])
            ->add(ListMapper::NAME_ACTIONS, ListMapper::TYPE_ACTIONS, [
                'label' => 'Actions',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Informations générales', ['class' => 'col-md-6'])
                ->add('titre', TextType::class, ['label' => 'Titre'])
                ->add('slug', TextType::class, ['label' => 'Slug', 'required' => false])
                ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false])
                ->add('destination', TextType::class, ['label' => 'Destination'])
                ->add('depart', TextType::class, ['label' => 'Départ', 'required' => false])
            ->end()
            ->with('Détails du circuit', ['class' => 'col-md-6'])
                ->add('type', TextType::class, ['label' => 'Type', 'required' => false])
                ->add('status', TextType::class, ['label' => 'Statut', 'required' => false])
                ->add('difficulte', TextType::class, ['label' => 'Difficulté', 'required' => false])
                ->add('duree', IntegerType::class, ['label' => 'Durée (jours)', 'required' => false])
            ->end()
            ->with('Dates et prix', ['class' => 'col-md-6'])
                ->add('startDate', DateType::class, ['label' => 'Date début', 'required' => false])
                ->add('endDate', DateType::class, ['label' => 'Date fin', 'required' => false])
                ->add('prix', NumberType::class, ['label' => 'Prix (TND)'])
                ->add('budget', NumberType::class, ['label' => 'Budget', 'required' => false])
                ->add('placesDisponibles', IntegerType::class, ['label' => 'Places disponibles', 'required' => false])
            ->end()
            ->with('Promotion', ['class' => 'col-md-6'])
                ->add('promoPrix', NumberType::class, ['label' => 'Prix promo', 'required' => false])
                ->add('promoStart', DateType::class, ['label' => 'Début promo', 'required' => false])
                ->add('promoEnd', DateType::class, ['label' => 'Fin promo', 'required' => false])
            ->end()
            ->with('Paramètres', ['class' => 'col-md-6'])
                ->add('image', TextType::class, ['label' => 'Image URL', 'required' => false])
                ->add('actif', CheckboxType::class, ['label' => 'Actif', 'required' => false])
                ->add('isCustom', CheckboxType::class, ['label' => 'Personnalisé', 'required' => false])
                ->add('isAiGenerated', CheckboxType::class, ['label' => 'Généré par IA', 'required' => false])
            ->end()
            ->with('Plan B', ['class' => 'col-md-12'])
                ->add('planB', TextareaType::class, ['label' => 'Plan B', 'required' => false])
            ->end();
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Informations', ['class' => 'col-md-6'])
                ->add('id', null, ['label' => 'ID'])
                ->add('titre', null, ['label' => 'Titre'])
                ->add('slug', null, ['label' => 'Slug'])
                ->add('destination', null, ['label' => 'Destination'])
                ->add('depart', null, ['label' => 'Départ'])
            ->end()
            ->with('Détails', ['class' => 'col-md-6'])
                ->add('type', null, ['label' => 'Type'])
                ->add('status', null, ['label' => 'Statut'])
                ->add('difficulte', null, ['label' => 'Difficulté'])
                ->add('duree', null, ['label' => 'Durée (jours)'])
            ->end()
            ->with('Prix et places', ['class' => 'col-md-6'])
                ->add('prix', null, ['label' => 'Prix (TND)'])
                ->add('budget', null, ['label' => 'Budget'])
                ->add('placesDisponibles', null, ['label' => 'Places disponibles'])
            ->end()
            ->with('Dates', ['class' => 'col-md-6'])
                ->add('startDate', null, ['label' => 'Date début'])
                ->add('endDate', null, ['label' => 'Date fin'])
            ->end()
            ->with('Notes et avis', ['class' => 'col-md-6'])
                ->add('noteMoyenne', null, ['label' => 'Note moyenne'])
                ->add('nbAvis', null, ['label' => 'Nombre d\'avis'])
            ->end()
            ->with('Statut', ['class' => 'col-md-6'])
                ->add('actif', null, ['label' => 'Actif'])
                ->add('isCustom', null, ['label' => 'Personnalisé'])
                ->add('isAiGenerated', null, ['label' => 'Généré par IA'])
                ->add('sourceType', null, ['label' => 'Source'])
            ->end()
            ->with('Image', ['class' => 'col-md-12'])
                ->add('image', null, ['label' => 'Image'])
            ->end()
            ->with('Description', ['class' => 'col-md-12'])
                ->add('description', null, ['label' => 'Description'])
            ->end()
            ->with('Dates système', ['class' => 'col-md-6'])
                ->add('createdAt', null, ['label' => 'Créé le'])
            ->end();
    }

    public function toString(object $object): string
    {
        return $object instanceof Circuit
            ? $object->getTitre() ?? "Circuit #" . $object->getId()
            : "Circuit";
    }
}
