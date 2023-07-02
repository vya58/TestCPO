@extends('layouts.admin')

@section('content')

<div>
    <p>Все события</p>
</div>

@foreach($allEvents as $event)
<div>
    <a href="{{ route('event.show', $event->id) }}">{{$event->title}}</a>
</div>
@endforeach

<div>
    {{$allEvents->links()}}
</div>

@endsection
