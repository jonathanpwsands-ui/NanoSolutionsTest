# NanoSolutions Laravel + Vue (Quasar) Notes Application with Authentication

This project is a full-stack Notes CRUD application built using
**Laravel**, **Vue 3**, **Quasar**, **Axios**, and **Vite**.\
It was created as part of a technical assessment and fulfills all
required functionalities.

------------------------------------------------------------------------

## Assessment Requirements Checklist

### Backend

-   [x] Laravel project created
-   [x] Notes table + model with user_id foreign key
-   [x] REST API routes
-   [x] CRUD methods implemented
-   [x] Validation for create/update
-   [x] JSON responses
-   [x] Laravel Sanctum API authentication (register/login/logout)
-   [x] Protected routes with auth:sanctum middleware
-   [x] User-specific notes scoping and NotePolicy authorization

### Frontend

-   [x] Vue 3 configured with Vite
-   [x] Quasar UI integrated
-   [x] Axios API layer (notes.js + auth.js)
-   [x] Create/Edit/Delete Note pages
-   [x] Note list with q-table
-   [x] Login/Register pages
-   [x] Pinia auth store with persistence
-   [x] Vue Router with auth guards
-   [x] Axios auth interceptor (Bearer token)
-   [x] Auto-redirect on 401 unauthorized
-   [x] Fully functional protected CRUD UI

------------------------------------------------------------------------

## ⚙ Installation & Setup

### 0️⃣ Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Configure your `DB_*` settings in `.env`.

### 1️⃣ Install dependencies

```bash
composer install
npm install
```

### 2️⃣ Run database migrations

```bash
php artisan migrate
php artisan db:seed  # Optional: creates test users via factories/seeders
```

### 3️⃣ Start Laravel backend

``` bash
php artisan serve
```

### 4️⃣ Start Vite frontend

``` bash
npm run dev
```

------------------------------------------------------------------------

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

Tests cover authentication flows, protected notes CRUD, and authorization policies.

For manual API testing, first login/register to obtain a token, then use `Authorization: Bearer {token}` header.

------------------------------------------------------------------------

## API Endpoints

All notes endpoints require authentication (`auth:sanctum` middleware).

**Public (no auth):**

| Method | Endpoint    | Description                  |
|--------|-------------|------------------------------|
| POST   | /api/register | Create new user account    |
| POST   | /api/login   | Authenticate and get token  |

**Protected (Bearer token required):**

| Method | Endpoint         | Description                          |
|--------|------------------|--------------------------------------|
| GET    | /api/user        | Get current user profile             |
| POST   | /api/logout      | Revoke current token                 |
| GET    | /api/notes       | List user's notes                    |
| POST   | /api/notes       | Create new note (auto-assigns user)  |
| GET    | /api/notes/{id}  | Get specific note (user-owned)       |
| PUT    | /api/notes/{id}  | Update note (user-owned)             |
| DELETE | /api/notes/{id}  | Delete note (user-owned)             |

------------------------------------------------------------------------

## Features

### ✔ Backend (Laravel)

- User management: register, login, logout, profile
- Laravel Sanctum for SPA API token authentication
- Protected API routes (`auth:sanctum`)
- User-scoped notes (`user_id` foreign key)
- Authorization via `NotePolicy` (ownership checks)
- Full CRUD with validation & JSON responses
- Resource controllers with model binding
- Factories & seeders for testing
- Feature & unit tests

### ✔ Frontend (Vue 3 + Quasar)

-   Note list table with Quasar `<q-table>`
-   Create Note page
-   Edit Note page
-   Delete confirmation modal
-   Responsive UI styled via Quasar framework
-   Axios-based API service (`notesApi.js`)
-   Vue Router for navigation
-   Hot Module Reloading via Vite

------------------------------------------------------------------------

## 📂 Project Structure

    NanoSolutionsTest/
    ├── app/
    │   ├── Http/Controllers/AuthController.php
    │   ├── Http/Controllers/NoteController.php
    │   ├── Models/Note.php
    │   ├── Models/User.php (HasApiTokens)
    │   └── Policies/NotePolicy.php
    ├── config/sanctum.php
    ├── database/migrations/
    │   ├── ..._create_users_table.php
    │   ├── ..._create_personal_access_tokens_table.php
    │   └── ..._create_notes_table.php (with user_id)
    ├── public/
    ├── resources/
    │   ├── css/app.css
    │   ├── js/
    │   │   ├── api/auth.js
    │   │   ├── api/notes.js
    │   │   ├── pages/
    │   │   │   ├── LoginPage.vue
    │   │   │   ├── RegisterPage.vue
    │   │   │   ├── NoteListPage.vue
    │   │   │   ├── CreateNotePage.vue
    │   │   │   └── EditNotePage.vue
    │   │   ├── stores/auth.js
    │   │   ├── router.js
    │   │   └── app.js
    ├── routes/api.php (auth & protected notes)
    ├── routes/web.php
    ├── tests/Feature/AuthTest.php
    ├── vite.config.js
    └── README.md

## 🔐 Authentication

Uses **Laravel Sanctum** for secure SPA API authentication:

- **Register/Login**: Frontend forms send credentials, receive API token
- **Token Storage**: localStorage (frontend), auto-sent via Axios interceptor
- **Protected Routes**: All `/api/notes*` require valid Bearer token
- **Logout**: Revokes token server-side
- **Security**: CSRF protection via Sanctum cookies, note ownership enforced

**Quick Start**:
1. `npm run dev && php artisan serve`
2. Visit `http://localhost:5173/register` → create account
3. Auto-redirect to notes after login

**API Token Usage** (Postman/cURL):
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
```

------------------------------------------------------------------------

## 📜 License

MIT License --- free to use, modify, and distribute.

------------------------------------------------------------------------

## 🙌 Thank You

Thank you for reviewing this assessment.
If you have any questions, feel free to ask!
