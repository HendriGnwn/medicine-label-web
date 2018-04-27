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
            {!! Form::label('registered_id', 'Kode Pendaftaran') !!}
            {!! Form::number('registered_id', null, ['class' => 'form-control', 'type'=>'number']) !!}
            {!! $errors->first('registered_id', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('receipt_number') ? 'has-error' : ''}}">
            {!! Form::label('receipt_number', 'Nomor Resep') !!}
            {!! Form::number('receipt_number', null, ['class' => 'form-control', 'type'=>'number']) !!}
            {!! $errors->first('receipt_number', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('medical_record_number') ? 'has-error' : ''}}">
            {!! Form::label('medical_record_number', 'Nomor Rekam Medis*') !!}
            {!! Form::select('medical_record_number', [], null, ['class' => 'form-control', 'required'=>true]) !!}
            {!! $errors->first('medical_record_number', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    <div class="col-md-6">
        <div aria-required="true" class="form-group required form-group-default {{ $errors->has('medicine_date') ? 'has-error' : ''}}">
            {!! Form::label('medicine_date', 'Tanggal*') !!}
            {!! Form::text('medicine_date', null, ['class' => 'form-control datepicker', 'required'=>true]) !!}
            {!! $errors->first('medicine_date', '<p class="help-block">:message</p>') !!}
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
        <th style="width:20%; vertical-align: middle;"><button type="button" name="add" id="add" class="btn btn-primary btn-sm"><i class="fa fa-plus-square"></i></button></th>
    </tr>
    @php
    $no = 0;
    @endphp
    @foreach ($model->transactionMedicineDetail as $detail)
        <tr id="row-{{ $no }}">
            <td>
                <input type="hidden" name="count[{{ $no }}]" value="{{ $no }}" />
                <input type="hidden" name="detail_id[{{ $no }}]" id="detail_id_{{ $no }}" value="{{ $detail->id }}" />
                <input type="hidden" name="medicine_label[{{ $no }}]" id="medicine_label_{{ $no }}" value="{{ $detail->name }}" />
                <select class="form-control" name="medicine_id[{{ $no }}]" id="medicine_id_{{ $no }}" required></select>
            </td>
            <td>
                <input type="number" class="form-control" name="quantity[{{ $no }}]" required value="{{ $detail->quantity }}"/>
            </td>
            <td>
                <input type="text" class="form-control" name="how_to_use[{{ $no }}]" id="how_to_use_{{ $no }}" required value="{{ $detail->how_to_use }}" />
            </td>
            <td>
                <button type="button" id="{{ $no }}" class="btn btn-danger btn-sm" onclick="clickRemove({{ $no }})"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
        @push('script')
        <script>
            $('#medicine_id_{{ $no }}').select2({
                width: 'resolve',
                placeholder: "Obat",
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
                },
                initSelection: function (element, callback) {
                    $(element).html("<option value='{{$detail->medicine_id}}' selected='selected'>{{ $detail->name }}</option>");
                    callback({id: {{$detail->medicine_id}}, text: "{{ $detail->name }}" });
                }
            });
            $('#medicine_id_{{ $no }}').on('select2:select', function (e) {
                $("#medicine_label_{{ $no }}").val(e.params.data.text);
            });
            $('#how_to_use_{{ $no }}').autocomplete({
                source: function( request, response ) {
                    $.ajax( {
                        url: "{{route('medicine.how-to-use')}}",
                        data: {
                            term: request.term,
                            medicine_id: $('#medicine_id_{{ $no }}').val()
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
        </script>
        @endpush
        @php
        $no++;
        @endphp
    @endforeach
</table>

<br/>
<br/>

{!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
<button class="btn btn-default" type="reset"><i class="pg-close"></i> Clear</button>

@prepend('script')
<script>
    var i = {{ $no }};
    $('#add').click(function() {
        $('#medicine-detail').append('' +
            '<tr id="row-'+i+'" >' +
                '<td>' +
                    '<input type="hidden" name="count[]" value="'+ i +'" />' +
                    '<input type="hidden" name="detail_id['+ i +']" id="detail_id_'+ i +'" />' +
                    '<input type="hidden" name="medicine_label['+i+']" id="medicine_label_'+i+'" value="" />' +
                    '<select class="form-control" name="medicine_id['+i+']" id="medicine_id_'+i+'" required></select>' +
                '</td>' +
                '<td>' +
                    '<input type="number" class="form-control" name="quantity[]" required />' +
                '</td>' +
                '<td>' +
                    '<input type="text" class="form-control" name="how_to_use[]" id="how_to_use_'+i+'" required />' +
                '</td>' +
                '<td><button type="button" id="'+ i +'" class="btn btn-danger btn-sm" onclick="clickRemove('+i+')"><i class="fa fa-trash"></i></button></td>' +
            '</tr>');
        $('#medicine_id_' + i).select2({
            width: 'resolve',
            placeholder: "Obat",
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
        },
        initSelection: function (element, callback) {
            $(element).html("<option value='{{$model->doctor_id}}' selected='selected'>{{ $model->mmDoctor->nip . ' - ' . $model->mmDoctor->nama_dokter }}</option>");
            callback({id: {{$model->doctor_id}}, text: "{{ $model->mmDoctor->nip . ' - ' . $model->mmDoctor->nama_dokter }}" });
        }
    });
    
    $('#medical_record_number').select2({
        placeholder: "No Rekam Medis - Nama Pasien",
        minimumInputLength: 2,
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
            cache: true,
        },
        initSelection: function (element, callback) {
            $(element).html("<option value='{{$model->medical_record_number}}' selected='selected'>{{ $model->medical_record_number . ' - ' . $model->mmPatient->nama }}</option>");
            callback({id: {{$model->medical_record_number}}, text: "{{ $model->medical_record_number . ' - ' . $model->mmPatient->nama }}" });
        }
    });

    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true
    });
</script>
@endprepend