# 📦 PHASE 2: ORGANIZE & TEST

## 🎯 You've Completed Phase 1! (Cleanup Scripts)

Now let's organize your files and test the new infrastructure.

---

## 📁 **STEP 1: MOVE FILES TO ORGANIZED FOLDERS** (15 minutes)

### **Move CSS Files:**
```
FROM: CAR2GO/css/
TO:   CAR2GO/public/css/

Files to move:
✓ bootstrap.css
✓ style.css
✓ Any other .css files
```

**How:**
1. Select all files in `css/` folder
2. Cut (Ctrl+X)
3. Navigate to `public/css/`
4. Paste (Ctrl+V)
5. Delete empty `css/` folder

---

### **Move JavaScript Files:**
```
FROM: CAR2GO/js/
TO:   CAR2GO/public/js/

Files to move:
✓ jquery.min.js
✓ bootstrap.js
✓ All other .js files
```

**How:**
1. Select all files in `js/` folder
2. Cut (Ctrl+X)
3. Navigate to `public/js/`
4. Paste (Ctrl+V)
5. Delete empty `js/` folder

---

### **Move Fonts:**
```
FROM: CAR2GO/fonts/
TO:   CAR2GO/public/fonts/

Files to move:
✓ All font files (.ttf, .woff, .eot, etc.)
```

---

### **Move Database File:**
```
FROM: CAR2GO/carservice.sql
TO:   CAR2GO/database/carservice.sql
```

---

### **Move Documentation (Optional):**
```
FROM: CAR2GO/images/projectdocumentation.docx
      CAR2GO/images/projectdocumentation.pdf
TO:   CAR2GO/docs/
```

---

### **Keep Documentation Files in Root:**
```
KEEP in CAR2GO/:
✓ README.md
✓ INDEX.md
✓ 3_SIMPLE_STEPS.md
✓ All other .md files
✓ All .bat files
```

---

## 🧪 **STEP 2: CREATE TEST FILE** (5 minutes)

Create this file to test your templates:

**File**: `test_templates.php`
**Location**: `CAR2GO/test_templates.php`

**Content**:
```php
<?php
session_start();

// Include configuration
require_once 'config/db_connect.php';
require_once 'includes/security.php';

// Set page title
$page_title = 'Template Test Page - CAR2GO';

// Include header
include 'templates/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center">
                <i class="fas fa-check-circle text-success"></i> 
                Templates Working!
            </h1>
            
            <div class="alert alert-success mt-4">
                <h4><i class="fas fa-info-circle"></i> Test Results:</h4>
                <ul class="mb-0">
                    <li>✅ Header loaded successfully</li>
                    <li>✅ Navigation bar displayed</li>
                    <li>✅ Bootstrap CSS loaded</li>
                    <li>✅ Font Awesome icons working</li>
                    <li>✅ Footer will load below</li>
                </ul>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-database"></i> Database Connection</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($con) && $con->ping()): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> 
                            Database connected successfully!
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> 
                            Database connection failed. Check config/db_connect.php
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-shield-alt"></i> Security Functions</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>CSRF Token:</strong> 
                            <code><?php echo generate_csrf_token(); ?></code>
                        </li>
                        <li class="list-group-item">
                            <strong>Password Hashing:</strong> 
                            <?php 
                            $test_hash = hash_password('test123');
                            echo strlen($test_hash) > 0 ? '✅ Working' : '❌ Failed';
                            ?>
                        </li>
                        <li class="list-group-item">
                            <strong>Input Sanitization:</strong> 
                            <?php 
                            $test = sanitize_input('<script>test</script>');
                            echo $test === '&lt;script&gt;test&lt;/script&gt;' ? '✅ Working' : '❌ Failed';
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="alert alert-info mt-4">
                <h5><i class="fas fa-lightbulb"></i> Next Steps:</h5>
                <ol>
                    <li>If everything looks good, templates are ready!</li>
                    <li>Update your existing PHP files to use these templates</li>
                    <li>Replace old login.php with login_secure.php</li>
                    <li>Add security functions to all forms</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
```

**How to Create:**
1. Open text editor (Notepad, VS Code, etc.)
2. Copy the code above
3. Save as: `test_templates.php` in CAR2GO folder
4. Open in browser: `http://localhost/CAR2GO/test_templates.php`

---

## ✅ **STEP 3: UPDATE CSS/JS PATHS** (10 minutes)

After moving files, update paths in:

### **templates/header.php:**

Change:
```php
<link rel="stylesheet" href="<?php echo $base_url; ?>css/bootstrap.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
```

To:
```php
<link rel="stylesheet" href="<?php echo $base_url; ?>public/css/bootstrap.css">
<link rel="stylesheet" href="<?php echo $base_url; ?>public/css/style.css">
```

### **templates/footer.php:**

Change:
```php
<script src="<?php echo $base_url ?? '/'; ?>js/jquery.min.js"></script>
<script src="<?php echo $base_url ?? '/'; ?>js/bootstrap.bundle.js"></script>
```

To:
```php
<script src="<?php echo $base_url ?? '/'; ?>public/js/jquery.min.js"></script>
<script src="<?php echo $base_url ?? '/'; ?>public/js/bootstrap.bundle.js"></script>
```

---

## 🎯 **STEP 4: UPDATE ONE PHP FILE** (20 minutes)

Let's update `login.php` as our first file:

### **Option A: Replace Completely (Recommended)**

1. **Backup current login:**
   ```
   Rename: login.php → login_OLD_backup.php
   ```

2. **Use secure version:**
   ```
   Copy: login_secure.php → login.php
   ```

3. **Test it:**
   - Open: `http://localhost/CAR2GO/login.php`
   - Try logging in
   - Should work with templates!

### **Option B: Update Existing (Advanced)**

Add to top of `login.php`:
```php
<?php
session_start();
require_once 'config/db_connect.php';
require_once 'includes/security.php';

$page_title = 'Login - CAR2GO';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request';
    } else {
        $email = sanitize_input($_POST['l_uname']);
        $password = $_POST['l_password'];
        
        // Use prepared statement
        $query = "SELECT l_id, l_password, l_type, l_approve FROM login WHERE l_uname = ?";
        $user = db_fetch_one($con, $query, "s", [$email]);
        
        if ($user && verify_password($password, $user['l_password'])) {
            // Login successful
            $_SESSION['l_id'] = $user['l_id'];
            $_SESSION['l_type'] = $user['l_type'];
            session_regenerate_id(true);
            
            // Redirect based on type
            // ... rest of logic
        } else {
            $error = 'Invalid email or password';
        }
    }
}

include 'templates/header.php';
?>

<!-- Your existing HTML -->
<!-- Add CSRF token to form -->
<input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

<?php include 'templates/footer.php'; ?>
```

---

## 📋 **COMPLETION CHECKLIST:**

**File Organization:**
- [ ] CSS files moved to public/css/
- [ ] JS files moved to public/js/
- [ ] Fonts moved to public/fonts/
- [ ] Database moved to database/
- [ ] Old css/, js/, fonts/ folders deleted

**Testing:**
- [ ] test_templates.php created
- [ ] Opened in browser
- [ ] All checks passing (green)
- [ ] Database connected
- [ ] Security functions working

**Path Updates:**
- [ ] header.php paths updated
- [ ] footer.php paths updated
- [ ] Test page loads correctly

**Login Update:**
- [ ] login_OLD_backup.php created
- [ ] login.php updated or replaced
- [ ] Login page loads
- [ ] Can login successfully

---

## 🎉 **SUCCESS LOOKS LIKE:**

```
http://localhost/CAR2GO/test_templates.php
┌─────────────────────────────────────┐
│ ✅ Templates Working!               │
│                                     │
│ ✅ Header loaded                    │
│ ✅ Navigation displayed             │
│ ✅ Database connected               │
│ ✅ Security functions work          │
│ ✅ Footer loaded                    │
└─────────────────────────────────────┘
```

---

## 🚀 **NEXT PHASE:**

After completing this phase:
1. **Update Registration Files** - usereg.php, driverreg.php, servicereg.php
2. **Update Booking Files** - Add security to all booking pages
3. **Update Admin Files** - Secure admin panel
4. **Test Everything** - Make sure all features work

**Read**: ROADMAP.md for complete 6-week plan

---

## ⏱️ **Time Estimate:**

- File Organization: 15 minutes
- Create Test File: 5 minutes
- Update Paths: 10 minutes
- Update Login: 20 minutes

**Total: ~50 minutes**

---

## 🆘 **TROUBLESHOOTING:**

### "Test page shows errors"
- Check file paths in header.php and footer.php
- Make sure config/db_connect.php has correct credentials
- Verify includes/security.php exists

### "CSS not loading"
- Clear browser cache (Ctrl+F5)
- Check file paths updated correctly
- Verify files moved to public/css/

### "Database connection failed"
- Update credentials in config/db_connect.php
- Make sure MySQL is running
- Test connection separately

### "Can't login after update"
- Old passwords won't work (plain text vs hashed)
- Create new test user
- Or check password in database directly

---

**Ready for Phase 2?** Start with moving the CSS files! 📁✨
