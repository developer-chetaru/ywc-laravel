// Strong Password Validation
// Pattern: At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special character

const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};':"\\|,.<>\/])[A-Za-z\d@$!%*?&#^()_+\-=\[\]{};':"\\|,.<>\/]{8,}$/;

function validatePassword(password) {
    const errors = [];
    const checks = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[@$!%*?&#^()_+\-=\[\]{};':"\\|,.<>\/]/.test(password)
    };

    if (!checks.length) errors.push('At least 8 characters');
    if (!checks.uppercase) errors.push('One uppercase letter');
    if (!checks.lowercase) errors.push('One lowercase letter');
    if (!checks.number) errors.push('One number');
    if (!checks.special) errors.push('One special character');

    return {
        isValid: passwordPattern.test(password),
        errors,
        checks
    };
}

function getPasswordStrength(password) {
    if (!password) return { strength: 0, label: '', color: '' };
    
    const validation = validatePassword(password);
    const passedChecks = Object.values(validation.checks).filter(Boolean).length;
    
    if (passedChecks <= 2) {
        return { strength: 1, label: 'Weak', color: 'red' };
    } else if (passedChecks <= 4) {
        return { strength: 2, label: 'Medium', color: 'yellow' };
    } else if (validation.isValid) {
        return { strength: 3, label: 'Strong', color: 'green' };
    } else {
        return { strength: 2, label: 'Medium', color: 'yellow' };
    }
}

function initPasswordValidation(inputId, requirementsId = null, strengthId = null) {
    const passwordInput = document.getElementById(inputId);
    if (!passwordInput) return;

    let requirementsDiv = null;
    let strengthDiv = null;

    if (requirementsId) {
        requirementsDiv = document.getElementById(requirementsId);
    }

    if (strengthId) {
        strengthDiv = document.getElementById(strengthId);
    }

    // Create requirements div if it doesn't exist
    if (!requirementsDiv && passwordInput.parentElement) {
        requirementsDiv = document.createElement('div');
        requirementsDiv.id = requirementsId || inputId + '_requirements';
        requirementsDiv.className = 'password-requirements mt-2 text-sm';
        passwordInput.parentElement.appendChild(requirementsDiv);
    }

    // Create strength div if it doesn't exist
    if (!strengthDiv && passwordInput.parentElement) {
        strengthDiv = document.createElement('div');
        strengthDiv.id = strengthId || inputId + '_strength';
        strengthDiv.className = 'password-strength mt-2';
        passwordInput.parentElement.appendChild(strengthDiv);
    }

    function updateValidation() {
        const password = passwordInput.value;
        const validation = validatePassword(password);
        const strength = getPasswordStrength(password);

        // Update requirements display
        if (requirementsDiv) {
            const requirements = [
                { check: validation.checks.length, text: 'At least 8 characters' },
                { check: validation.checks.uppercase, text: 'One uppercase letter (A-Z)' },
                { check: validation.checks.lowercase, text: 'One lowercase letter (a-z)' },
                { check: validation.checks.number, text: 'One number (0-9)' },
                { check: validation.checks.special, text: 'One special character (@$!%*?&#^()_+-=[]{};\':"|,.<>/)' }
            ];

            requirementsDiv.innerHTML = requirements.map(req => `
                <div class="flex items-center ${req.check ? 'text-green-600' : 'text-gray-500'}">
                    <i class="fa-solid ${req.check ? 'fa-check-circle' : 'fa-circle'} mr-2"></i>
                    <span>${req.text}</span>
                </div>
            `).join('');
        }

        // Update strength display
        if (strengthDiv && password) {
            const colors = {
                red: 'bg-red-500',
                yellow: 'bg-yellow-500',
                green: 'bg-green-500'
            };
            strengthDiv.innerHTML = `
                <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="${colors[strength.color]} h-2 rounded-full transition-all" style="width: ${(strength.strength / 3) * 100}%"></div>
                    </div>
                    <span class="text-sm font-medium text-${strength.color}-600">${strength.label}</span>
                </div>
            `;
        } else if (strengthDiv) {
            strengthDiv.innerHTML = '';
        }

        // Update input border color
        if (password) {
            if (validation.isValid) {
                passwordInput.classList.remove('border-red-500');
                passwordInput.classList.add('border-green-500');
            } else {
                passwordInput.classList.remove('border-green-500');
                passwordInput.classList.add('border-red-500');
            }
        } else {
            passwordInput.classList.remove('border-red-500', 'border-green-500');
        }
    }

    passwordInput.addEventListener('input', updateValidation);
    passwordInput.addEventListener('blur', updateValidation);

    // Add custom validation
    passwordInput.addEventListener('invalid', function(e) {
        e.preventDefault();
        if (!passwordPattern.test(passwordInput.value)) {
            passwordInput.setCustomValidity('Password must be at least 8 characters with uppercase, lowercase, number, and special character.');
        }
    });

    passwordInput.addEventListener('input', function() {
        passwordInput.setCustomValidity('');
    });
}

// Initialize on DOM load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-initialize common password fields
        const passwordFields = document.querySelectorAll('input[type="password"][name="password"], input[type="password"][name="new_password"]');
        passwordFields.forEach(field => {
            if (field.id) {
                initPasswordValidation(field.id);
            }
        });
    });
} else {
    const passwordFields = document.querySelectorAll('input[type="password"][name="password"], input[type="password"][name="new_password"]');
    passwordFields.forEach(field => {
        if (field.id) {
            initPasswordValidation(field.id);
        }
    });
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { validatePassword, getPasswordStrength, initPasswordValidation, passwordPattern };
}

