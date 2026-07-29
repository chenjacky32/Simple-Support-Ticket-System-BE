# Product Requirement Document (PRD)

## Simple Support Ticket System - Backend API

### 1. Executive Summary

This project aims to build a Backend API using **Laravel 13** and **MySQL** that purely serves as an API provider for a frontend application.

This application is a **Simple Support Ticket System** that allows users to create tickets and administrators to manage and respond to those tickets. The backend architecture is designed using a **Stateless API** pattern with **JWT (JSON Web Token)** based authentication, and adopts an advanced folder structure (based on _Action_, _Payload_, and _Invokable Controller_) in accordance with _enterprise_-scale Laravel API development best practices.

### 2. Core Technical Specifications

- **Backend Framework**: Laravel 13.x
- **Database**: MySQL 8.x
- **Authentication**: Stateless JWT using the `php-open-source-saver/jwt-auth` package.
- **Primary Key (PK)**: ULID (_Universally Unique Lexicographically Sortable Identifier_) for all entities.
- **Authorization**: Custom Role Middleware (1-M relation) and Laravel Policies (without external packages like Spatie, for performance and simplicity).

### 3. API Architecture Directory Structure (Laravel API Standard)

The application will not place business logic inside Controllers. The folder structure will be adjusted as follows:

- `routes/api/v1/` : Separates routes modularly based on resources (e.g., `tickets.php`, `auth.php`).
- `app/Http/Controllers/{Resource}/V1` : Contains only _Invokable Controllers_ responsible for handling HTTP flow (Receive Request -> Call Action -> Return Response).
- `app/Http/Requests/{Resource}/V1` : Handles form input validation.
- `app/Http/Payloads/{Resource}` : Simple Data Transfer Objects (DTO) to parse request data for type safety.
- `app/Actions/{Resource}` : Classes with a single responsibility (_Single-purpose Business Logic class_) to execute business processes (e.g., `CreateTicket.php`).

### 4. Core Features List

This system uses 3 roles: **USERS**, **ADMIN**, and **SUPERADMIN**.

**A. Authentication & Account Management**

- **Register**: New users sign up (Default account status: `is_active = false`).
- **Activation (Role: SUPERADMIN)**: Activates newly registered accounts (`is_active = true`).
- **Login**: Users receive a JWT `accessToken` (Only succeeds if `is_active = true`). The JWT claim will contain user _role_ information.

**B. Ticket Management (Role: USERS)**

- Create a new ticket (Input: Title, Description, Optional photo/video attachment).
- View their ticket list (Supports Filter & Search functions).
- View ticket details and replies within it.
- View Statistics Dashboard (Displays aggregated ticket counts per status: _Opened, In Progress, Resolved_).

**C. Ticket Operations (Role: ADMIN)**

- View the entire ticket list (Filter & Search).
- Update ticket status (Transitions: _Opened -> In Progress -> Resolved_).
- Add responses/comments (replies) to a ticket.
- Export ticket data to **JSON** or **CSV** formats.
- View Statistics Dashboard for all tickets.

**D. Webhook Integration (Incoming)**

- A specific API endpoint that can be hit by third-party systems when there is a change in ticket status. The payload or response from this external execution will be recorded in the database.

### 5. Application Workflow (Business Flowchart)

1. **Registration**: A new client (User) registers. The account is recorded in the database but remains inactive.
2. **Activation**: Superadmin verifies and activates the account (`is_active = true`).
3. **Authentication**: The client logs in. The backend verifies the activation status and sends a _stateless_ JWT token.
4. **Ticket Creation**: The client creates a complaint ticket. The system generates a unique ticket receipt number (`ticket_code`) and saves attached files (if any).
5. **Ticket Processing**: An Admin receives the incoming ticket, updates the ticket processing status, or provides a response to the client.
6. **History Logging**: The system automatically records every status change into a log along with the time and the user who made the change.
7. **Webhook Trigger**: External systems can hit the Webhook endpoint to change the status programmatically, with the response recorded in the log.
8. **Reporting**: Clients, Admins, and Superadmins can access reporting metrics on the Dashboard or export data to CSV/JSON formats.

### 6. Database Schema (Physical Data Model)

#### 6.1 Entity-Relationship Diagram (ASCII)

```text
                             +-------------------+
                             |       roles       |
                             +-------------------+
                             | id (PK)           |
                             | role (ENUM)       |
                             +-------------------+
                                       | 1
                                       |
                                       | M
                             +-------------------+
                             |       users       |
                             +-------------------+
                             | id (PK)           |
                             | role_id (FK)      |
                             | is_active (BOOL)  |
                             | name, email, etc  |
                             +-------------------+
                                 |   |   |
             +-------------------+   |   +-------------------+
           1 |                     1 |                     1 |
             | M                     | M                     | M
+-------------------+      +-------------------+   +--------------------+
|      tickets      |      | ticket_responses  |   | ticket_status_logs |
+-------------------+      +-------------------+   +--------------------+
| id (PK)           | 1  M | id (PK)           |   | id (PK)            |
| user_id (FK)      |------| ticket_id (FK)    |   | ticket_id (FK)     |
| ticket_code       |      | responded_by (FK) |   | changed_by (FK)    |
| status (ENUM)     |      | message (TEXT)    |   | current_status     |
| attachment_path   | 1  M +-------------------+   | result (TEXT)      |
|                   |------------------------------|                    |
+-------------------+                              +--------------------+
```

#### 6.2 Detailed Table Structure

The design is optimized using _normalization_ principles, with strict _constraints_ (boundary rules).

**1. `roles` Table**

- `id` : ULID (PK)
- `role` : ENUM ('USERS', 'ADMIN', 'SUPERADMIN')

**2. `users` Table**

- `id` : ULID (PK)
- `name`, `email`, `password`, `remember_token` : VARCHAR(255)
- `email_verified_at` : TIMESTAMP NULL
- `role_id` : ULID (FK to `roles.id` ON DELETE RESTRICT)
- `is_active` : BOOLEAN (NOT NULL, DEFAULT FALSE)
- `created_at`, `updated_at` : TIMESTAMP

**3. `tickets` Table**

- `id` : ULID (PK)
- `ticket_code` : VARCHAR(255) (UNIQUE, NOT NULL)
- `title` : VARCHAR(255)
- `description` : TEXT
- `attachment_path` : VARCHAR(255) NULL
- `status` : ENUM ('OPENED', 'IN_PROGRESS', 'RESOLVED') DEFAULT 'OPENED'
- `user_id` : ULID (FK to `users.id`)
- `resolved_at` : TIMESTAMP NULL
- `created_at`, `updated_at` : TIMESTAMP

**4. `ticket_responses` Table**

- `id` : ULID (PK)
- `ticket_id` : ULID (FK to `tickets.id` ON DELETE CASCADE)
- `message` : TEXT
- `responded_by` : ULID (FK to `users.id`)
- `created_at`, `updated_at` : TIMESTAMP

**5. `ticket_status_logs` Table**

- `id` : ULID (PK)
- `ticket_id` : ULID (FK to `tickets.id` ON DELETE CASCADE)
- `changed_by` : ULID (FK to `users.id` NULLABLE - can be Null if changed by external system webhook)
- `previous_status` : ENUM ('OPENED', 'IN_PROGRESS', 'RESOLVED')
- `current_status` : ENUM ('OPENED', 'IN_PROGRESS', 'RESOLVED')
- `result` : TEXT NULL (To store payload log or webhook message)
- `created_at` : TIMESTAMP

### 7. Execution Strategy (For Development Team / AI)

If this document is to be handed over to a junior programmer or AI, here are the workflow instructions:

1. **Focus 1 (Core Infrastructure)**: Install the `jwt-auth` package, set up MySQL connection configurations in `.env`, and do not modify core framework files.
2. **Focus 2 (Folder Structure)**: Create `Actions` and `Payloads` directories inside `app/`. Create version-separated routes in `routes/api/routes.php`.
3. **Focus 3 (Database)**: Run the _migration_ that precisely follows the ULID schema above. Create a minimal seeder for `roles` and one Superadmin account.
4. **Focus 4 (Endpoints & Logic)**: Create CRUD functionalities using a single _Invokable Controller_ approach per endpoint. Do not write complex _logic_ in the _Controller_; delegate that task to the _Action_ classes.
