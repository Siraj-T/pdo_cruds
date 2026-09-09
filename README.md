# Users Management CRUD (The Laravel Way)

An interactive, modern Laravel web application and educational CRUD guide powered by **Eloquent ORM** and **SQLite**. 

This repository has been refactored from procedural Vanilla PHP / PDO into the idiomatic **Laravel MVC** architecture.

---

## 🚀 Quick Start

### 1. Run the Laravel Application
In the project root, start the Laravel development server:
```bash
php artisan serve --port=8000
```
Open your browser at:
```
http://localhost:8000
```

### 2. Run the Legacy Vanilla PHP App (Optional Comparison)
The original procedural PDO implementation has been preserved in `legacy_vanilla_php/`. To run it on another port:
```bash
php -S localhost:8080 -t legacy_vanilla_php
```
Open your browser at:
```
http://localhost:8080
```
*(Both apps can run at the same time on ports 8000 and 8080 for side-by-side comparison)*

---

## 🌟 Key Features

- **MVC Architecture**: Clean separation between Model (`User.php`), Controller (`UserController.php`), and Blade Views (`resources/views/`).
- **Eloquent ORM**: Replaces manual SQL queries with expressive methods (`User::all()`, `User::create()`, `$user->update()`, `$user->delete()`).
- **Interactive Eloquent Code Runner**: Real-time evaluator supporting live Eloquent queries with template snippets.
- **SQLite Migrations & Seeders**: Schema defined in `database/migrations/` and pre-populated with `UserSeeder.php`.
- **Form Security & Validation**: Built-in CSRF protection (`@csrf`), method spoofing (`@method('PUT')`, `@method('DELETE')`), and server-side request validation.
- **Educational Guide**: An in-app side-by-side guide comparing Vanilla PDO code against Laravel Eloquent at `/guide`.

---

## 📂 Project Structure

```
├── app/
│   ├── Http/Controllers/UserController.php  # RESTful CRUD & Eloquent runner
│   └── Models/User.php                     # Eloquent Model with $fillable
├── database/
│   ├── migrations/                         # SQLite users table schema
│   └── seeders/UserSeeder.php              # Initial sample users
├── resources/views/
│   ├── layouts/app.blade.php               # Master layout
│   ├── users/index.blade.php               # CRUD dashboard & live runner
│   └── guide.blade.php                     # Vanilla PDO vs Eloquent guide
├── routes/web.php                          # RESTful resource routing
└── legacy_vanilla_php/                     # Archived original plain PHP app
```
