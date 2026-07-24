FROM laravelsail/php83-composer:latest

WORKDIR /var/www/html

EXPOSE 8000

# Agregar restart policy para evitar reinicios infinitos
STOPSIGNAL SIGINT

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
