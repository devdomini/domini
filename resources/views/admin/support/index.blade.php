@extends('admin.layout')

@section('title', 'Messagerie support')
@section('page-title', 'Messagerie support')

@section('content')
@if($errors->any())
    <div style="background:#f8d7da;color:#721c24;padding:0.85rem 1rem;border-radius:8px;margin-bottom:1rem;">
        {{ $errors->first() }}
    </div>
@endif

@include('support.partials.messenger', [
    'threads' => $threads,
    'activeThread' => $activeThread ?? null,
    'unreadTotal' => $unreadTotal,
    'routePrefix' => 'admin.support',
    'accent' => '#FF0000',
    'staffBubble' => '#111111',
    'subtitle' => 'Clients et livreurs (app mobile)',
])
@endsection

@section('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="{{ asset('js/support-messenger.js') }}?v=20260712b"></script>
@endsection
