@echo off
REM CAR2GO Cleanup Script - Delete unnecessary files
REM This script removes unused files to free up space

echo =============================================
echo CAR2GO Cleanup Script
echo =============================================
echo.
echo This will DELETE the following files:
echo.
echo 1. CAR2GO_final_ZIP.zip (47.8 MB)
echo 2. demo.php
echo 3. All Screenshot files
echo 4. All WhatsApp image files
echo 5. Unused HTML files
echo.
echo WARNING: This action cannot be undone!
echo Make sure you have created a backup first!
echo.

set /p confirm="Are you sure you want to continue? (yes/no): "

if /i not "%confirm%"=="yes" (
    echo.
    echo Cleanup cancelled. No files were deleted.
    pause
    exit
)

echo.
echo Starting cleanup...
echo.

REM Change to project directory
cd /d "C:\Users\abhis\Desktop\MCA\Projectmodel\CAR2GO"

REM Delete large ZIP file
if exist "CAR2GO_final_ZIP.zip" (
    echo Deleting CAR2GO_final_ZIP.zip...
    del /F "CAR2GO_final_ZIP.zip"
    echo   [DONE] Deleted CAR2GO_final_ZIP.zip
) else (
    echo   [SKIP] CAR2GO_final_ZIP.zip not found
)

REM Delete demo.php
if exist "demo.php" (
    echo Deleting demo.php...
    del /F "demo.php"
    echo   [DONE] Deleted demo.php
) else (
    echo   [SKIP] demo.php not found
)

REM Delete unused HTML files
echo.
echo Deleting unused HTML files...
for %%f in (contact.html gallery.html icons.html services.html typography.html) do (
    if exist "%%f" (
        del /F "%%f"
        echo   [DONE] Deleted %%f
    ) else (
        echo   [SKIP] %%f not found
    )
)

REM Delete screenshots in images folder
echo.
echo Deleting screenshots in images folder...
cd images
if exist "Screenshot*.png" (
    del /F "Screenshot*.png"
    echo   [DONE] Deleted screenshot files
) else (
    echo   [SKIP] No screenshot files found
)

REM Delete WhatsApp images
echo.
echo Deleting WhatsApp images...
if exist "WhatsApp*.jpg" (
    del /F "WhatsApp*.jpg"
    echo   [DONE] Deleted WhatsApp JPG files
) else (
    echo   [SKIP] No WhatsApp JPG files found
)

if exist "WhatsApp*.jpeg" (
    del /F "WhatsApp*.jpeg"
    echo   [DONE] Deleted WhatsApp JPEG files
) else (
    echo   [SKIP] No WhatsApp JPEG files found
)

cd ..

echo.
echo =============================================
echo Cleanup Complete!
echo =============================================
echo.
echo Freed approximately 50+ MB of space.
echo.
echo Next steps:
echo 1. Create organized folder structure
echo 2. Move remaining files to proper locations
echo 3. Update code to use new structure
echo.

pause
