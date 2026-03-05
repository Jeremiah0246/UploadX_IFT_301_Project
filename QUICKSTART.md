# 🚀 UploadX Quick Start Guide

## Get UploadX Running in 5 Minutes!

### ✅ Step 1: Database Setup (2 minutes)
1. Start XAMPP → Start Apache and MySQL
2. Open: `http://localhost/phpmyadmin/`
3. Click "SQL" tab
4. Open `database.sql` with Notepad
5. Copy all → Paste in phpMyAdmin → Click "Go"
6. Done! Database "uploadx" created ✓

### ✅ Step 2: Install Files (1 minute)
1. Copy `UploadX` folder
2. Paste into: `C:\xampp\htdocs\`
3. Result: `C:\xampp\htdocs\UploadX\`

### ✅ Step 3: Create Uploads Folder (1 minute)
1. Go to: `C:\xampp\htdocs\UploadX\`
2. Create new folder: `uploads`
3. Right-click uploads → Properties → Security
4. Give "Full control" to Users

### ✅ Step 4: Add Your Logo (30 seconds)
1. Design your UploadX logo
2. Save as: `UploadX-logo.png`
3. Put in: `C:\xampp\htdocs\UploadX\assets\images\`

### ✅ Step 5: Access UploadX!
Open browser and go to:
- `http://localhost/UploadX/`
- Or `http://localhost:8080/UploadX/` (if using port 8080)

### 🎉 You're Done!

1. Click "Sign Up"
2. Create your account
3. Login
4. Start uploading files!

---

## 📧 Optional: Email Configuration (For Password Reset)

If you want password reset emails to work:

1. Open `includes/config.php` with Notepad
2. Find these lines (around line 17-21):
   ```php
   define('SMTP_HOST', 'smtp.gmail.com');
   define('SMTP_USER', 'your-email@gmail.com');
   define('SMTP_PASS', 'your-app-password');
   ```
3. Replace with your Gmail:
   - Get App Password: Google Account → Security → 2-Step Verification → App passwords
4. Save file

---

## ⚡ Quick Tips

### Change Upload Size Limit
1. Open `includes/config.php`
2. Find: `define('MAX_FILE_SIZE', 52428800);`
3. Change number (in bytes):
   - 10MB = 10485760
   - 50MB = 52428800
   - 100MB = 104857600

### Add More File Types
1. Open `includes/config.php`
2. Find: `ALLOWED_PHOTO_TYPES` or `ALLOWED_DOCUMENT_TYPES`
3. Add your type: `'mp4', 'avi', 'zip'`

---

## 🆘 Common Issues

**"Database connection failed"**
→ Make sure MySQL is running in XAMPP

**"Can't upload files"**
→ Create the `uploads` folder!

**"Logo not showing"**
→ Add your logo as `assets/images/UploadX-logo.png`

**"Access denied"**
→ Give uploads folder full permissions

---

## 🎯 What You Get

✨ Beautiful landing page
🔐 Secure login & signup
📁 Drag & drop file upload
🖼️ Automatic photo/document sorting
🔍 Search & filter files
👁️ Preview files before downloading
📊 Grid & list view toggle
♾️ Unlimited storage
📱 Works on mobile & desktop

---

**Need detailed help?** Check the full README.md file!

**Ready to start?** Let's upload some files! 🚀
