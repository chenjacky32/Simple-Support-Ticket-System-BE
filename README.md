<h1 align="left">Simple Support Ticket System - Backend API</h1>

<div align="left">

[![Status](https://img.shields.io/badge/status-active-success.svg)]() 
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](/LICENSE)

</div>

<p align="left"> A robust, scalable RESTful API backend built with Laravel 13 and MySQL for the Simple Support Ticket System.
    <br> 
</p>

## 📝 Table of Contents
- [About](#about)
- [Architecture & Features](#features)
- [Getting Started](#getting_started)
- [Testing](#testing)
- [API Documentation](#api_docs)
- [Built Using](#built_using)
- [Authors](#authors)

## 🧐 About <a name = "about"></a>
The **Simple Support Ticket System - Backend** provides a high-performance RESTful API designed to manage customer support operations. It serves as the core data processing and storage layer for the frontend application.

The API is strictly **Stateless**, utilizing **JWT (JSON Web Tokens)** for authentication. It adopts a modern, enterprise-scale architectural pattern by decoupling business logic from routing, using **Actions**, **Payloads** (Data Transfer Objects), and **Single Action (Invokable) Controllers**.

## ✨ Architecture & Features <a name = "features"></a>
- **Enterprise Structure**: Uses strictly typed `Payloads`, pure `Actions` for business logic, and `Invokable Controllers` to ensure the codebase remains scalable and highly maintainable.
- **Stateless Authentication**: Secured with `php-open-source-saver/jwt-auth`.
- **Role-Based Access Control (RBAC)**: Custom middleware protects endpoints based on user roles (`USERS`, `ADMIN`, `SUPERADMIN`).
- **Ticket Lifecycle Management**: APIs to create, retrieve, filter, reply, and securely update the status of support tickets.
- **Dashboard Aggregation**: Real-time statistics and percentage compositions calculated directly at the database level for performance.
- **High Test Coverage**: Comprehensive Unit Tests for data validation and Integration Tests for all API routes ensuring robust stability.

## 🏁 Getting Started <a name = "getting_started"></a>
Follow these instructions to set up the backend on your local machine for development and testing.

### Prerequisites
- PHP (v8.2 or higher)
- Composer
- MySQL (v8.x)

### Installing

1. Clone the repository and install dependencies:
```bash
git clone <repository-url>
cd "Simple Support Ticket System - BE"
composer install
```

2. Configure environment variables:
```bash
cp .env.example .env
```
Update your `.env` with your local MySQL database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

3. Generate application keys:
```bash
php artisan key:generate
php artisan jwt:secret
```

4. Run database migrations and seeders (populates essential roles):
```bash
php artisan migrate --seed
```

5. Start the local development server:
```bash
php artisan serve
```
The API will be accessible at `http://localhost:8000/api/v1/`.

## 🧪 Testing <a name="testing"></a>
The project uses PHPUnit for robust automated testing, utilizing the `RefreshDatabase` trait to ensure an isolated and pristine environment for every test.

Run the entire test suite (Unit & Integration tests):
```bash
php vendor/phpunit/phpunit/phpunit
```
*Note: Depending on your environment configuration, using `php artisan test` might be slower, hence direct execution via the PHPUnit binary is recommended.*

## 📚 API Documentation <a name="api_docs"></a>
The complete API Contract is documented using the OpenAPI (Swagger) 3.0 specification.
You can find the specification in the root directory:
- [`openapi.yaml`](./openapi.yaml)

You can import this file into [Postman](https://www.postman.com/), [Insomnia](https://insomnia.rest/), or view it using [Swagger Editor](https://editor.swagger.io/) to interact with the endpoints and review request/response schemas easily.

## ⛏️ Built Using <a name = "built_using"></a>
- [Laravel 13](https://laravel.com/) - PHP Framework
- [MySQL 8.x](https://www.mysql.com/) - Relational Database
- [JWT Auth](https://github.com/php-open-source-saver/jwt-auth) - Stateless Authentication Library
- [PHPUnit](https://phpunit.de/) - Testing Framework

## ✍️ Authors <a name = "authors"></a>
- [@chenjacky32](https://github.com/chenjacky32) - Idea, Backend & Frontend Implementation
