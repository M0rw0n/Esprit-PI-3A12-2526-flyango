<?php

namespace App\Admin;

use App\Entity\Activity;
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

class ActivityAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'activity';
    protected $baseRouteName = 'admin_activity';

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id', null, ['label' => 'ID'])
            ->add('title', null, ['label' => 'Titre'])
            ->add('lieu', null, ['label' => 'Lieu'])
            ->add('category', null, ['label' => 'Catégorie'])
            ->add('price', 'doctrine_orm_number', ['label' => 'Prix'])
            ->add('actif', null, ['label' => 'Actif']);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', null, ['label' => 'ID'])
            ->add('title', null, ['label' => 'Titre'])
            ->add('lieu', null, ['label' => 'Lieu'])
            ->add('category', null, ['label' => 'Catégorie'])
            ->add('price', null, ['label' => 'Prix (TND)'])
            ->add('duration', null, ['label' => 'Durée'])
            ->add('rating', null, ['label' => 'Note'])
            ->add('actif', 'boolean', ['label' => 'Actif'])
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
            ->with('Informations', ['class' => 'col-md-6'])
                ->add('title', TextType::class, ['label' => 'Titre'])
                ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false])
                ->add('lieu', TextType::class, ['label' => 'Lieu'])
                ->add('category', TextType::class, ['label' => 'Catégorie', 'required' => false])
            ->end()
            ->with('Détails', ['class' => 'col-md-6'])
                ->add('price', NumberType::class, ['label' => 'Prix (TND)'])
                ->add('duration', TextType::class, ['label' => 'Durée', 'required' => false])
                ->add('capacity', IntegerType::class, ['label' => 'Capacité', 'required' => false])
                ->add('actif', CheckboxType::class, ['label' => 'Actif', 'required' => false])
            ->end()
            ->with('Localisation', ['class' => 'col-md-6'])
                ->add('latitude', NumberType::class, ['label' => 'Latitude', 'required' => false])
                ->add('longitude', NumberType::class, ['label' => 'Longitude', 'required' => false])
            ->end()
            ->with('Médias', ['class' => 'col-md-12'])
                ->add('image', TextType::class, ['label' => 'Image URL', 'required' => false])
            ->end();
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Informations', ['class' => 'col-md-6'])
                ->add('id', null, ['label' => 'ID'])
                ->add('title', null, ['label' => 'Titre'])
                ->add('lieu', null, ['label' => 'Lieu'])
                ->add('category', null, ['label' => 'Catégorie'])
            ->end()
            ->with('Prix et disponibilité', ['class' => 'col-md-6'])
                ->add('price', null, ['label' => 'Prix (TND)'])
                ->add('duration', null, ['label' => 'Durée'])
                ->add('capacity', null, ['label' => 'Capacité'])
            ->end()
            ->with('Notes', ['class' => 'col-md-6'])
                ->add('rating', null, ['label' => 'Note'])
                ->add('noteMoyenne', null, ['label' => 'Note moyenne'])
                ->add('nbAvis', null, ['label' => 'Nombre d\'avis'])
            ->end()
            ->with('Statut', ['class' => 'col-md-6'])
                ->add('actif', null, ['label' => 'Actif'])
            ->end()
            ->with('Localisation', ['class' => 'col-md-6'])
                ->add('latitude', null, ['label' => 'Latitude'])
                ->add('longitude', null, ['label' => 'Longitude'])
            ->end()
            ->with('Médias', ['class' => 'col-md-12'])
                ->add('image', null, ['label' => 'Image'])
            ->end()
            ->with('Description', ['class' => 'col-md-12'])
                ->add('description', null, ['label' => 'Description'])
            ->end();
    }

    public function toString(object $object): string
    {
        return $object instanceof Activity
            ? $object->getTitle() ?? "Activité #" . $object->getId()
            : "Activité";
    }
}
