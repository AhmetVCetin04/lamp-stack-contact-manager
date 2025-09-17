// Contact management functions with proper HTTP methods

// Get all contacts for the authenticated user
async function getContacts(userId = null) {
  let url = window.apiEndpoint('GetContacts.php');
  if (userId) {
    url += '?user_id=' + userId;
  }

  return fetch(url, {
    method: 'GET',
    credentials: 'include'
  })
    .then(response => response.json());
}

// Create a new contact
async function createContact(contactData) {
  return fetch(window.apiEndpoint('CreateContact.php'), {
    method: 'POST',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(contactData)
  })
    .then(response => response.json());
}

// Update an existing contact
async function updateContact(contactId, contactData, userId = null) {
  const updateData = {
    contact_id: contactId,
    ...contactData
  };

  if (userId) {
    updateData.user_id = userId;
  }

  return fetch(window.apiEndpoint('UpdateContact.php'), {
    method: 'PUT',
    credentials: 'include',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(updateData)
  })
    .then(response => response.json());
}

// Delete a contact
async function deleteContact(contactId, userId = null) {
  let url = window.apiEndpoint('DeleteContact.php') + '?contact_id=' + contactId;
  if (userId) {
    url += '&user_id=' + userId;
  }

  return fetch(url, {
    method: 'DELETE',
    credentials: 'include'
  })
    .then(response => response.json());
}

// Search contacts
async function searchContacts(searchTerm, userId = null) {
  let url = window.apiEndpoint('SearchContacts.php') + '?search_term=' + encodeURIComponent(searchTerm);
  if (userId) {
    url += '&user_id=' + userId;
  }

  return fetch(url, {
    method: 'GET',
    credentials: 'include'
  })
    .then(response => response.json());
}

// Example usage functions for UI integration

// Load and display contacts
async function loadContacts() {
  const table = document.getElementById('contact-table');
  table.classList.add('no-data'); // Hide header

  const tableBody = document.querySelector('#contact-table tbody');
  tableBody.innerHTML = `
    <tr id="loading-row">
      <td colspan="5">
        <div class="spinner"></div>
      </td>
    </tr>
  `;

  getContacts()
    .then(data => {
      if (data.success) {
        displayContacts(data.contacts);
      } else {
        showError(data.error || 'Failed to load contacts');
      }
    })
    .catch(error => {
      console.error('Error loading contacts:', error);
      showError('Failed to load contacts');
    });
}

// Handle contact form submission
function handleContactForm(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const contactData = {
    first_name: formData.get('first_name'),
    last_name: formData.get('last_name'),
    email: formData.get('email'),
    phone_number: formData.get('phone_number')
  };

  const contactId = formData.get('contact_id');

  if (contactId) {
    // Update existing contact
    updateContact(contactId, contactData)
      .then(data => {
        if (data.success) {
          showSuccess('Contact updated successfully');
          loadContacts();
          event.target.reset();
        } else {
          showError(data.error || 'Failed to update contact');
        }
      })
      .catch(error => {
        console.error('Error updating contact:', error);
        showError('Failed to update contact');
      });
  } else {
    // Create new contact
    createContact(contactData)
      .then(data => {
        if (data.success) {
          showSuccess('Contact created successfully');
          loadContacts();
          event.target.reset();
        } else {
          showError(data.error || 'Failed to create contact');
        }
      })
      .catch(error => {
        console.error('Error creating contact:', error);
        showError('Failed to create contact');
      });
  }
}

// Handle contact deletion
function handleDeleteContact(contactId, contactName) {
  if (confirm(`Are you sure you want to delete ${contactName}?`)) {
    deleteContact(contactId)
      .then(data => {
        if (data.success) {
          showSuccess('Contact deleted successfully');
          loadContacts();
        } else {
          showError(data.error || 'Failed to delete contact');
        }
      })
      .catch(error => {
        console.error('Error deleting contact:', error);
        showError('Failed to delete contact');
      });
  }
}

// Handle search
function handleSearch(event) {
  const searchTerm = event.target.value.trim();

  if (searchTerm.length === 0) {
    loadContacts();
    return;
  }

  if (searchTerm.length >= 2) {
    searchContacts(searchTerm)
      .then(data => {
        if (data.success) {
          displayContacts(data.contacts);
          showSearchInfo(data.results_count, data.search_term);
        } else {
          showError(data.error || 'Search failed');
        }
      })
      .catch(error => {
        console.error('Error searching contacts:', error);
        showError('Search failed');
      });
  }
}

// Utility functions for UI (implement based on your HTML structure)
function displayContacts(contacts) {
  const table = document.getElementById('contact-table');
  const tableBody = document.querySelector('#contact-table tbody');
  tableBody.innerHTML = ''; // Clear existing rows

  if (contacts.length === 0) {
    table.classList.add('no-data'); // Hide header
    const noContactsRow = document.createElement('tr');
    const noContactsCell = document.createElement('td');
    noContactsCell.colSpan = 5;
    noContactsCell.textContent = 'No contacts found.';
    noContactsCell.style.textAlign = 'center';
    noContactsRow.appendChild(noContactsCell);
    tableBody.appendChild(noContactsRow);
    return;
  }

  table.classList.remove('no-data'); // Show header

  contacts.forEach(contact => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${contact.first_name}</td>
      <td>${contact.last_name}</td>
      <td>${contact.email}</td>
      <td>${contact.phone_number}</td>
      <td>
        <button class="edit-btn" onclick='openEditContactModal(${JSON.stringify(contact)})'>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V12h2.293l6.5-6.5-.207-.207z"/>
          </svg>
        </button>
        <button class="delete-btn" onclick='handleDeleteContact(${contact.contact_id}, "${contact.first_name} ${contact.last_name}")'>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
            <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
          </svg>
        </button>
      </td>
    `;
    tableBody.appendChild(row);
  });
}

function showError(message) {
  console.error('Error:', message);
  const table = document.getElementById('contact-table');
  table.classList.add('no-data'); // Hide header

  const tableBody = document.querySelector('#contact-table tbody');
  tableBody.innerHTML = `
    <tr>
      <td colspan="5" style="text-align: center; color: red;">${message}</td>
    </tr>
  `;
}

function showSuccess(message) {
  console.log('Success:', message);
  // Implement success display
}

function showSearchInfo(count, term) {
  console.log(`Found ${count} results for "${term}"`);
  // Implement search info display
}

// Make functions available globally
window.getContacts = getContacts;
window.createContact = createContact;
window.updateContact = updateContact;
window.deleteContact = deleteContact;
window.searchContacts = searchContacts;
window.loadContacts = loadContacts;
window.handleContactForm = handleContactForm;
window.handleDeleteContact = handleDeleteContact;
window.handleSearch = handleSearch;

function openAddContactModal() {
  const modal = document.getElementById('add-contact-modal');
  modal.style.display = 'flex';
}

function closeAddContactModal() {
  const modal = document.getElementById('add-contact-modal');
  modal.style.display = 'none';

  // Reset modal to "Add Contact" mode
  document.querySelector('#add-contact-modal h2').textContent = 'Add New Contact';
  document.querySelector('#add-contact-form button').textContent = 'Add Contact';
  document.getElementById('add-contact-form').reset();
  const contactIdInput = document.getElementById('contact_id');
  if (contactIdInput) {
    contactIdInput.remove();
  }
}

function openEditContactModal(contact) {
  // Change modal title
  const modalTitle = document.querySelector('#add-contact-modal h2');
  modalTitle.textContent = 'Edit Contact';

  // Change form button text
  const formButton = document.querySelector('#add-contact-form button');
  formButton.textContent = 'Update Contact';

  // Populate the form
  document.getElementById('first_name').value = contact.first_name;
  document.getElementById('last_name').value = contact.last_name;
  document.getElementById('email').value = contact.email;
  document.getElementById('phone_number').value = contact.phone_number;

  // Add contact_id to a hidden input field in the form
  let contactIdInput = document.getElementById('contact_id');
  if (!contactIdInput) {
    contactIdInput = document.createElement('input');
    contactIdInput.type = 'hidden';
    contactIdInput.id = 'contact_id';
    contactIdInput.name = 'contact_id';
    document.getElementById('add-contact-form').appendChild(contactIdInput);
  }
  contactIdInput.value = contact.contact_id;

  // Open the modal
  openAddContactModal();
}

document.addEventListener('DOMContentLoaded', () => {
  loadContacts(); // Load contacts when the page is ready

  const addContactForm = document.getElementById('add-contact-form');
  if (addContactForm) {
    addContactForm.addEventListener('submit', (event) => {
      handleContactForm(event);
      closeAddContactModal();
    });
  }
});
