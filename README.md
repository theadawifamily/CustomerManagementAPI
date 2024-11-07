
This is a Laravel-based RESTful API for managing customer data. The application allows creating, reading, updating, and deleting (CRUD) customer records, along with filtering options by name and email. It also contains logic that calculates customer tier based on annualSpend and lastPurchaseDate
The current version of this API is v1

## Table of Contents
- [Building and Running the Application](#building-and-running-the-application)
- [Accessing the SQLite Database Console](#accessing-the-sqlite-database-console)
- [Accessing app container](#accessing-app-container)
- [API Endpoints and Sample Requests](#api-endpoints-and-sample-requests)
- [Validation Rules](#validation-rules)
- [Business Logic Requirements](#business-logic-requirements)
- [Assumptions Made](#assumptions-made)
- [Unit testing](#unit-testing)
---

## Building and Running the Application

1. **Install Docker Desktop and Docker Compose**:
   - **Docker Desktop**: Download and install Docker Desktop based on your operating system:
     - [Docker Desktop for Windows](https://docs.docker.com/desktop/install/windows-install/)
     - [Docker Desktop for Mac](https://docs.docker.com/desktop/install/mac-install/)
     - [Docker Desktop for Linux](https://docs.docker.com/desktop/install/linux-install/)
   - **Docker Compose**: Install Docker Compose separately, as it is required for running the application scripts:
     - [Docker Compose Installation Guide](https://docs.docker.com/compose/install/)

2. **Clone the Repository**:
   ```bash
   git clone https://github.com/theadawifamily/CustomerManagementAPI.git

3. **Build and Start the Containers**:
   Run the following command to build and start the application with Docker Compose:
   ```bash
   docker-compose up -d --build
   ```
   Migration will automatically run via the docker-compose.yml file.
   You can aslo run migrations manually by executing the following script from the root directory:
   ```bash
   bin/run_migrations
   ```

## Accessing the SQLite Database Console

1. **Inside the root directory, run**:
   ```bash
   bin/access_sqlite

2. **Once inside SQLite, you can view schema**:
   ```bash
   .schema table_name

2. **Once inside SQLite, you can select a table**:
   ```bash
   select * from table_name

## Accessing app container
  - To ssh into the app container, run the following from the root directory:
    ```bash
    bin/access_app

## API Endpoints and Sample Requests

#### 1. Create a Customer
- **Endpoint**: `POST /api/v1/customers`
- **Description**: Creates a new customer with the specified attributes.
- **Request Body**:
   ```json
   {
     "name": "John Doe",
     "email": "johndoe@example.com",
     "annualSpend": 1000.50,
     "lastPurchaseDate": "2024-01-01T00:00:00Z"
   }
- **Sample Request**:
  ```bash
  curl -X POST http://localhost:8000/api/v1/customers -H "Content-Type: application/json" -d '{"name": "John Doe", "email": "johndoe@example.com", "annualSpend": 1000.50, "lastPurchaseDate": "2024-01-01T00:00:00Z"}'
- **Responses**:
  - 201: Customer created successfully.
    ```json
    {
      "id": "uuid",
      "name": "John Doe",
      "email": "johndoe@example.com",
      "annualSpend": 1000.50,
      "lastPurchaseDate": "2024-01-01T00:00:00Z",
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  - 400: Invalid request (e.g., email not provided or formatted incorrectly).
    ```json
    {
      "error": "Invalid request",
      "message": "The email field is required."
    }
  - 500: Internal server error due to a database  error.
    ```json
    {
      "error": "Failed to create customer due to a database error."
    }
  - 500: Internal server error due to a  general error.
    ```json
    {
      "error": "An unexpected error occurred while creating the customer."
    }
#### 2. Retrieve a Customer by ID
- **Endpoint**: `GET /api/v1/customers/{id}`
- **Description**: Fetches a customer’s details by their unique ID.
- **Sample Request**:
  ```bash
  curl -X GET http://localhost:8000/api/v1/customers/{id}
- **Responses**:
  - 200: Customer details retrieved successfully.
    ```json
    {
      "id": "uuid",
      "name": "John Doe",
      "email": "johndoe@example.com",
      "annualSpend": 1000.50,
      "lastPurchaseDate": "2024-01-01T00:00:00Z",
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  - 404: Customer not found
    ```json
    {
      "error": "Customer not found"
    }
  - 500: Internal server error due to a database error.
    ```json
    {
      "error": "Failed to retrieve customer due to a database error."
    }
  - 500: Internal server error due to a  general error.
    ```json
    {
      "error": "An unexpected error occurred while retrieving the customer.."
    }

#### 3. Retrieve Customers by Name or Email
- **Endpoint**: `GET /api/v1/customers`
- **Description**: Fetches customers filtered by name, email, or both. If both are provided, it uses OR logic to match either.
- **Sample Request**:
  - **Filter by Name**:
    ```bash
    curl -X GET "http://localhost:8000/api/v1/customers?name=Jane Doe"
    ```
  - **Filter by Email**:
    ```bash
    curl -X GET "http://localhost:8000/api/v1/customers?email=test_customer@yahoo.com"
    ```
  - **Filter by Both Name and Email (OR logic)**:
    ```bash
    curl -X GET "http://localhost:8000/api/v1/customers?name=Alice Smith&email=nonexistent@example.com"
    ```

- **Responses**:
  - **200**: Customers retrieved successfully. Returns an array of customer objects that match the filter criteria.
    ```json
    [
      {
        "id": "uuid",
        "name": "Jane Doe",
        "email": "janedoe@example.com",
        "annualSpend": 1500.00,
        "lastPurchaseDate": "2024-02-01T00:00:00Z",
        "created_at": "2023-05-01T00:00:00Z",
        "updated_at": "2024-02-01T00:00:00Z"
      },
      {
        "id": "uuid",
        "name": "John Smith",
        "email": "johnsmith@example.com",
        "annualSpend": 2000.75,
        "lastPurchaseDate": "2024-06-01T00:00:00Z",
        "created_at": "2024-01-01T00:00:00Z",
        "updated_at": "2024-06-01T00:00:00Z"
      }
    ]
    ```
  - **400**: Invalid request (e.g., if parameters are not provided in the expected format).
    ```json
    {
      "error": "Invalid request",
      "message": "The name or email parameter is required."
    }
    ```
  - **500**: Internal server error due to a database  error.
    ```json
    {
      "error": "Failed to retrieve customers due to a database error."
    }
    ```
  - **500**: Internal server error due to a  general error.
    ```json
    {
      "error": "Failed to retrieve customers due to a database error."
    }
    ```
#### 4. Upate a Customer
- **Endpoint**: `PUT /api/v1/customers/{id}`
- **Description**: Updates an existing customer’s information. Email uniqueness is validated, but the customer’s current email is excluded from this check.
- **Request Body**:
   ```json
   {
     "name": "John Doe",
     "email": "johndoe@example.com",
     "annualSpend": 1000.50,
     "lastPurchaseDate": "2024-01-01T00:00:00Z"
   }
- **Sample Request**:
  ```bash
  curl -X PUT http://localhost:8000/api/v1/customers/{id} -H "Content-Type: application/json" -d '{"name": "John Smith", "email": "johnsmith@example.com", "annualSpend": 2000.75, "lastPurchaseDate": "2024-06-01T00:00:00Z"}'
- **Responses**:
  - 200: Customer updated successfully.
    ```json
     {
      "id": "uuid",
      "name": "Jane Doe",
      "email": "janedoe@example.com",
      "annualSpend": 2000.75,
      "lastPurchaseDate": "2024-06-01T00:00:00Z",
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-06-01T00:00:00Z"
     }
  - 404: Customer not found
    ```json
    {
      "error": "Customer not found"
    }
  - 500: Internal server error due to a database  error
    ```json
    {
      "error": "Failed to update customer due to a database error."
    }
  - 500: Internal server error due to a  general error
    ```json
    {
      "error": "An unexpected error occurred while updating the customer."
    }
#### 5. Delete a Customer
- **Endpoint**: `DELETE /api/v1/customers/{id}`
- **Description**: Deletes a customer by their unique ID.
- **Sample Request**:
  ```bash
  curl -X DELETE http://localhost:8000/api/v1/customers/{id}
- **Responses**:
  - 200: Customer deleted successfully.
    ```json
    {
      "message": "Customer deleted successfully"
    }
  - 404: Customer not found
    ```json
    {
      "message": "Customer not found"
    }
  - 500: Internal server error due to a database error
    ```json
    {
      "message": "Failed to delete customer due to a database error."
    }
  - 500: Internal server error due to a  general error
    ```json
    {
      "message": "An unexpected error occurred while deleting the customer."
    }
## Validation Rules

#### Create Customer Validation
When creating a customer, the following validation rules are applied:

- **Name**:
  - Required, must be a string, and a maximum of 255 characters.

- **Email**:
  - Required, must be a string.
  - Must be in a valid email format.
  - Must be unique and a maximum of 255 characters.

- **Annual Spend**:
  - Optional, must be numeric if provided.

- **Last Purchase Date**:
  - Optional, must be a valid date if provided.

#### Update Customer Validation
When updating a customer, the validation rules are similar to creating, with the exception that the current email can remain the same (checked with `unique:customers,email,{$id}`):

- **Name**:
  - Optional, must be a string, and a maximum of 255 characters.

- **Email**:
  - Optional, must be a string.
  - Must be a valid email format.
  - Must be unique if changed, with a maximum of 255 characters.

- **Annual Spend**:
  - Optional, must be numeric if provided.

- **Last Purchase Date**:
  - Optional, must be a valid date if provided.

## Business Logic Requirements

The API implements an on-the-fly **tier calculation** based on the `annualSpend` and `lastPurchaseDate` when retrieving a customer. The response will include a calculated `tier` value based on the following rules:

- **Silver**: 
  - `annualSpend` < $1000

- **Gold**: 
  - `annualSpend` >= $1000 and < $10000
  - `lastPurchaseDate` within the last 12 months

- **Platinum**: 
  - `annualSpend` >= $10000
  - `lastPurchaseDate` within the last 6 months

The `tier` value is automatically calculated and included in the customer response when retrieving customer information.

## Example Response with Tier Calculation
- **A sample response might look like this**:

  ```json
  {
    "id": "12345",
    "name": "John Doe",
    "email": "johndoe@example.com",
    "annualSpend": 12000,
    "lastPurchaseDate": "2024-06-01T00:00:00Z",
    "created_at": "2023-06-01T00:00:00Z",
    "updated_at": "2024-01-01T00:00:00Z",
    "tier": "Platinum"
  }

## Assumptions Made
- **.env file**:
  - Since this code is for demo purposes and not meant to be deployed to production, I included the `.env` file with the repo.
  - In case of a production deployment, the .env file will be added to `.gitignore` file. 
  - In a production deployment, sensitive data will be added to AWS secret manager and the `.env` will be built during deployment by retrieving data stored in AWS secret manager.

- **customers table**:
  - I did not provide any optimizations to the table as I was more focused on delivering the requirements. In a real life scenario, indexing can be applied to the name and email columns.
- **Payload**:
  - For simplicity, I am not encoding data in query parameters. 
  - Encoding query parameters may be helpful if they contain complex or special characters, such as +, =, or &, which can break URLs if not encoded properly.

- **Authentication**:
  - This API does not include authentication, as it was not specified in the project requirements. The current implementation is designed to fulfill the specified functionality without requiring customers to provide credentials.

- **Retrieving customers by name / email**:
  - The logic pertaining to retrieving customers by id or email was a little confusing to me. I was not sure if we use `where` or `orWhere` statement. 
  - The logic I wrote to retrieve customers allows either condition to match (email or name).
  - If only name exists, it will match based on name, and if only email exists, it will match based on email.
  - If both are provided, it will return any customer with either the matching name or the matching email.
  - The search logic I am using relies on exact matches (using `=` instead of `like` in my query) and therefor, users  must provide exact name and / or email.
  - Business logic for calculating tier will default to Silver if all conditions fail.

- **Open API Specification**:
  - I used the README.md file to document the API

- **Base URL**:
  - This documentation assumes local development testing and uses http://localhost:8000 as a base URL. Make sure to adjust the base URL based on your domain name/host
## Unit testing
  - To test crud functionality, run:
    ```bash
    bin/artisan test --group crud

  - To test tier functionality, run:
    ```bash
    bin/artisan test --group tier

  - To test all functionality, run:
    ```bash
    bin/artisan test
