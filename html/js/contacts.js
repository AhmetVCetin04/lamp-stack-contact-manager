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
function handleDeleteContact(contactId) {
  if (confirm('Are you sure you want to delete this contact?')) {
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
  console.log('Displaying contacts:', contacts);
  // Implement based on your UI needs
}

function showError(message) {
  console.error('Error:', message);
  // Implement error display
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
