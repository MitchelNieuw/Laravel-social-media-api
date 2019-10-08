@extends('layouts.app')

@section('content')
    @if($errors->any())
        <div class="row">
            <div class="col-md-6 mx-auto text-center alert alert-dismissible alert-danger">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                @foreach($errors->all() as $error)
                    <p class="text-danger">&dot;{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif
    @if(count($followers) > 0)
        @foreach($followers as $follow)
            <div class="row">
                <div class="col-md-6 mx-auto mb-3">
                    <div class="card shadow">
                        <div class="card-body">
                            @if(strtolower($follow->user->tag) === strtolower($userTag))
                                <img class="img-profile img-small mr-2" src="{{ asset('profilePictures') . '/' .
                                $follow->following->profilePicture }}" alt="{{ $follow->following->tag }}">
                                <p class="m-0 d-inline-block">{{ $follow->following->name }}</p>
                                <p class="m-0 d-inline-block text-primary">{{ '@'.$follow->following->tag }}</p>
                            @endif
                            @if(strtolower($follow->following->tag) === strtolower($userTag))
                                <img class="img-profile img-small mr-2" src="{{ asset('profilePictures') . '/' .
                                $follow->user->profilePicture }}" alt="{{ $follow->user->tag }}">
                                <p class="m-0 d-inline-block">{{ $follow->user->name }}</p>
                                <p class="m-0 d-inline-block text-primary">{{ '@'.$follow->user->tag }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="row">
            <div class="col-md-6 mx-auto text-center mt-5">
                <p>No followers found</p>
            </div>
        </div>
    @endif
@endsection
