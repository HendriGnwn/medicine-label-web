@extends('layouts.admin')
@section('headerTitle', 'Update Aturan Pakai')
@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        {!! Form::open(['url' => route('transaction-add-medicine.update', ['id'=>$model->no_pendaftaran, 'receipt_number' => $model->mmTransactionAddMedicine->no_resep]), 'method'=>'PATCH']) !!}
        <div class="row">
            <div class="col-md-6">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('registration_number') ? 'has-error' : ''}}">
                    {!! Form::label('registration_number', 'Nomor Pendaftaran') !!}
                    {!! Form::text('registration_number', old('registration_number') ? old('registration_number') : $model->no_pendaftaran, ['class' => 'form-control', 'readonly' => true]) !!}
                    {!! $errors->first('registration_number', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('medical_record_number') ? 'has-error' : ''}}">
                    {!! Form::label('medical_record_number', 'Nomor Rekam Medis - Pasien') !!}
                    {!! Form::text('medical_record_number', old('medical_record_number') ? old('medical_record_number') : $model->no_rekam_medis .' - ' . $model->mmPatient->nama, ['class' => 'form-control', 'readonly'=>true]) !!}
                    {!! $errors->first('medical_record_number', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('doctor_id') ? 'has-error' : ''}}">
                    {!! Form::label('doctor_id', 'Unit - Dokter') !!}
                    {!! Form::text('doctor_id', old('doctor_id') ? old('doctor_id') : $model->mmTransactionAddMedicine->id_unit .' - ' . $model->mmTransactionAddMedicine->mmDoctor->nama_dokter, ['class' => 'form-control', 'readonly'=>true]) !!}
                    {!! $errors->first('doctor_id', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div aria-required="true" class="form-group required form-group-default {{ $errors->has('receipt_number') ? 'has-error' : ''}}">
                    {!! Form::label('tipe_rawatan', 'Tipe Rawatan') !!}
                    {!! Form::text('tipe_rawatan', old('tipe_rawatan') ? old('tipe_rawatan') : $model->mmTransactionAddMedicine->getTipeRawatanLabel(), ['class' => 'form-control', 'readonly'=>true]) !!}
                    {!! $errors->first('tipe_rawatan', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        
        <table class="table table-condensed table-hover">
            <thead>
                <tr>
                    <th style="width:5%">No</th>
                    <th style="width:35%">Obat</th>
                    <th style="width:10%">Jumlah</th>
                    <th style="width:20%">Aturan Pakai</th>
                </tr>
            </thead>
            <tbody>
                @php
                $no = 0;
                $medicineQty = 0;
                @endphp
                @foreach ($model->mmTransactionAddMedicines as $medicine)
                    <tr>
                        <td>
                            {{ $no + 1 }}
                        </td>
                        <td>
                            {{ $medicine->mmItem->nama_barang }}
                        </td>
                        <td>
                            {{ $medicine->jml_permintaan }}
                        </td>
                        <td>
                            {!! Form::hidden('id[]', $medicine->id_transaksi_obat) !!}
                            {{ Form::select("how_to_use[]", array_merge(["" => "Pilih"], \App\MmHowToUse::pluck("nama", "nama")->toArray()), old('how_to_use.' . $no) ? old('how_to_use_' . $no) : $medicine->getHowToUse(), ["class"=>"form-control"]) }}
                        </td>
                    </tr>
                    @php 
                        $no++; 
                        $medicineQty += $medicine->jml_permintaan; 
                    @endphp
                @endforeach
            </tbody>
        </table>
        <div class="col-md-offset-8 col-md-4">
            <table class="table table-condensed table-hover">
                <tr>
                    <th>Total Keseluruhan Obat</th>
                    <th>:</th>
                    <th>{{ $no }}</th>
                </tr>
                <tr>
                    <th>Jumlah Keseluruhan Obat</th>
                    <th>:</th>
                    <th>{{ $medicineQty }}</th>
                </tr>
            </table>
        </div>
        {!! Form::submit('Update and Print Preview', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
        {!! Form::close() !!}
    </div>
</div>
@endsection