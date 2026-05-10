==>Task & File Management System (Laravel 12 + AJAX)

A modern, high-performance web application built with Laravel 12. This system allows users to manage their personal tasks and handle multiple file uploads through a seamless, "no-refresh" interface powered by jQuery AJAX.

==> Features

1. Authentication (AJAX)
Secure Access: Registration and Login forms using asynchronous requests.
JSON Responses: Custom logic in AuthController for real-time validation and redirects.
Security: Integrated CSRF protection and secure password hashing.

2. Task Management (CRUD)

Private Workspace: Strict user-level data isolation. Users can only view and manage tasks they created.
Fields: Includes Title, Description, Status (Pending/Completed), and Due Date.
Advanced UI: Integrated Searching, Status Filtering, and Pagination for a professional user experience.

3. Multiple File Uploads

Asynchronous Uploads: Handle bulk uploads using jQuery AJAX and FormData.
File Previews: Direct in-browser previews for Images and PDF documents.
Storage: Managed via Laravel’s Storage facade for security and scalability.

==> Tech Stack

Backend: PHP 8.2+ & Laravel 12.x
Frontend: Blade Templates, Bootstrap 5, and jQuery
Database: MySQL

Protocol: AJAX (via jQuery) for all form submissions

==>Installation & Setup

Follow these steps to get the project running locally:

1. Clone the Repository
Bash
git clone https://github.com/Soni63897654/task-manager.git
cd your-repo-name
2. Install Dependencies
Bash
composer install
3. Environment Configuration
Create your environment file and generate the application key:
Bash
cp .env.example .env
php artisan key:generate
Note: Update the .env file with your database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

4. Database Setup
Run the migrations to build your tables:

Bash
php artisan migrate

5. Create Storage Link
Crucial for displaying uploaded images and PDFs:
==>Bash

php artisan storage:link
==> **Important: Storage Link**
   If images are not showing in the Media Library, it's because the storage shortcut is missing or broken. Run the following commands:

   **For Windows (CMD as Administrator):**```bash
   # Delete the broken link if it exists
   del /s /q public\storage
   rmdir /s /q public\storage

   # Create a new link
   php artisan storage:link

6. Start the Server
Bash
php artisan serve
Visit the app at: http://127.0.0.1:8000

==>Project Architecture
Controllers:

AuthController.php: Handles login, registration, and logout logic.
TaskController.php: Manages CRUD operations, search, and filtering.
FileController.php: Logic for multi-file uploads and preview generation.

Middleware:
All task and file routes are protected by the auth middleware.

Routes:
Uses the simplified Laravel 12 routing structure in routes/web.php.

==> Security Features
CSRF Protection: All AJAX requests automatically include the X-CSRF-TOKEN header.

Input Validation: Server-side validation using Laravel's Validator to prevent invalid data or malicious scripts.

Data Privacy: Query-level security ensures users cannot access other users' tasks or files.

==>How to Push to GitHub

# Initialize and Add
git init
git add .
git commit -m "Final Submission: Laravel 12 Task & File Manager with AJAX"
# Branch and Remote (Using your URL)
git branch -M main
git remote add origin https://github.com/Soni63897654/task-manager.git
# Final Push
git push -u origin main