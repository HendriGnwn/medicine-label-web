@extends('layouts.admin')
@section('headerTitle', 'List Manually')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">@yield('headerTitle')</div>

    <div class="panel-body">
        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#filter-search-dialog">Filter Search</button>
        <div class="table-responsive">
            <table class="table table-lg table-bordered table-hover" width="100%">
                <thead>
                    <tr>
                        <th>*</th>
                        <th>Pasien</th>
                        <th>No Resep</th>
                        @foreach ($medicines as $medicine)
                        <th style="width:5%;">{{ $medicine->mmItem->nama_barang }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                    $no = 1;
                    @endphp
                    @foreach ($models as $model)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $model->mmPatient->nama }}</td>
                        <td>{{ $model->mmTransactionAddMedicine->no_resep }}</td>
                        @foreach ($medicines as $medicine)
                        <td style="text-align:center;">{{ $model->getItemQuantity($medicine->id_barang) }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>    
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="filter-search-dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="filter-search-form" action="">
                <div class="modal-header">
                    <h4 style="margin-top:0px;margin-bottom:0px;">Filter Search<button type="button" class="close" data-dismiss="modal">&times;</button></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div aria-required="true" class="form-group required form-group-default">
                                {!! Form::label('date_period', 'Tanggal') !!}
                                {!! Form::text('date_period', null, ['class' => 'form-control date form-sm']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="search" class="btn btn-primary btn-rounded">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$('.date').datepicker();
$('.date-range').daterangepicker({
    autoUpdateInput: false
});
$('.date-range').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
});

$('.date-range').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
});

</script>
@endpush