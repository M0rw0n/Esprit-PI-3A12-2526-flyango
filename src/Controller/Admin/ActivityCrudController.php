<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

#[AdminCrud(routePath: '/activity', routeName: 'activity')]
class ActivityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Activity::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title', 'Titre'),
            TextEditorField::new('description', 'Description'),
            NumberField::new('price', 'Prix (TND)'),
            TextField::new('duration', 'Durée'),
            TextField::new('lieu', 'Lieu'),
            NumberField::new('capacity', 'Capacité'),
            DateField::new('date', 'Date'),
            ImageField::new('image', 'Image')
                ->setBasePath('/uploads/activities')
                ->setUploadDir('public/uploads/activities')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]'),
            BooleanField::new('actif', 'Actif'),
        ];
    }
}
