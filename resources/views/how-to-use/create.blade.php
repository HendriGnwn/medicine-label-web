@extends('layouts.admin')
@section('headerTitle', 'Tambah Aturan Pakai')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        {!! Form::model($model, [
            'method' => 'POST',
            'url' => route('how-to-use.store'),
            'files' => true,
            'id' => 'formValidate',
        ]) !!}
        @include('how-to-use.form', compact('model'))
        {!! Form::close() !!}
    </div>
</div>
@endsection