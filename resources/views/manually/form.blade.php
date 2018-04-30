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
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('receipt_number') ? 'has-error' : ''}}">
            {!! Form::label('receipt_number', 'Nomor Resep') !!}
            {!! Form::number('receipt_number', null, ['class' => 'form-control', 'type'=>'number']) !!}
            {!! $errors->first('receipt_number', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('medicine_date') ? 'has-error' : ''}}">
            {!! Form::label('medicine_date', 'Tanggal*') !!}
            {!! Form::text('medicine_date', null, ['class' => 'form-control datepicker', 'required'=>true]) !!}
            {!! $errors->first('medicine_date', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('medical_record_number') ? 'has-error' : ''}}">
            {!! Form::label('medical_record_number', 'Nomor Rekam Medis*') !!}
            {!! Form::select('medical_record_number', [], null, ['class' => 'form-control', 'required'=>true]) !!}
            {!! $errors->first('medical_record_number', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    @if (!\Auth::user()->getIsRoleDoctor())
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('doctor_id') ? 'has-error' : ''}}">
            {!! Form::label('doctor_id', 'Dokter*') !!}
            {!! Form::select('doctor_id', [], null, ['class' => 'form-control', 'required'=>true]) !!}
            {!! $errors->first('doctor_id', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    @else
        {!! Form::hidden('doctor_id', \Auth::user()->getMmDoctorPrimaryKey()) !!}
    @endif
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('care_type') ? 'has-error' : ''}}">
            {!! Form::label('care_type', 'Tipe Rawatan*') !!}
            {!! Form::select('care_type', [''=>'Pilih'] + \App\TransactionMedicine::careTypeLabels(), null, ['class' => 'form-control select2', 'required'=>true]) !!}
            {!! $errors->first('care_type', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>
        
<table class="table table-condensed table-hover" id="medicine-detail">
    <tr>
        <th style="width:40%; vertical-align: middle;">Obat*</th>
        <th style="width:20%; vertical-align: middle;">Jumlah*</th>
        <th style="width:20%; vertical-align: middle;">Aturan Pakai (sehari)*</th>
        <th style="width:20%; vertical-align: middle;"><button type="button" name="add" id="add" class="btn btn-primary btn-sm" title="Add"><i class="fa fa-plus-square"></i></button></th>
    </tr>
</table>

<br/>
<br/>

{!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
<button class="btn btn-default" type="reset"><i class="pg-close"></i> Clear</button>

@push('script')
<script>
    var i = 0;
    $('#add').click(function() {
        $('#medicine-detail').append('' +
            '<tr id="row-'+i+'" >' +
                '<td>' +
                    '<input type="hidden" name="count[]" value="'+ i +'" />' +
                    '<input type="hidden" name="medicine_label['+i+']" id="medicine_label_'+i+'" value="" />' +
                    '<select class="form-control" name="medicine_id['+i+']" id="medicine_id_'+i+'" required></select>' +
                '</td>' +
                '<td>' +
                    '<input type="number" class="form-control" name="quantity[]" required />' +
                '</td>' +
                '<td>' +
                    '<input type="text" class="form-control" name="how_to_use[]" id="how_to_use_'+i+'" required />' +
                '</td>' +
                '<td><button type="button" id="'+ i +'" class="btn btn-danger btn-sm" onclick="clickRemove('+i+')" title="Delete"><i class="fa fa-trash"></i></button></td>' +
            '</tr>');
        $('#medicine_id_' + i).select2({
            width: 'resolve',
            placeholder: "Obat",
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('medicine.find') }}",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
        $('#medicine_id_' + i).on('select2:select', function (e) {
            $("#medicine_label_"+ (i-1)).val(e.params.data.text);
        });
        $('#how_to_use_' + i).autocomplete({
            source: function( request, response ) {
                $.ajax( {
                    url: "{{route('medicine.how-to-use')}}",
                    data: {
                        term: request.term,
                        medicine_id: $('#medicine_id_' + (i-1)).val()
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function( event, ui ) {
                console.log( "Selected: " + ui.item.value + " aka " + ui.item.id );
            }  
        });
        i++;
    });
    
    function clickRemove(i) {
        $('#row-'+i+'').remove();  
    }
    
    $('.select2').select2({
        width: 'resolve'
    });

    $('#doctor_id').select2({
        placeholder: "NIP - Nama Dokter",
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: "{{ route('doctor.find') }}",
            dataType: 'json',
            data: function (params) {
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });
    
    $('#medical_record_number').select2({
        placeholder: "No Rekam Medis - Nama Pasien",
        minimumInputLength: 2,
        allowClear: true,
        ajax: {
            url: "{{ route('patient.find') }}",
            dataType: 'json',
            data: function (params) {
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true
    });
</script>
@endpush