/* ============================================
   PROFIL.JS - User Profile Page
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    // Image preview before upload
    const profileUpload = document.getElementById('profile-upload');
    const coverUpload = document.getElementById('cover-upload');

    if (profileUpload) {
        profileUpload.addEventListener('change', handleProfileImagePreview);
    }

    if (coverUpload) {
        coverUpload.addEventListener('change', handleCoverImagePreview);
    }

    // Tab switching
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            const tabName = e.target.getAttribute('data-bs-target');
            localStorage.setItem('activeTab', tabName);
        });
    });

    // Restore active tab
    const activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        const tabButton = document.querySelector(`[data-bs-target="${activeTab}"]`);
        if (tabButton) {
            const tab = new bootstrap.Tab(tabButton);
            tab.show();
        }
    }

    // Form validation
    const forms = document.querySelectorAll('form[novalidate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Password strength indicator (if on password change page)
    const newPasswordInput = document.querySelector('input[name="change_password_type[newPassword][first]"]');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', updatePasswordStrength);
    }
});

// Handle profile image preview
function handleProfileImagePreview(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (max 5MB)
        if (file.size > 5242880) {
            alert('Le fichier doit faire moins de 5MB');
            e.target.value = '';
            return;
        }

        // Validate file type
        if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type)) {
            alert('Format d\'image invalide. Utilisez JPEG, PNG, GIF ou WebP');
            e.target.value = '';
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(event) {
            console.log('Image preview:', event.target.result);
            // Image will be uploaded when form is submitted
        };
        reader.readAsDataURL(file);
    }
}

// Handle cover photo preview
function handleCoverImagePreview(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (max 5MB)
        if (file.size > 5242880) {
            alert('Le fichier doit faire moins de 5MB');
            e.target.value = '';
            return;
        }

        // Validate file type
        if (!['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.type)) {
            alert('Format d\'image invalide. Utilisez JPEG, PNG, GIF ou WebP');
            e.target.value = '';
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(event) {
            console.log('Cover preview:', event.target.result);
            // Image will be uploaded when form is submitted
        };
        reader.readAsDataURL(file);
    }
}

// Update password strength indicator
function updatePasswordStrength(e) {
    const password = e.target.value;
    let strength = 0;

    // Check length
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (password.length >= 14) strength++;

    // Check for uppercase
    if (/[A-Z]/.test(password)) strength++;

    // Check for lowercase
    if (/[a-z]/.test(password)) strength++;

    // Check for numbers
    if (/[0-9]/.test(password)) strength++;

    // Check for special characters
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

    // Display strength
    const strengthDiv = document.getElementById('password-strength');
    if (strengthDiv) {
        strengthDiv.innerHTML = getStrengthHTML(strength);
    }
}

// Get strength indicator HTML
function getStrengthHTML(strength) {
    const strengthTexts = [
        'Très faible',
        'Faible',
        'Moyen',
        'Bon',
        'Exellent',
        'Très excellent',
        'Très excellent'
    ];

    const strengthColors = [
        'danger',
        'orange',
        'warning',
        'info',
        'success',
        'success',
        'success'
    ];

    return `
        <div class="mt-2">
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-${strengthColors[strength]}" style="width: ${(strength / 7) * 100}%"></div>
            </div>
            <small class="text-${strengthColors[strength]}">
                Force: ${strengthTexts[strength]}
            </small>
        </div>
    `;
}

// Close tab and confirm
function closeTab(url) {
    if (confirm('Êtes-vous sûr? Vos modifications ne seront pas sauvegardées.')) {
        window.location.href = url;
    }
}

// Export profile data
function exportProfile() {
    const profileData = {
        timestamp: new Date().toISOString(),
        profile: document.querySelector('.profil-card')?.innerText || ''
    };

    const dataStr = JSON.stringify(profileData, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `profile-${new Date().getTime()}.json`;
    link.click();
}

console.log('Profil JS Loaded');
