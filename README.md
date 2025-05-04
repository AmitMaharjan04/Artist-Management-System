# Artist Management System

## Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL database

## Installation Steps

1. Clone the repository
   ```
   git clone [repository-url]
   cd [project-directory]
   ```

2. Environment configuration
   ```
   cp .env.example .env
   php artisan key:generate
   ```
3. Install PHP dependencies with migration and seeders
   ```
    composer install
    php artisan migrate:fresh --seed
   ```

4. Install JavaScript dependencies and build assets
   ```
    npm install
    npm run build
   ```

5. Configure database and other services in `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=song_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password

   JWT_SECRET_KEY=your_secret_key # Or leave it empty as .env.example has default value
   ```

6. Start the development server
   ```
   php artisan serve
   ```
