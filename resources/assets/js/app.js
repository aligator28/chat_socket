
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');


import Echo from "laravel-echo"
window.io = require('socket.io-client');

// Have this in case you stop running your laravel echo server
if (typeof io !== 'undefined') {
  
  let token = document.head.querySelector('meta[name="csrf-token"]');

  window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6001',
    csrfToken: token.content
  });
}


window.Vue = require('vue');

// *
//  * Next, we will create a fresh Vue application instance and attach it to
//  * the page. Then, you may begin adding components to this application
//  * or customize the JavaScript scaffolding to fit your unique needs.
 
window.EventBus = new Vue();

Vue.component('message', require('./components/Message.vue'));
Vue.component('sent-message', require('./components/Sent.vue'));


const app = new Vue({
    el: '#app'

    // data: {
   	// 	messagesss: []
    // },
    // mounted() {
		// this.fetchMessages();

		// const channel = window.Echo.private('messs');

		// channel
            // .listen('.message.sent', function(e) {
                // let classname = 'other_message';

                // if (e.user.name == laravel_user.name) {
                //     classname = 'author_message';
                // }

     //            this.messagesss.push({ 
     //            	message: e.message.content,
					// user: e.user
     //            }); 

                //добавляем новое сообщение
                // sbm.removeAttr('disabled'); //"освобождаем" кнопку сабмита
                // txtfld.val(''); //в текстовом поле чистота:)
                // list.find('li').first().remove(); //из списка убираем сообщение, которое стоит в самом верху
        // });
    // },
    // methods: {
		// addMessage(message) {
		// 	this.messagesss.push(message);

		// 	axios.post('/messages', message)
	 //          .then(function (response) {
	 //            // console.log(response.data);
	 //          })
	 //          .catch(function (error) {
	 //            // console.log(error);
	 //          });
		// },

		// fetchMessages() {
		// 	axios.get('/messages')
	 //          .then(function (response) {
	 //            this.messagesss = response.data;
	 //          })
	 //          .catch(function (error) {
	 //            // console.log(error);
	 //          });			
		// }
    // }
});
// 
// import Pusher from "pusher-js"
// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: '27ceda8f6b6960202ec3',
//     cluster: 'eu',
//     encrypted: true
// });



