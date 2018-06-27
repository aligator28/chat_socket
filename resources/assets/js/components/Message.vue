<template>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md col-md-offset-2">
				<div class="panel panel-default" v-for="message in messages">
					<div class="panel-heading">{{ message.user.name }}</div>
					<div class="panel-body">
						<p>{{ message.content }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		// props: ['messages'],
		data() { 
			return {
				messages: []
    		}
    	},

		created() {
			this.fetchMessages();

			EventBus.$on('messagesent', (e) => {
				this.addMessage(e.message);
			});
			
			const channel = window.Echo.private('messs');
			channel
            	.listen('.message.sent', (e) => {
                // let classname = 'other_message';

                // if (e.user.name == laravel_user.name) {
                //     classname = 'author_message';
                // }

            		console.log("e", e);
            	this.fetchMessages();
     //            this.messages.push({ 
     //            	content: e.chatMessage.content,
					// user: e.user
     //            }); 

                // добавляем новое сообщение
                // sbm.removeAttr('disabled'); //"освобождаем" кнопку сабмита
                // txtfld.val(''); //в текстовом поле чистота:)
                // list.find('li').first().remove(); //из списка убираем сообщение, которое стоит в самом верху
        	});
		},

		methods: {
			fetchMessages() {

				axios.get('/messages')
		          .then( (response) => {
		            this.messages = response.data;
		            // console.log("this.messages", this.messages);
		          })
		          .catch(function (error) {
		            console.log(error);
		          });			
			},

			addMessage(message) {
				// let arr = Object.keys(this.messages).map(i => this.messages[i]);

				// arr.push(message);
				// let m = Object.setPrototypeOf(arr, Object.prototype);
				
				// this.messages = m;
				

				axios.post('/messages', {message: message})
		          .then( (response) => {
					this.fetchMessages();
		            // console.log(response.data);
		          })
		          .catch(function (error) {
		            // console.log(error);
		          });
			}
		}
	}
</script>