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
- PDO (Postgres compatible)
- Bootstrap 5 + Font Awesome
- Clean and organized code structure

## Deployment

This application is designed for deployment on Render using Docker.

### Environment Variables

The application relies on environment variables for sensitive data, especially database credentials. A `.env` file is used for local development, but these variables should be configured directly on Render for production.

Example `.env` file:

```
DB_HOST=your_postgres_host
DB_NAME=your_postgres_database_name
DB_USER=your_postgres_username
DB_PASS=your_postgres_password
DB_PORT=5432
APP_ENV=production
APP_URL=https://your-render-app-url.onrender.com
```

### Docker Deployment on Render

1.  **Build the Docker Image**: Render will automatically build the Docker image based on the `Dockerfile` in the root of the repository.
2.  **Configure Environment Variables**: Set the environment variables (as listed above) in your Render service settings.
3.  **Database Connection**: Ensure your Render Postgres database is correctly linked and accessible using the provided environment variables.

## Installation (Local Development)

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/oko300/campus-hub.git
    cd campus-hub
    ```
2.  **Create `.env` file**: Copy `.env.example` to `.env` and update with your local PostgreSQL credentials.
    ```bash
    cp .env.example .env
    ```
3.  **Build and run with Docker Compose (recommended)**:
    (Note: A `docker-compose.yml` file will be provided later for easier local setup.)
    ```bash
    docker-compose up --build -d
    ```
4.  **Access the application**: Open your browser and navigate to `http://localhost:8000` (or the port configured in `docker-compose.yml`).

## Contributing

Feel free to contribute to the development of CampusHub.