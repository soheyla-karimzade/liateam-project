# My Laravel Project with JWT Authentication

This is a Laravel project that implements JWT-based authentication, MongoDB for the database, Redis for caching, and the
Repository design pattern. The project includes user registration, login, and CRUD operations for products and orders.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-repo.git
   cd your-repo

2. Install dependencies:
   ```bash
    composer install
3. Copy the .env.example file and update the environment variables:
    ```bash
   cp .env.example .env

4. for run mongodb container in Docker
   ```bash
   docker-compose up -d

5. Update the database and cache configuration in the .env file:
    ```bash
    DB_CONNECTION=mongodb
    DB_HOST=127.0.0.1
    DB_PORT=27017
    DB_DATABASE=liateam
    DB_USERNAME=
    DB_PASSWORD=
    DB_AUTHENTICATION_DATABASE=
6. Run the database migrations:
    ```bashe
    php artisan migrate

7. Run the database seeders:
    ```bashe
    php artisan db:seed

8. Generate JWT secret:
    ```bash
    php artisan jwt:secret
9. Start the application:
    ```bash
   php artisan serve

10. Running Tests
    ```bash
       ./vendor/bin/pest
    ```
    OR
    ```bash
        php artisan test
    ```

### API Routes

#### Auth

User Registration

#### Endpoint: /api/register

#### Method: POST

Headers:

    Content-Type: application/json

Request Body:

```bash
    {
      "name": "test",
      "email": "user_test2@gmail.com",
      "password": "password",
      "password_confirmation": "password"
    }
    ```
    Response:
    Success (201 Created):
    ```bash
    {
        "message": "user register successfully.",
        "user": {
            "name": "test",
            "email": "user_test2@gmail.com",
            "updated_at": "2024-09-12T16:01:09.399000Z",
            "created_at": "2024-09-12T16:01:09.399000Z",
            "_id": "66e31045d6bcfa1735022602"
        },
        "token": "your-jwt-token"
    }
```

Error (422 Unprocessable Entity):

  ```bash
        {
        "email": [
        "The email has already been taken."
        ]
        }
```

2. User Login
   #### Endpoint: /api/login
   #### Method: POST

   Headers:

        Content-Type: application/json

   Request Body:

    ```
    {
      "email": "user_test2@gmail.com",
      "password": "password"
    }
    ```
   Response:
   Success (200 OK):
    ```
    {
        "message": "login successfully",
        "token": "your-jwt-token"
    }
    ```

   Error (401 Unauthorized):
    ```bash
        {
        "error": "unAuthorised"
    }
    ```

3. Get Authenticated User Info
   #### Endpoint: /api/user
   #### Method: GET

   Headers:

        Authorization: Bearer {token}

   Response:
   Success (200 OK):
    ```
   {
        "_id": "66e312d1d6bcfa1735022603",
        "name": "test",
        "email": "user_test3@gmail.com",
        "updated_at": "2024-09-12T16:12:01.997000Z",
        "created_at": "2024-09-12T16:12:01.997000Z"
    }
   ```
   Error (401 Unauthorized):

    ```
    {
      "message": "Unauthenticated."
      }
   ```

----------------------------------------------------------------------

#### Product CRUD Operations

1. Create Product:

   #### Endpoint: /api/products
   #### Method: POST

   Headers:

       Authorization: Bearer {token}
       Content-Type: application/json

   Request Body:

    ```
       {
           "name": "Product Name",
           "price": 100.50,
           "inventory": 10
       }
    ```

   Response:

   Success (201 Created):
    ```
    {
      "name": "Product Name",
      "price": 100.50,
      "inventory": 10,
      "created_at": "2024-09-12T12:34:56.000000Z",
      "updated_at": "2024-09-12T12:34:56.000000Z"
    }
    ```

   Error (422 Unprocessable Entity):
    ```
    {
        "message":"The name field is required.",
        "errors":{
            "name":["The name field is required."]
        }
    }
    
    ```


2. Get All Products
   #### Endpoint: /api/products
   #### Method: GET

   Headers:

        Authorization: Bearer {token}
        Response:

   Success (200 OK):
    ```
            [
                {
                "name": "Product 1",
                "price": 100.50,
                "inventory": 10,
                "created_at": "2024-09-12T12:34:56.000000Z",
                "updated_at": "2024-09-12T12:34:56.000000Z",
                "user_id": "1"
                },
                {
                "id": 2,
                "name": "Product 2",
                "price": 50.00,
                "inventory": 5,
                "created_at": "2024-09-12T12:34:56.000000Z",
                "updated_at": "2024-09-12T12:34:56.000000Z",
                "user_id": "1"
                }
            ]
    ```

3. Get Product by ID
   #### Endpoint: /api/products/{id}
   #### Method: GET

   Headers:

        Authorization: Bearer {token}
        Response:

   Success (200 OK):
    ```
    {
      "id": 1,
      "name": "Product Name",
      "price": 100.50,
      "inventory": 10,
      "created_at": "2024-09-12T12:34:56.000000Z",
      "updated_at": "2024-09-12T12:34:56.000000Z",
       "user_id": "1"
    }
    
    ```
   Error (404 Not Found):
    ```
    {
      "message": "Product not found."
    }
    
    ```


4. Update Product
   #### Endpoint: /api/products/{id}
   #### Method: PUT

   Headers:

        Authorization: Bearer {token}
        Content-Type: application/json

   Request Body:
    ```
       {
           "name": "Product Name",
           "price": 100.50,
           "inventory": 10
       }
    ```

   Response:

   Success (200):
    ```
    {
      "name": "Product Name",
      "price": 100.50,
      "inventory": 10,
      "created_at": "2024-09-12T12:34:56.000000Z",
      "updated_at": "2024-09-12T12:34:56.000000Z"
    }
    ```

   Error (422 Unprocessable Entity):
    ```
    {
        "message":"The name field is required.",
        "errors":{
            "name":["The name field is required."]
        }
    }   
    ```

   Error (404 Not Found):
    ```
    {
      "message": "Product not found."
    }
    
    ```

5. Delete Product
   #### Endpoint: /api/products/{id}
   #### Method: DELETE

   Headers:

        Authorization: Bearer {token}
        Response:

   Success (204 OK):
    ```
    {
    "message": "Product deleted successfully."
    }
    
    ```
   Error (404 Not Found):
    ```
    {
      "message": "Product not found."
    }
    
    ```

## Order CRUD Operations




### Update Order:

#### Endpoint: /api/orders/{id}

#### Method: PUT

Headers:
    
    Authorization: Bearer {token},
    Content-Type: application/json

Request Body:

```baash
    {
        'products' => [
            ['id' => 1, 'quantity' => 2],
        ],
        'count' => 1,
        'total_price' => 100.00,
    }
```

Response:

Success (200):

```
{

  "total_price": 100.00,
 'count' => 1,
  "products": [
       ['id' => 1, 'quantity' => 2],
  ],
  "created_at": "2024-09-12T12:34:56.000000Z",
  "updated_at": "2024-09-12T12:34:56.000000Z"
}
```

Error (422 Unprocessable Entity):

```bash
{
    "message":"The products field is required.",
    "errors":
    {
        "products":["The products field is required."]
    }
}
```

2. Get All Orders

   #### Endpoint: /api/orders
   #### Method: GET

   Headers:

        Authorization: Bearer {token}
   Response:

   Success (200 OK):

    ```
    [
      {
       "count":3,
        "total_price": 201.00,
        "products": [
          {
            "id": 1,
            "quantity": 2
          },
          {
            "id": 2,
            "quantity": 1
          }
        ],
        "created_at": "2024-09-12T12:34:56.000000Z"
        "updated_at": "2024-09-12T12:34:56.000000Z"
      }
    ]
    
    ```


3. Get Order by ID
   #### Endpoint: /api/orders/{id}
   #### Method: GET
   Headers:

        Authorization: Bearer {token}
        Response:

   Success (200 OK):

    ```bash
            {
               "count":3,
                "total_price": 201.00,
                "products": [
                  {
                    "id": 1,
                    "quantity": 2
                  },
                  {
                    "id": 2,
                    "quantity": 1
                  }
                ],
                "created_at": "2024-09-12T12:34:56.000000Z"
                "updated_at": "2024-09-12T12:34:56.000000Z"
            }
            
      ```

Error (404 Not Found):

```
        {
          "message": "Order not found."
        }
    
```

4. Delete Order
   #### Endpoint: /api/orders/{id}
   #### Method: DELETE

   Headers:

   Authorization: Bearer {token}
   Response:
   Success (204 OK):

```
{
  "message": "Order deleted successfully."
}

```

Error (404 Not Found):

```
{
  "message": "Order not found."
}
```

License
This project is open-source and available under the MIT License.

### توضیحات:

- **Endpoint**: مسیر API
- **Method**: نوع درخواست (GET, POST, PUT, DELETE)
- **Headers**: هدرهایی که باید به درخواست اضافه شوند
- **Request Body**: داده‌های ورودی که در درخواست ارسال می‌شوند
- **Response**: خروجی که API برمی‌گرداند، شامل حالت‌های موفقیت‌آمیز و خطا

