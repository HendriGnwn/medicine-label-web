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
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('payment_id') ? 'has-error' : ''}}">
            {!! Form::label('payment_id', 'Kode Pembayaran') !!}
            {!! Form::text('payment_id', null, ['class' => 'form-control']) !!}
            {!! $errors->first('payment_id', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('registered_id') ? 'has-error' : ''}}">
            {!! Form::label('registered_id', 'Kode Pendaftaran') !!}
            {!! Form::text('registered_id', null, ['class' => 'form-control']) !!}
            {!! $errors->first('registered_id', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('medical_record_number') ? 'has-error' : ''}}">
            {!! Form::label('medical_record_number', 'Nomor Rekam Medis') !!}
            {!! Form::text('medical_record_number', null, ['class' => 'form-control']) !!}
            {!! $errors->first('medical_record_number', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('payment_detail_date') ? 'has-error' : ''}}">
            {!! Form::label('payment_detail_date', 'Tanggal Tipe Pembayaran') !!}
            {!! Form::text('payment_detail_date', null, ['class' => 'form-control datepicker']) !!}
            {!! $errors->first('payment_detail_date', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('doctor_id') ? 'has-error' : ''}}">
            {!! Form::label('doctor_id', 'Dokter') !!}
            {!! Form::select('doctor_id', [''=>'Pilih'] + \App\TransactionMedicine::paymentDetailStatusLabels(), null, ['class' => 'form-control select2']) !!}
            {!! $errors->first('doctor_id', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('care_type') ? 'has-error' : ''}}">
            {!! Form::label('care_type', 'Tipe Rawatan') !!}
            {!! Form::select('care_type', [''=>'Pilih'] + \App\TransactionMedicine::careTypeLabels(), null, ['class' => 'form-control select2']) !!}
            {!! $errors->first('care_type', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('payment_detail_status') ? 'has-error' : ''}}">
            {!! Form::label('payment_detail_status', 'Status Tipe Pembayaran') !!}
            {!! Form::select('payment_detail_status', [''=>'Pilih'] + \App\TransactionMedicine::paymentDetailStatusLabels(), null, ['class' => 'form-control select2']) !!}
            {!! $errors->first('payment_detail_status', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('approval_status') ? 'has-error' : ''}}">
            {!! Form::label('approval_status', 'Status Persetujuan') !!}
            {!! Form::select('approval_status', [''=>'Pilih'] + \App\TransactionMedicine::approvalStatusLabels(), null, ['class' => 'form-control select2']) !!}
            {!! $errors->first('approval_status', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
        
<table class="table table-condensed table-hover" id="medicine-detail">
    <tr>
        <th style="width:50%">Obat</th>
        <th style="width:10%">Jumlah</th>
        <th style="width:30%">Harga</th>
        <th style="width:10%"></th>
    </tr>
</table>
<button type="button" name="add" id="add" class="btn btn-primary"><i class="fa fa-plus-square"></i></button>
<br/>
<br/>

{!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
<button class="btn btn-default" type="reset"><i class="pg-close"></i> Clear</button>

@push('script')
<script>
    var i = 1;
    $('#add').click(function() {
        i++;
        $('#medicine-detail').append('' +
            '<tr id="row-'+i+'" >' +
                '<td>{!! Form::select("medicine_id[]", [""=>"Pilih"] + \App\TransactionMedicine::approvalStatusLabels(), null, ["class" => "form-control select2"]) !!}</td>' +
                '<td>{!! Form::text("quantity[]", null, ["class" => "form-control"]) !!}</td>' +
                '<td>{!! Form::text("price[]", null, ["class" => "form-control"]) !!}</td>' +
                '<td><button type="button" id="'+ i +'" class="btn btn-danger btn-remove"><i class="fa fa-trash"></i></button></td>' +
            '</tr>');
    });
    
$('.select2').select2({
    width: 'resolve'
});
$('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
});
</script>
@endpush