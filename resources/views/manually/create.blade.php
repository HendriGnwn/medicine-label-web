@extends('layouts.admin')
@section('headerTitle', 'Tambah Label Obat')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        {!! Form::model($model, [
            'method' => 'POST',
            'url' => route('manually.store'),
            'files' => true,
            'id' => 'formValidate',
        ]) !!}
        @include('manually.form', compact('model'))
        {!! Form::close() !!}
    </div>
</div>
@endsection