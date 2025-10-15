# Laravel 12 API + Orchid — Checklist de Produção

Este guia resume as configurações essenciais para colocar este boilerplate em produção com foco em API (versionada) e painel admin (Orchid).

## Requisitos
- PHP 8.2+ com extensões: pdo, pdo_mysql/postgresql (conforme DB), mbstring, openssl, tokenizer, xml, ctype, json, curl
- Servidor web: Nginx (recomendado) + PHP-FPM
- Banco: MySQL/MariaDB ou PostgreSQL
- Cache/Queue: Redis (recomendado)
- Node opcional (apenas para build de assets quando necessário)

## Variáveis de Ambiente (.env)
- APP_ENV=production
- APP_DEBUG=false
- APP_URL=https://api.suaempresa.com
- LOG_CHANNEL=stack
- LOG_LEVEL=info
- DB_CONNECTION=mysql|pgsql
- DB_HOST=...
- DB_PORT=...
- DB_DATABASE=...
- DB_USERNAME=...
- DB_PASSWORD=...
- CACHE_DRIVER=redis
- QUEUE_CONNECTION=redis
- SESSION_DRIVER=redis (se usar sessão)
- REDIS_HOST=...
- REDIS_PASSWORD=null
- REDIS_PORT=6379
- TRUSTED_PROXIES='*' (quando atrás de proxy/load balancer)

## Build/Deploy
1) Dependências e otimizações
- composer install --no-dev --prefer-dist --optimize-autoloader
- php artisan key:generate --force (se necessário)
- php artisan storage:link (se serve arquivos)
- php artisan migrate --force
- php artisan db:seed --force (se necessário)
- php artisan config:cache
- php artisan route:cache
- php artisan view:cache

2) Orchid Admin
- composer require orchid/platform (já instalado)
- php artisan orchid:install (já aplicado neste projeto)
- php artisan orchid:admin email@dominio senha
- Painel: /admin

3) Fila e Agendador
- Supervisor para workers:
```
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=120
autostart=true
autorestart=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/laravel-worker.log
```
- Cron para schedule (a cada minuto):
```
* * * * * php /var/www/artisan schedule:run >> /dev/null 2>&1
```

4) Nginx (exemplo)
```
server {
    listen 80;
    server_name api.suaempresa.com;
    root /var/www/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass php:9000; # ou unix:/run/php/php8.2-fpm.sock
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }

    location ~* \.(jpg|jpeg|png|gif|css|js|ico|svg)$ {
        expires 7d;
        access_log off;
    }
}
```

5) Segurança e Performance
- HTTPS obrigatório (TLS) via reverse proxy/ingress
- Rate limiting para APIs (Throttle; ver `app/Http/Kernel.php` ou middleware custom)
- CORS: configure `config/cors.php`
- Headers de segurança (Nginx/Proxy): HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy
- Opcache habilitado no PHP-FPM
- Monitoração: logs centralizados + métricas (Health: /up)

## Docker (opcional)
- PHP-FPM + Nginx + Redis + MySQL/Postgres
- Build multi-stage para PHP

Exemplo Dockerfile (PHP-FPM de produção simplificado):
```
FROM php:8.3-fpm-alpine as base
RUN apk add --no-cache bash git unzip libzip-dev oniguruma-dev icu-dev libpng-dev libjpeg-turbo-dev libwebp-dev libxml2-dev \
  && docker-php-ext-install pdo pdo_mysql opcache

WORKDIR /var/www
COPY composer.json composer.lock ./
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --prefer-dist --no-scripts --no-interaction

COPY . .
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

CMD ["php-fpm"]
```

## Teste final (Smoke tests)
- GET /api/v1/ping => 200 {"version":"v1","message":"API is working"}
- GET /admin (login Orchid) => 200 após autenticar
- php artisan about, php artisan route:list

## Dicas
- Desativar Xdebug em produção (XDEBUG_MODE=off)
- Não rodar `php artisan serve` em produção; use Nginx + PHP-FPM
- Use `.env` distinto por ambiente e gestão segura de segredos (Vault/Secrets)
