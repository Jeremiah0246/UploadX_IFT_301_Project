# UploadX - Professional File Management System

🎨 **Beautiful** • 🚀 **Fast** • 🔒 **Secure**

A modern, professional file upload and management system with a stunning UI, built with PHP, MySQL, and vanilla JavaScript.

## ✨ Features

### Authentication
- ✅ Beautiful landing page with modern design
- ✅ User registration with password strength indicator
- ✅ Secure login system
- ✅ Forgot password with email reset (SMTP configured)
- ✅ Session management

### File Management
- ✅ Drag & drop file upload with glow effects
- ✅ Multi-file upload support
- ✅ Real-time upload progress tracking
- ✅ Automatic categorization (Photos & Documents)
- ✅ Grid and List view toggle
- ✅ Advanced search and filtering
- ✅ File preview (images and PDFs)
- ✅ Multi-select download (sequential)
- ✅ Multi-select delete with confirmation
- ✅ Unlimited storage

### Design
- ✅ Professional blue/white/gray color scheme
- ✅ Smooth animations and transitions
- ✅ Responsive design (mobile, tablet, desktop)
- ✅ Toast notifications
- ✅ Loading states and skeletons
- ✅ Modern glassmorphism effects
- ✅ Scroll animations

## 📋 Installation

### Step 1: Import Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin/` (or `http://localhost:8080/phpmyadmin/`)
2. Click "SQL" tab
3. Copy and paste contents from `database.sql`
4. Click "Go"

### Step 2: Extract Files

1. Extract the UploadX folder
2. Copy to: `C:\xampp\htdocs\UploadX\`

### Step 3: Configure

1. Open `includes/config.php`
2. Update database settings (if needed - defaults work for XAMPP)
3. Configure SMTP settings for password reset emails:
   ```php
   define('SMTP_HOST', 'smtp.gmail.com');
   define('SMTP_USER', 'your-email@gmail.com');
   define('SMTP_PASS', 'your-app-password');
   ```

### Step 4: Add Your Logo

1. Design your UploadX logo
2. Save as: `UploadX-logo.png`
3. Place in: `assets/images/UploadX-logo.png`
4. Recommended size: 200-300px wide, transparent background

### Step 5: Create Uploads Folder

1. Create folder: `C:\xampp\htdocs\UploadX\uploads\`
2. Set permissions (Windows: Right-click → Properties → Security → Full control)

### Step 6: Access

- Landing Page: `http://localhost/UploadX/`
- Or: `http://localhost:8080/UploadX/` (if using port 8080)

## 🎯 Usage

1. **Sign Up**: Click "Sign Up" and create your account
2. **Login**: Use your credentials to login
3. **Dashboard**: Welcome screen with quick actions
4. **Upload Files**: Drag & drop or click to browse
5. **Manage Files**: View in Photos or Documents tabs
6. **Preview**: Click any file to preview
7. **Download**: Select files and download
8. **Delete**: Select files and delete with confirmation

## 🔧 Configuration

### File Upload Limits

Edit `includes/config.php`:
```php
define('MAX_FILE_SIZE', 52428800); // 50MB
```

### Allowed File Types

Photos: jpg, jpeg, png, gif, webp, svg
Documents: pdf, doc, docx, txt, xls, xlsx, ppt, pptx, csv

To modify, edit `includes/config.php`:
```php
define('ALLOWED_PHOTO_TYPES', ['jpg', 'jpeg', 'png', ...]);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', ...]);
```

### Email Settings (Password Reset)

For Gmail:
1. Enable 2-factor authentication
2. Generate App Password
3. Use App Password in `SMTP_PASS`

## 📁 File Structure

```
UploadX/
├── assets/
│   ├── css/
│   │   ├── style.css (main design system)
│   │   ├── landing.css (landing page styles)
│   │   ├── auth.css (authentication pages)
│   │   └── dashboard.css (file manager styles)
│   ├── js/
│   │   ├── landing.js (scroll animations)
│   │   ├── auth.js (password strength, validation)
│   │   └── files.js (drag & drop, file management)
│   └── images/
│       └── UploadX-logo.png (YOUR LOGO HERE)
├── includes/
│   └── config.php (configuration & functions)
├── uploads/ (CREATE THIS FOLDER)
├── index.php (landing page)
├── signup.php (registration)
├── login.php (authentication)
├── forgot-password.php (password reset request)
├── reset-password.php (password reset form)
├── dashboard.php (welcome screen)
├── files.php (main file manager)
├── profile.php (user profile)
├── upload-handler.php (file upload)
├── download-handler.php (file download)
├── delete-handler.php (file deletion)
├── database.sql (database schema)
└── README.md (this file)
```

## 🎨 Design System

### Colors
- Primary Blue: `#2563eb`
- Light Blue: `#3b82f6`
- Dark Blue: `#1e40af`
- White: `#ffffff`
- Grays: 50-900 scale

### Typography
- Font: Inter (Google Fonts)
- Headings: 700-800 weight
- Body: 400-600 weight

### Components
- Buttons with ripple effects
- Cards with hover animations
- Forms with real-time validation
- Toast notifications
- Loading skeletons
- Modal previews

## 🔒 Security Features

- ✅ Password hashing (bcrypt)
- ✅ SQL injection protection (prepared statements)
- ✅ XSS protection (input sanitization)
- ✅ CSRF token protection
- ✅ Session security
- ✅ File type validation
- ✅ File size limits
- ✅ User file isolation

## 📱 Responsive Design

- ✅ Mobile (320px+)
- ✅ Tablet (768px+)
- ✅ Desktop (1024px+)
- ✅ Large screens (1440px+)

## 🐛 Troubleshooting

### Can't upload files
- Check `uploads/` folder exists
- Verify folder permissions
- Check PHP upload limits in `php.ini`

### Database connection error
- Verify MySQL is running in XAMPP
- Check database name in `config.php`
- Ensure database is created

### Password reset not working
- Configure SMTP settings in `config.php`
- Check email credentials
- Verify internet connection

### Logo not showing
- Add `UploadX-logo.png` to `assets/images/`
- Check file name spelling
- Clear browser cache

## 🚀 Future Enhancements (Optional)

- File sharing with links
- Folder organization
- File versions/history
- Admin dashboard
- Storage quotas
- API integration
- Cloud storage backup

## 📄 License

Free to use and modify for personal and commercial projects.

## 🎉 Credits

Built with love for professional file management.

---

**Ready to manage your files beautifully? Let's go!** 🚀
