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
        <div class="row">
            {!! Form::hidden('patient_registration_doctor_id', null) !!}
            {!! Form::hidden('medical_record_number', null) !!}
            {!! Form::hidden('unit_id', null) !!}
            <div class="col-md-12">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('registered_id') ? 'has-error' : ''}}">
                    {!! Form::label('registered_id', 'No Rekam Medis/Nama Pasien*') !!}
                    {!! Form::select('registered_id', [], null, ['class' => 'form-control', 'required'=>true]) !!}
                    {!! $errors->first('registered_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            @if (!\Auth::user()->getIsRoleDoctor())
            <div class="col-md-12" id="visible-doctor">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('doctor_id') ? 'has-error' : ''}}">
                    {!! Form::label('doctor_id', 'Dokter*') !!}
                    {!! Form::select('doctor_id', [], null, ['class' => 'form-control', 'required'=>true]) !!}
                    {!! $errors->first('doctor_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            @else
                {!! Form::hidden('doctor_id', \Auth::user()->getMmDoctorPrimaryKey()) !!}
            @endif
            <div class="col-md-12">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('care_type') ? 'has-error' : ''}}">
                    {!! Form::label('care_type', 'Tipe Rawatan*') !!}
                    {!! Form::select('care_type', [''=>'Pilih'] + \App\TransactionMedicine::careTypeLabels(), null, ['class' => 'form-control select2', 'required'=>true]) !!}
                    {!! $errors->first('care_type', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-md-12">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('receipt_number') ? 'has-error' : ''}}">
                    {!! Form::label('receipt_number', 'Nomor Resep') !!}
                    {!! Form::text('receipt_number', null, ['class' => 'form-control', 'type'=>'number', 'readonly'=>true, 'id' => 'receipt-number']) !!}
                    {!! $errors->first('receipt_number', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-md-12">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('medicine_date') ? 'has-error' : ''}}">
                    {!! Form::label('medicine_date', 'Tanggal*') !!}
                    {!! Form::text('medicine_date', null, ['class' => 'form-control datepicker', 'required'=>true]) !!}
                    {!! $errors->first('medicine_date', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <table class="table table-condensed">
            <tr>
                <th colspan="3">Detail</td>
            </tr>
            <tr>
                <td style="width:30%">No Pendaftaran</td>
                <td style="width:5%">:</td>
                <td style="width:75%" id="result-registered-number"></td>
            </tr>
            <tr>
                <td>Pasien</td>
                <td>:</td>
                <td id="result-patient"></td>
            </tr>
            <tr>
                <td>Tgl Daftar</td>
                <td>:</td>
                <td id="result-registered-at"></td>
            </tr>
            <tr>
                <td>Dokter</td>
                <td>:</td>
                <td id="result-doctor"></td>
            </tr>
            <tr>
                <td>Unit</td>
                <td>:</td>
                <td id="result-unit"></td>
            </tr>
        </table>
    </div>
    
</div>
        
<table class="table table-condensed table-hover" id="medicine-detail">
    <tr>
        <th colspan="3"></th>
        <th style="width:40%; vertical-align: middle;" id="medicine-record-total">Total 0 kolom</th>
    </tr>
    <tr>
        <th style="width:40%; vertical-align: middle;">Obat*</th>
        <th style="width:10%; vertical-align: middle;">Jumlah*</th>
        <th style="width:25%; vertical-align: middle;">Aturan Pakai (sehari)*</th>
        <th style="width:10%; vertical-align: middle;"><button type="button" name="add" id="add" class="btn btn-primary btn-sm" title="Add"><i class="fa fa-plus-square"></i></button></th>
    </tr>
</table>

<br/>
<br/>

{!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
<button class="btn btn-default" type="reset"><i class="pg-close"></i> Clear</button>

@push('script')
<script>
    var i = medicineRecordTotal = 0;
    $('#add').click(function() {
        $('#medicine-detail').append('' +
            '<tr id="row-'+i+'" >' +
                '<td>' +
                    '<input type="hidden" name="count[]" value="'+ i +'" />' +
                    '<input type="hidden" name="medicine_label[]" id="medicine_label_'+i+'" value="" />' +
                    '<select class="form-control" name="medicine_id[]" id="medicine_id_'+i+'" required></select>' +
                '</td>' +
                '<td>' +
                    '<input type="number" class="form-control" name="quantity[]" required />' +
                '</td>' +
                '<td>' +
                    '{{ Form::select("how_to_use[]", array_merge(["" => "Pilih"], \App\MmHowToUse::pluck("nama", "nama")->toArray()), null, ["class"=>"form-control"]) }}' +
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
                        q: $.trim(params.term),
                        except: $("select[name='medicine_id[]']").serializeArray(),
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
        medicineRecordTotal++;
        $('#medicine-record-total').html('Total: ' + medicineRecordTotal + ' kolom');
    });
    
    
    function clickRemove(i) {
        $('#row-'+i+'').remove();  
        medicineRecordTotal--;
        $('#medicine-record-total').html('Total: ' + medicineRecordTotal + ' kolom');
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
    
    $('#registered_id').select2({
        placeholder: "No Rekam Medis - Nama Pasien",
        minimumInputLength: 2,
        allowClear: true,
        ajax: {
            url: "{{ route('patient.find-registered') }}",
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
    
    $('#registered_id').change(function() {
        console.log($(this).val());
        $.ajax({
            type: 'POST',
            url: "{{ route('patient.get-result-find-registered') }}",
            data: {
                registered_id: $(this).val(),
                "_token": "{{ csrf_token() }}",
            },
            success: function(result) {
                console.log(result);
                if (result.status == 0) {
                    alert(result.message);
                    $('#result-registered-number').html("");
                    $('#result-patient').html("");
                    $('#result-registered-at').html("");
                    $('#result-doctor').html("");
                    $('#result-unit').html("");
                    $("input[name='medical_record_number']").val("");
                    $('#receipt-number').val("");
                    $('#care_type').val("").trigger('change');
                    return false;
                }
                var data = result.data;
                
                if (data.doctor_id != 0) {
                   disableEditDoctor();
                   $('#result-doctor').html(data.doctor + ' <button type="button" onclick="availableEditDoctor()" class="btn btn-xs btn-primary">Edit</button> <button type="button" onclick="disableEditDoctor()" class="btn btn-xs btn-danger">Cancel Edit</button> ');
                   $("input[name='patient_registration_doctor_id']").val(data.doctor_id);
                } else {
                    $('#result-doctor').html(data.doctor);
                }
                $('#care_type').val(data.care_type_id).trigger('change');
                
                $("input[name='medical_record_number']").val(data.medical_record_number);
                $("input[name='unit_id']").val(data.unit_id);
                
                $('#medicine_date').val(data.registered_at);
                $('#result-registered-number').html(data.registered_number);
                $('#result-patient').html(data.patient);
                $('#result-registered-at').html(data.registered_at);
                $('#result-unit').html(data.unit);
                $('#receipt-number').val(data.receipt_number);
            }
        })
    });
    
    function availableEditDoctor() {
        $('#visible-doctor').show();
        $("#doctor_id").attr("required", 'required');
    }
    
    function disableEditDoctor() {
        $('#visible-doctor').hide();
        $("#doctor_id").removeAttr("required");
    }

    $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true
    });
</script>
@endpush