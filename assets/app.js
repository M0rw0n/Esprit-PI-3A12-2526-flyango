import './bootstrap.js';
import './styles/app.css';

const themeKey = 'flyandgo-theme';

const applyTheme = (theme) => {
    const finalTheme = theme === 'dark' ? 'dark' : 'light';
    document.documentElement.dataset.theme = finalTheme;
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const label = finalTheme === 'dark' ? '☀️ Mode clair' : '🌙 Mode sombre';
        button.innerHTML = `<span>${label}</span>`;
        button.setAttribute('aria-pressed', String(finalTheme === 'dark'));
    });
};

const notify = (message, type = 'info') => {
    if (!message) return;
    let stack = document.querySelector('.flash-stack');
    if (!stack) {
        stack = document.createElement('div');
        stack.className = 'flash-stack';
        stack.setAttribute('aria-live', 'polite');
        document.body.appendChild(stack);
    }

    const item = document.createElement('div');
    item.className = `flash flash-${type}`;
    item.dataset.autoDismiss = 'true';
    item.textContent = message;
    stack.prepend(item);
    window.setTimeout(() => item.classList.add('is-leaving'), 3200);
    window.setTimeout(() => item.remove(), 3600);
};

const setButtonLoading = (button, loading) => {
    if (!button) return;
    if (loading) {
        if (!button.dataset.originalLabel) button.dataset.originalLabel = button.innerHTML;
        button.disabled = true;
        button.classList.add('is-loading');
        button.innerHTML = `<span class="spinner" aria-hidden="true"></span>${button.dataset.loadingLabel || button.getAttribute('data-loading-label') || 'Chargement...'}`;
    } else {
        button.disabled = false;
        button.classList.remove('is-loading');
        button.innerHTML = button.dataset.originalLabel || button.innerHTML;
    }
};

const setLoading = (target, loading, message = 'Chargement...') => {
    if (!target) return;
    target.classList.toggle('is-loading-region', !!loading);
    if (loading) {
        target.setAttribute('aria-busy', 'true');
        if (!target.querySelector(':scope > .loading-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `<div class="loading-pill"><span class="spinner" aria-hidden="true"></span><span>${message}</span></div>`;
            target.prepend(overlay);
        }
    } else {
        target.removeAttribute('aria-busy');
        target.querySelectorAll(':scope > .loading-overlay').forEach((node) => node.remove());
    }
};

const getGroupMessage = (field) => {
    if (field.dataset.validateMatch) {
        return field.dataset.validateMessage || 'Les valeurs ne correspondent pas.';
    }
    if (field.dataset.minValueOf) {
        return field.dataset.validateMessage || 'La valeur doit être supérieure ou égale au minimum demandé.';
    }
    if (field.dataset.dateAfter) {
        return field.dataset.validateMessage || 'La date doit être postérieure ou égale à la date précédente.';
    }

    return field.validationMessage || 'Champ invalide.';
};

const applyCustomValidation = (field) => {
    if (!(field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement || field instanceof HTMLSelectElement)) {
        return true;
    }

    field.setCustomValidity('');

    if (field.dataset.validateMatch) {
        const other = document.querySelector(field.dataset.validateMatch);
        if (other && field.value !== other.value) {
            field.setCustomValidity(getGroupMessage(field));
        }
    }

    if (!field.validationMessage && field.dataset.minValueOf) {
        const other = document.querySelector(field.dataset.minValueOf);
        if (other && other.value && field.value && Number(field.value) < Number(other.value)) {
            field.setCustomValidity(getGroupMessage(field));
        }
    }

    if (!field.validationMessage && field.dataset.dateAfter) {
        const other = document.querySelector(field.dataset.dateAfter);
        if (other && other.value && field.value && field.value < other.value) {
            field.setCustomValidity(getGroupMessage(field));
        }
    }

    return field.checkValidity();
};

const refreshFieldState = (field) => {
    if (!(field instanceof HTMLInputElement || field instanceof HTMLTextAreaElement || field instanceof HTMLSelectElement)) {
        return;
    }

    field.classList.remove('is-valid', 'is-invalid');
    const feedback = field.parentElement?.querySelector(':scope > .field-feedback');
    if (feedback) feedback.remove();

    if (!field.willValidate) return;
    if (!field.value && !field.required) return;

    applyCustomValidation(field);
    const valid = field.checkValidity();
    field.classList.add(valid ? 'is-valid' : 'is-invalid');

    if (!valid) {
        const hint = document.createElement('small');
        hint.className = 'field-feedback';
        hint.textContent = field.validationMessage;
        field.parentElement?.appendChild(hint);
    }
};

const validateCheckboxGroups = (form) => {
    let valid = true;
    form.querySelectorAll('[data-checkbox-group]').forEach((group) => {
        const checkboxes = group.querySelectorAll('input[type="checkbox"]');
        const checked = Array.from(checkboxes).some((item) => item.checked);
        let feedback = group.querySelector(':scope > .field-feedback');

        if (!checked) {
            valid = false;
            group.classList.add('is-invalid-group');
            if (!feedback) {
                feedback = document.createElement('small');
                feedback.className = 'field-feedback';
                feedback.textContent = group.dataset.checkboxMessage || 'Sélectionnez au moins une option.';
                group.appendChild(feedback);
            }
        } else {
            group.classList.remove('is-invalid-group');
            if (feedback) feedback.remove();
        }
    });

    return valid;
};

const initThemeToggle = () => {
    const stored = localStorage.getItem(themeKey) || document.documentElement.dataset.theme || 'light';
    applyTheme(stored);
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem(themeKey, next);
            applyTheme(next);
            notify(next === 'dark' ? 'Mode sombre activé.' : 'Mode clair activé.', 'success');
        });
    });
};

const initFlashes = () => {
    document.querySelectorAll('[data-auto-dismiss]').forEach((item, index) => {
        window.setTimeout(() => item.classList.add('is-visible'), 30 + (index * 40));
        window.setTimeout(() => item.classList.add('is-leaving'), 3200 + (index * 40));
        window.setTimeout(() => item.remove(), 3600 + (index * 40));
    });
};

const initForms = () => {
    document.querySelectorAll('form[data-validate-form], form.enhanced-form').forEach((form) => {
        form.querySelectorAll('input, textarea, select').forEach((field) => {
            field.addEventListener('input', () => refreshFieldState(field));
            field.addEventListener('blur', () => refreshFieldState(field));
            if (field.dataset.minValueOf || field.dataset.dateAfter || field.dataset.validateMatch) {
                const selector = field.dataset.minValueOf || field.dataset.dateAfter || field.dataset.validateMatch;
                const relatedField = selector ? document.querySelector(selector) : null;
                relatedField?.addEventListener('input', () => refreshFieldState(field));
                relatedField?.addEventListener('change', () => refreshFieldState(field));
            }
        });

        form.addEventListener('submit', (event) => {
            let valid = true;
            form.querySelectorAll('input, textarea, select').forEach((field) => {
                refreshFieldState(field);
                if (field.willValidate && !field.checkValidity()) valid = false;
            });

            if (!validateCheckboxGroups(form)) {
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
                notify('Merci de corriger les champs mis en évidence.', 'warning');
                form.querySelector('.is-invalid, .is-invalid-group input, .is-invalid-group')?.focus?.();
                return;
            }

            const submitButton = event.submitter || form.querySelector('button[type="submit"], .btn[type="submit"]');
            setButtonLoading(submitButton, true);
        });
    });
};

const initGlobalButtons = () => {
    document.querySelectorAll('button[data-loading-label]').forEach((button) => {
        button.dataset.loadingLabel = button.getAttribute('data-loading-label') || 'Chargement...';
    });
};

window.FlyAndGo = {
    notify,
    setLoading,
    setButtonLoading,
    refreshFieldState,
};

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initFlashes();
    initForms();
    initGlobalButtons();
});
