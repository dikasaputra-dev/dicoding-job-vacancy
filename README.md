# Dicoding Job Vacancy

Full-stack technical assessment for creating, managing, listing, searching,
and viewing job vacancies.

The repository contains a Laravel REST API and a Next.js web application.

## Features

### Job seeker

- View active job vacancies.
- Search vacancies by title.
- View vacancy details.
- See loading, error, and empty states.

### Recruiter

- View vacancies on the recruiter dashboard.
- Create a vacancy.
- Update a vacancy.
- Delete a vacancy.

## Tech stack

### Backend

- PHP 8.3+
- Laravel 13
- MySQL
- PHPUnit

### Frontend

- Next.js 16
- React 19
- TypeScript
- Tailwind CSS 4
- TanStack Query
- Axios
- Playwright

## Project structure

```text
dicoding-job-vacancy/
├── backend/     # Laravel REST API, migrations, seeders, and tests
├── frontend/    # Next.js application and Playwright E2E tests
└── README.md
```

## Prerequisites

Install the following tools:

- PHP 8.3 or newer with the extensions required by Laravel and MySQL.
- Composer 2.
- MySQL 8 or another compatible MySQL version.
- Node.js 20.9 or newer.
- npm.

## Database setup

Create separate development and testing databases:

```sql
CREATE DATABASE dicoding_jobs
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE DATABASE dicoding_jobs_testing
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

The default configuration expects the MySQL user `root` without a password.
Adjust `DB_USERNAME` and `DB_PASSWORD` in `backend/.env` when your local
configuration is different.

## Backend setup

Open a terminal from the repository root:

```bash
cd backend
composer install
```

Create the environment file.

macOS/Linux:

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate the application key, run migrations, and seed sample vacancies:

```bash
php artisan key:generate
php artisan migrate --seed
```

Start the Laravel API:

```bash
php artisan serve
```

The API is available at `http://127.0.0.1:8000/api/v1`.

## Frontend setup

Open another terminal from the repository root:

```bash
cd frontend
npm ci
```

Create the environment file.

macOS/Linux:

```bash
cp .env.example .env.local
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env.local
```

The default frontend configuration points to:

```env
NEXT_PUBLIC_API_BASE_URL=http://127.0.0.1:8000/api/v1
```

Start Next.js:

```bash
npm run dev
```

Open `http://localhost:3000`.

## API endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/v1/vacancies` | List active vacancies |
| `GET` | `/api/v1/vacancies?search=engineer` | Search by title |
| `GET` | `/api/v1/vacancies/{id}` | View vacancy details |
| `POST` | `/api/v1/vacancies` | Create a vacancy |
| `PUT/PATCH` | `/api/v1/vacancies/{id}` | Update a vacancy |
| `DELETE` | `/api/v1/vacancies/{id}` | Delete a vacancy |

The list endpoint also supports:

- `status=active|expired|all`
- `page={number}`
- `per_page={number}`

## Running backend tests

The PHPUnit configuration uses `dicoding_jobs_testing`. Ensure that database
exists before running the tests.

Run all backend tests:

```bash
cd backend
php artisan test
```

Run unit tests only:

```bash
php artisan test --testsuite=Unit
```

Run integration/feature tests only:

```bash
php artisan test --testsuite=Feature
```

The feature tests use Laravel database refresh mechanisms and must never point
to the development or production database.

## Running frontend checks

```bash
cd frontend
npm run lint
npm run typecheck
npm run build
```

## Running Playwright E2E tests

Install the Chromium browser once:

```bash
cd frontend
npx playwright install chromium
```

Run all E2E tests:

```bash
npm run test:e2e
```

Run Playwright in UI mode:

```bash
npm run test:e2e:ui
```

The E2E suite builds and starts the Next.js application automatically. It
intercepts API requests with deterministic data, so it does not modify the
local MySQL database.

Covered scenarios:

- A job seeker opens the vacancies list.
- A job seeker searches by title.
- A job seeker sees the empty search state.
- A job seeker opens vacancy details.
- A recruiter creates a vacancy and sees it on the dashboard.
- A recruiter updates a vacancy.
- A recruiter deletes a vacancy.

## Production build

```bash
cd frontend
npm run build
npm run start
```

## Submission repository

Repository:
[github.com/dikasaputra-dev/dicoding-job-vacancy](https://github.com/dikasaputra-dev/dicoding-job-vacancy)
