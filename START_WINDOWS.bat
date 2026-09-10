@echo off
set PHP=C:\xampp\php\php.exe
if not exist "%PHP%" (
  echo PHP XAMPP tidak ditemukan di %PHP%
  echo Edit START_WINDOWS.bat jika lokasi PHP berbeda.
  pause
  exit /b 1
)
if not exist .env (
  copy .env.example .env >nul
  echo File .env dibuat dari .env.example
)
echo.
echo Pastikan MySQL XAMPP sudah START dan database agenda_surat_bps sudah di-import.
echo Membuka server pada http://127.0.0.1:8080/login
echo.
"%PHP%" -S 127.0.0.1:8080 -t public public\router.php
pause
