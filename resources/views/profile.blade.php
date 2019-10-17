@extends('layouts.app')

@section('content')
    @if($errors->any())
        <div class="row">
            <div class="col-md-6 mx-auto text-center alert alert-dismissible alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                @foreach($errors->all() as $error)
                    <p class="text-danger">&ndash;&nbsp;{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-3">
            <img class="img-profile mr-3"
                 src="{{ 'profilePictures/' . auth()->user()->profilePicture }}" alt="profile picture">
            <div class="d-inline-block align-middle">
                <p class="h4 mb-0">{{ auth()->user()->name }}</p>
                <p class="h5 font-weight-normal text-primary mb-0">{{ '@'.auth()->user()->tag }}</p>
            </div>
            <div class="mt-3">
                <a href="/user/{{ auth()->user()->tag }}/following"
                   class="d-inline-block m-0 mr-3">Following {{ $followingCount }}</a>
                <a href="/user/{{ auth()->user()->tag }}/followers"
                   class="d-inline-block m-0">Followers {{ $followersCount }}</a>
                <p class="">Since {{ auth()->user()->created_at->format('Y M d') }}</p>
            </div>
        </div>
        <create-message></create-message>
    </div>
    <user-messages></user-messages>
@endsection