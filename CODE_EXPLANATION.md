# Complete Code & Architecture Breakdown: The Laravel Way

This document explains **every single file, line of code, concept, and interaction** in this project. It is structured so you can understand the complete inner workings and confidently explain every detail to your teacher.

---

## 📑 Table of Contents
1. [The Big Picture: MVC Architecture & Request Lifecycle](#1-the-big-picture-mvc-architecture--request-lifecycle)
2. [File 1: `routes/web.php` (Routing Engine)](#file-1-routeswebphp)
3. [File 2: `app/Models/User.php` (Eloquent Model)](#file-2-appmodelsuserphp)
4. [File 3: `app/Http/Controllers/UserController.php` (Controller Logic)](#file-3-apphttpcontrollersusercontrollerphp)
5. [File 4: `database/migrations/..._create_users_table.php` (Database Schema)](#file-4-databasemigrations0001_01_01_000000_create_users_tablephp)
6. [File 5: `database/seeders/UserSeeder.php` (Database Seeder)](#file-5-databaseseedersuserseederphp)
7. [File 6: `resources/views/layouts/app.blade.php` (Master Blade Layout)](#file-6-resourcesviewslayoutsappbladephp)
8. [File 7: `resources/views/users/index.blade.php` (CRUD Dashboard & Code Runner)](#file-7-resourcesviewsusersindexbladephp)
9. [File 8: `resources/views/guide.blade.php` (PDO vs. Eloquent Comparison)](#file-8-resourcesviewsguidebladephp)
10. [File 9: `public/css/app.css` (Styling System)](#file-9-publiccssappcss)
11. [How Everything Interacts (Step-by-Step Flow Diagrams)](#how-everything-interacts-step-by-step-flow-diagrams)

---

## 1. The Big Picture: MVC Architecture & Request Lifecycle

In your original Vanilla PHP app, **SQL queries, HTML markup, input validation, and business logic were all jammed together** into `index.php`.

In Laravel, responsibilities are separated into **MVC**:
1. **Model (`app/Models/User.php`)**: Manages the database table data and rules.
2. **View (`resources/views/...`)**: Formats and displays the HTML to the browser using Blade.
3. **Controller (`app/Http/Controllers/UserController.php`)**: The "traffic cop" and brain. It accepts user requests, asks the Model for data, and passes that data to the View.

### The Request Lifecycle Flow:
```
Browser Request (e.g. GET /users)
       │
       ▼
1. public/index.php (Front Controller entry point)
       │
       ▼
2. routes/web.php (Finds which Controller method to call)
       │
       ▼
3. UserController.php (Executes logic, validates inputs)
       │
       ├─► 4. User.php Model (Queries/Inserts/Updates in SQLite)
       │
       ▼
5. resources/views/users/index.blade.php (Compiles HTML)
       │
       ▼
HTTP Response returned to the Browser!
```

---

## File 1: `routes/web.php`

**Location:** `routes/web.php`  
**Purpose:** Maps incoming browser URLs and HTTP methods (`GET`, `POST`, `PUT`, `DELETE`) to the controller methods that handle them.

### Code & Line-by-Line Explanation:

```php
1: <?php
2: 
3: use App\Http\Controllers\UserController;
4: use Illuminate\Support\Facades\Route;
```
- **Line 1 (`<?php`):** Standard PHP opening tag.
- **Line 3 (`use App\Http\Controllers\UserController;`):** Imports the `UserController` class so we can map routes to its methods.
- **Line 4 (`use Illuminate\Support\Facades\Route;`):** Imports Laravel's `Route` facade which provides static helper methods (`get`, `post`, `resource`).

```php
6: // Redirect root to users index
7: Route::get('/', [UserController::class, 'index'])->name('home');
```
- **Line 7:** Whenever someone visits the root URL (`http://localhost:8000/`) with a `GET` request, execute the `index()` method inside `UserController`. The `.name('home')` assigns an internal nickname so you can reference it anywhere as `route('home')`.

```php
9: // RESTful resource routes for User CRUD
10: Route::resource('users', UserController::class);
```
- **Line 10 (`Route::resource(...)`):** A powerful Laravel one-liner! It automatically generates **7 RESTful routes** behind the scenes:
  1. `GET /users` ➔ `index()` (List all users)
  2. `GET /users/create` ➔ `create()` (Show create form)
  3. `POST /users` ➔ `store()` (Save new user)
  4. `GET /users/{user}` ➔ `show()` (View single user)
  5. `GET /users/{user}/edit` ➔ `edit()` (Show edit form)
  6. `PUT/PATCH /users/{user}` ➔ `update()` (Save edited user)
  7. `DELETE /users/{user}` ➔ `destroy()` (Delete user)

```php
12: // Interactive Eloquent code runner
13: Route::post('/run-snippet', [UserController::class, 'runSnippet'])->name('users.run');
```
- **Line 13:** When someone submits the code-runner form via `POST /run-snippet`, it calls the `runSnippet()` method in `UserController`. Named as `users.run`.

```php
15: // Eloquent vs Vanilla PDO comparison guide
16: Route::get('/guide', [UserController::class, 'guide'])->name('guide');
```
- **Line 16:** When someone clicks the "📖 PDO vs Eloquent Guide" button via `GET /guide`, call the `guide()` method in `UserController`.

---

## File 2: `app/Models/User.php`

**Location:** `app/Models/User.php`  
**Purpose:** The **Eloquent Model**. Represents the `users` table in SQLite. Every instance of this class corresponds to a row in the database.

### Code & Line-by-Line Explanation:

```php
1: <?php
2: 
3: namespace App\Models;
```
- **Line 3:** Defines the PHP namespace. In Laravel, models live in `App\Models`.

```php
5: use Database\Factories\UserFactory;
6: use Illuminate\Database\Eloquent\Attributes\Fillable;
7: use Illuminate\Database\Eloquent\Attributes\Hidden;
8: use Illuminate\Database\Eloquent\Factories\HasFactory;
9: use Illuminate\Foundation\Auth\User as Authenticatable;
10: use Illuminate\Notifications\Notifiable;
```
- **Lines 5–10:** Imports Laravel's core classes for authentication, factory seeding, and notifications.

```php
12: #[Fillable(['name', 'email', 'role', 'status', 'password'])]
13: #[Hidden(['password', 'remember_token'])]
14: class User extends Authenticatable
15: {
16:     /** @use HasFactory<UserFactory> */
17:     use HasFactory, Notifiable;
```
- **Line 12 & 13:** Modern PHP 8 attributes specifying which fields can be mass-assigned and which should be hidden in JSON.
- **Line 14:** `class User extends Authenticatable`: `User` inherits Laravel's built-in authentication and Eloquent ORM powers.
- **Line 17:** Trait inclusion for factories and notifications.

```php
19:     /**
20:      * The attributes that are mass assignable.
21:      */
22:     protected $fillable = [
23:         'name',
24:         'email',
25:         'role',
26:         'status',
27:         'password',
28:     ];
```
- **Lines 22–28 (`$fillable`):** **Crucial Security Feature!** In Vanilla PHP, people often accidentally let attackers inject malicious fields into `INSERT` or `UPDATE` queries. Laravel protects against this using "Mass Assignment Protection". Only fields listed inside `$fillable` are allowed to be passed directly into `User::create($request->all())` or `$user->update($data)`.

```php
30:     protected function casts(): array
31:     {
32:         return [
33:             'email_verified_at' => 'datetime',
34:             'password' => 'hashed',
35:         ];
36:     }
37: }
```
- **Lines 30–36:** Defines type casting. For example, any password saved is automatically hashed with bcrypt, and dates become Carbon datetime instances.

---

## File 3: `app/Http/Controllers/UserController.php`

**Location:** `app/Http/Controllers/UserController.php`  
**Purpose:** Handles all user interactions, input validation, calls to Eloquent, and passing data to Blade views.

### Code & Line-by-Line Breakdown:

```php
1: <?php
2: 
3: namespace App\Http\Controllers;
4: 
5: use App\Models\User;
6: use Illuminate\Http\RedirectResponse;
7: use Illuminate\Http\Request;
8: use Illuminate\View\View;
9: use Throwable;
10: 
11: class UserController extends Controller
12: {
```
- **Lines 5–9:** Imports the `User` model, request/response types, and `Throwable` for catching errors.

---

### Method 1: `index()` — Displaying the Main Dashboard

```php
16:     public function index(Request $request): View
17:     {
18:         $users = User::orderBy('id', 'asc')->get();
19:         $editUser = null;
20: 
21:         if ($request->has('edit')) {
22:             $editUser = User::find($request->query('edit'));
23:         }
24: 
25:         return view('users.index', compact('users', 'editUser'));
26:     }
```
- **Line 18 (`User::orderBy('id', 'asc')->get()`):** Eloquent query that translates behind the scenes to: `SELECT * FROM users ORDER BY id ASC`. It returns an Eloquent Collection of user objects.
- **Lines 21–23:** If the URL has `?edit=3`, `User::find(3)` fetches that user record from the SQLite database.
- **Line 25 (`return view('users.index', compact(...));`):** Loads the `resources/views/users/index.blade.php` template and sends `$users` and `$editUser` variables into it.

---

### Method 2: `store()` — Creating a New User (The Laravel Way)

```php
31:     public function store(Request $request): RedirectResponse
32:     {
33:         $validated = $request->validate([
34:             'name'   => 'required|string|max:255',
35:             'email'  => 'required|email|max:255|unique:users,email',
36:             'role'   => 'required|string|max:100',
37:             'status' => 'required|in:active,pending,inactive',
38:         ]);
39: 
40:         User::create($validated);
41: 
42:         return redirect()->route('users.index')->with('success', '✓ User created successfully!');
43:     }
```
- **Lines 33–38 (`$request->validate([...])`):** Built-in Laravel validation:
  - `name`: Must not be empty, must be a string, max 255 chars.
  - `email`: Must be a valid email, unique in the `users` table. If someone tries to create a duplicate email, Laravel halts execution and sends an error message back to the form automatically!
  - `status`: Must be one of `active`, `pending`, or `inactive`.
- **Line 40 (`User::create($validated)`):** Eloquent automatically builds and executes the safe SQL `INSERT INTO users (name, email, role, status, created_at, updated_at) VALUES (...)` with parameter binding!
- **Line 42:** Redirects the user back to the index page with a flash message in session: `'✓ User created successfully!'`.

---

### Method 3: `edit()` — Show Edit Form

```php
48:     public function edit(User $user): View
49:     {
50:         $users = User::orderBy('id', 'asc')->get();
51:         $editUser = $user;
52: 
53:         return view('users.index', compact('users', 'editUser'));
54:     }
```
- **Line 48 (`public function edit(User $user)`):** **Route Model Binding!** Laravel sees the route `users/{user}/edit`. If the URL is `/users/2/edit`, Laravel automatically queries the database: `SELECT * FROM users WHERE id = 2`, finds user #2, and injects it into `$user` automatically! No manual `User::find()` needed.
- **Line 53:** Re-renders the dashboard with `$editUser` populated, turning the form into the "Edit User" mode.

---

### Method 4: `update()` — Updating an Existing Record

```php
59:     public function update(Request $request, User $user): RedirectResponse
60:     {
61:         $validated = $request->validate([
62:             'name'   => 'required|string|max:255',
63:             'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
64:             'role'   => 'required|string|max:100',
65:             'status' => 'required|in:active,pending,inactive',
66:         ]);
67: 
68:         $user->update($validated);
69: 
70:         return redirect()->route('users.index')->with('success', '✓ User updated successfully!');
71:     }
```
- **Line 63 (`unique:users,email,' . $user->id`):** Validates email uniqueness while ignoring this user's current ID (so you can save other fields without being blocked by your own email!).
- **Line 68 (`$user->update($validated)`):** Generates and runs `UPDATE users SET name=?, email=?, role=?, status=?, updated_at=CURRENT_TIMESTAMP WHERE id=?`.
- **Line 70:** Redirects back with a success notification.

---

### Method 5: `destroy()` — Deleting a Record

```php
76:     public function destroy(User $user): RedirectResponse
77:     {
78:         $user->delete();
79: 
80:         return redirect()->route('users.index')->with('success', '✓ User deleted successfully!');
81:     }
```
- **Line 78 (`$user->delete()`):** Executes `DELETE FROM users WHERE id = :id`.
- **Line 80:** Redirects back with flash success message.

---

### Method 6: `runSnippet()` — Live Eloquent Code Runner

```php
86:     public function runSnippet(Request $request): View|RedirectResponse
87:     {
88:         $snippet = trim($request->input('code', ''));
```
- **Line 88:** Grabs the code string submitted by the user from the code editor textarea.

```php
99:         try {
100:             $cleanCode = preg_replace('/^\s*<\?(php)?/i', '', $snippet);
101:             $cleanCode = preg_replace('/\?>\s*$/', '', $cleanCode);
102: 
103:             ob_start();
104:             $result = eval("use App\Models\User; return (" . rtrim($cleanCode, ';') . ");");
105:             $output = ob_get_clean();
```
- **Lines 100–101:** Strips opening/closing PHP tags (`<?php` and `?>`).
- **Lines 103–105:** Uses output buffering (`ob_start()`) and `eval()` with `use App\Models\User;` so any typed Eloquent code like `User::all()` or `User::where('status', 'active')->get()` executes live against SQLite!
- **Lines 107–120:** Inspects `$result`:
  - If it returns an Eloquent Collection or Array, it replaces the table data with that result.
  - If it returns a single `User`, it wraps it in a collection.
  - If it returns a number (like `User::count()`), it displays `Result: 3`.
- **Lines 131–138:** Catches any PHP syntax or runtime errors gracefully into `$errorMessage` so the app never white-screens or crashes!
- **Line 140:** Returns the view with `$customResults`, `$executedCode`, and flash messages.

---

### Method 7: `guide()` — Rendering the Comparison Guide

```php
151:     public function guide(): View
152:     {
153:         return view('guide');
154:     }
```
- Returns `resources/views/guide.blade.php`.

---

## File 4: `database/migrations/0001_01_01_000000_create_users_table.php`

**Location:** `database/migrations/0001_01_01_000000_create_users_table.php`  
**Purpose:** Version-controlled database schema definition. Instead of manually running raw SQL `CREATE TABLE` commands in a database tool or hardcoding it in `db.php`, Laravel uses migrations.

### Code & Line-by-Line Explanation:

```php
1: <?php
2: 
3: use Illuminate\Database\Migrations\Migration;
4: use Illuminate\Database\Schema\Blueprint;
5: use Illuminate\Support\Facades\Schema;
6: 
7: return new class extends Migration
8: {
```
- Anonymous migration class extending Laravel's `Migration` base class.

```php
13:     public function up(): void
14:     {
15:         Schema::create('users', function (Blueprint $table) {
16:             $table->id();
17:             $table->string('name');
18:             $table->string('email')->unique();
19:             $table->string('role')->default('Developer');
20:             $table->string('status')->default('active');
21:             $table->timestamp('email_verified_at')->nullable();
22:             $table->string('password')->nullable();
23:             $table->rememberToken();
24:             $table->timestamps();
25:         });
```
- **Line 15 (`Schema::create('users', ...)`):** Instructs the database driver (SQLite in our case) to create a table named `users`.
- **Line 16 (`$table->id()`):** Creates an auto-incrementing unsigned integer primary key column: `id INTEGER PRIMARY KEY AUTOINCREMENT`.
- **Line 17 (`$table->string('name')`):** Creates a `VARCHAR(255)` / `TEXT` column for full name.
- **Line 18 (`$table->string('email')->unique()`):** Creates an `email` column and adds a `UNIQUE` index constraint to guarantee no two users share the same email.
- **Line 19 (`$table->string('role')->default('Developer')`):** `role` column with `'Developer'` default value.
- **Line 20 (`$table->string('status')->default('active')`):** `status` column with `'active'` default value.
- **Line 24 (`$table->timestamps()`):** Automatically adds **two** datetime columns: `created_at` and `updated_at`. Eloquent updates these automatically whenever you create or edit records!

```php
43:     public function down(): void
44:     {
45:         Schema::dropIfExists('users');
46:     }
```
- **Lines 43–46 (`down()`):** Defines how to rollback this migration (`php artisan migrate:rollback` drops the table).

---

## File 5: `database/seeders/UserSeeder.php`

**Location:** `database/seeders/UserSeeder.php`  
**Purpose:** Pre-populates the database with initial sample records using Eloquent.

### Code & Line-by-Line Explanation:

```php
1: <?php
2: 
3: namespace Database\Seeders;
4: 
5: use App\Models\User;
6: use Illuminate\Database\Seeder;
7: 
8: class UserSeeder extends Seeder
9: {
10:     public function run(): void
11:     {
12:         $users = [
13:             [
14:                 'name' => 'Alice Smith',
15:                 'email' => 'alice@example.com',
16:                 'role' => 'Lead Architect',
17:                 'status' => 'active',
18:             ],
19:             [
20:                 'name' => 'Bob Jones',
21:                 'email' => 'bob@example.com',
22:                 'role' => 'Backend Developer',
23:                 'status' => 'active',
24:             ],
25:             [
26:                 'name' => 'Charlie Brown',
27:                 'email' => 'charlie@example.com',
28:                 'role' => 'UI/UX Designer',
29:                 'status' => 'pending',
30:             ],
31:         ];
```
- **Lines 12–31:** Defines an array of initial user records (the same ones from your original app!).

```php
33:         foreach ($users as $userData) {
34:             User::firstOrCreate(
35:                 ['email' => $userData['email']],
36:                 $userData
37:             );
38:         }
39:     }
40: }
```
- **Lines 34–37 (`User::firstOrCreate(...)`):** Checks if a user with that email already exists in SQLite:
  - If found: leaves it alone.
  - If not found: inserts the record.
  This makes seeding idempotent (safe to run multiple times without creating duplicates!).

---

## File 6: `resources/views/layouts/app.blade.php`

**Location:** `resources/views/layouts/app.blade.php`  
**Purpose:** The Master Layout template. Contains the `<head>`, CSS links, top navigation bar, flash notification alerts, and footer. Child pages (`index.blade.php`, `guide.blade.php`) extend this layout.

### Code & Line-by-Line Explanation:

```blade
1: <!DOCTYPE html>
2: <html lang="en">
3: <head>
4:     <meta charset="UTF-8">
5:     <meta name="viewport" content="width=device-width, initial-scale=1.0">
6:     <title>@yield('title', 'Laravel Users CRUD & Eloquent Runner')</title>
7:     <link rel="stylesheet" href="{{ asset('css/app.css') }}">
8: </head>
```
- **Line 6 (`@yield('title', ...)`):** Blade placeholder. Child templates can specify their own custom page title using `@section('title', '...')`.
- **Line 7 (`{{ asset('css/app.css') }}`):** Generates the absolute URL to the CSS file located in `public/css/app.css`.

```blade
10: <body>
11:     <header class="navbar">
12:         <div class="navbar-container">
13:             <a href="{{ route('users.index') }}" class="brand">
14:                 <div class="brand-icon">L</div>
15:                 <div class="brand-text">
16:                     <h1>Laravel Users CRUD</h1>
17:                     <span>The Modern MVC & Eloquent Way</span>
18:                 </div>
19:             </a>
20: 
21:             <nav class="nav-links">
22:                 <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
23:                     👥 Users Management
24:                 </a>
25:                 <a href="{{ route('guide') }}" class="nav-link {{ request()->routeIs('guide') ? 'active' : '' }}">
26:                     📖 PDO vs Eloquent Guide
27:                 </a>
28:                 <span class="nav-badge">SQLite Database</span>
29:             </nav>
30:         </div>
31:     </header>
```
- **Lines 13, 22, 25 (`{{ route(...) }}`):** Generates clean URLs using named routes.
- **Line 22 (`request()->routeIs('users.index') ? 'active' : ''`):** Automatically adds the `.active` CSS class to the navigation link if that's the page you are currently viewing!

```blade
33:     <main class="main-content">
34:         @if (session('success'))
35:             <div class="alert alert-success">
36:                 <span>{{ session('success') }}</span>
37:             </div>
38:         @endif
39: 
40:         @if (session('error'))
41:             <div class="alert alert-error">
42:                 <strong>⚠ Notice:</strong> {{ session('error') }}
43:             </div>
44:         @endif
45: 
46:         @yield('content')
47:     </main>
```
- **Lines 34–38 (`session('success')`):** Checks if the controller flashed a success message (e.g. after creating or deleting a user) and renders a styled green alert badge.
- **Line 46 (`@yield('content')`):** **The core slot!** This is where child views inject their unique page content.

---

## File 7: `resources/views/users/index.blade.php`

**Location:** `resources/views/users/index.blade.php`  
**Purpose:** The main dashboard view containing the CRUD form, live Eloquent runner, and the live SQLite database table.

### Code & Key Sections Explained:

```blade
1: @extends('layouts.app')
2: 
3: @section('title', 'Users Management - Laravel Eloquent CRUD')
4: 
5: @section('content')
```
- **Line 1 (`@extends('layouts.app')`):** Tells Blade: "Wrap this content inside `resources/views/layouts/app.blade.php`".
- **Line 5 (`@section('content')`):** Begins the HTML block that injects into `@yield('content')`.

```blade
20:     <div class="card">
21:         <h2 class="card-title">
22:             {{ $editUser ? '✎ Edit User #' . $editUser->id . ' (Eloquent UPDATE)' : '+ Add New User (Eloquent CREATE)' }}
23:         </h2>
...
28:         <form method="POST" action="{{ $editUser ? route('users.update', $editUser) : route('users.store') }}">
29:             @csrf
30:             @if ($editUser)
31:                 @method('PUT')
32:             @endif
```
- **Line 22:** Dynamic title: If an edit user is selected, title says "Edit User #1", otherwise "Add New User".
- **Line 28 (`action="{{ ... }}"`):** 
  - If editing: posts to `route('users.update', $editUser)` (`/users/1`).
  - If adding: posts to `route('users.store')` (`/users`).
- **Line 29 (`@csrf`):** **CRITICAL SECURITY TOKEN!** Protects against Cross-Site Request Forgery attacks. Laravel automatically verifies this hidden token on every form submission.
- **Lines 30–32 (`@method('PUT')`):** HTML forms natively only support `GET` and `POST`. Laravel's `@method('PUT')` creates a hidden `_method` input telling the Laravel router to treat this request as an HTTP `PUT` request!

```blade
37:                     <input 
38:                         type="text" 
39:                         id="name" 
40:                         name="name" 
41:                         class="form-input" 
42:                         required 
43:                         value="{{ old('name', $editUser->name ?? '') }}" 
44:                         placeholder="e.g. Alice Smith"
45:                     >
46:                     @error('name')
47:                         <span class="input-error">{{ $message }}</span>
48:                     @enderror
```
- **Line 43 (`old('name', ...)`):** If validation fails, `old('name')` keeps whatever the user previously typed so they don't have to re-type it! If editing, it falls back to `$editUser->name`.
- **Lines 46–48 (`@error('name')`):** If Laravel's validator flagged this field as invalid, Blade renders this error span automatically with `$message`.

```blade
122:         <form method="POST" action="{{ route('users.run') }}">
123:             @csrf
124:             <textarea id="code_input" name="code" class="code-textarea">...</textarea>
125:             <button type="submit" class="btn btn-primary">▶ Run Eloquent Code</button>
```
- Submits the custom Eloquent snippet to `UserController@runSnippet`.

```blade
175:                 <tbody>
176:                     @forelse ($users as $user)
177:                         <tr>
178:                             <td><strong>#{{ $user->id }}</strong></td>
179:                             <td>{{ $user->name }}</td>
180:                             <td>{{ $user->email }}</td>
...
199:                                         <a href="{{ route('users.edit', $user) }}" class="btn-sm btn-edit">Edit</a>
200: 
201:                                         <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure?');">
202:                                             @csrf
203:                                             @method('DELETE')
204:                                             <button type="submit" class="btn-sm btn-delete">Delete</button>
205:                                         </form>
...
212:                     @empty
213:                         <tr><td colspan="7">No users found.</td></tr>
214:                     @endforelse
```
- **Line 176 (`@forelse ($users as $user)`):** Blade's loop construct. Loops over users; if the collection is empty, jumps straight to `@empty`.
- **Line 199:** Link to edit the user.
- **Lines 201–205:** A secure DELETE form with `@csrf` and `@method('DELETE')` triggering `UserController@destroy`.

---

## File 8: `resources/views/guide.blade.php`

**Location:** `resources/views/guide.blade.php`  
**Purpose:** Educational reference guide comparing Plain PHP / PDO queries against Laravel Eloquent side-by-side.

### Key Sections:
- **Architecture Overview**: Explains Model-View-Controller separation.
- **Side-by-side comparisons**:
  1. **SELECT**: `$stmt = $pdo->prepare(...)` vs `User::all()`
  2. **INSERT**: Manual SQL string with `:name, :email` vs `User::create($validated)`
  3. **UPDATE**: Manual SQL update vs `$user->update($validated)`
  4. **DELETE**: `$pdo->prepare('DELETE WHERE id = ?')` vs `$user->delete()`
  5. **TEMPLATING**: Mixed `<?php foreach(...): ?>` vs clean `@forelse` Blade directives.
- Uses `@verbatim ... @endverbatim` so Blade displays raw curly braces without compiling them.

---

## File 9: `public/css/app.css`

**Location:** `public/css/app.css`  
**Purpose:** Pure, vanilla CSS design system loaded directly by the browser (zero npm/vite compilation required).

### Key Highlights:
- **Custom CSS Variables (`:root`)**: Defines color palettes (`--primary: #6366f1`, `--success: #10b981`, `--danger: #ef4444`, slate neutrals).
- **Modern Typography**: Imports Google Font `Inter` for clean UI text and `Fira Code` for code blocks and snippets.
- **Interactive UI Components**: Responsive navbar, flexbox cards with hover shadows, custom status pill badges (`.badge-active`, `.badge-pending`, `.badge-inactive`), styled tables, and dark-theme code runner box.

---

## How Everything Interacts (Step-by-Step Flow Diagrams)

### Scenario A: User submits the "Add User" form
```
1. User types "David", "david@test.com", "DevOps" and clicks "Add User"
2. Browser sends POST /users with Form Data + _token (CSRF)
3. routes/web.php directs request to UserController@store
4. UserController@store executes:
   a. $request->validate([...])
   b. User::create($validated)  --->  Inserts row into SQLite database
5. UserController redirects to route('users.index') with session('success')
6. Browser requests GET /users
7. UserController@index runs User::orderBy('id', 'asc')->get()
8. users/index.blade.php compiles with the new user in the table and green alert
9. User sees their new user displayed in the table!
```

### Scenario B: User clicks "Delete" on user #2
```
1. User clicks "Delete" button (triggers JavaScript confirm dialog)
2. Browser sends POST /users/2 with _method=DELETE and _token
3. routes/web.php routes to UserController@destroy
4. Route Model Binding finds User where id = 2
5. $user->delete() executes SQLite DELETE
6. Controller redirects back to /users with "User deleted successfully"
7. Table renders without user #2
```

### Scenario C: User runs an Eloquent expression in the code runner
```
1. User selects "Where Active" and clicks "▶ Run Eloquent Code"
2. Browser sends POST /run-snippet with code="User::where('status', 'active')->get();"
3. routes/web.php routes to UserController@runSnippet
4. Controller evaluates snippet against SQLite database
5. Result is an Eloquent Collection of active users
6. View re-renders showing only active users in the live table, with a count banner!
```
