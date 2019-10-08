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
                <p class="d-inline-block m-0 mr-3">Following {{ $followingCount }}</p>
                <p class="d-inline-block m-0">Followers {{ $followersCount }}</p>
                <p class="">Since {{ auth()->user()->created_at->format('Y M d') }}</p>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="card">
                <div class="card-body">
                    <form action="/message/store" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="content">Message</label>
                            <textarea name="content" class="form-control" id="content"></textarea>
                        </div>
                        <button class="btn btn-outline-dark float-right">Post</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if(count($messages) > 0)
        @foreach($messages as $message)
            <div class="row">
                <div class="col-md-6 mx-auto m-2">
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-0">{!! nl2br($message->content) !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        {{ $messages->links() }}
    @endif
@endsection