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
        <div class="col-md-6 mx-auto mb-4">
            <div class="card">
                <div class="card-body">
                    <img class="img-profile mr-3"
                         src="../profilePictures/{{ $user->profilePicture }}" alt="profile picture">
                    <div class="d-inline-block align-middle">
                        <p class="m-1 h5">{{ $user->name }}</p>
                        <p class="m-1 text-primary">{{ '@'.$user->tag }}</p>
                    </div>
                    <div class="mt-3">
                        <div class="">
                            <a href="/user/{{ $user->tag }}/following"
                               class="mb-0 mr-3 d-inline-block">Follows {{ $following }}</a>
                            <a href="/user/{{ $user->tag }}/followers"
                               class="mb-0 d-inline-block">Followers {{ $followers }}</a>
                        </div>
                        <div class="btn-group my-1">
                        @auth
                            @if($possibleFollow)
                                <form class="mt-2 float-right" action="/user/{{ $user->tag }}" method="post">
                                    @csrf
                                    @method('patch')
                                    <button class="btn btn-outline-dark mr-3" type="submit">Follow</button>
                                </form>
                            @endif
                            @if($possibleUnFollow)
                                <form class="mt-2 float-right" action="/user/{{ $user->tag }}/unfollow" method="post">
                                    @csrf
                                    @method('patch')
                                    <button class="btn btn-dark mr-3" type="submit">Unfollow</button>
                                </form>
                            @endif
                            @if($possibleBan)
                                <form class="mt-2 float-right" action="/user/{{ $user->tag }}/ban" method="post">
                                    @csrf
                                    @method('patch')
                                    <button class="btn btn-outline-danger mr-3" type="submit">Ban</button>
                                </form>
                            @endif
                            @if($possibleUnBan)
                                <form class="mt-2 float-right" action="/user/{{ $user->tag }}/unban" method="post">
                                    @csrf
                                    @method('patch')
                                    <button class="btn btn-danger mr-3" type="submit">Unban</button>
                                </form>
                            @endif
                            @if($possibleTurnOnNotifications)
                                <form class="mt-2 float-right" action="/user/{{ $user->tag }}/notifications-on"
                                      method="post">
                                    @csrf
                                    <button class="btn btn-outline-info mr-3">
                                        <i class="far fa-bell"></i>
                                    </button>
                                </form>
                            @endif
                                @if($possibleTurnOffNotifications)
                                    <form class="mt-2 float-right" action="/user/{{ $user->tag }}/notifications-off"
                                          method="post">
                                        @csrf
                                        @method('patch')
                                        <button class="btn btn-info mr-3">
                                            <i class="far fa-bell-slash"></i>
                                        </button>
                                    </form>
                                @endif
                        @endauth
                        </div>
                        <p class="m-0">Since {{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach($messages as $message)
        <div class="row mb-4">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <p class="m-1">{!! nl2br($message->content) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    {{ $messages->links() }}
@endsection