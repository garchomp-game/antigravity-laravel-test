@extends('layouts.tenant')

@section('title', 'Ticket Details')

@section('content')
    @php
        $ticket = \App\Models\Ticket::findOrFail($ticketId);
    @endphp
    <livewire:tickets.show :ticket="$ticket" />
@endsection

