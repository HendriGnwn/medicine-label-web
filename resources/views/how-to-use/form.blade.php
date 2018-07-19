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
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('id_barang_satuan_kecil') ? 'has-error' : ''}}">
            {!! Form::label('id_barang_satuan_kecil', 'Barang Satuan Kecil*') !!}
            {!! Form::select('id_barang_satuan_kecil', [''=>'Pilih'] + \App\MmItemSmall::pluck('nama_satuan_kecil', 'id_barang_satuan_kecil')->toArray(), null, ['class' => 'form-control select2', 'required'=>true]) !!}
            {!! $errors->first('id_barang_satuan_kecil', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('nama') ? 'has-error' : ''}}">
            {!! Form::label('nama', 'Nama*') !!}
            {!! Form::text('nama', null, ['class' => 'form-control', 'required'=>true]) !!}
            {!! $errors->first('nama', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
{!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
<button class="btn btn-default" type="reset"><i class="pg-close"></i> Clear</button>