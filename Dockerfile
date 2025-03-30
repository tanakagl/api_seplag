FROM php:8.2-fpm

# Argumentos definidos no docker-compose.yml
ARG user=www-data
ARG uid=1000

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    postgresql-client \

# Instalar Node.js e npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd
 
# Instalar o Redis
RUN pecl install redis && docker-php-ext-enable redis

# Obter Composer mais recente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar diretório do sistema
RUN mkdir -p /var/www/html

# Copiar permissões de diretório da aplicação existente
COPY . /var/www/html

# Definir diretório de trabalho
WORKDIR /var/www/html

# Instalar dependências do Composer
RUN composer install

# Instalar dependências do Node.js e compilar assets
RUN npm install
RUN npm run build

# Expor porta 9000
EXPOSE 9000

# Comando padrão para iniciar o servidor de desenvolvimento do Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]


COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]