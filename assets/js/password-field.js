/**
 * Initializes password visibility toggling, password generation, and strength indication.
 * 
 * @since 0.4.3
 */
function buddycTogglePasswordVisibility() {
  const passwordFields = document.querySelectorAll('.buddyc-password-field');

  if ( ! passwordFields ) {
    return;
  }

  passwordFields.forEach((field) => {
    buddycCreatePasswordFieldWrapper(field);
  });
}

/**
 * Creates a wrapper around the password field and adds toggle, generation, and strength functionality.
 * 
 * @param {HTMLElement} field - The password field to wrap.
 */
function buddycCreatePasswordFieldWrapper(field) {
  const wrapper = document.createElement('div');
  wrapper.className = 'password-field-wrapper';

  field.parentElement.insertBefore(wrapper, field);
  wrapper.appendChild(field);

  buddycAddToggleIcon(wrapper, field);
  buddycAddGeneratePasswordAndStrengthIndicator(wrapper, field);
}

/**
 * Adds a toggle icon to show or hide the password content.
 * 
 * @param {HTMLElement} wrapper - The wrapper around the password field.
 * @param {HTMLElement} field - The password field.
 */
function buddycAddToggleIcon(wrapper, field) {
  const eyeClass = passwordFieldData.eyeClass;
  const eyeSlashClass = passwordFieldData.eyeSlashClass;

  const toggleIcon = document.createElement('i');
  toggleIcon.className = eyeClass + ' toggle-password-visibility';

  wrapper.appendChild(toggleIcon);

  toggleIcon.addEventListener('click', () => {
    if (field.type === 'password') {
      field.type = 'text';
      toggleIcon.className = eyeSlashClass + ' toggle-password-visibility';
    } else {
      field.type = 'password';
      toggleIcon.className = eyeClass + ' toggle-password-visibility';
    }
  });
}

/**
 * Adds a link to generate a password and a strength indicator next to the password field.
 * 
 * @param {HTMLElement} wrapper - The wrapper around the password field.
 * @param {HTMLElement} field - The password field.
 */
function buddycAddGeneratePasswordAndStrengthIndicator(wrapper, field) {
  const generateLink = document.createElement('a');
  generateLink.textContent = 'Generate Password';
  generateLink.href = '#';
  generateLink.className = 'buddyc-generate-password-link';

  const strengthIndicator = document.createElement('span');
  strengthIndicator.className = 'buddyc-password-strength-indicator';

  const container = document.createElement('div');
  container.className = 'password-options-container';
  container.style.display = 'flex';
  container.style.gap = '10px';
  container.style.alignItems = 'center';
  container.appendChild(strengthIndicator);
  container.appendChild(generateLink);

  const messageContainer = document.createElement('div');
  messageContainer.className = 'clipboard-message';
  container.appendChild(messageContainer);

  wrapper.parentElement.insertBefore(container, wrapper.nextSibling);

  buddycUpdateStrengthIndicator(strengthIndicator, field.value);

  generateLink.addEventListener('click', (e) => {
    e.preventDefault();
    const newPassword = buddycGenerateStrongPassword();
    field.value = newPassword;
    buddycCopyPasswordToClipboard(newPassword, messageContainer);
    buddycUpdateStrengthIndicator(strengthIndicator, newPassword);
  });

  field.addEventListener('input', () => {
    buddycUpdateStrengthIndicator(strengthIndicator, field.value);
  });
}

/**
 * Generates a strong password of a given length and ensures it meets strong criteria.
 * 
 * @param {number} length - The length of the generated password.
 * @returns {string} The generated strong password.
 */
function buddycGenerateStrongPassword(length = 12) {
  const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+[]{}|;:,.<>?';
  let password = '';

  do {
    password = '';
    for (let i = 0; i < length; i++) {
      password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
  } while (!buddycEvaluatePasswordStrength(password).class === 'strong');

  return password;
}

/**
 * Copies the provided text to the clipboard and displays a message.
 * 
 * @param {string} text - The text to copy to the clipboard.
 * @param {HTMLElement} messageContainer - The container to display the copy message.
 */
function buddycCopyPasswordToClipboard(text, messageContainer) {
  // Check if Clipboard API is supported
  if (!navigator.clipboard || !navigator.clipboard.writeText) {
    return;
  }

  navigator.clipboard.writeText(text)
    .then(() => {
      messageContainer.textContent = 'Password copied to clipboard!';
      messageContainer.classList.add('buddyc-clipboard-success');
      setTimeout(() => {
        messageContainer.textContent = '';
        messageContainer.classList.remove('buddyc-clipboard-success');
      }, 3000);
    })
    .catch(() => {
      messageContainer.textContent = 'Failed to copy password to clipboard.';
      messageContainer.classList.add('buddyc-clipboard-error');
      setTimeout(() => {
        messageContainer.textContent = '';
        messageContainer.classList.remove('buddyc-clipboard-error');
      }, 3000);
    });
}

/**
 * Updates the password strength indicator based on the provided password.
 * Hides the indicator if the password field is empty.
 * 
 * @param {HTMLElement} indicator - The strength indicator element.
 * @param {string} password - The password to evaluate.
 */
function buddycUpdateStrengthIndicator(indicator, password) {
  if (!password) {
    // Hide the indicator if the password is empty
    indicator.style.display = 'none';
    return;
  }

  // Show the indicator and update its content
  indicator.style.display = 'inline';
  const strength = buddycEvaluatePasswordStrength(password);
  indicator.textContent = strength.text;
  indicator.classList.remove('strength-strong', 'strength-medium', 'strength-weak');
  indicator.classList.add(`strength-${strength.class}`);
}

/**
 * Evaluates the strength of a given password.
 * 
 * @param {string} password - The password to evaluate.
 * @returns {Object} An object containing strength text and class.
 */
function buddycEvaluatePasswordStrength(password) {
  // Adjusted to match PHP criteria: at least 8 characters and include at least one of each type
  const strongPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
  const mediumPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

  if (strongPattern.test(password)) {
    return { text: 'Strong', class: 'strong' };
  } else if (mediumPattern.test(password)) {
    return { text: 'Medium', class: 'medium' };
  } else {
    return { text: 'Weak', class: 'weak' };
  }
}

// Call the function to apply the password toggle, generation, and strength indicator functionality
buddycTogglePasswordVisibility();
