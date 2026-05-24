# Gym System Management

A full-featured gym management system built with Laravel, featuring role-based access control for admins and members.

## Tech Stack

- **Backend:** Laravel 13, PHP 8.4
- **Database:** SQLite
- **Frontend:** Blade, Tailwind CSS
- **Mail:** Mailtrap (SMTP) with Laravel Queues

## Features

### Admin
- Full CRUD for members and membership plans
- When creating a member, the system automatically creates a user account and sends login credentials via email
- View and manage plan requests from members (approve / reject / pending)
- Members are notified by email when their request status is updated
- Filter plan requests by status on the dashboard

### Member
- Members can self-register and log in
- When a member self-registers, a user account and a linked member record are automatically created in the database, connected via a foreign key relationship
- Members without a plan can browse available plans and submit a request
- Once a request is pending, the member sees a waiting message and cannot submit duplicate requests
- Once approved, the member is redirected to their portal showing plan details, start date, and end date

## Installation

```bash
git clone https://github.com/youssraalali/gym-system-laravel.git
cd gym-system-laravel
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Configure your mail settings in `.env`:

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password

## Default Credentials

| Role  | Email             | Password |
|-------|-------------------|----------|
| Admin | admin@gmail.com   | password |

## Queue

To process email notifications, run the queue worker:
```bash
php artisan queue:work
```

## Portfolio

[youssraelali.42web.io](https://youssraelali.42web.io)
