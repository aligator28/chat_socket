<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Порядок действий:

1. env файл - BROADCAST_DRIVER=redis QUEUE_DRIVER=redis
2. Установка composer require pusher/pusher-php-server
3. npm install -g laravel-echo-server
4. laravel-echo-server init 
там будут несколько вопросов, соглашаемся со всем, сгенирится ключ приложения

5. Стартуем сервер laravel-echo-server start
Если все ок, увидим:
L A R A V E L  E C H O  S E R V E R

version 1.3.6

⚠ Starting server in DEV mode...

✔  Running at localhost on port 6001
✔  Channels are ready.
✔  Listening for http events...
✔  Listening for redis events...

Server ready!

И пускай висит

6. npm install --save laravel-echo
7. npm install --save socket.io-client

8. Идем по пути: resources/assets/js/app.js

```javascript
import Echo from "laravel-echo"
window.io = require('socket.io-client');

// Have this in case you stop running your laravel echo server
if (typeof io !== 'undefined') {
  window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001',
  });
}
```

9. npm run watch - и пускай висит
запускаем laravel mix - генерим js файл

10. Смотри файл welcome.blade.php - там накидана примерная логика фронтенда
11. Смотри laravel-echo-server.json (в корне проекта) - примерное правильное содержание настроек - там разве что можно прописать только authHost, если не прописан

## Серверная часть

1. php artisan make:event MessagePushed (ну или любое другое название для класса)
2. php artisan make:controller MessageController
3. смотри код этих файлов
4. файл routes/channel.php

```php
Broadcast::channel('messs', function ($user) {
    return (int) $user->id === (int) Auth::id();
});
```
5. routes/web.php

```php
Route::get('/', 'MessageController@index');

Route::post('/message', 'MessageController@messageSent');
```

6. не забудь php artisan make:auth

##Ну, думаю, все.
Если ничего не забыл