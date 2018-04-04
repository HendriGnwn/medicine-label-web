@extends('layouts.admin')
@section('headerTitle', 'Create Manually Input')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        {!! Form::open(['url' => route('manually.store'), 'id' => 'formValidate', 'files' => true]) !!}
        @include('manually.form')
        {!! Form::close() !!}
    </div>
</div>
@endsection