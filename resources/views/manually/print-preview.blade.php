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

@foreach ($model->transactionMedicineDetails as $detail)
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
        <p>Dokter&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $model->getDoctorName() }}</p>
        <div style="width:50%;float:left;">
            <p>No Resep&nbsp;&nbsp;&nbsp;&nbsp;: {{ $model->receipt_number }}</p>
        </div>
        <div style="width:50%;float:right;text-align: right;">
            <p>Tgl  {{ $model->getFormattedMedicineDate() }}</p>
        </div>
        <p>Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $model->getName() }}</p>
        <div style="width:50%;float:left;">
            <p>Tgl Lahir&nbsp;&nbsp;&nbsp;&nbsp;: {{ $model->getDob() }}</p>
        </div>
        <div style="width:50%;float:right;text-align: right;">
            <p>No RM : {{ $model->medical_record_number }}</p>
        </div>
        <div style="width:100%;float:left;">
            <p>Obat / Exp : <span style="font-size:11px">{{ $detail->getMedicineNameAndExp() }}</span></p>
        </div>
        <div style="width:15%;float:right;text-align: right;">
            <p>Qty : {{ $detail->quantity }}</p>
        </div>
        
        <br/><br/>
        <div style="width:85%">
            <p class="text-center" style="margin-top:8px">Sehari {{ $detail->how_to_use }} {{ $detail->getItemSmallName() }}</p>
            <p class="text-center">Sebelum / sesudah makan</p>
            <br/>
            <p style="text-align:center">................................................</p>
        </div>
        
        <span style="float:right;font-size:9px;margin-top:-15px;margin-right:10px;">{{ \Auth::user()->username }}</span>
    </div>
</div>
@endforeach