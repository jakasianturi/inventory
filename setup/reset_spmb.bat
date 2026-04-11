@echo off
cd /d C:\xampp\htdocs\spmb
echo Menarik update dari branch meksi...
git pull origin meksi

echo Menjalankan migrate:fresh...
php artisan migrate:fresh

echo Menjalankan seeder SetupSeeder...
php artisan db:seed --class=SetupSeeder

echo Menghapus cache dan optimize...
php artisan optimize:clear`

echo Selesai!
pause