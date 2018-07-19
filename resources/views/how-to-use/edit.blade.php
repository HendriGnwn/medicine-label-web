@extends('layouts.admin')
@section('headerTitle', 'Update')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">

    {!! Form::model($model, [
            'method' => 'PATCH',
            'url' => route('how-to-use.update', ['id'=>$model->id_aturan_pakai]),
            'files' => true,
            'id' => 'formValidate',
        ]) !!}

        @include ('how-to-use.form', ['submitButtonText' => 'Update'])

	{!! Form::close() !!}
    </div>
</div>
@endsection