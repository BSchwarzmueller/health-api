# eHealth API

## Installation

### Install dependencies and build docker image:
make init

### Set up the database:  
make build-database

### Run the application:
make up

### Open form to create a new medication:

open: http://localhost:8080/ (unfortunately the router is not working...)

### Run test
make unit-tests

### Stop the application:
make down

### Run PHPStan
make phpstan level=? (level can be 0-10)

## API Description

### OpenAPI Documentation
- The API documentation can be found at  backend/doc/openapi.yml

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
    "role": "pharmacist"
    }
```
- Responses:
  - 200 OK: Medications retrieved successfully.
  - 400 Bad Request: Invalid input.
  - 500 Internal Server Error: Database error.



# TODO:

- FIX THE ROUTER!!!!!
- ADD COMMENTS TO THE CODE
- Add authentication and authorization.
- Add image upload for medications.
- Add more forms for different api actions
- Add openapi documentation via SwaggerUI
- Add pagination and filter to reduce data size
- Add more unit tests.
- Add version control to the API.
- run phpstan code analysis


# Recommendations

- Use symfony or laravel for a more robust and scalable application.
- Use symfony for simple and working routing