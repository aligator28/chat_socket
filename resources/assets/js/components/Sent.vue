<template>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-footer">
						<form @submit.prevent.keyup="sent">
							<div class="form-group">
								<input type="text" class="form-control" v-model="message.message" @keyup="typing">
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-primary">Send</button>
							</div>
						</form>
					</div>
					<div class="panel-body">
						<p>{{ message.message }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>	
</template>

<script>
	export default {
		props: ['user'],

		data() {
			return {
				message: {
					message: '',
					user: this.user
				},
				channel: window.Echo.private('messs')
			}
		},

		created() {

            // а тут слушаем оповещение о пользовательском событии (печатает гад!)
            this.channel.listenForWhisper('typ', (e) => {
                console.log('typing...', e.user);
                
                // setTimeout(function() {
                //   user_field.text(''); // чтобы вечно эта надпись не висела, через 1,5 сек. очищаю надпись ... is typing...
                // }, 1500);
            });

		},

		methods: {
			sent() {
				EventBus.$emit('messagesent', this.message);
	
				this.message = {}
			},
			typing() {
				// EventBus.$emit('typing', this.user);

                this.channel.whisper('typ', {
                    user: this.user
                });
			}
		}
	}
</script>