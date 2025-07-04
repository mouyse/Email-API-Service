# Email API Service

A corePHP-based RESTful API for queuing and sending emails using multiple providers (SendGrid, Mailgun, etc.), built OOP, design patterns, and PSR-4 standards.

---

## Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Code Style](#code-style)
- [Project Structure](#project-structure)
- [Nginx Setup](#nginx-setup)
- [Cron Setup](#cron-setup)
- [Run PHPUnit Tests](#run-php-unit-tests)

---

## Features
- Email queueing and processing
- Multiple mailer support (SendGrid, Mailgun)
- Input validation and error handling
- PSR-4 autoloading and PSR-12 code style
- PHPUnit tests and code coverage
- Environment-based configuration

---

## Requirements
- PHP 8.1+
- Composer
- MySQL (or compatible DB)

---

## Installation
```bash
git clone https://github.com/mouyse/email-api-service.git
cd email-api-service
composer install
cp .env.example .env
# Edit .env with your DB and mailer credentials
```

---

## Configuration
Edit your `.env` file with the following variables:

```
DB_HOST=localhost
DB_NAME=email_db
DB_USER=root
DB_PASSWORD=secret
ESP1=SendGrid
ESP1_API_KEY=your_sendgrid_key
ESP1_DOMAIN=yourdomain.com
ESP2=Mailgun
ESP2_API_KEY=your_mailgun_key
ESP2_DOMAIN=yourdomain.com
MAX_RETRIES=3
BATCH_SIZE=100
```

---

## Usage
### Running the Service
```bash
php -S localhost:8000
```

### Sending an Email (Example)
```bash
curl -X POST http://localhost:8000/api/email \
  -H 'Content-Type: application/json' \
  -d '{
    "subject": "Hello World",
    "body": "This is a test email.",
    "to": "recipient@example.com",
    "from": "sender@example.com",
    "parameters": {},
    "status": "pending"
  }'
```

---

## API Endpoints
| Method | Endpoint         | Description         |
|--------|------------------|--------------------|
| POST   | `/api/email`     | Queue an email     |

#### Request Body Example
```json
{
  "subject": "Hello World",
  "body": "This is a test email.",
  "to": "recipient@example.com",
  "from": "sender@example.com",
  "parameters": {},
  "status": "pending"
}
```

#### Response Example
```json
{
  "message": "Email queued successfully"
}
```

---

## Testing
Run all tests:
```bash
composer test
```

Generate code coverage:
```bash
./vendor/bin/phpunit --coverage-html code-coverage-reports
```

---

## Code Style
- Follows [PSR-4](https://www.php-fig.org/psr/psr-4/) autoloading and [PSR-12](https://www.php-fig.org/psr/psr-12/) formatting.
- Use tools like PHP_CodeSniffer or PHP-CS-Fixer to check/fix code style.

---

## Project Structure
```
src/
  Config/           # Configuration singleton
  Controllers/      # MVC controllers
  CronProcessors/   # Cron job scripts
  Database/         # DB connection and factories
  Email/            # Mailer interfaces and implementations
  Factories/        # Factory classes
  Middleware/       # HTTP middleware
  Models/           # Data models
  Queues/           # Queue logic
  Repositories/     # Data repositories
  Validators/       # Input validation

tests/              # PHPUnit tests
```

---

## Nginx setup
```# Specific location for email-api-service
location /email-api-service/ {
    # Route all requests to index.php
    try_files $uri $uri/ /email-api-service/index.php?$query_string;
    error_log /opt/homebrew/var/log/nginx/email-api-service-error.log debug;
}
```

---

## Cron setup

```
* * * * * php /path/to/MailQueueProcessor.php --worker-id=1 >> log1.log 2>&1
* * * * * php /path/to/MailQueueProcessor.php --worker-id=2 >> log2.log 2>&1
```

### cURL Snippet

#### With Parameters:

```
curl --location 'localhost/email-api-service/api/email' \
--header 'Content-Type: application/json' \
--data-raw '{
    "subject": "Test subject",
    "to": "jayy.shah16@gmail.com",
    "from": "jayy.shah16@gmail.com",
    "body": "Hi, {{first_name}} {{last_name}}, How are you doing today?",
    "parameters": {
        "first_name": "Jay",
        "last_name": "Shah"
    }
}'
```

#### Without Parameters:

```
curl --location 'localhost/email-api-service/api/email' \
--header 'Content-Type: application/json' \
--data-raw '{
    "subject": "Test subject",
    "to": "jayy.shah16@gmail.com",
    "from": "jayy.shah16@gmail.com",
    "body": "Hi Test, How are you doing today?"
}'
```

---

## Run PHP Unit Tests
```
./vendor/bin/phpunit tests                        
```
