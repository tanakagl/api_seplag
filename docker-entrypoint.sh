#!/bin/bash
set -e

echo "Esperando a conexão com o banco de dados..."
while ! pg_isready -q -h $DB_HOST -p $DB_PORT -U $DB_USER
do
  sleep 1
done
echo "Banco de dados beleza!"

echo "Configurando a app..."
php artisan key:generate --no-interaction --force
php artisan config:clear


echo "Executando as migrations..."
php artisan migrate --force

echo "Executando as seeders..."
php artisan db:seed --force

php artisan cache:clean
php artisan route:clean
php artisan view:clean

echo "Executando o build do frontend..."
npm run build

echo "Iniciando o servidor PHP..."
php artisan serve --host=0.0.0.0 --port=8000