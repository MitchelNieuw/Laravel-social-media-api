@extends('layouts.app')

@section('content')
    @foreach($messages as $message)
        <div class="row">
            <div class="col-md-6 mx-auto m-3">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="border-bottom pb-3">
                            <img class="img-profile img-small d-inline-block mr-2"
                                 src="{{ '../profilePictures/' . $message->user->profilePicture }}" alt="profile picture">
                            <a class="d-inline-block font-weight-bolder text-dark"
                               href="/user/{{ $message->user->name }}">{{ $message->user->name }}</a>
                            <p class="mb-0 text-primary d-inline-block">{{ '@'.$message->user->tag }}</p>
                        </div>
                        <p class="mt-3 px-3">{!! nl2br($message->content) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
