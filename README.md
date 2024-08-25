# Movie Library API

The **Movie Library API** is a RESTful web service built with PHP and MySQL that allows users to manage a collection of movies and their ratings. 

## Table of Contents

- [Movie Library API](#movie-library-api)
  - [Table of Contents](#table-of-contents)
  - [Features](#features)
  - [Getting Started](#getting-started)
    - [Prerequisites](#prerequisites)
    - [Installation](#installation)
    - [Postman Collection](#postman-collection)

## Features

1. Movies
  - Add new movies to the library
  - Retrieve details of a specific movie or all movies
  - Update movie information
  - Delete movies from the library
  - Search for movies by director
  - Sort for movies (Asc/Desc)
  - Paginate

2. Ratings
  - Add new ratings to the movies
  - Retrieve details of a specific rating or all ratings
  - Update rating information
  - Delete ratings from the library

3. Authorization
  - Registration for new user
  - Login user
  - Logout user

## Getting Started

These instructions will help you set up and run the Movie Library API on your local machine for development and testing purposes.

### Prerequisites

- **PHP** (version 7.4 or later)
- **MySQL** (version 5.7 or later)
- **Apache** or **Nginx** web server
- **Composer** (PHP dependency manager, if you are using any PHP libraries)


### Installation

1. **Clone the repository**:

   ```
   git clone https://github.com/osama806/Movie-library-api.git
   cd Movie-library-api
   ```

2. **Set up the environment variables:**:

  Create a .env file in the root directory and add your database configuration:
  ```
  DB_HOST=localhost
  DB_PORT=3306
  DB_DATABASE=movie_library
  DB_USERNAME=root
  DB_PASSWORD=password
  ```

3. **Set up the MySQL database:**:

  - Create a new database in MySQL:
    ```
    CREATE DATABASE movie_library;
    ```
  - Run the provided SQL script to create the necessary tables:
    ```
    mysql -u root -p movie_library < database/schema.sql
    ```

4. **Configure the server**:  
  - Ensure your web server (Apache or Nginx) is configured to serve PHP files.
  - Place the project in the appropriate directory (e.g., /var/www/html for Apache on Linux).

5. **Install dependencies (if using Composer)**:
  ```
  composer install
  ```

6. **Start the server:**:
  - For Apache or Nginx, ensure the server is running.
  - The API will be accessible at http://localhost/Movie-library-api.


### Postman Collection
- Link:
    ```
    https://web.postman.co/workspace/Public-Collections-to-Share~6c698a35-a5d0-4170-b396-cae86a275be3
    ```
