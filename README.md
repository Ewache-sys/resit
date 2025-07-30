
Student-Facing Features
- **Programme Browsing**: Clean, intuitive interface to explore all available programmes
- **Advanced Search & Filtering**: Search by keywords, filter by level (Undergraduate/Postgraduate)
- **Detailed Programme Information**: Comprehensive programme details including:
  - Module breakdown by year
  - Staff information and expertise
  - Entry requirements
  - Career prospects
  - Programme leader details
- **Interest Registration**: Secure form to register interest in specific programmes
- **Mobile-Responsive Design**: Optimized for all devices
- **Accessibility Compliant**: WCAG2 guidelines followed with keyboard navigation support

Administrative Features
- **Secure Authentication**: Role-based access control with CSRF protection
- **Programme Management**: Full CRUD operations for programmes
- **Student Interest Tracking**: View and manage student registrations
- **Mailing List Generation**: Export interested students data
- **Publish/Unpublish Control**: Manage programme visibility
- **Comprehensive Dashboard**: Overview of system statistics and recent activity

Security Features
- **XSS Prevention**: All user input sanitized and validated
- **CSRF Protection**: Secure forms with token verification
- **Password Hashing**: Secure password storage using PHP's password_hash()
- **Session Security**: Secure session handling with timeouts
- **Activity Logging**: Track all administrative actions
- **SQL Injection Prevention**: Prepared statements throughout



### Database Schema
The system uses a MySQL database with the following core tables:
- Users - System administrators and staff
- UserRoles - Role-based access control
- Programmes - Academic programme information
- Modules - Course modules
- ProgrammeModules - Links programmes to modules by year
- Staff - Faculty and programme leaders
- Levels - Programme levels (Undergraduate, Postgraduate, PhD)
- InterestedStudents - Student interest registrations
- ActivityLog - System activity tracking

### File Structure
```
student-course-hub/
├── config/
│   ├── config.php          # Main configuration and helper functions
│   └── database.php        # Database connection class
├── classes/
│   ├── Programme.php       # Programme management logic
│   ├── StudentInterest.php # Student interest handling
│   ├── Staff.php          # Staff management
│   └── Level.php          # Programme levels
├── includes/
│   └── security.php       # Security and authentication
├── admin/
│   ├── login.php          # Admin login
│   ├── dashboard.php      # Admin dashboard
│   ├── programmes.php     # Programme management
│   ├── logout.php         # Secure logout
│   └── includes/          # Admin navigation components
├── assets/
│   └── css/
│       └── style.css      # Professional styling
├── uploads/               # File uploads directory
├── index.php             # Homepage with programme browsing
├── programme.php         # Individual programme details
├── register_interest.php # Interest registration handler
├── unsubscribe.php       # Email unsubscribe management
├── about.php             # University information
├── contact.php           # Contact information
└── database.sql          # Complete database schema
```

## Installation & Setup



### Installation Steps

1. **Database Setup**
   ```sql
   -- Import the database schema
   source student_course_hub.sql;
   ```

2. **Configuration**
   - Update database credentials in `config/database.php`
   - Modify site settings in `config/config.php`
   - Set up web server to point to the project root



3. **Default Admin Account**
   - Username: `admin`
   - Password: `admin123`
  


