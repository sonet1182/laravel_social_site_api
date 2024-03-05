# Laravel Project

Welcome to the Laravel project! This repository contains a Laravel application with database seeds to help you get started.

## Getting Started

### Prerequisites

Make sure you have the following installed on your machine:

- [PHP](https://www.php.net/manual/en/install.php)
- [Composer](https://getcomposer.org/download/)
- [Node.js](https://nodejs.org/)
- [NPM](https://www.npmjs.com/)

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/sonet1182/laravel_social_site_api.git

2. Change into the project directory:

   ```bash
   cd laravel-project

3. Install dependencies:

   ```bash
   composer install
   npm install

4. Copy the .env.example file to .env:
   ```bash
   cp .env.example .env

5. Generate the application key:
   ```bash
   php artisan key:generate

6. Update the database configuration in the .env file with your database credentials.

7. Migrate the database:
    ```bash
   php artisan migrate

8. To seed the database with sample data, run the following command:
     ```bash
   php artisan db:seed

9. Run the following command to start the development server:
   ```bash
   php artisan serve
