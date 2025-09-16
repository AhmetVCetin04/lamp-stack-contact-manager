function apiEndpoint(endpoint) {
  return window.config.BASE_URL + endpoint
}

// Make available
window.apiEndpoint = apiEndpoint
