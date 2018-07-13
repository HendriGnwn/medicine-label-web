@extends('layouts.admin')
@section('headerTitle', 'Tambah Apoteker')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        {!! Form::model($model, [
            'method' => 'POST',
            'url' => route('pharmacist.store'),
            'files' => true,
            'id' => 'formValidate',
        ]) !!}
        @include('pharmacist.form', compact('model'))
        {!! Form::close() !!}
    </div>
</div>
@endsection