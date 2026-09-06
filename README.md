# PDO CRUDs

A lightweight, zero-configuration PHP PDO CRUD web application and code guide powered by SQLite.

## Quick Start

1. Clone the repository:
```bash
git clone https://github.com/Siraj-T/pdo_cruds.git
cd pdo_cruds
```

2. Start the built-in PHP development server:
```bash
php -S localhost:8000
```

3. Open your browser:
```
http://localhost:8000
```

The SQLite database and `users` table are created and seeded automatically on first launch. No external database servers or drivers required.

## Files

- `index.php`: Interactive web application with live CRUD UI and a real-time PHP PDO code runner.
- `db.php`: SQLite PDO database connection and automatic table initialization.
- `CRUD.php`: Standalone script demonstrating Create, Read, Update, and Delete in pure PHP.
- `examples.php`: Visual reference guide explaining PDO query structure.
- `style.css`: Clean, modern styling with CSS Grid and Flexbox.

## Tech Stack

- Backend: PHP 8+ with native PDO SQLite (`pdo_sqlite`)
- Frontend: Vanilla HTML5, CSS3, JavaScript (0 dependencies)
