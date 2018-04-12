@extends('layouts.admin')
@section('headerTitle', 'Update Setting')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">

    {!! Form::model($model, [
            'method' => 'PATCH',
            'url' => route('setting.index'),
            'files' => true,
            'id' => 'formValidate',
        ]) !!}

        @include ('setting.form', ['submitButtonText' => 'Update'])

	{!! Form::close() !!}
    </div>
</div>
@endsection