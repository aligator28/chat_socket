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

При загрузке страницы консоль Echo выдаст такое:

```javascript
[20:39:39] - OD-_Bvqa_ozj2mynAAAA authenticated for: private-messs
[20:39:39] - OD-_Bvqa_ozj2mynAAAA joined channel: private-messs
```
При успешной отправке сообщения добавиться такое:

```javascript
Channel: private-messs
Event: message.sent
CHANNEL private-messs
```


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

```json
{
	"authHost": "http://chat",
	"authEndpoint": "/broadcasting/auth",
	"clients": [
		{
			"appId": "68861d4f71b2c38d",
			"key": "6fec3c0363f2fd030c1c2e4057fa409a"
		}
	],
	"database": "redis",
	"databaseConfig": {
		"redis": {
			"port": "6379",
			"host": "127.0.0.1"
		},
		"sqlite": {
			"databasePath": "/database/laravel-echo-server.sqlite"
		}
	},
	"devMode": true,
	"host": null,
	"port": "6001",
	"protocol": "http",
	"socketio": {},
	"sslCertPath": "",
	"sslKeyPath": "",
	"sslCertChainPath": "",
	"sslPassphrase": "",
	"apiOriginAllow": {
		"allowCors": true,
		"allowOrigin": "http://chat:80",
		"allowMethods": "GET, POST",
		"allowHeaders": "Origin, Content-Type, X-Auth-Token, X-Requested-With, Accept, Authorization, X-CSRF-TOKEN, X-Socket-Id"
	}
}
```
## Серверная часть

1. php artisan make:event MessagePushed (ну или любое другое название для класса)
2. php artisan make:controller MessageController
3. php artisan make:model Message -m (модель с миграцией см. мою миграцию, также можно создать связи между Message и User см. соответсвующие модели)
4. смотри код этих файлов
```php
<?php

namespace App\Events;

use App\Events\Event;
use App\Message;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class MessagePushed extends Event implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $chatMessage;
    public $user;

    public function __construct(Message $chatMessage, User $user)//, User $user
    {
        $this->chatMessage = $chatMessage;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('messs');
    }

    public function broadcastAs() {
        return 'message.sent';
    }
}

```

```php
<?php

namespace App\Http\Controllers;

use App\Events\MessagePushed;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Auth;

class MessageController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
	}

	public function index() {
		$messages = Message::orderBy('created_at','desc')
			->take(10)->get()->reverse();

		return view('welcome', compact('messages', $messages) );
	}

    public function messageSent(Request $req) {
    	$mess =  $req->message;
		$user = Auth::id() ? Auth::id() : '1';

	    $message = new Message();
	    $message->user_id = $user;
	    $message->content = $mess;
	    $message->save();

	    // broadcast(new MessagePushed($message))->toOthers();//, $user
	    event( new MessagePushed( $message, Auth::user() ) );//Auth::user()
	    return $message;
    }
}

```

5. файл routes/channel.php

```php
Broadcast::channel('messs', function ($user) {
    return (int) $user->id === (int) Auth::id();
});
```
6. routes/web.php

```php
Route::get('/', 'MessageController@index');

Route::post('/message', 'MessageController@messageSent');
```

7. не забудь php artisan make:auth

##Ну, думаю, все.
Если ничего не забыл