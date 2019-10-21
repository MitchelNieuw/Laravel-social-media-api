<template>
    <div class="col-md-6 mb-2">
        <div class="card">
            <div class="card-body">
                <form @submit.prevent="storeMessage()">
                    <div class="form-group">
                        <label for="content">Message</label>
                        <textarea name="content" class="form-control" id="content" v-model="content"></textarea>
                    </div>
                    <button class="btn btn-outline-dark float-right" type="submit">Place</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import { updateBus } from '../event';
    export default {
        name: 'CreateMessage',
        data() {
            return {
                content: '',
            };
        },
        methods: {
            storeMessage() {
                axios.post('https://localhost/api/v1/user/messages/store', { content: this.content, }, {
                    headers: {
                        Authorization: 'Bearer ' + JWTToken,
                        Accept: 'application/json',
                    },
                }).then((response) => {
                    updateBus.$emit('update-messages', response.data);
                    this.content = '';
                }).catch((error) => {
                    console.log(error)
                });
            },
        },
    };
</script>
