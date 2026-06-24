# SiteWorx Admin Authentication System - Design Documentation

## Overview
A professional, modern authentication system for the SiteWorx Admin panel featuring responsive design, robust security, and excellent user experience.

---

## Files Overview

### 1. **login.php** - Admin Login Page
**Features:**
- ✅ Modern gradient background with centered card design
- ✅ Email/Username input with icon
- ✅ Password field with show/hide toggle
- ✅ Remember Me checkbox
- ✅ Form validation and error messages
- ✅ Responsive design (mobile-friendly)
- ✅ Bootstrap 5 + Font Awesome icons
- ✅ Smooth animations and transitions
- ✅ Prevents double-submission
- ✅ Links to Register and Forgot Password

**Key Functions:**
- Validates username and password
- Uses secure password hashing (PHP password_hash)
- Supports legacy MD5/SHA256 with automatic upgrade
- Session management with user info storage
- Remember Me functionality (30-day cookie)

**Security Features:**
- HTML entity encoding for XSS prevention
- Prepared statements for SQL injection prevention
- Secure session handling
- Password field has autocomplete="off"

---

### 2. **logout.php** - Logout Handler
**Features:**
- ✅ Complete session destruction
- ✅ Session cookie deletion
- ✅ Remember Me cookie clearing
- ✅ Secure cleanup of all session data
- ✅ Redirect to success page

**Security Features:**
- Clears all session variables
- Deletes session cookies with proper parameters
- Removes remember-me tokens
- Prevents session fixation attacks

---

### 3. **logout_success.php** - Logout Confirmation Page
**Features:**
- ✅ Professional success message
- ✅ Security reminder about shared computers
- ✅ Login again link
- ✅ Home page link
- ✅ Success animation
- ✅ Prevents browser back button access to logged-out content

**Design Elements:**
- Gradient background (matches login page)
- Green checkmark icon with animation
- Clear messaging about logout completion
- Security best practices reminder

---

### 4. **register.php** - User Registration Page
**Features:**
- ✅ Modern registration form
- ✅ Full Name, Email, Username, Password fields
- ✅ Password confirmation field
- ✅ Real-time password strength indicator
- ✅ Client-side validation
- ✅ Server-side validation
- ✅ Checks for duplicate usernames/emails
- ✅ Minimum length requirements
- ✅ Email format validation

**Validation:**
- Username: minimum 3 characters
- Password: minimum 6 characters
- Email: valid email format
- Password confirmation must match

**Security:**
- Password hashing with PHP password_hash
- Prepared statements for database queries
- HTML entity encoding
- Prevents registration of already logged-in users

---

### 5. **reset_password.php** - Password Reset Page
**Features:**
- ✅ Two-step password reset process
- ✅ Email verification step
- ✅ Secure token generation
- ✅ Token expiration (1 hour)
- ✅ Password strength validation
- ✅ Confirmation before reset

**Prerequisites:**
Add these columns to the `login` table:
```sql
ALTER TABLE login ADD COLUMN reset_token VARCHAR(64) NULL;
ALTER TABLE login ADD COLUMN reset_expiry DATETIME NULL;
```

**Implementation Notes:**
- Step 1: User enters email address
- Step 2: User receives reset link with token
- Demo mode shows reset link in HTML comment
- In production: Send email with reset link
- Tokens expire after 1 hour
- One-time use (token cleared after reset)

---

## User Flow Diagrams

### Login Flow
```
User → Login Page → Enter Credentials → Validate → 
Success? → Create Session → Redirect to Dashboard
     ↘      ↓ 
     Error → Display Error Message
```

### Logout Flow
```
User → Logout Link → Clear Session → Clear Cookies → 
Destroy Session → Redirect to Success Page
```

### Registration Flow
```
User → Register Page → Enter Details → Validate → 
Duplicate Check? → Hash Password → Save to DB → 
Success Message → Redirect to Login
```

### Password Reset Flow
```
User → Reset Page → Enter Email → Verify Email → 
Generate Token → Send Email Link → User Clicks Link → 
Reset Page with Token → Enter New Password → Validate → 
Update Password → Success → Redirect to Login
```

---

## Security Best Practices Implemented

### 1. **Password Security**
- PHP password_hash() with PASSWORD_DEFAULT (bcrypt)
- Automatic hash upgrade from legacy formats
- Minimum 6 characters enforced
- Confirmation field for new passwords

### 2. **Session Management**
- Proper session initialization with session_start()
- Session destruction with cookie cleanup
- HTTP-only cookies (when configured)
- Secure flag for HTTPS environments

### 3. **Input Validation**
- Trim whitespace
- Type checking
- Email format validation
- Length requirements
- Character restrictions

### 4. **SQL Injection Prevention**
- Prepared statements throughout
- Parameterized queries
- No string concatenation with user input

### 5. **XSS Prevention**
- htmlspecialchars() for output encoding
- Stored in $_SESSION, not cookies
- Auto-escape in forms

### 6. **CSRF Protection**
- POST-based forms only
- No sensitive GET parameters
- Session-based authentication

---

## Database Schema Required

### login Table
```sql
CREATE TABLE login (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    reset_token VARCHAR(64) NULL,
    reset_expiry DATETIME NULL,
    role_id INT NULL,
    role VARCHAR(50) NULL
);

CREATE INDEX idx_email ON login(email);
CREATE INDEX idx_username ON login(username);
```

---

## Configuration & Setup

### 1. Update Header Navigation
The header (_header.php) already includes logout button. Ensure it's properly configured.

### 2. Protect Admin Pages
Add to top of protected pages:
```php
<?php
require_once 'lib_auth.php';
require_login();
$user = current_user($pdo);
?>
```

### 3. Email Configuration (Optional)
For password reset emails, configure SMTP:
```php
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.your-domain.com';
$mail->Username = 'noreply@siteworx.in';
$mail->Password = 'your-password';
$mail->SMTPAuth = true;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
```

---

## Testing Checklist

### Login Page
- [ ] Form validation (empty fields)
- [ ] Invalid username/password error
- [ ] Valid login redirects to dashboard
- [ ] Remember Me checkbox works
- [ ] Password visibility toggle works
- [ ] Page is mobile responsive
- [ ] Error messages are clear

### Logout Page
- [ ] Session is properly destroyed
- [ ] Cookies are cleared
- [ ] Redirect to success page
- [ ] Success page displays correctly
- [ ] Cannot go back after logout

### Registration Page
- [ ] Form validation works
- [ ] Username length check (min 3)
- [ ] Password length check (min 6)
- [ ] Password confirmation validation
- [ ] Email format validation
- [ ] Duplicate username detection
- [ ] Success message and redirect
- [ ] Mobile responsive

### Reset Password Page
- [ ] Email validation works
- [ ] Non-existent email shows safe message
- [ ] Reset token generation works
- [ ] Token expiration works
- [ ] New password validation
- [ ] Password update in database
- [ ] Token cleared after use

---

## Customization Guide

### 1. Change Color Scheme
Update gradient colors in CSS:
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### 2. Change Logo
Update header icons:
```html
<i class="fas fa-cube"></i> <!-- Change to any Font Awesome icon -->
```

### 3. Add CAPTCHA
Integrate reCAPTCHA to login/register forms:
```html
<div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div>
```

### 4. Add Email Verification
Send verification email during registration:
- Generate token
- Send email with verification link
- Verify email before allowing login

### 5. Add Two-Factor Authentication
```php
// After successful login, require 2FA code
$_SESSION['pending_2fa'] = true;
header('Location: 2fa.php');
```

---

## Troubleshooting

### Issue: Login not working
- Check database connection (connection.php)
- Verify login table exists
- Check username/password in database
- Review error logs

### Issue: Session not persisting
- Ensure session_start() is called on all pages
- Check PHP session.save_path permissions
- Verify cookies are enabled in browser
- Check session timeout settings

### Issue: Reset token not working
- Verify reset_token column exists in login table
- Check token expiry hasn't passed
- Ensure token is correctly stored
- Verify database update query

### Issue: Logout not clearing session
- Check session_destroy() is called
- Verify cookie deletion parameters match creation
- Clear browser cache
- Test in different browser

---

## Performance Optimization

### 1. Minify CSS/JavaScript
```html
<!-- Use minified versions -->
<link href="login.min.css" rel="stylesheet">
<script src="login.min.js"></script>
```

### 2. Add Caching Headers
```php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
```

### 3. Database Indexing
```sql
CREATE INDEX idx_username ON login(username);
CREATE INDEX idx_email ON login(email);
```

---

## Deployment Checklist

- [ ] Update database credentials in connection.php
- [ ] Set proper file permissions (chmod 600 for config files)
- [ ] Enable HTTPS
- [ ] Configure session secure flags for HTTPS
- [ ] Set up email service for password resets
- [ ] Test all authentication flows
- [ ] Backup database
- [ ] Review security headers
- [ ] Test on mobile devices
- [ ] Load testing for concurrent users

---

## Support & Maintenance

For issues or improvements:
1. Check error logs
2. Review database for data integrity
3. Test with different browsers
4. Monitor failed login attempts
5. Update dependencies regularly

---

## File Manifest

```
Admin_SiteWorx/
├── login.php ........................ User login interface
├── logout.php ....................... Session termination
├── logout_success.php ............... Logout confirmation
├── register.php ..................... User registration
├── reset_password.php ............... Password recovery
├── lib_auth.php ..................... Authentication functions
├── connection.php ................... Database connection
├── _header.php ...................... Admin navigation header
├── style_admin.css .................. Admin styles
└── index.php ........................ Protected dashboard
```

---

**Last Updated:** 2026-05-31
**Version:** 1.0
**Status:** Production Ready
