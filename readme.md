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

```javascript
L A R A V E L  E C H O  S E R V E R

version 1.3.6

⚠ Starting server in DEV mode...

✔  Running at localhost on port 6001
✔  Channels are ready.
✔  Listening for http events...
✔  Listening for redis events...

Server ready!
```

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
```html
<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Laravel
                </div>
                <div class="chat">
                    <ul id="chat_list" style="text-align: left">
                        @foreach($messages as $message)
                        <li>{{ $message->id }} {{ $message->content }}</li>
                        @endforeach
                    </ul>
                    <div class="user" id="user_field"></div>
                </div>
                <div class="form">
                    <form id="fff" method="post">
                        @csrf
                        <input type="text" name="message" id="message">
                        <input id="submit" type="submit" name="submit" value="Send">
                    </form>
                </div>

                <div class="links">
                    <a href="https://laravel.com/docs">Documentation</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </div>
            </div>
        </div>
        
        <script src="js/app.js"></script>
        
        <script type="text/javascript">
        $(document).ready(function() {
    
            //объявляем переменные
            const form = $('#fff'); //наша форма отправки
            const list = $('#chat_list'); //список для вывода сообщений чата
            const txtfld = $('#message'); //текстовое поле для ввода сообщений 
            const sbm = $('#submit'); //кнопка
            
            //это пока не нужно - это для приватного канала, но как заставить работать приват, я пока не знаю
            let user = {!! json_encode(Auth::user()); !!};
            let user_field = $('#user_field');
            
            const channel = window.Echo.private('messs'); // ну и сам канал в виде переменной

            form.on('submit', function(e) {

                e.preventDefault(); //не даем сабмиттить форму по дефолту
                
                let mess = form.find("#message").val(); //записываем текст введенный в текстовое поле формы в переменную
                sbm.attr('disabled', 'true'); //"обезвреживаем" кнопку сабмита, шоб дурак не нажимал много раз
                txtfld.val('Sending...'); //внутри текстового поля пишем Sending... шоб дурак видел, что что-то происходит
                
                //просто отправляем наше сообщение на сервер
                axios.post('/message', {
                    message: mess
                  })
                  .then(function (response) {
                    // console.log(response.data);
                  })
                  .catch(function (error) {
                    // console.log(error);
                  });
            });

            //этот блок считывает номер сообщения из присланного массива со стороны сервера и записывем в переменную самый последний номер (можно этого не делать)
            let laravel_messages = {!! json_encode($messages) !!};
            let max_message_id = Math.max.apply(
                Math, $.map(laravel_messages, function(o) {
                return o.id;
            }));

            // $.each(laravel_messages, function(index, el) {
            //     // console.log(el);                
            // });
            // 
            
            // console.log("channel", channel);

            //слушаем канал и выводим не более 10 сообщений(это прописано в MessageController)
            channel
                .listen('.message.sent', function(e) {
                    list.append('<li>' + (++max_message_id) + ' ' + e.chatMessage.content + '</li>'); //добавляем новое сообщение
                    sbm.removeAttr('disabled'); //"освобождаем" кнопку сабмита
                    txtfld.val(''); //в текстовом поле чистота:)
                    list.find('li').first().remove(); //из списка убираем сообщение, которое стоит в самом верху
            });

            

            // txtfld.on('keydown', function(e) {
            //     channel.whisper('typ', {
            //         //do whatever
            //     });
            // });


            // channel.listenForWhisper('typ', (e) => {
            //     user_field.text(user.name + ' is typing...');
                
            //     setTimeout(function() {
            //       user_field.text('');
            //     }, 1500);
            // });
        });

        </script>

    </body>
</html>

```
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