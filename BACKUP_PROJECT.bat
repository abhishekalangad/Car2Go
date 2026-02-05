@echo off
REM CAR2GO Backup Script
REM Creates a complete backup of the project before making changes

echo =============================================
echo CAR2GO Project Backup Script
echo =============================================
echo.

REM Set timestamp
set timestamp=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set timestamp=%timestamp: =0%

REM Set paths
set SOURCE_DIR=C:\Users\abhis\Desktop\MCA\Projectmodel\CAR2GO
set BACKUP_DIR=C:\Users\abhis\Desktop\MCA\Projectmodel\CAR2GO_BACKUP_%timestamp%

echo Source: %SOURCE_DIR%
echo Backup: %BACKUP_DIR%
echo.
echo Creating backup...
echo.

REM Create backup using xcopy
xcopy "%SOURCE_DIR%" "%BACKUP_DIR%" /E /I /H /Y

if %errorlevel% equ 0 (
    echo.
    echo =============================================
    echo SUCCESS! Backup completed.
    echo =============================================
    echo.
    echo Backup location: %BACKUP_DIR%
    echo.
    echo You can now safely make changes to your project.
    echo If anything goes wrong, copy files back from the backup folder.
    echo.
) else (
    echo.
    echo =============================================
    echo ERROR! Backup failed.
    echo =============================================
    echo.
    echo Please check:
    echo 1. Source directory exists
    echo 2. You have write permissions
    echo 3. Enough disk space available
    echo.
)

pause
