## Запуск

~~~
php -S 0.0.0.0:8080
~~~

## Маршруты

Доступные маршруты перечислены в файле `index.php`

## Аутентификация

Аутентификация осуществляется с помощью мидлваре `App\Middleware\JwtAuthMiddleware`

Данные для аутентификации:

~~~
    "email": "admin@admin.com",
    "password": "secret"
~~~

На верный запрос выдается JWT токен доступа. Токен отправляется на API сервер через `HTTP Bearer Tokens`.