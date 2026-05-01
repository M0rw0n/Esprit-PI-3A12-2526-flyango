<?php

namespace App\Controller;

use App\Entity\Hebergement;
use App\Entity\PromoCode;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/promos')]
class PromoCodeController extends AbstractController
{
    #[Route('', name: 'app_admin_promos')]
    public function index(ManagerRegistry $registry): Response
    {
        $conn = $registry->getConnection();
        $rows = $conn->fetchAllAssociative("SELECT * FROM promo_code ORDER BY id DESC");
        
        return $this->render('admin/promo_codes.html.twig', [
            'promos' => $rows,
            'page_title' => 'Codes Promo'
        ]);
    }

    #[Route('/new', name: 'app_admin_promo_new')]
    public function new(Request $request, ManagerRegistry $registry): Response
    {
        if ($request->isMethod('POST')) {
            $conn = $registry->getConnection();
            $conn->insert('promo_code', [
                'code' => strtoupper($request->request->get('code')),
                'type' => $request->request->get('type'),
                'value' => (float)$request->request->get('value'),
                'max_uses' => $request->request->get('max_uses') ? (int)$request->request->get('max_uses') : null,
                'max_uses_per_user' => $request->request->get('max_uses_per_user') ? (int)$request->request->get('max_uses_per_user') : null,
                'valid_from' => $request->request->get('valid_from') ?: null,
                'valid_until' => $request->request->get('valid_until') ?: null,
                'min_nuits' => $request->request->get('min_nuits') ? (int)$request->request->get('min_nuits') : null,
                'min_amount' => $request->request->get('min_amount') ? (int)$request->request->get('min_amount') : null,
                'active' => 1
            ]);
            
            $this->addFlash('success', 'Code promo créé');
            return $this->redirectToRoute('app_admin_promos');
        }
        
        return $this->render('admin/promo_form.html.twig', [
            'promo' => null,
            'page_title' => 'Nouveau Code Promo'
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_promo_edit')]
    public function edit(int $id, Request $request, ManagerRegistry $registry): Response
    {
        if ($request->isMethod('POST')) {
            $conn = $registry->getConnection();
            $conn->update('promo_code', [
                'code' => strtoupper($request->request->get('code')),
                'type' => $request->request->get('type'),
                'value' => (float)$request->request->get('value'),
                'max_uses' => $request->request->get('max_uses') ?: null,
                'max_uses_per_user' => $request->request->get('max_uses_per_user') ?: null,
                'valid_from' => $request->request->get('valid_from') ?: null,
                'valid_until' => $request->request->get('valid_until') ?: null,
                'min_nuits' => $request->request->get('min_nuits') ?: null,
                'min_amount' => $request->request->get('min_amount') ?: null,
                'active' => $request->request->get('active') ? 1 : 0
            ], ['id' => $id]);
            
            $this->addFlash('success', 'Code promo mis à jour');
            return $this->redirectToRoute('app_admin_promos');
        }
        
        $conn = $registry->getConnection();
        $promo = $conn->fetchAssociative("SELECT * FROM promo_code WHERE id = ?", [$id]);
        
        return $this->render('admin/promo_form.html.twig', [
            'promo' => $promo,
            'page_title' => 'Modifier Code Promo'
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_promo_delete')]
    public function delete(int $id, ManagerRegistry $registry): Response
    {
        $conn = $registry->getConnection();
        $conn->delete('promo_code', ['id' => $id]);
        
        $this->addFlash('success', 'Code promo supprimé');
        return $this->redirectToRoute('app_admin_promos');
    }

    #[Route('/{id}/toggle', name: 'app_admin_promo_toggle')]
    public function toggle(int $id, ManagerRegistry $registry): Response
    {
        $conn = $registry->getConnection();
        $row = $conn->fetchAssociative("SELECT active FROM promo_code WHERE id = ?", [$id]);
        $newActive = $row['active'] ? 0 : 1;
        
        $conn->update('promo_code', ['active' => $newActive], ['id' => $id]);
        
        return $this->redirectToRoute('app_admin_promos');
    }
}