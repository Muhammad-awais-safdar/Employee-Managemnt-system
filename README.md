# Employee Management System

A Laravel 12-based employee management platform built for multi-role HR, finance, and company administration. The app provides attendance, leave, payroll, expense, tax, and user management for SuperAdmin, Admin, HR, Finance, TeamLead, and Employee roles.

## Product Summary

This project is a complete employee management dashboard with role-based access and company-level scoping.

Key capabilities:
- Multi-company admin hierarchy with SuperAdmin and Company Admin.
- Department and user management with role-based permissions.
- Attendance tracking with check-in, check-out, break tracking, reporting, and export.
- Leave request workflow with approval, balance tracking, calendar view, and bulk actions.
- Payroll and salary management with employee salary updates, payroll processing, and export.
- Expense approval and reimbursement workflows.
- Tax configuration including tax rates, brackets, deductions, and employee tax info.
- Working hours settings per company.
- Salary increment request submission and review.
- Profile management and role-specific dashboards.

## Features

### Authentication & roles
- Login/logout flow using custom `AuthController`
- Role-based middleware for `superAdmin`, `Admin`, `HR`, `Finance`, `TeamLead`, and `Employee`
- Access control powered by `spatie/laravel-permission`

### Administration
- SuperAdmin: company management, global user management, attendance reports, working hours, leave types
- Admin: company settings, department management, user management, attendance, leave approvals, working hours, leave types
- HR: team user management, attendance monitoring, leave review, leave calendar, increment requests
- Finance: salary management, payroll processing, expense approval, financial reports, tax settings
- TeamLead: limited team user viewing and dashboard access
- Employee: personal dashboard, attendance actions, leave requests, leave calendar, salary history

### Attendance
- Check-in and check-out actions
- Break start/end
- Attendance statistics and export
- Employee attendance lists and reports

### Leave Management
- Request leave creation, editing, and cancellation
- Leave balance display and calendar view
- Manager/HR approval, rejection, bulk approval, and bulk reject
- Leave type management with active/inactive toggles
- Leave export and employee-specific reports

### Finance & Payroll
- Salary tables and updates
- Payroll processing and export
- Expense approval, rejection, reimbursement, and export
- Salary history for employees and company-wide export
- Salary increment requests and review workflows

### Tax & Payroll Configuration
- Tax rates, brackets, and deductions management
- Employee tax information tracking
- Tax calculation and default setup

### Platform Utilities
- Profile editing and password update
- Company-scoped permissions and security filtering
- Dashboard pages for each role
- Responsive admin UI with sidebar, topbar, dark mode, and search

## Tech Stack

- PHP 8.2
- Laravel 12
- MySQL 8.1
- Redis
- Docker Compose
- Vite
- Tailwind CSS 4
- Bootstrap 5
- Laravel Vite Plugin
- Axios
- FontAwesome icons
- Tabler icons
- C3 charts
- Spatie Laravel Permission
- Pest for PHP testing

## UI & Theme

The frontend uses a Bootstrap-based admin dashboard theme with these design tokens:

- Primary brand: `#3E007C` (dark purple)
- Accent / action: `#FAB33D` (gold)
- Neutral backgrounds: `#FFFFFF`, `#F8F9FA`, `#E8E8E9`
- Dark mode base: `#06081F`, `#03041A`
- Sidebar hover / active: `rgba(250, 179, 61, 0.1)`, `#2a2c33`
- Text: `#303335`, `#828997`, `#D9DCFF`

The layout includes:
- Topbar with search, language switcher, fullscreen toggle, dark mode button, and settings
- Collapsible sidebar navigation
- Role-specific dashboard pages
- Responsive card/grid styling and admin tables

## Docker Development Setup

### Prerequisites
- Docker
- Docker Compose

### Start locally

1. Copy environment variables:
   ```bash
   cp .env.docker .env
   ```
2. Build and start containers:
   ```bash
   docker compose up -d --build
   ```
3. Install dependencies:
   ```bash
   docker compose exec laravel.test composer install
   docker compose exec laravel.test npm install
   ```
4. Build frontend assets:
   ```bash
   docker compose exec laravel.test npm run build
   ```
5. Generate the application key and run migrations:
   ```bash
   docker compose exec laravel.test php artisan key:generate
   docker compose exec laravel.test php artisan migrate --force
   ```

### Access the app
- Application: `http://localhost:8080`
- Vite dev server: `http://localhost:5173`

### Development workflow
- Normal code edits are reflected immediately because the project directory is mounted into the container.
- Start existing containers without rebuilding:
  ```bash
  docker compose up -d
  ```
- When you change PHP or Blade views, refresh the browser.
- When you change composer or npm dependencies:
  ```bash
  docker compose exec laravel.test composer install
  docker compose exec laravel.test npm install
  ```
- Rebuild only when Docker configuration changes:
  - `Dockerfile`
  - `docker-compose.yml`
  - `.dockerignore`

### Docker permissions
If Docker requires `sudo`, add your user to the `docker` group and restart your session:
```bash
sudo usermod -aG docker $USER
newgrp docker
```

## Testing

Run tests locally inside the container:
```bash
docker compose exec laravel.test php artisan test
```

## Project Structure

- `app/` — application controllers, models, policies, services
- `config/` — Laravel config files
- `database/` — migrations, factories, seeders
- `resources/views/` — Blade views and layouts
- `resources/css/` — Tailwind + Vite CSS entrypoint
- `routes/web.php` — all app routes and role-based route groups
- `Dockerfile` — PHP Apache application image
- `docker-compose.yml` — app, MySQL, Redis stack

## Notes

- The app uses custom role-based route groups for SuperAdmin, Admin, HR, Finance, TeamLead, and Employee.
- Session and cache are configured to use database storage in `.env.docker`.
- The UI is based on a Bootstrap admin template with Tailwind support via Vite.
