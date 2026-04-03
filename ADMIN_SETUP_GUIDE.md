# Admin and System Admin Setup Guide

## Overview

This guide covers setting up and using the admin functionality for the MINSU Alumni Management System.

## Features Implemented

### 🔐 Admin Features

- **Separate Authentication**: Admins login via `/admin/login` (separate from alumni login)
- **Dashboard**: Analytics with total alumni, new registrations, event attendance stats
- **Alumni Management**:
  - View all alumni in a searchable, filterable table
  - Filter by college, program, batch year, status
  - View full profile details for any alumni
  - Update alumni status and manage accounts
  - Export alumni data to CSV
- **Event Management**: Create and manage events, track attendance, award points
- **Announcement System**: Post and manage announcements with targeting options

### 👑 System Admin Features

- **User Management**: Add, edit, remove admins and staff members
- **Theme Customization**: Modify website colors, fonts, logo
- **Content Management**: Edit homepage content, social links, footer
- **Email Templates**: Customize email notifications
- **Organization Management**: Manage colleges, programs, and sections

## Database Tables

### Main Tables:

1. **users** - All user accounts (alumni and admins)
2. **alumni_profiles** - Extended alumni profile information
3. **colleges, programs, sections** - Organization hierarchy
4. **events, event_registrations** - Event management
5. **announcements** - News and updates
6. **messages, conversations** - Messaging system
7. **gamification tables** - Points, badges, rewards

## Setup Instructions

### 1. Import Database

```bash
mysql -u root -p < database/schema.sql
```

This will:
- Create the `alumni_system` database
- Create all required tables
- Insert sample colleges, programs, sections
- Create the default admin account

### 2. Configure Backend

Edit `server/.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=alumni_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Start Servers

**Terminal 1 - Backend:**
```bash
php -S localhost:8000 -t server server/index.php
```

**Terminal 2 - Frontend:**
```bash
php -S localhost:3000 -t client
```

## Default Admin Credentials

After importing the database:

- **Email**: admin@minsu.edu.ph
- **Password**: password

⚠️ **Change this password immediately after first login!**

## Testing the Admin System

### 1. Admin Login

- Navigate to: `http://localhost:3000/#/admin/login`
- Login with: `admin@minsu.edu.ph` / `password`
- You should see the admin dashboard

### 2. Dashboard

The dashboard shows:
- Total alumni count
- New registrations (this month)
- Total events
- Total points distributed

### 3. Alumni Management

- View list of all registered alumni
- Filter by college, program, batch year
- Search by name, email, or alumni ID
- Click any row to view full details
- Export data to CSV

### 4. Event Management

- Create new events with details
- Set venue type (physical/online/hybrid)
- Configure point rewards
- Track RSVPs and attendance
- Check in attendees using alumni ID

### 5. Organization Management

- Add/edit/delete colleges
- Manage programs under colleges
- Manage sections under programs

## API Endpoints for Admins

### Dashboard
- `GET /api/v1/admin/dashboard/stats` - Get dashboard statistics

### Alumni
- `GET /api/v1/admin/alumni` - List all alumni (with filters)
- `GET /api/v1/admin/alumni/:id` - Get alumni details
- `PUT /api/v1/admin/alumni/:id` - Update alumni
- `DELETE /api/v1/admin/alumni/:id` - Delete alumni

### Users
- `GET /api/v1/admin/users` - List all users
- `POST /api/v1/admin/users` - Create new admin
- `PUT /api/v1/admin/users/:id` - Update user
- `DELETE /api/v1/admin/users/:id` - Delete user

### Events
- `POST /api/v1/events` - Create event
- `PUT /api/v1/events/:id` - Update event
- `DELETE /api/v1/events/:id` - Delete event
- `GET /api/v1/events/:id/attendance` - Get attendance
- `POST /api/v1/events/:id/check-in` - Check in attendee

### Announcements
- `POST /api/v1/announcements` - Create announcement
- `PUT /api/v1/announcements/:id` - Update announcement
- `DELETE /api/v1/announcements/:id` - Delete announcement

## Troubleshooting

### Can't Login as Admin

1. Verify database was imported: Check if `users` table has the admin entry
2. Check password hash: The hash in schema.sql is for "password"
3. Verify JWT secret is set in `server/.env`

### API Returns 401 Unauthorized

1. Check if token is present in request headers
2. Verify token hasn't expired (default: 30 days)
3. Check user role is 'admin' or 'system_admin'

### CORS Errors

Ensure backend server is running on port 8000 and frontend is using correct API URL in `client/assets/js/api.js`.

### System Admin - Site Content

- `GET /api/v1/system-admin/content` - Get site content (with section filter)
- `POST /api/v1/system-admin/content` - Create/update site content
- `DELETE /api/v1/system-admin/content/:id` - Delete site content

### System Admin - Activity Logs

- `GET /api/v1/system-admin/activity-logs` - Get admin activity logs

## Default Credentials

### System Administrator

- **Email**: systemadmin@minsu.edu.ph
- **Password**: admin123
- **Access**: Full system access including staff management, theme, and content

### Administrator

- **Email**: admin@minsu.edu.ph
- **Password**: admin123
- **Access**: View applications, dashboard analytics, print forms

**⚠️ IMPORTANT**: Change these default passwords in production!

## Security Features

1. **Separate Authentication**: Admins and alumni use different login endpoints and tokens
2. **Role-Based Access Control**: System admin vs regular admin permissions
3. **JWT Tokens**: Secure token-based authentication
4. **Password Hashing**: bcrypt with 10 salt rounds
5. **Activity Logging**: All admin actions are logged

## Next Steps

1. **Customize Theme**: Login as admin and update colors/branding
2. **Configure Organization**: Set up Colleges, Programs, and Sections
3. **Create Events**: Start creating events for alumni to attend
4. **Configure Form Fields**: Customize the alumni profile form fields
5. **Production Deployment**:
   - Change default passwords
   - Set up proper environment variables
   - Configure production database

## Support

For issues, check:
1. PHP error logs in the terminal running the server
2. Browser console for frontend errors
3. Network tab for API request/response details
