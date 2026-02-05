@echo off
REM CAR2GO Folder Structure Setup
REM Creates organized folder structure for the clean project

echo =============================================
echo CAR2GO Folder Structure Setup
echo =============================================
echo.
echo This will create the following folders:
echo.
echo - config/
echo - includes/
echo - templates/
echo - admin/
echo - user/
echo - driver/
echo - service/
echo - uploads/
echo   - uploads/cars/
echo   - uploads/drivers/
echo   - uploads/services/
echo   - uploads/documents/
echo - public/
echo   - public/css/
echo   - public/js/
echo   - public/fonts/
echo   - public/images/
echo - docs/
echo - database/
echo.

set /p confirm="Create these folders? (yes/no): "

if /i not "%confirm%"=="yes" (
    echo.
    echo Cancelled. No folders were created.
    pause
    exit
)

echo.
echo Creating folders...
echo.

REM Change to project directory
cd /d "C:\Users\abhis\Desktop\MCA\Projectmodel\CAR2GO"

REM Create main directories
for %%d in (config includes templates admin user driver service uploads public docs database) do (
    if not exist "%%d" (
        mkdir "%%d"
        echo   [CREATED] %%d/
    ) else (
        echo   [EXISTS] %%d/
    )
)

REM Create upload subdirectories
cd uploads
for %%d in (cars drivers services documents) do (
    if not exist "%%d" (
        mkdir "%%d"
        echo   [CREATED] uploads/%%d/
    ) else (
        echo   [EXISTS] uploads/%%d/
    )
)
cd ..

REM Create public subdirectories
cd public
for %%d in (css js fonts images) do (
    if not exist "%%d" (
        mkdir "%%d"
        echo   [CREATED] public/%%d/
    ) else (
        echo   [EXISTS] public/%%d/
    )
)
cd ..

REM Create .gitkeep files to preserve empty folders in git
echo.
echo Creating .gitkeep files...
echo.

for %%d in (uploads\cars uploads\drivers uploads\services uploads\documents) do (
    echo. > "%%d\.gitkeep"
    echo   [CREATED] %%d\.gitkeep
)

echo.
echo =============================================
echo Folder Structure Created!
echo =============================================
echo.
echo Next steps:
echo 1. Move CSS files to public/css/
echo 2. Move JS files to public/js/
echo 3. Move fonts to public/fonts/
echo 4. Move static images to public/images/
echo 5. Move database files to database/
echo 6. Move documentation to docs/
echo.

pause
