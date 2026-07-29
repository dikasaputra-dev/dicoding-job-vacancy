# Dicoding Job Vacancy API

Laravel REST API for the Dicoding Job Vacancy technical assessment.

Complete full-stack setup and test instructions are available in the
[repository README](../README.md).

## Quick start

```bash
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

Configure the MySQL connection in `.env`, then run:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

The API is available at `http://127.0.0.1:8000/api/v1`.

## Tests

The default PHPUnit configuration uses a separate MySQL database named
`dicoding_jobs_testing`.

```bash
php artisan test
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

Do not configure the testing environment to use a development or production
database because feature tests refresh database state.

## Main endpoints

| Method | Endpoint |
|---|---|
| `GET` | `/api/v1/vacancies` |
| `GET` | `/api/v1/vacancies/{id}` |
| `POST` | `/api/v1/vacancies` |
| `PUT/PATCH` | `/api/v1/vacancies/{id}` |
| `DELETE` | `/api/v1/vacancies/{id}` |
