# NanoSolutions Laravel + Vue (Quasar) Notes Application

This project is a full-stack Notes CRUD application built using
**Laravel**, **Vue 3**, **Quasar**, **Axios**, and **Vite**.\
It was created as part of a technical assessment and fulfills all
required functionalities.

------------------------------------------------------------------------

## Assessment Requirements Checklist

### Backend

-   [x] Laravel project created\
-   [x] Notes table + model\
-   [x] REST API routes\
-   [x] CRUD methods implemented\
-   [x] Validation for create/update\
-   [x] JSON responses

### Frontend

-   [x] Vue 3 configured with Vite\
-   [x] Quasar UI integrated\
-   [x] Axios API layer (`notes.js`)\
-   [x] Create Note page\
-   [x] Edit Note page\
-   [x] Delete confirmation dialog\
-   [x] Navigation with Vue Router\
-   [x] Fully functional CRUD in UI

------------------------------------------------------------------------

## ⚙ Installation & Setup

### 1️⃣ Install dependencies

``` bash
composer install
npm install
```

### 2️⃣ Run database migrations

``` bash
php artisan migrate
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

## Testing

If you want to test the update route:

``` bash
php artisan tinker
>>> Note::create(['title' => 'Test', 'content' => 'Example'])
```

------------------------------------------------------------------------

## API Endpoints

  Method   Endpoint          Description
  -------- ----------------- -------------------
  GET      /api/notes        List all notes
  POST     /api/notes        Create a new note
  GET      /api/notes/{id}   Retrieve a note
  PUT      /api/notes/{id}   Update a note
  DELETE   /api/notes/{id}   Delete a note

------------------------------------------------------------------------

## Features

### ✔ Backend (Laravel)

-   Create, read, update, delete notes
-   RESTful API routes under `/api`
-   JSON validation responses
-   Auto-routed model binding using `Note $note`
-   Fully tested CRUD functionality

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
    ├── public/
    ├── resources/
    │   ├── css/
    │   │   ├── app.css
    │   │   └── quasar-variables.sass
    │   ├── js/
    │       ├── api/notes.js
    │       ├── pages/
    │       │   ├── NoteListPage.vue
    │       │   ├── CreateNotePage.vue
    │       │   └── EditNotePage.vue
    │       ├── router.js
    │       └── app.js
    ├── routes/api.php
    ├── routes/web.php
    ├── vite.config.js
    └── README.md

------------------------------------------------------------------------

## 📜 License

MIT License --- free to use, modify, and distribute.

------------------------------------------------------------------------

## 🙌 Thank You

Thank you for reviewing this assessment.
If you have any questions, feel free to ask!
