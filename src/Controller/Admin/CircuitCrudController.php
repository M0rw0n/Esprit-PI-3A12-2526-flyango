<?php

namespace App\Controller\Admin;

use App\Entity\Circuit;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

#[AdminCrud(routePath: '/circuit', routeName: 'circuit')]
class CircuitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Circuit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->onlyOnIndex(),
            TextField::new('titre', 'Titre'),
            TextEditorField::new('description', 'Description'),
            TextField::new('destination', 'Destination'),
            TextField::new('type', 'Type'),
            TextField::new('status', 'Statut'),
            TextField::new('duree', 'Durée (jours)'),
            NumberField::new('prix', 'Prix (TND)'),
            NumberField::new('budget', 'Budget'),
            NumberField::new('placesDisponibles', 'Places'),
            ImageField::new('image', 'Image')
                ->setBasePath('/uploads/circuits')
                ->setUploadDir('public/uploads/circuits')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]'),
            DateField::new('startDate', 'Date début'),
            DateField::new('endDate', 'Date fin'),
            BooleanField::new('organise', 'Organisé'),
        ];
    }
}
