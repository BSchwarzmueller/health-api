# eHealth API

## Installation

### Install dependencies and docker-compose build:
make init 

### Set up the database:  
make deploy-database

### Stop the application:
make down

### Run the application:
make up

### Run test
make unit-tests

### Run PHPStan
make phpstan level=? (level can be 0-10)

## How to

 visit http://localhost:8080/ to access the 'menu'



## API Description

### OpenAPI Documentation
- The API documentation can be found at  /src/Doc/openapi.yml

### Create a new medication
- Endpoint: POST /medications
- Request Body:
```json
{
    "user_id": 1,
    "name": "Medication Name",
    "started_at": "2023-01-01T00:00:00Z",
    "dosage": 10,
    "note": "Optional note",
    "role": "customer"
}
```
- Responses:
    - 201 Created: Medication created successfully.
    - 400 Bad Request: Invalid input.
    - 500 Internal Server Error: Database error.
  
### Delete a medication
- Endpoint: DELETE /medications/{id}
- Parameters: 
  - id (path): The ID of the medication to delete.
- Request Body:
```json
  {
  "id": 1,
  "role": "customer"
  }
  ```
- Responses:
  - 200 OK: Medication deleted successfully.
   - 400 Bad Request: Invalid input or unauthorized access.
    - 500 Internal Server Error: Database error.
    
### Update a medication
- Endpoint: PUT /medications/{id}
- Parameters:
    - id (path): The ID of the medication to update.
- Request Body:
```json
    {
    "user_id": 1,
    "name": "Updated Medication Name",
    "started_at": "2023-01-01T00:00:00Z",
    "dosage": 20,
    "note": "Updated note",
    "role": "customer"
    }
```
- Responses:
    - 200 OK: Medication updated successfully.
    - 400 Bad Request: Invalid input or unauthorized access.
    - 500 Internal Server Error: Database error.
    
### Get medications for a user
- Endpoint: GET /medications/{user_id}
- Parameters:
  - user_id (path): The ID of the user to get medications for.
  - Request Body:
```json
    {
    "user_id": 1,
    "role": "pharmacist"
    }
```
- Responses:
  - 200 OK: Medications retrieved successfully.
  - 400 Bad Request: Invalid input.
  - 500 Internal Server Error: Database error.



# TODO:

- Add authentication and authorization.
- Add image upload for medications.
- Add openapi documentation via SwaggerUI
- Add pagination and filter to reduce data size
- Add more unit tests.
- Add version control to the API


# Recommendations

- Use symfony framework for better structure and security.
