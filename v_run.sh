#!/bin/bash


# Chạy vite cho Laravel
npm run dev

# Chạy server Laravel trên cổng 8080 để tránh xung đột với XAMPP (mặc định là 80)
php artisan serve --port=8080
# php artisan serve


#localhost:8080
http://localhost:8080