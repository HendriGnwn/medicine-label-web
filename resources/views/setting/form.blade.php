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
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('address') ? 'has-error' : ''}}">
            {!! Form::label('address', 'Alamat Rumah Sakit') !!}
            {!! Form::text('address', null, ['class' => 'form-control']) !!}
            {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('apoteker') ? 'has-error' : ''}}">
            {!! Form::label('apoteker', 'Apoteker') !!}
            {!! Form::text('apoteker', null, ['class' => 'form-control']) !!}
            {!! $errors->first('apoteker', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('sik') ? 'has-error' : ''}}">
            {!! Form::label('sik', 'SIPA') !!}
            {!! Form::text('sik', null, ['class' => 'form-control']) !!}
            {!! $errors->first('sik', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
        
{!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
<button class="btn btn-default" type="reset"><i class="pg-close"></i> Clear</button>