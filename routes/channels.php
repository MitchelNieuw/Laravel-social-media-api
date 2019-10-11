<?php

Broadcast::channel('App.User.{id}', static function ($user, $id) {
    return (int) $user->id === (int) $id;
});
