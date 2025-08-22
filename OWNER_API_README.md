# Owner Login API Documentation

## Overview
This API provides authentication endpoints specifically for users with the "owner" role. The API uses JWT tokens for authentication and includes role-based access control.

## Base URL
```
/api/owner
```

## Endpoints

### 1. Owner Login
**POST** `/api/owner/login`

Authenticates an owner user and returns a JWT token.

#### Request Body
```json
{
    "username": "owner_username",
    "password": "owner_password"
}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "user": {
            "id": 1,
            "user_id": "HRUUS-27012025-0001",
            "username": "owner_username",
            "role": "owner",
            "change_password_at": "2025-01-27"
        },
        "expires_at": "2025-02-03T00:00:00.000000Z"
    }
}
```

#### Response (Error - 401)
```json
{
    "success": false,
    "message": "Akses ditolak. Role tidak sesuai"
}
```

#### Validation Errors (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "data": [
        "Username harus diisi",
        "Password harus diisi"
    ]
}
```

### 2. Owner Logout
**POST** `/api/owner/logout`

Logs out an owner user by invalidating their JWT token.

#### Headers
```
Authorization: Bearer {jwt_token}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "message": "Logout berhasil",
    "data": null
}
```

### 3. Owner Profile
**GET** `/api/owner/profile`

Retrieves the profile information of the authenticated owner.

#### Headers
```
Authorization: Bearer {jwt_token}
```

#### Response (Success - 200)
```json
{
    "success": true,
    "data": {
        "id": 1,
        "user_id": "HRUUS-27012025-0001",
        "username": "owner_username",
        "role": "owner",
        "change_password_at": "2025-01-27"
    }
}
```

## Authentication

### JWT Token
- **Algorithm**: HS256
- **Expiration**: 7 days
- **Payload includes**: user ID, username, role, expiration time, and type identifier

### Token Usage
Include the JWT token in the Authorization header for protected endpoints:
```
Authorization: Bearer {jwt_token}
```

## Error Handling

### Common Error Codes
- **401**: Unauthorized (invalid token, wrong role, or expired token)
- **422**: Validation errors
- **400**: Bad request

### Error Response Format
```json
{
    "success": false,
    "message": "Error description",
    "data": null
}
```

## Security Features

1. **Role Validation**: Only users with "owner" role can access these endpoints
2. **Token Expiration**: JWT tokens expire after 7 days
3. **Logout Functionality**: Tokens can be invalidated on logout
4. **Password Hashing**: Passwords are securely hashed using Laravel's Hash facade
5. **Input Validation**: All inputs are validated and sanitized

## Testing

### Run Tests
```bash
php artisan test --filter=OwnerAuthTest
```

### Test Data
The seeder creates test owner accounts:
- Username: `owner`, Password: `owner123`
- Username: `admin_owner`, Password: `admin123`

### Seed Test Data
```bash
php artisan db:seed --class=OwnerSeeder
```

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    change_password_at DATE NULL,
    delete_at DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## Example Usage

### cURL Examples

#### Login
```bash
curl -X POST http://localhost:8000/api/owner/login \
  -H "Content-Type: application/json" \
  -d '{"username": "owner", "password": "owner123"}'
```

#### Get Profile (with token)
```bash
curl -X GET http://localhost:8000/api/owner/profile \
  -H "Authorization: Bearer {your_jwt_token}"
```

#### Logout
```bash
curl -X POST http://localhost:8000/api/owner/logout \
  -H "Authorization: Bearer {your_jwt_token}"
```

## Notes

- The API automatically generates unique user IDs with the format: `HRUUS-{DDMMYYYY}-{SEQUENCE}`
- Passwords must be at least 6 characters long
- Usernames must be unique across the system
- The system checks for deleted accounts and prevents login for deleted users
- All timestamps are in Asia/Jakarta timezone