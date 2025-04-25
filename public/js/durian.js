function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function showDurianDetails(name, total) {
    document.getElementById('viewDurianName').textContent = name;
    document.getElementById('viewDurianTotal').textContent = total;
    openModal('viewDurianModal');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function openEditModal(id, name, total, orchard_id) {
    document.getElementById('editDurianId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editTotal').value = total;

    // Select the correct orchard based on orchard_id
    document.getElementById('editOrchard').value = orchard_id;

    // Update the form action URL to include the Durian ID for the update
    document.getElementById('editDurianForm').action = `/durians/${id}`;

    openModal('editDurianModal');
}

// Custom Response Modal System
// ---------------------------

/**
 * Show a Bootstrap modal with a custom message
 * @param {string} message - The message to display in the modal
 * @param {string} type - The type of message (success, error, warning, info)
 * @param {string} title - Optional title for the modal
 * @param {function} callback - Optional callback function to execute when modal is closed
 */
function showResponseModal(message, type = 'info', title = null, callback = null) {
    // Create modal if it doesn't exist
    if (!document.getElementById('responseModal')) {
        createResponseModal();
    }
    
    // Set default title based on type if not provided
    if (!title) {
        switch (type) {
            case 'success':
                title = 'Success!';
                break;
            case 'error':
                title = 'Error!';
                break;
            case 'warning':
                title = 'Warning!';
                break;
            case 'info':
            default:
                title = 'Information';
                break;
        }
    }
    
    // Get modal elements
    const modal = document.getElementById('responseModal');
    const modalTitle = document.getElementById('responseModalTitle');
    const modalBody = document.getElementById('responseModalBody');
    const modalHeader = document.querySelector('#responseModal .modal-header');
    const modalIcon = document.getElementById('responseModalIcon');
    
    // Set content
    modalTitle.textContent = title;
    modalBody.textContent = message;
    
    // Remove previous color classes
    modalHeader.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
    modalIcon.classList.remove('fa-check-circle', 'fa-times-circle', 'fa-exclamation-triangle', 'fa-info-circle');
    
    // Set color and icon based on type
    switch (type) {
        case 'success':
            modalHeader.classList.add('bg-success');
            modalIcon.classList.add('fa-check-circle');
            break;
        case 'error':
            modalHeader.classList.add('bg-danger');
            modalIcon.classList.add('fa-times-circle');
            break;
        case 'warning':
            modalHeader.classList.add('bg-warning');
            modalIcon.classList.add('fa-exclamation-triangle');
            break;
        case 'info':
        default:
            modalHeader.classList.add('bg-info');
            modalIcon.classList.add('fa-info-circle');
            break;
    }
    
    // Store callback if provided
    if (callback) {
        modal.dataset.callback = callback.toString();
    } else {
        delete modal.dataset.callback;
    }
    
    // Show the modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

/**
 * Create the response modal HTML structure
 */
function createResponseModal() {
    const modalHTML = `
    <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalTitle"></h5>
                    <i class="fas fa-2x me-2 text-white" id="responseModalIcon"></i>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="responseModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>`;
    
    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add event listener for modal hidden event
    const modal = document.getElementById('responseModal');
    modal.addEventListener('hidden.bs.modal', function() {
        if (this.dataset.callback) {
            // Execute the callback function
            const callback = new Function('return ' + this.dataset.callback)();
            callback();
        }
    });
}

/**
 * Show a success message modal
 * @param {string} message - The success message
 * @param {string} title - Optional custom title
 * @param {function} callback - Optional callback function
 */
function showSuccessModal(message, title = 'Success!', callback = null) {
    showResponseModal(message, 'success', title, callback);
}

/**
 * Show an error message modal
 * @param {string} message - The error message
 * @param {string} title - Optional custom title
 * @param {function} callback - Optional callback function
 */
function showErrorModal(message, title = 'Error!', callback = null) {
    showResponseModal(message, 'error', title, callback);
}

/**
 * Show a warning message modal
 * @param {string} message - The warning message
 * @param {string} title - Optional custom title
 * @param {function} callback - Optional callback function
 */
function showWarningModal(message, title = 'Warning!', callback = null) {
    showResponseModal(message, 'warning', title, callback);
}

/**
 * Show an info message modal
 * @param {string} message - The info message
 * @param {string} title - Optional custom title
 * @param {function} callback - Optional callback function
 */
function showInfoModal(message, title = 'Information', callback = null) {
    showResponseModal(message, 'info', title, callback);
}

/**
 * Show a confirmation modal with Yes/No options
 * @param {string} message - The confirmation message
 * @param {function} confirmCallback - Function to call when user confirms
 * @param {function} cancelCallback - Optional function to call when user cancels
 * @param {string} title - Optional custom title
 */
function showConfirmModal(message, confirmCallback, cancelCallback = null, title = 'Confirmation') {
    // Create modal if it doesn't exist
    if (!document.getElementById('confirmModal')) {
        createConfirmModal();
    }
    
    // Get modal elements
    const modal = document.getElementById('confirmModal');
    const modalTitle = document.getElementById('confirmModalTitle');
    const modalBody = document.getElementById('confirmModalBody');
    const confirmBtn = document.getElementById('confirmModalYesBtn');
    
    // Set content
    modalTitle.textContent = title;
    modalBody.textContent = message;
    
    // Set up confirm button
    confirmBtn.onclick = function() {
        const bootstrapModal = bootstrap.Modal.getInstance(modal);
        bootstrapModal.hide();
        if (confirmCallback) confirmCallback();
    };
    
    // Store cancel callback if provided
    if (cancelCallback) {
        modal.dataset.cancelCallback = cancelCallback.toString();
    } else {
        delete modal.dataset.cancelCallback;
    }
    
    // Show the modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

/**
 * Create the confirmation modal HTML structure
 */
function createConfirmModal() {
    const modalHTML = `
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="confirmModalTitle">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="confirmModalYesBtn">Yes</button>
                </div>
            </div>
        </div>
    </div>`;
    
    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Add event listener for modal hidden event
    const modal = document.getElementById('confirmModal');
    modal.addEventListener('hidden.bs.modal', function() {
        if (this.dataset.cancelCallback) {
            // Execute the cancel callback function
            const cancelCallback = new Function('return ' + this.dataset.cancelCallback)();
            cancelCallback();
        }
    });
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check for flash messages from server-side redirects
    if (typeof flashMessages !== 'undefined') {
        if (flashMessages.success) {
            showSuccessModal(flashMessages.success);
        } else if (flashMessages.error) {
            showErrorModal(flashMessages.error);
        } else if (flashMessages.warning) {
            showWarningModal(flashMessages.warning);
        } else if (flashMessages.info) {
            showInfoModal(flashMessages.info);
        }
    }
});
