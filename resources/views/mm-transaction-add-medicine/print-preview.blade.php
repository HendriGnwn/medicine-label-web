<style>
    .row-label {
        width: 340.15748px;
/*        height: 226.771654px;*/
        height: 253.228346px;
        border: 1px solid #111;
        border-radius: 30px;
        margin-bottom: 15px;
    }
    .row-label .header {
        height: 85px;
        border-bottom: 1px solid #111;
    }
    
    .row-label .header .farmasi-logo {
        width: 40px;
        margin-left: 18px;
        margin-top: 20px;
    }
    
    .row-label .header .rsmm-logo {
        width: 40px;
        margin-right: 15px;
        margin-top: 20px;
    }
    
    .row-label .header .header-title h3 {
        margin-top: 10px;
        margin-bottom: 5px;
        letter-spacing: 1.5px;
        padding: 0;
        font-weight: bold;
        font-size: 16px;
    }
    .row-label .header .header-title p {
        padding: 0;
        margin: 0;
        margin-top: 2px;
        font-size: 12px;
    }
    .text-center {
        text-align:center;
    }
    .content {
        padding: 10px;
        font-size: 13px;
    }
    .content p {
        padding: 0;
        margin: 0;
        margin-bottom: 1px;
    }
</style>

@foreach ($model->mmTransactionAddMedicines as $detail)
<div class="row-label">
    <div class="header">
        <div style="width:22%;float:left;">
            <img src="{{ asset('files/farmasi-logo.png') }}" class="farmasi-logo" />
        </div>
        <div class="header-title" style="width:78%;float:right;">
            <div style="width:78%; float:left;">
                <h3>INSTALASI FARMASI</h3>
                <p>{{ \App\Setting::getAddressLabel() }}</p>
                <p>Apoteker : {{ \App\Setting::getApotekerLabel() }}</p>
                <p>SIPA : {{ \App\Setting::getSikLabel() }}</p>
            </div>
            <div style="width:22%;float:right;">
                <img src="{{ asset('files/rsmm-logo.png') }}" class="rsmm-logo" />
            </div>
        </div>
    </div>
    <div class="content">
        <p>Dokter&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $detail->getDoctorName() }}</p>
        <div style="width:50%;float:left;">
            <p>No Resep&nbsp;&nbsp;&nbsp;&nbsp;: {{ $detail->getReceiptNumber() }}</p>
        </div>
        <div style="width:50%;float:right;text-align: right;">
            <p>Tgl  {{ $detail->getFormattedMedicineDate() }}</p>
        </div>
        <p>Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $detail->getName() }}</p>
        <div style="width:50%;float:left;">
            <p>Tgl Lahir&nbsp;&nbsp;&nbsp;&nbsp;: {{ $detail->getDob() }}</p>
        </div>
        <div style="width:50%;float:right;text-align: right;">
            <p>No RM : {{ $detail->no_rekam_medis }}</p>
        </div>
        <div style="width:100%;float:left;">
            <p>Obat / Exp : <span style="font-size:10px">{{ $detail->getMedicineNameAndExp() }}</span></p>
        </div>
        <div style="width:15%;float:right;text-align: right;">
            <p>Qty : {{ $detail->jml_permintaan }}</p>
        </div>
        
        <br/><br/>
        <div style="width:85%">
            <p class="text-center" style="margin-top:8px">Sehari {{ $detail->getHowToUse() }} {{ $detail->getItemSmallName() }}</p>
            <p class="text-center">Sebelum / sesudah makan</p>
            <br/>
            <p style="text-align:center">................................................</p>
        </div>
        <span style="float:right;font-size:9px;margin-top:-15px;margin-right:10px;">{{ \Auth::user()->username }}</span>
    </div>
</div>
@endforeach

<script src="{{ asset('js/jquery.js') }}"></script>
<script>
window.onbeforeprint = function() {
    console.log('This will be called before the user prints.');
    $.ajax({
        url: '{{route("transaction-add-medicine.post-print", ["id"=>$model->no_pendaftaran, "receipt_number" => $model->mmTransactionAddMedicine->no_resep])}}' + '?' + $.param({"_token" : '{{ csrf_token() }}' }),
        type: 'POST',
        complete: function(data) {
        }
    });
};    
</script>