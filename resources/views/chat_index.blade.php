@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Chat</div>

                <div class="card-body">
                    {{-- <message :messages="messagesss"></message> --}}
                    <message></message>
                    {{-- <sent-message v-on:messagesent="addMessage" :user="{{ Auth::user() }}"></sent-message> --}}
                    <sent-message :user="{{ Auth::user() }}"></sent-message>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
