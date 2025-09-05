# Contact Manager API

POOSD Team 20 - Contact Management API

**Base URL:** `http://165.232.128.10/api`  
**SwaggerHub URL** `https://app.swaggerhub.com/apis/universityofcentralf-9ab/Contact-Manager/1`

## Authentication

### Register User
**POST** `/Register.php`

```json
{
  "user_name": "john_doe",
  "first_name": "John", 
  "last_name": "Doe",
  "email": "john@example.com",
  "password": "mypassword123"
}
```

**Response:**
```json
{
  "id": 1,
  "firstName": "John",
  "lastName": "Doe",
  "error": ""
}
```

### Login
**POST** `/Login.php`

```json
{
  "login": "john_doe",
  "password": "mypassword123"
}
```

**Response:**
```json
{
  "id": 1,
  "firstName": "John",
  "lastName": "Doe", 
  "error": ""
}
```

### Check Authentication
**GET** `/CheckAuth.php`

**Response:**
```json
{
  "authenticated": true,
  "user": {
    "id": 1,
    "firstName": "John",
    "lastName": "Doe",
    "userName": "john_doe",
    "email": "john@example.com"
  }
}
```

### Logout
**DELETE** `/Logout.php`

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

## Contact Management

### Create Contact
**POST** `/CreateContact.php`

```json
{
  "first_name": "Jane",
  "last_name": "Smith", 
  "email": "jane@example.com",
  "phone_number": "+1-555-123-4567"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Contact created successfully",
  "contact_id": 5,
  "error": ""
}
```

### Get All Contacts
**GET** `/GetContacts.php`

**Response:**
```json
{
  "success": true,
  "contacts": [
    {
      "contact_id": 5,
      "first_name": "Jane",
      "last_name": "Smith",
      "email": "jane@example.com", 
      "phone_number": "+1-555-123-4567",
      "record_created": "2024-01-15"
    }
  ],
  "error": ""
}
```

### Search Contacts
**GET** `/SearchContacts.php?search_term=jane`

**Response:**
```json
{
  "success": true,
  "search_term": "jane",
  "results_count": 1,
  "contacts": [
    {
      "contact_id": 5,
      "first_name": "Jane", 
      "last_name": "Smith",
      "email": "jane@example.com",
      "phone_number": "+1-555-123-4567",
      "record_created": "2024-01-15"
    }
  ],
  "error": ""
}
```

### Update Contact
**PUT** `/UpdateContact.php`

```json
{
  "contact_id": 5,
  "first_name": "Jane",
  "last_name": "Doe",
  "email": "jane.doe@example.com",
  "phone_number": "+1-555-987-6543"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Contact updated successfully",
  "error": ""
}
```

### Delete Contact
**DELETE** `/DeleteContact.php?contact_id=5`

**Response:**
```json
{
  "success": true,
  "message": "Contact deleted successfully",
  "error": ""
}
```

## Authentication

## Error Responses

All endpoints return errors in this format:
```json
{
  "success": false,
  "error": "Error description here"
}
```
