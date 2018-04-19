@extends('layouts.admin')
@section('headerTitle', 'Tambah User')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        {!! Form::open(['url' => route('user.store'), 'id' => 'formValidate', 'files' => true]) !!}
        @include('user.form')
        {!! Form::close() !!}
    </div>
</div>
@endsection