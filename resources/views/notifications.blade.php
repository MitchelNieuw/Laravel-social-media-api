@extends('layouts.app')

@section('content')
    @if(!$notifications->isEmpty())
        <div class="row">
            <div class="col-md-3 mx-auto">
                <form action="/notifications/delete" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">Delete All</button>
                </form>
            </div>
        </div>
        @foreach($notifications as $notification)
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card shadow mb-3">
                    <div class="card-body">
                        @if($notification->read_at === null)
                            <p class="d-inline-block">Unread</p>
                        @endif
                        <p class="d-inline-block">{{ $notification->data['message'] }}</p>
                        <p class="d-inline-block">{{ $notification->data['messageId'] }}</p>
                        <a href="{{ $notification->data['link'] }}"
                           class="d-inline-block">{{ $notification->data['link'] }}</a>
                        <form action="/notifications/{{ $notification->id }}/delete" method="post">
                            @method('delete')
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="row">
            <div class="col-md-6 mx-auto text-center mt-5">
                <p>No notifications at the moment</p>
            </div>
        </div>
    @endif
@endsection