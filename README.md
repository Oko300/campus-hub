# CampusHub

CampusHub is a School News, Gist, and Past Question Web Portal designed for students to access verified news, submit campus gist, download past questions, and participate in a discussion forum. It also features a full admin dashboard for content management.

## Features

- User registration and login (Student and Admin roles)
- Homepage with statistics and latest content
- News module (CRUD for admin, view for students)
- Campus Gist module (students submit, admins approve/reject)
- Past Questions module (admin upload, students search and download with download counter)
- Discussion Forum (create threads and replies)
- Admin Dashboard with stats and management tools
- Basic search functionality

## Tech Stack

- PHP 8.3+
- PDO (PostgreSQL via Supabase)
- Bootstrap 5 + Font Awesome
- Clean and organized code structure

## Database (Supabase)

This project now uses **Supabase** (PostgreSQL) as the database.

### Environment Variables

Create a `.env` file in the root with the following:

```
DB_DRIVER=pgsql
DB_HOST=db.ovrvubkeipikltixgtfi.supabase.co
DB_PORT=5432
DB_NAME=postgres
DB_USER=postgres
DB_PASS=your_supabase_database_password
APP_ENV=production
APP_NAME=Bing Hub
APP_SECRET=change_this_to_a_strong_random_string_32_chars_or_more
SCHOOL_NAME=University of Excellence
SCHOOL_SHORT=UOE
```

> The tables are automatically created on first connection. You can also run the SQL in `database/schema.sql` manually in the Supabase SQL Editor if you prefer.

## Deployment

### Local Development with Docker

```bash
docker-compose up --build -d
```

Then open `http://localhost:8000`

### Production (e.g. Render)

Set the same environment variables above in your hosting provider dashboard.

## Installation (Local Development)

1. Clone the repository
2. Copy `.env` and fill in your Supabase credentials
3. Run with Docker Compose or a local PHP + Nginx setup
