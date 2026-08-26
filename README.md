# Laravel Mail & Password Reset Demo

A practical Laravel project built to understand and experiment with **Email Sending, SMTP, Mailpit, Queues, Blade Email Templates, and Password Reset functionality**.

This project was created as a learning/demo project before implementing the same concepts in a real Laravel application.

---

## 📌 Project Overview

The main goal of this project is to understand how Laravel handles emails and password-reset workflows from start to finish.

The project demonstrates:

- Sending emails using Laravel Mail
- Configuring SMTP
- Using **Mailpit** for local email testing
- Creating email templates using **Blade**
- Using Laravel **Mailables**
- Sending emails through **Queues**
- Running a Queue Worker
- Generating password-reset tokens
- Creating password-reset URLs
- Sending password-reset emails
- Validating reset tokens
- Updating user passwords
- Understanding the complete Forgot Password flow

The complete password-reset flow implemented in this project is:

```text
User requests Forgot Password
        ↓
Find User
        ↓
Generate Reset Token
        ↓
Create Reset URL
        ↓
ForgetPassMail
        ↓
Queue
        ↓
Queue Worker
        ↓
Laravel Mail
        ↓
SMTP
        ↓
Mailpit
        ↓
User receives Email
        ↓
User clicks Reset Link
        ↓
Reset Password Page
        ↓
Password::reset()
        ↓
Password Updated
```

---

# 🚀 Features

## Email System

- Laravel Mail configuration
- SMTP integration
- Local email testing with Mailpit
- Blade-based email templates
- Custom Mailable classes

## Queue System

- Queueable Mailable classes
- Asynchronous email sending
- Queue Worker
- Database queue driver

## Password Reset

- Generate password reset tokens
- Generate reset URLs
- Send reset links through email
- Reset password using Laravel Password Broker
- Password confirmation
- Password hashing
- Token validation and expiration handling

---

# 🛠️ Tech Stack

- **PHP 8.2**
- **Laravel 12**
- **MySQL**
- **Blade**
- **Laravel Mail**
- **SMTP**
- **Mailpit**
- **Laravel Queue**
- **Laravel Password Broker**

---

# 📋 Requirements

Before running the project, make sure you have:

- PHP 8.2+
- Composer
- MySQL
- Laravel
- Mailpit
- Git

You can check your PHP version with:

```bash
php -v
```

And Composer:

```bash
composer -V
```

---

# 📥 Installation

Clone the repository:

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git
```

Move into the project directory:

```bash
cd YOUR_REPOSITORY
```

Install PHP dependencies:

```bash
composer install
```

---

# ⚙️ Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

On Windows PowerShell/CMD, you can also create `.env` manually from `.env.example`.

Generate the Laravel application key:

```bash
php artisan key:generate
```

---

# 🗄️ Database Configuration

Create a MySQL database.

For example:

```text
mail_test
```

Then configure your `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mail_test
DB_USERNAME=root
DB_PASSWORD=
```

Run the migrations:

```bash
php artisan migrate
```

The project uses Laravel's database tables required for:

- Users
- Password reset tokens
- Jobs / Queue

---

# 📧 Mail Configuration

This project uses **Mailpit** to test emails locally without sending real emails.

Configure the mail section in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

The important SMTP settings are:

```text
Host: 127.0.0.1
Port: 1025
```

Mailpit receives the emails locally.

---

# 📬 Mailpit

Mailpit provides a local SMTP server and a web interface for viewing emails.

After starting Mailpit, open:

```text
http://localhost:8025
```

The SMTP server normally listens on:

```text
127.0.0.1:1025
```

This allows the application to behave as if it is sending real emails without actually delivering them to real email addresses.

---

# 👤 Creating a Test User

For testing, a user can be created using Laravel Tinker.

Start Tinker:

```bash
php artisan tinker
```

Then:

```php
$user = \App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('12345678'),
]);
```

You can verify the user:

```php
$user;
```

Then exit Tinker:

```php
exit
```

---

# 🔐 Forgot Password Flow

The project demonstrates a complete password-reset flow.

## 1. Find the User

The application searches for the user using their email address.

```php
$user = User::where('email', $email)->first();
```

If the user doesn't exist, the process stops.

---

## 2. Generate Reset Token

Laravel generates the password-reset token:

```php
$token = Password::createToken($user);
```

---

## 3. Create Reset URL

The token and email are added to the reset URL:

```php
$resetUrl = url(
    "/reset-password?token={$token}&email={$user->email}"
);
```

The result looks similar to:

```text
/reset-password?token=XXXX&email=test@example.com
```

---

## 4. Send the Email

The password-reset email is sent through the queue:

```php
Mail::to($user->email)
    ->queue(new ForgetPassMail($resetUrl));
```

This means the request doesn't have to wait for the email to be sent.

---

# 📨 ForgetPassMail

The project contains a dedicated Mailable:

```text
app/Mail/ForgetPassMail.php
```

The Mailable receives the reset URL:

```php
public function __construct(
    public string $resetUrl
) {}
```

The email subject is:

```text
Reset Your Password
```

And the email uses a Blade view:

```php
return new Content(
    view: 'emails.forget-password',
);
```

---

# 🎨 Blade Email Template

The email template is located at:

```text
resources/views/emails/forget-password.blade.php
```

The reset URL is passed to the Blade template:

```blade
<a href="{{ $resetUrl }}">
    Reset Password
</a>
```

This allows the email content to remain separate from the PHP Mailable logic.

---

# ⚡ Queue System

The password-reset email is queued instead of being sent synchronously.

Run the Queue Worker:

```bash
php artisan queue:work
```

Keep this terminal running.

When the application executes:

```php
Mail::to($user->email)
    ->queue(new ForgetPassMail($resetUrl));
```

Laravel adds the email job to the queue.

The Queue Worker then processes that job.

---

# 🔄 Queue Architecture

The process is:

```text
HTTP Request
     ↓
Create Reset Token
     ↓
Create Reset URL
     ↓
Queue Mailable
     ↓
jobs table
     ↓
Queue Worker
     ↓
Laravel Mail
     ↓
SMTP
     ↓
Mailpit
```

---

# 🔑 Reset Password Page

After the user clicks the reset link, Laravel opens:

```text
/reset-password?token=XXXX&email=test@example.com
```

The reset page contains:

- Email
- Reset token
- New password
- Password confirmation

The token and email are submitted as hidden fields.

---

# 🔄 Reset Password

The password-reset request uses Laravel's Password Broker:

```php
$status = Password::reset(
    $request->only(
        'email',
        'password',
        'password_confirmation',
        'token'
    ),
    function ($user, $password) {
        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();
    }
);
```

Laravel validates the reset information.

If the reset is successful:

```text
Password reset successfully!
```

Otherwise:

```text
Invalid or expired reset token.
```

---

# 🧪 Testing the Project

## Step 1 — Start Laravel

```bash
php artisan serve
```

Laravel will normally be available at:

```text
http://127.0.0.1:8000
```

---

## Step 2 — Start Queue Worker

Open another terminal:

```bash
php artisan queue:work
```

Keep it running.

---

## Step 3 — Start Mailpit

Make sure Mailpit is running.

Open:

```text
http://localhost:8025
```

---

## Step 4 — Request Password Reset

Open:

```text
http://127.0.0.1:8000/forgot-password
```

The application will:

1. Find the test user
2. Generate a reset token
3. Generate a reset URL
4. Create `ForgetPassMail`
5. Add the email to the queue

---

## Step 5 — Check Mailpit

Open:

```text
http://localhost:8025
```

You should see:

```text
Reset Your Password
```

Open the email and click the reset link.

---

## Step 6 — Change Password

Enter a new password.

For example:

```text
newpassword123
```

Confirm the password.

If everything is correct, the application should return:

```text
Password reset successfully!
```

---

# 📁 Project Structure

The important parts of the project are:

```text
app/
├── Mail/
│   ├── TestMail.php
│   └── ForgetPassMail.php
│
├── Models/
│   └── User.php

resources/
└── views/
    ├── emails/
    │   ├── test-mail.blade.php
    │   └── forget-password.blade.php
    │
    └── reset-password.blade.php

routes/
└── web.php

database/
└── migrations/
```

---

# 📨 Test Email

Before implementing password reset, the project also demonstrates a basic email.

The test email uses:

```text
Laravel Mail
SMTP
Queue
Mailpit
Blade
```

The Blade template is:

```text
resources/views/emails/test-mail.blade.php
```

The corresponding Mailable is:

```text
app/Mail/TestMail.php
```

---

# 🧠 What This Project Teaches

This project helped demonstrate several important Laravel concepts.

### Laravel Mail

How Laravel creates and sends emails through Mailables.

### SMTP

How Laravel communicates with an SMTP server.

### Mailpit

How to safely test emails during development.

### Blade Email Templates

How email HTML can be separated into reusable Blade views.

### Queue

How long-running tasks such as email sending can be processed asynchronously.

### Queue Worker

How Laravel processes queued jobs.

### Password Broker

How Laravel generates and validates password-reset tokens.

### Password Hashing

How the new password is securely hashed before being stored.

---

# ⚠️ Important Note

This repository is primarily a **learning and experimental project**.

Some parts are intentionally simplified to make the Laravel Mail, Queue, SMTP, and Password Reset concepts easier to understand.

For a real production application, the Forgot Password functionality should be moved into a proper architecture such as:

```text
Controller
    ↓
Form Request
    ↓
Service / Password Broker
    ↓
Mailable
    ↓
Blade
    ↓
Queue
    ↓
SMTP Provider
```

Instead of putting the complete logic directly inside `routes/web.php`.

The original experiment intentionally keeps the flow simple so each individual component can be observed and understood.

---

# 🔒 Production Considerations

For a production application, consider:

- Using Controllers instead of route closures
- Using Form Requests for validation
- Using Laravel's official password-reset flow
- Using proper authentication middleware
- Using HTTPS
- Using a real SMTP provider
- Using environment variables for credentials
- Never committing `.env`
- Using proper queue workers in production
- Configuring failed jobs
- Adding rate limiting
- Avoiding exposing whether an email exists
- Using proper error handling
- Adding automated tests

---

# 🔐 Environment Variables

Never commit your real `.env` file.

The repository should contain:

```text
.env.example
```

but not:

```text
.env
```

Sensitive information such as:

```text
Database passwords
SMTP credentials
API keys
Application secrets
```

should always remain outside Git.

---

# 🧩 Useful Artisan Commands

### Start Laravel

```bash
php artisan serve
```

### Start Queue Worker

```bash
php artisan queue:work
```

### Run Migrations

```bash
php artisan migrate
```

### Reset Database

```bash
php artisan migrate:fresh
```

### Open Tinker

```bash
php artisan tinker
```

### Clear Configuration Cache

```bash
php artisan config:clear
```

### Clear Application Cache

```bash
php artisan cache:clear
```

---

# 📚 Learning Flow

The project was built progressively.

```text
Basic Email
     ↓
Blade Email Template
     ↓
Mailable
     ↓
SMTP
     ↓
Mailpit
     ↓
Queue
     ↓
Queue Worker
     ↓
Forgot Password
     ↓
Reset Token
     ↓
Reset URL
     ↓
Password Reset Page
     ↓
Password::reset()
```

This progression makes it easier to understand how the individual Laravel components work together.

---

# 🎯 Final Result

By the end of this project, the application can perform a complete local password-reset workflow:

```text
Forgot Password
      ↓
Reset Token
      ↓
Reset URL
      ↓
ForgetPassMail
      ↓
Queue
      ↓
Queue Worker
      ↓
SMTP
      ↓
Mailpit
      ↓
Reset Link
      ↓
Reset Password Form
      ↓
Password::reset()
      ↓
New Password
```

---

# 👨‍💻 Author

**Omar Magdy**

Computer Science & Artificial Intelligence Student

Backend Developer

---

# ⭐ Purpose of the Repository

This repository was created as a practical Laravel experiment to understand the internals and workflow of:

**Mail + SMTP + Mailpit + Queue + Blade + Password Reset**

The concepts learned here can later be transferred into larger production Laravel applications.