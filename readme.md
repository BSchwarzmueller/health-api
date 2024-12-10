# eHealth API

## Installation

### Install dependencies:  

composer install

### Set up the database:  
make build-database

### Run the application:
make up

Make Commands
make db-setup: Sets up the database schema and seeds initial data.
make run: Starts the application server.
make test: Runs the unit tests.

## API Description
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
    200 OK: Medications retrieved successfully.
    400 Bad Request: Invalid input.
    500 Internal Server Error: Database error.