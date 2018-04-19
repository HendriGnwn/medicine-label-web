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
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('name') ? 'has-error' : ''}}">
            {!! Form::label('name', 'Nama') !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('username') ? 'has-error' : ''}}">
            {!! Form::label('username', 'Username') !!}
            {!! Form::text('username', null, ['class' => 'form-control']) !!}
            {!! $errors->first('username', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('password') ? 'has-error' : ''}}">
            {!! Form::label('password', 'Password') !!}
            {!! Form::password('password', ['class' => 'form-control']) !!}
            {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    @php
    $roles = \App\User::roleLabels();
    unset($roles[\App\User::ROLE_DOCTOR]);
    @endphp
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('role') ? 'has-error' : ''}}">
            {!! Form::label('role', 'Role') !!}
            {!! Form::select('role', [''=>'Pilih'] + $roles, null, ['class' => 'form-control select2', 'required'=>true]) !!}
            {!! $errors->first('role', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

<div class="panel panel-default" id="pharmacist">
    <div class="panel-heading">
        Detail Apoteker (Untuk keperluan Header Print Label)
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('apoteker_name') ? 'has-error' : ''}}">
                    {!! Form::label('apoteker_name', 'Apoteker') !!}
                    {!! Form::text('apoteker_name', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('sikapoteker_name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('apoteker_sik') ? 'has-error' : ''}}">
                    {!! Form::label('apoteker_sik', 'Apoteker SIK') !!}
                    {!! Form::text('apoteker_sik', null, ['class' => 'form-control']) !!}
                    {!! $errors->first('apoteker_sik', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
</div>
        
{!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
<button class="btn btn-default" type="reset"><i class="pg-close"></i> Clear</button>

@push('script')
<script>
    $('.select2').select2({
        width: 'resolve'
    });
    $("#role").change(function() {
        if ($(this).val() == '{{\App\User::ROLE_PHARMACIST}}') {
            $('#pharmacist').show();
        } else {
            $('#pharmacist').hide();
        }
    });
    $("#role").change();
</script>
@endpush