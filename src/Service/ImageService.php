<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageService
{
    private string $profilePictureDir = 'uploads/profile-pictures';
    private string $coverPhotoDir = 'uploads/cover-photos';
    private array $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private int $maxFileSize = 5242880; // 5MB

    public function __construct(
        private string $publicDir,
        private SluggerInterface $slugger,
    ) {}

    /**
     * Upload a profile picture
     */
    public function uploadProfilePicture(UploadedFile $file, User $user): ?string
    {
        return $this->uploadImage($file, $user, 'profile');
    }

    /**
     * Upload a cover photo
     */
    public function uploadCoverPhoto(UploadedFile $file, User $user): ?string
    {
        return $this->uploadImage($file, $user, 'cover');
    }

    /**
     * Generic image upload method
     */
    private function uploadImage(UploadedFile $file, User $user, string $type): ?string
    {
        // Validate file
        if (!$this->validateImage($file)) {
            throw new \Exception('Invalid image file');
        }

        // Determine directory
        $directory = $type === 'profile' ? $this->profilePictureDir : $this->coverPhotoDir;
        $targetDir = $this->publicDir . '/' . $directory;

        // Create directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Generate unique filename
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        // Move file
        $file->move($targetDir, $newFilename);

        // Return relative path for storage
        return $directory . '/' . $newFilename;
    }

    /**
     * Remove an image
     */
    public function removeImage(User $user, string $type): void
    {
        $filePath = $type === 'profile' ? $user->getProfilePicturePath() : $user->getCoverPhotoPath();

        if (!$filePath) {
            return;
        }

        $fullPath = $this->publicDir . '/' . $filePath;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        if ($type === 'profile') {
            $user->setProfilePicturePath(null);
        } else {
            $user->setCoverPhotoPath(null);
        }
    }

    /**
     * Validate image file
     */
    private function validateImage(UploadedFile $file): bool
    {
        // Check file type
        if (!in_array($file->getMimeType(), $this->allowedMimes)) {
            return false;
        }

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            return false;
        }

        return true;
    }

    /**
     * Get avatar HTML (initials or image)
     */
    public function getAvatarHtml(User $user, int $size = 50): string
    {
        if ($user->getProfilePicturePath()) {
            return sprintf(
                '<img src="/%s" alt="%s" class="avatar avatar-%d" />',
                $user->getProfilePicturePath(),
                htmlspecialchars($user->getNomComplet()),
                $size
            );
        }

        // Generate avatar with initials
        $initials = $user->getInitiales();
        $bgColor = $this->getColorForInitials($initials);

        return sprintf(
            '<div class="avatar avatar-%d avatar-initials" style="background-color: %s;">%s</div>',
            $size,
            $bgColor,
            htmlspecialchars($initials)
        );
    }

    /**
     * Get consistent color for initials based on hash
     */
    private function getColorForInitials(string $initials): string
    {
        $colors = [
            '#16a085', '#1abc9c', '#27ae60', '#2ecc71',
            '#3498db', '#2980b9', '#9b59b6', '#8e44ad',
            '#e67e22', '#d35400', '#e74c3c', '#c0392b',
        ];

        $hash = crc32($initials);
        $index = $hash % count($colors);

        return $colors[$index];
    }
}
