@extends('commercial.layout')

@section('title', 'Messagerie clients')
@section('page-title', 'Messagerie clients')

@section('content')
@if($errors->any())
    <div class="commercial-alert" style="background:#f8d7da;color:#721c24;">
        {{ $errors->first() }}
    </div>
@endif

@include('support.partials.messenger', [
    'threads' => $threads,
    'activeThread' => $activeThread ?? null,
    'unreadTotal' => $unreadTotal,
    'routePrefix' => 'commercial.support',
    'accent' => '#FF0000',
    'staffBubble' => '#111111',
    'subtitle' => 'Employés de vos entreprises',
])
@endsection

@section('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="{{ asset('js/support-messenger.js') }}?v=20260712b"></script>
@endsection
