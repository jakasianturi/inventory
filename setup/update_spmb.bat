@echo off
cd /d C:\xampp\htdocs\spmb
echo Menarik update dari branch meksi...
git pull origin meksi

echo Menjalankan migrate...
php artisan migrate

echo Menghapus cache dan optimize...
php artisan optimize:clear

echo Selesai!
pause