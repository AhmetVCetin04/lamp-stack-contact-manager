// Authentication management for Contact Manager

// Check authentication status on page load
window.addEventListener("DOMContentLoaded", function() {
  checkAuthStatus();
});

function checkAuthStatus() {
  fetch(window.apiEndpoint("CheckAuth.php"), {
    method: "GET",
    credentials: "include"
  })
    .then(response => response.json())
    .then(data => {
      if (data.authenticated) {
        handleAuthenticatedUser(data.user);
      } else {
        handleUnauthenticatedUser();
      }
    })
    .catch(error => {
      console.error("Auth check failed:", error);
      handleUnauthenticatedUser();
    });
}

function handleAuthenticatedUser(user) {
  // Store user info globally
  window.currentUser = user;

  // If on login page, redirect to dashboard
  if (
    window.location.pathname.includes("index.html") ||
    window.location.pathname === "/"
  ) {
    window.location.href = "dashboard.html";
    return;
  }

  // Show user info in UI if elements exist
  const userNameElement = document.getElementById("user-name");
  if (userNameElement) {
    userNameElement.textContent = user.firstName + " " + user.lastName;
  }

  const userEmailElement = document.getElementById("user-email");
  if (userEmailElement) {
    userEmailElement.textContent = user.email;
  }
}

function handleUnauthenticatedUser() {
  // Clear any stored user info
  window.currentUser = null;

  // If on protected pages, redirect to login
  const protectedPages = ["dashboard.html", "contacts.html"];
  const currentPage = window.location.pathname.split("/").pop();

  if (protectedPages.includes(currentPage)) {
    window.location.href = "index.html";
    return;
  }
}

// Login form handler
function handleLogin(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const loginData = {
    login: formData.get("username"),
    password: formData.get("password")
  };

  // Show loading state
  const submitButton = event.target.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  submitButton.textContent = "Signing in...";
  submitButton.disabled = true;

  fetch(window.apiEndpoint("Login.php"), {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(loginData)
  })
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        showError(data.error);
        submitButton.textContent = originalText;
        submitButton.disabled = false;
      } else {
        // Success - redirect to dashboard
        window.location.href = "dashboard.html";
      }
    })
    .catch(error => {
      console.error("Login failed:", error);
      showError("Login failed. Please try again.");
      submitButton.textContent = originalText;
      submitButton.disabled = false;
    });
}

// Logout function
function logout() {
  fetch(window.apiEndpoint("Logout.php"), {
    method: "DELETE",
    credentials: "include"
  })
    .then(response => response.json())
    .then(() => {
      // Redirect to login page regardless of response
      window.location.href = "index.html";
    })
    .catch(error => {
      console.error("Logout error:", error);
      // Still redirect to login page
      window.location.href = "index.html";
    });
}

// Utility function to show errors
function showError(message) {
  // Remove any existing error messages
  const existingError = document.getElementById("error-message");
  if (existingError) {
    existingError.remove();
  }

  // Create error element
  const errorDiv = document.createElement("div");
  errorDiv.id = "error-message";
  errorDiv.style.cssText =
    "color: red; background: #ffebee; padding: 10px; margin: 10px 0; border: 1px solid #f44336; border-radius: 4px;";
  errorDiv.textContent = message;

  // Insert error message near the form
  const form = document.getElementById("loginform");
  if (form) {
    form.insertBefore(errorDiv, form.firstChild);
  } else {
    document.body.insertBefore(errorDiv, document.body.firstChild);
  }

  // Auto-remove after 5 seconds
  setTimeout(() => {
    if (errorDiv.parentNode) {
      errorDiv.parentNode.removeChild(errorDiv);
    }
  }, 5000);
}

// Registration form handler
function handleRegister(event) {
  event.preventDefault();

  const formData = new FormData(event.target);
  const password = formData.get("password");
  const confirmPassword = formData.get("confirmPassword");

  // Validate passwords match
  if (password !== confirmPassword) {
    showError("Passwords do not match");
    return;
  }

  const registerData = {
    user_name: formData.get("username"),
    first_name: formData.get("firstName"),
    last_name: formData.get("lastName"),
    email: formData.get("email"),
    password: password
  };

  // Show loading state
  const submitButton = event.target.querySelector('button[type="submit"]');
  const originalText = submitButton.textContent;
  submitButton.textContent = "Creating Account...";
  submitButton.disabled = true;

  fetch(window.apiEndpoint("Register.php"), {
    method: "POST",
    credentials: "include",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(registerData)
  })
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        showError(data.error);
        submitButton.textContent = originalText;
        submitButton.disabled = false;
      } else {
        // Registration successful - redirect to login
        window.location.href = "index.html";
      }
    })
    .catch(error => {
      console.error("Registration failed:", error);
      showError("Registration failed. Please try again.");
      submitButton.textContent = originalText;
      submitButton.disabled = false;
    });
}

// Make functions available globally
window.handleLogin = handleLogin;
window.handleRegister = handleRegister;
window.logout = logout;
