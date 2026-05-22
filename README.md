# MINSU Alumni Management System

A comprehensive alumni management system for Mindoro State University (MINSU). Built with vanilla HTML, CSS, JavaScript frontend and PHP backend.

## Features

### For Alumni

- **Profile Management** - Create and maintain your alumni profile
- **Alumni Directory** - Find and connect with fellow alumni
- **Messaging System** - Personal, group, section, program, and college-based messaging
- **Events** - View upcoming events, RSVP, and check-in for attendance
- **Announcements** - Stay updated with university and alumni association news
- **Gamification** - Earn points for participation and redeem rewards

### For Administrators

- **Alumni Management** - View, filter, and manage alumni records
- **Organization Structure** - Manage colleges, programs, and sections
- **Event Management** - Create events, track attendance, award points
- **Announcement System** - Post and manage announcements
- **Form Builder** - Dynamic forms for alumni data collection
- **Gamification Settings** - Configure points, badges, and rewards
- **Site Settings** - Theme, content, and email template customization

## Tech Stack

- **Frontend**: Vanilla HTML5, CSS3, JavaScript (SPA with hash-based routing)
- **Backend**: PHP 8.0+ with PDO
- **Database**: MySQL 8.0+
- **Authentication**: JWT tokens, Google OAuth support
- **Email**: PHPMailer for email verification and notifications

## Project Structure

```
Alumni_system/
├── client/                 # Frontend application
│   ├── assets/
│   │   ├── css/           # Stylesheets
│   │   ├── js/            # JavaScript modules
│   │   └── images/        # Static images
│   ├── pages/             # HTML page templates
│   │   ├── admin/         # Admin panel pages
│   │   ├── alumni/        # Alumni dashboard pages
│   │   └── auth/          # Authentication pages
│   └── index.php          # Main entry point
├── server/                 # Backend API
│   ├── api/               # API endpoints
│   ├── config/            # Database & environment config
│   ├── middleware/        # Auth & CORS middleware
│   ├── models/            # Database models
│   ├── utils/             # Utility functions
│   ├── uploads/           # User uploaded files
│   └── index.php          # API router
├── database/              # SQL schema and migrations
│   ├── migrations/        # Database migration files
│   └── schema.sql         # Main database schema
├── vendor/                # Composer dependencies (not in repo)
├── composer.json          # PHP dependencies
└── README.md              # This file
```

## Installation

### Prerequisites

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Composer (for PHP dependencies)

### Setup

1. **Clone the repository**

   ```bash
   git clone https://github.com/YOUR_USERNAME/alumni-system.git
   cd alumni-system
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Configure environment**

   ```bash
   cp server/.env.example server/.env
   # Edit server/.env with your database credentials
   ```

4. **Import database**

   ```bash
   # Import main schema
   mysql -u root -p alumni_system < database/schema.sql
   
   # Run migrations in order
   mysql -u root -p alumni_system < database/migrations/001_add_multi_campus_support_SAFE.sql
   mysql -u root -p alumni_system < database/migrations/002_verification_system.sql
   mysql -u root -p alumni_system < database/migrations/003_analytics_views.sql
   mysql -u root -p alumni_system < database/migrations/004_security_tables.sql
   mysql -u root -p alumni_system < database/migrations/005_messaging_enhancements.sql
   mysql -u root -p alumni_system < database/migrations/006_add_verification_email_templates.sql
   ```

5. **Start the servers (Development)**

   **Terminal 1 - Backend:**
   ```bash
   cd server
   php -S localhost:8000 index.php
   ```
   
   **Terminal 2 - Frontend:**
   ```bash
   cd client
   php -S localhost:3000
   ```

   **For Production Deployment:** See [DEPLOYMENT.md](DEPLOYMENT.md)

6. **Access the application**
   - Frontend: http://localhost:3000
   - Admin Login: http://localhost:3000/#/admin/login

### Default Credentials

- **Admin**: admin@minsu.edu.ph / password

## Configuration

### Environment Variables (server/.env)

```env
DB_HOST=localhost
DB_NAME=alumni_system
DB_USER=root
DB_PASS=your_password

JWT_SECRET=your-secret-key

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=your-email@gmail.com
MAIL_PASS=your-app-password
MAIL_FROM=noreply@minsu.edu.ph

GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
```

## API Endpoints

### Authentication

- `POST /api/v1/auth/register` - Register new alumni
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/verify-email` - Verify email
- `POST /api/v1/auth/forgot-password` - Request password reset
- `POST /api/v1/auth/reset-password` - Reset password

### Alumni

- `GET /api/v1/profile` - Get current user profile
- `PUT /api/v1/profile` - Update profile
- `GET /api/v1/alumni` - List alumni (with filters)
- `GET /api/v1/alumni/:id` - Get alumni details

### Events

- `GET /api/v1/events` - List events
- `GET /api/v1/events/:id` - Get event details
- `POST /api/v1/events/:id/rsvp` - RSVP to event
- `POST /api/v1/events/:id/check-in` - Check in to event

### Messages

- `GET /api/v1/messages/conversations` - List conversations
- `POST /api/v1/messages/conversations` - Create conversation
- `GET /api/v1/messages/conversations/:id/messages` - Get messages
- `POST /api/v1/messages/conversations/:id/messages` - Send message

### Admin Endpoints

- `GET /api/v1/admin/alumni` - Manage alumni
- `GET /api/v1/admin/dashboard/stats` - Dashboard statistics
- `POST /api/v1/events` - Create event
- `POST /api/v1/announcements` - Create announcement
- And more...

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

Developed for Mindoro State University (MINSU).

## Support

For support or questions, contact the IT department at admin@minsu.edu.ph.
