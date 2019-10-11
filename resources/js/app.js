require('./bootstrap');
window.Pusher = require('pusher-js');
window.$ = window.jQuery = require('jquery');
import Echo from 'laravel-echo';

if (typeof JWTToken !== 'undefined') {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: process.env.MIX_PUSHER_APP_KEY,
        cluster: 'eu',
        forceTLS: true,
        encrypted: true,
        authEndpoint: 'https://localhost/broadcasting/auth',
        auth: {
            headers: {
                Authorization: `Bearer ${JWTToken}`,
                Accept: 'application/json',
            }
        }
    });
}

$(document).ready(function() {
    if (typeof userId !== 'undefined') {
        window.Echo.private(`App.User.${userId}`)
            .notification((notification) => {
                console.log(notification);
                $('#notificationsMenu').append(
                    '<li class="dropdown-header">' +
                        '<p>' + notification.message + '</p>' +
                        '<a href="'+ notification.link +'">'+ notification.link +'</a>' +
                        '<form action="/notifications/'+ notification.notification_id +'/delete" method="post">' +
                            '<input type="hidden" name="_method" value="delete">' +
                            '<button type="submit" class="btn btn-danger">Delete</button>' +
                        '</form>' +
                    '</li>'
                );
            });
    }
});
