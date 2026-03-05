# UploadX Installation Guide

## 🚀 Quick Install (5 Minutes)

### Prerequisites
- ✅ XAMPP installed and running
- ✅ Apache and MySQL started
- ✅ Web browser

---

## Step-by-Step Installation

### 1️⃣ Database Setup

**Option A: Using phpMyAdmin (Recommended)**
1. Open phpMyAdmin: `http://localhost/phpmyadmin/`
2. Click "SQL" tab at the top
3. Open `database.sql` file with Notepad
4. Copy ALL the SQL code
5. Paste into the SQL box in phpMyAdmin
6. Click "Go" button
7. ✅ Database "uploadx" created with all tables!

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p < database.sql
```

### 2️⃣ File Installation

1. **Extract** the UploadX folder
2. **Copy** entire folder to XAMPP's htdocs:
   - Windows: `C:\xampp\htdocs\UploadX\`
   - Mac: `/Applications/XAMPP/htdocs/UploadX/`
   - Linux: `/opt/lampp/htdocs/UploadX/`

### 3️⃣ Create Uploads Folder

1. Navigate to: `C:\xampp\htdocs\UploadX\`
2. Create new folder named: **`uploads`** (lowercase, no spaces)
3. Set permissions:

**Windows:**
- Right-click `uploads` folder
- Properties → Security tab
- Click "Edit" button
- Select "Users" group
- Check ✅ "Full control"
- Apply → OK

**Mac/Linux:**
```bash
cd /Applications/XAMPP/htdocs/UploadX
chmod 777 uploads
```

### 4️⃣ Add Your Logo

1. Design your UploadX logo (PNG format)
2. Name it: **`UploadX-logo.png`**
3. Place in: `UploadX/assets/images/UploadX-logo.png`
4. **Recommended specs:**
   - Width: 200-300px
   - Format: PNG with transparent background
   - Colors: Blue/white to match theme

### 5️⃣ Configure Email (Optional but Recommended)

For password reset functionality:

1. Open `includes/config.php` with text editor
2. Find lines 17-21 (SMTP Configuration)
3. Update with your email:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'youremail@gmail.com');
define('SMTP_PASS', 'your-app-password');
```

**Getting Gmail App Password:**
1. Go to Google Account → Security
2. Enable 2-Step Verification
3. Go to App passwords
4. Select app: Mail
5. Select device: Other (Custom name)
6. Enter "UploadX"
7. Copy the 16-character password
8. Use this in `SMTP_PASS`

### 6️⃣ Access UploadX

Open your browser and go to:
- **`http://localhost/UploadX/`**
- Or: **`http://localhost:8080/UploadX/`** (if using port 8080)

You should see the beautiful UploadX landing page! 🎉

---

## ✅ Verification Checklist

After installation, verify:

- [ ] Landing page loads correctly
- [ ] Can access signup page
- [ ] Can create account
- [ ] Can login successfully
- [ ] Dashboard shows welcome message
- [ ] Can access file manager
- [ ] Can upload files
- [ ] Uploads folder contains files
- [ ] Can download files
- [ ] Can delete files
- [ ] Logo appears in navbar
- [ ] All animations work smoothly

---

## 🎨 Customization

### Change Upload Size Limit

Edit `includes/config.php` line 11:
```php
define('MAX_FILE_SIZE', 52428800); // 50MB in bytes
```

Common sizes:
- 10MB = `10485760`
- 50MB = `52428800`
- 100MB = `104857600`
- 500MB = `524288000`

Also update PHP settings in `php.ini`:
```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
```

### Add More File Types

Edit `includes/config.php` lines 12-13:
```php
define('ALLOWED_PHOTO_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'zip', 'rar']);
```

### Change Colors

Edit `assets/css/style.css` lines 8-11:
```css
--primary-blue: #2563eb;    /* Your color */
--primary-blue-light: #3b82f6;
--primary-blue-dark: #1e40af;
```

---

## 🔧 Troubleshooting

### "Database connection failed"
**Solution:** 
- Ensure MySQL is running in XAMPP
- Verify database exists: Run `database.sql` in phpMyAdmin
- Check credentials in `includes/config.php`

### "Can't upload files"
**Solution:**
- Create `uploads` folder in project root
- Set folder permissions (full control)
- Check PHP upload limits

### "Internal Server Error"
**Solution:**
- Check Apache error logs
- Disable `.htaccess` if present (rename to `.htaccess.backup`)
- Verify all PHP files are present

### Logo not showing
**Solution:**
- Add logo file: `assets/images/UploadX-logo.png`
- Check file name spelling (case-sensitive)
- Clear browser cache

### Uploads not saving
**Solution:**
- Verify `uploads` folder exists
- Check folder permissions
- Look in `uploads` folder for files (they have random names)

### Password reset not working
**Solution:**
- Configure SMTP settings in `config.php`
- Use Gmail App Password (not regular password)
- Check internet connection

---

## 📁 Required Folder Structure

```
C:\xampp\htdocs\UploadX\
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   ├── landing.css
│   │   ├── auth.css
│   │   └── dashboard.css
│   ├── js/
│   │   ├── landing.js
│   │   ├── auth.js
│   │   └── files.js
│   └── images/
│       └── UploadX-logo.png ← YOUR LOGO HERE
├── includes/
│   └── config.php
├── uploads/ ← CREATE THIS FOLDER
├── *.php files
└── database.sql
```

---

## 🌐 URL Structure

After installation:
- Landing: `http://localhost/UploadX/`
- Signup: `http://localhost/UploadX/signup.php`
- Login: `http://localhost/UploadX/login.php`
- Dashboard: `http://localhost/UploadX/dashboard.php`
- Files: `http://localhost/UploadX/files.php`

---

## 🎯 Post-Installation

1. **Create your account** via signup page
2. **Login** with credentials
3. **Upload some test files**
4. **Test all features** (upload, download, delete, search)
5. **Customize colors/logo** to your brand

---

## 🆘 Need Help?

1. Check `README.md` for detailed documentation
2. Review `QUICKSTART.md` for fast setup
3. Verify all steps completed above
4. Check XAMPP error logs
5. Ensure all files extracted properly

---

## 🎉 Success!

If you can:
- ✅ See the landing page
- ✅ Create an account
- ✅ Login successfully
- ✅ Upload and download files

**Congratulations! UploadX is ready to use!** 🚀

Enjoy your professional file management system!
