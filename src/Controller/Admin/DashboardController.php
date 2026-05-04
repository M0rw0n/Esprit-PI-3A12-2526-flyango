<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Entity\Circuit;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/superadmin', routeName: 'superadmin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Fly&Go - Admin')
            ->setTranslationDomain('admin')
            ->setLocales(['fr']);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::section('Gestion');
        yield MenuItem::linkToCrud('Activités', 'fa fa-hiking', Activity::class);
        yield MenuItem::linkToCrud('Circuits', 'fa fa-route', Circuit::class);
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setPaginatorPageSize(20)
            ->setDefaultSort(['id' => 'DESC']);
    }
}
