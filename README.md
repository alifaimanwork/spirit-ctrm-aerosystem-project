# Digital Vision Inspection Project

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![Vite](https://img.shields.io/badge/Vite-B73BFE?style=for-the-badge&logo=vite&logoColor=FFD62E)](https://vitejs.dev/)

## 🚀 About the Project

Digital Vision Inspection (DVI) is a modern web application built with Laravel, designed to provide detailed analysis of aerosystem parts that undergoes part inspection automatically in tandem with another system that involves robot arms attached with camera and a deep learning app that learn from captured image of the parts to see the data that is needed for the inspection.

## ✨ Features

- **Modern Stack**: Built with Laravel, Tailwind CSS, and Vite
- **Responsive Design**: Works on all devices
- **Authentication**: User registration and login system
- **API Ready**: RESTful API endpoints for integration
- **Testing**: PHPUnit tests included

## 🛠️ Prerequisites

- PHP >= 8.1
- Composer
- Node.js >= 14.x
- NPM or Yarn
- MySQL/PostgreSQL/SQLite

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone [your-repository-url]
   cd sprt
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   - Create a database and update `.env` with your database credentials
   - Run migrations and seeders:
     ```bash
     php artisan migrate --seed
     ```

6. **Build assets**
   ```bash
   npm run build
   # or for development: npm run dev
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

   Visit `http://localhost:8000` in your browser.

## 🧪 Testing

Run the tests with:

```bash
php artisan test
```

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
