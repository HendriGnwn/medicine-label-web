@extends('layouts.admin')
@section('headerTitle', 'Update')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">

    {!! Form::model($model, [
            'method' => 'PATCH',
            'url' => route('manually.update', ['id'=>$model->id]),
            'files' => true,
            'id' => 'formValidate',
        ]) !!}

        @include ('manually.form-edit', ['submitButtonText' => 'Update'])

	{!! Form::close() !!}
    </div>
</div>
@endsection