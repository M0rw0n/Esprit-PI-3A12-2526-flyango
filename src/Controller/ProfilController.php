<?php

namespace App\Controller;

use App\Entity\ProfilVoyageur;
use App\Form\ChangePasswordType;
use App\Form\ProfilVoyageurType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\ImageService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil')]
#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserService $userService,
        private ImageService $imageService,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    ) {}

    #[IsGranted('ROLE_USER')]

    #[Route('', name: 'app_profil')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Create traveler profile if doesn't exist
        if ($user->getProfilVoyageur() === null) {
            $profil = new ProfilVoyageur();
            $profil->setUser($user);
            $profil->setDestinationPreferee('À déterminer');
            $profil->setTypeVoyage('Adventure');
            $profil->setBudget('1000');
            $user->setProfilVoyageur($profil);
            $this->entityManager->persist($profil);
            $this->entityManager->flush();
        }

        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'currentTab' => 'info',
        ]);
    }

    #[Route('/edit-info', name: 'app_profil_edit_info', methods: ['GET', 'POST'])]
    public function editInfo(Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->updateUser($user);
            $this->addFlash('success', 'Vos informations ont été mises à jour.');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/edit_info.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/edit-profil-voyageur', name: 'app_profil_edit_profil_voyageur', methods: ['GET', 'POST'])]
    public function editProfilVoyageur(Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $profil = $user->getProfilVoyageur();

        if (!$profil) {
            throw $this->createNotFoundException('Profil voyageur non trouvé');
        }

        $form = $this->createForm(ProfilVoyageurType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($profil);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre profil voyageur a été mis à jour.');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/edit_profil_voyageur.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/change-password', name: 'app_profil_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();

            // Verify current password
            if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('app_profil_change_password');
            }

            $newPassword = $form->get('newPassword')->getData();
            $this->userService->updateUserPassword($user, $newPassword);

            $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/change_password.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/upload-profile-picture', name: 'app_profil_upload_profile_picture', methods: ['POST'])]
    public function uploadProfilePicture(Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $file = $request->files->get('profile_picture');

        if (!$file instanceof UploadedFile) {
            $this->addFlash('error', 'Aucun fichier envoyé.');
            return $this->redirectToRoute('app_profil');
        }

        try {
            $path = $this->imageService->uploadProfilePicture($file, $user);
            $user->setProfilePicturePath($path);
            $this->userService->updateUser($user);
            $this->addFlash('success', 'Photo de profil mise à jour.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors du téléchargement: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_profil');
    }

    #[Route('/upload-cover-photo', name: 'app_profil_upload_cover_photo', methods: ['POST'])]
    public function uploadCoverPhoto(Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $file = $request->files->get('cover_photo');

        if (!$file instanceof UploadedFile) {
            $this->addFlash('error', 'Aucun fichier envoyé.');
            return $this->redirectToRoute('app_profil');
        }

        try {
            $path = $this->imageService->uploadCoverPhoto($file, $user);
            $user->setCoverPhotoPath($path);
            $this->userService->updateUser($user);
            $this->addFlash('success', 'Photo de couverture mise à jour.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors du téléchargement: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_profil');
    }

    #[Route('/remove-profile-picture', name: 'app_profil_remove_profile_picture', methods: ['POST'])]
    public function removeProfilePicture(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $this->imageService->removeImage($user, 'profile');
        $this->userService->updateUser($user);
        $this->addFlash('success', 'Photo de profil supprimée.');

        return $this->redirectToRoute('app_profil');
    }

    #[Route('/remove-cover-photo', name: 'app_profil_remove_cover_photo', methods: ['POST'])]
    public function removeCoverPhoto(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $this->imageService->removeImage($user, 'cover');
        $this->userService->updateUser($user);
        $this->addFlash('success', 'Photo de couverture supprimée.');

        return $this->redirectToRoute('app_profil');
    }
}
