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
            .chat {
                width: 80%;
                position: relative;
                left: 50%;
                transform: translateX(-50%);
                margin-bottom: 200px;
            }
            .chat_list {
                border: 2px dashed black;
                border-radius: 10px;
                list-style: none;
                text-align: left;
                padding: 20px;
                position: relative;
            }
            .submit {
                display: block;
                position: relative;
                left: 50%;
                transform: translateX(-50%);
                width: 25%;
                height: 50px;
                background-color: yellowgreen;
                border: 0;
                border-radius: 10px;
                color: white;
                font-size: 1.5em;
                cursor: pointer;
            }
            .user {
                height: 50px;
                color: #B61B1B;
                font-weight: bold;
            }
            .author {
                background-color: #F9F8BB;
                margin-left: 5%;
            }
            .time {
                margin-left: 2px;
                background-color: #F6F0F0;
            }
            .author, .time {
                position: relative;
                display: inline-block;
                vertical-align: sub;
                padding: 2px 5px;
                border-radius: 2px;
                font-size: 0.8em;
            }
            .author_message {
                background-color: #D2FAFF;
            }
            .other_message {
                background-color: #BEFEB7;
                text-align: right;
            }
            .author_message, .other_message {
                padding: 5px;
                border-radius: 10px;
                margin: 2% 0;
            }
        </style>
    </head>
    <body>
        <div>
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
                    <ul id="chat_list" class="chat_list">
                        @foreach($messages as $message)
                            @if ($message->user->name === Auth::user()->name)
                            <?php $classname = 'author_message' ?>
                            @else
                            <?php $classname = 'other_message' ?>
                            @endif
                        <li class="{{$classname}}">{{ $message->content }}
                        <span class="author">{{ $message->user->name }} </span><span class="time">{{ $message->created_at->format('H:i:s') }}</span></li>
                        @endforeach
                    </ul>
                    <div class="user" id="user_field"></div>
                    <div class="form">
                        <form id="fff" method="post">
                            @csrf
                            <textarea cols="50" rows="6" name="message" id="message"></textarea>
                            <input class="submit" id="submit" type="submit" name="submit" value="Send">
                        </form>
                    </div>
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
            let laravel_user = {!! json_encode(Auth::user()); !!};
            
            const channel = window.Echo.private('messs'); // ну и сам канал в виде переменной

            form.on('submit', function(e) {

                e.preventDefault(); //не даем сабмиттить форму по дефолту
                
                let mess = form.find("#message").val(); //записываем текст введенный в текстовое поле формы в переменную
                mess = removeTags(mess);
                
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
            // let laravel_messages = {!! json_encode($messages) !!};
            // let max_message_id = Math.max.apply(
            //     Math, $.map(laravel_messages, function(o) {
            //     return o.id;
            // }));


            //слушаем канал и выводим не более 10 сообщений(это прописано в MessageController)
            channel
                .listen('.message.sent', function(e) {
                    let classname = 'other_message';

                    if (e.user.name == laravel_user.name) {
                        classname = 'author_message';
                    }

                    list.append('<li class="' + classname + '">' + e.chatMessage.content + '<span class="author">' + e.user.name + '</span></li>'); //добавляем новое сообщение
                    sbm.removeAttr('disabled'); //"освобождаем" кнопку сабмита
                    txtfld.val(''); //в текстовом поле чистота:)
                    list.find('li').first().remove(); //из списка убираем сообщение, которое стоит в самом верху
            });

            // пользовательские события (показываем, что оппонент печатает)
            let user_field = $('#user_field'); //просто поле (div) куда будем писать сообщение
            
            // делаем как-бы оповещение (шепот - whisper)
            txtfld.on('keydown', function(e) {
                channel.whisper('typ', {
                    user: laravel_user //берем из бекенда залогиненого пользователя, который печатает
                });
            });

            // а тут слушаем оповещение о пользовательском событии (печатает гад!)
            channel.listenForWhisper('typ', (e) => {
                user_field.text(e.user.name + ' is typing...'); //вписываю внутрь дива имя печатающего и текст
                
                setTimeout(function() {
                  user_field.text(''); // чтобы вечно эта надпись не висела, через 1,5 сек. очищаю надпись ... is typing...
                }, 1500);
            });
        });



        function removeTags(string, array) {
          return array ? string.split("<").filter(function(val) { 
            return f(array, val); }).map(function(val){ 
                return f(array, val); }).join("") : string.split("<").map(function(d) { return d.split(">").pop(); }).join("");
          function f(array, value){
            return array.map(function(d){ return value.includes(d + ">"); }).indexOf(true) != -1 ? "<" + value : value.split(">")[1];
          }
        }
        // КАК ПОЛЬЗОВАТЬСЯ removeTags()
        // var x = "<span><i>Hello</i> <b>world</b>!</span>";
        // console.log(removeTags(x)); // Hello world!
        // console.log(removeTags(x, ["span", "i"])); // <span><i>Hello</i> world!</span>
        

        </script>

    </body>
</html>
