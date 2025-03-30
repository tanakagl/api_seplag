#!/bin/bash
set -e

echo "Esperando a conexão com o banco de dados..."
until PGPASSWORD=$DB_PASSWORD psql -h $DB_HOST -p $DB_PORT -U $DB_USERNAME -d $DB_DATABASE -c '\q' > /dev/null 2>&1; do
  echo "Postgres não está disponível ainda... aguardando"
  sleep 1
done
echo "Banco de dados beleza!"


echo "Configurando a app..."
php artisan key:generate --no-interaction --force
php artisan config:clear


echo "Executando as migrations..."
php artisan migrate --force

echo "Executando as seeders com comando personalizado..."
php artisan db:check-and-seed

php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Executando o build do frontend..."
# Inicia o Vite em segundo plano
nohup npm run dev > /var/www/html/storage/logs/vite.log 2>&1 &

echo "Iniciando o servidor PHP..."
php artisan serve --host=0.0.0.0 --port=8000