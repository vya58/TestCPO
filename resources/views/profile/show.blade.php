@extends('layouts.admin')

@section('content')

<div class="card">
    <div class="card-header border-0">
        <div class="d-flex justify-content-between">
            <h2 class="card-title text-bold text-lg">{{ $user->first_name . ' ' . $user->last_name }}</h2>

        </div>
    </div>
    <div class="card-body">
        <div class="">
            <p class="">
                <span class="text-bold">{{ __('Логин: ') }}</span>
                <span class="">{{ $user->login }}</span>
            </p>
        </div>
        <div class="">
            <p class="">
                <span class="text-bold">{{ __('День рождения: ') }}</span>
                <span class="">{{ $user->birthday }}</span>
            </p>
        </div>
        <div class="">
            <p class="">
                <span class="text-bold">{{ __('Участник событий: ') }}</span>
                @foreach($userEvents as $event)
            <div>
                <a href="{{ route('event.show', $event->id) }}">{{ $event->title }}</a>
            </div>
            @endforeach
            </p>
        </div>
    </div>
</div>

@endsection
