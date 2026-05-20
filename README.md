# Omakase AI - Backend API

This is the backend API for Omakase AI, built with **Laravel 13** and **Google Gemini AI**.

## Requirements
- PHP 8.3+
- PostgreSQL
- Composer
- Node.js & npm (for potential frontend/build tools)

## Setup Instructions

1. **Clone the repository** (if applicable) and navigate to the directory:
   ```bash
   cd omakase-api
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Environment Setup:**
   Copy `.env.example` to `.env` and fill in your database credentials and API keys.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **JWT Secret:**
   Generate the secret key for JWT authentication:
   ```bash
   php artisan jwt:secret
   ```

5. **Database Setup (PostgreSQL):**
   Make sure you have created a database named `omakase_ai` in PostgreSQL. Then run migrations and seeders:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Storage Link:**
   Create a symbolic link for public file storage (avatars, logos):
   ```bash
   php artisan storage:link
   ```

7. **Run the Queue Worker (for AI Generation):**
   AI generation tasks are dispatched to the background. You must run the queue worker:
   ```bash
   php artisan queue:work
   ```

8. **Start the Development Server:**
   ```bash
   php artisan serve
   ```

## Architecture

This project uses a **Modular Monolith** architecture inspired by Domain-Driven Design (DDD):
- **Modules (`app/Modules/`)**: Self-contained domains (Auth, Generation, BrandKit, etc.) containing their own Controllers, Services, Repositories, DTOs, and Resources.
- **Shared (`app/Shared/`)**: Common logic like Interfaces, Exceptions, Traits, and global Middleware.
- **Service Layer**: Business logic lives in services, keeping controllers thin.
- **Repository Pattern**: Database queries are abstracted behind repositories.
- **AI Integration**: Uses the official `laravel/ai` SDK, configured with Google Gemini (`gemini-2.0-flash-exp`).

## API Endpoints

All endpoints are prefixed with `/v1`. See `routes/api.php` for the complete list.
