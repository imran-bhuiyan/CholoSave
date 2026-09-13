import './bootstrap';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'b75e948317535e911883',
    cluster: 'ap1',
    forceTLS: true,
    enabledTransports: ['ws', 'wss']
});
