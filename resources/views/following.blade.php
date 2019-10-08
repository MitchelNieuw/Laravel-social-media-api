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
    @if(count($following) > 0)
        @foreach($following as $follow)
            <div class="row">
                <div class="col-md-6 mx-auto mb-3">
                    <div class="card shadow">
                        <div class="card-body">
                            @if(strtolower($follow->user->tag) === strtolower($userTag))
                                <img class="img-profile img-small mr-2" src="{{ asset('profilePictures') . '/' .
                                $follow->following->profilePicture }}" alt="{{ $follow->following->tag }}">
                                <p class="m-0 d-inline-block">{{ $follow->following->name }}</p>
                                <p class="m-0 d-inline-block">{{ '@'.$follow->following->tag }}</p>
                            @endif
                            @if(strtolower($follow->following->tag) === strtolower($userTag))
                                    <img class="img-profile img-small mr-2" src="{{ asset('profilePictures') . '/' .
                                $follow->user->profilePicture }}" alt="{{ $follow->following->tag }}">
                                <p class="m-0 d-inline-block">{{ $follow->user->name }}</p>
                                <p class="m-0 d-inline-block">{{ '@'.$follow->user->tag }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="row">
            <div class="col-md-6 mx-auto text-center mt-5">
                <p>No following users found</p>
            </div>
        </div>
    @endif
@endsection
