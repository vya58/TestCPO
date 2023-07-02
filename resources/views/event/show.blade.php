@extends('layouts.admin')

@section('content')

<div>
    <h2>{{$event->title}}</h2>
    <div>{{$event->description}}</div>
</div>

<h2>Участники</h2>
@foreach($members as $participant)
<div>
    <a href="{{ route('profile.show', $participant->id) }}">{{ $participant->first_name . ' ' . $participant->last_name }}</a>
</div>
@endforeach

@if ($event->user_id !== Auth::id())

<div>
    <x-primary-button class="ml-4" style="background-color:lightgray">
        <a href="{{ route('event.create', $event->id) }}"> @if ($myEvents->contains($event->id))
            {{ __('Отказаться от участия') }}
            @else
            {{ __('Принять участие') }}
            @endif</a>

    </x-primary-button>
</div>

@endif

@endsection
