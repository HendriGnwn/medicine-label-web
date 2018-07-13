@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row">
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('registered_id') ? 'has-error' : ''}}">
            {!! Form::label('name', 'Apoteker*') !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'required'=>true]) !!}
            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('doctor_id') ? 'has-error' : ''}}">
            {!! Form::label('sik', 'SIK*') !!}
            {!! Form::text('sik', null, ['class' => 'form-control', 'required'=>true]) !!}
            {!! $errors->first('sik', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
{!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
<button class="btn btn-default" type="reset"><i class="pg-close"></i> Clear</button>