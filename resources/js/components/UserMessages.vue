<template>
    <div>
        <div v-if="messages" v-for="(message, index) in messages" :key="index" class="row">
            <div class="col-md-6 mx-auto m-2">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-0 d-inline-block" v-text="message.content"></p>
                        <form class="d-inline-block float-right" @submit.prevent="deleteMessage(message, index)">
                            <button class="close text-danger pt-0" type="submit">
                                <span>&times;</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- TODO change that the new message is displayed ont top -->
<!-- TODO make vue frontend with typescript-->

<script>
    import { updateBus } from "../event";
    export default {
        name: 'UserMessages',
        data() {
            return {
                messages: [],
                response: '',
                loading: false,
            };
        },
        methods: {
            removeFromMessages(index) {
                this.messages.splice(index, 1);
            },
            updateMessages(item) {
                this.messages.push(item);
            },
            async getMessages() {
                await axios.get('https://localhost/api/v1/user/messages', {
                    headers: {
                        Authorization: 'Bearer ' + JWTToken,
                        Accept: 'application/json',
                    },
                }).then((response) => {
                    this.messages = response.data.data;
                }).catch((error) => {
                    console.log(error);
                });
            },
            deleteMessage(message, index) {
                axios.delete('https://localhost/api/v1/message/' + message.id + '/delete', {
                    headers: {
                        Authorization: 'Bearer ' + JWTToken,
                        Accept: 'application/json',
                        'Content-type': 'application/x-www-form-urlencoded',
                    },
                    _method: 'delete',
                }).then(() => {
                    this.removeFromMessages(index);
                }).catch((error) => {
                    console.log(error);
                });
            }
        },
        created() {
            this.getMessages();
            updateBus.$on('update-messages', data => this.messages.unshift(data));
        },
    };
</script>
