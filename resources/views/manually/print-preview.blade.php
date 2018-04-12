<style>
    .row-label {
        width: 340.15748px;
/*        height: 226.771654px;*/
        height: 264.566929px;
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
        margin-bottom: 5px;
    }
</style>

@foreach ($model->transactionMedicineDetail as $detail)
<div class="row-label">
    <div class="header">
        <div style="width:22%;float:left;">
            <img src="{{ asset('files/farmasi-logo.png') }}" class="farmasi-logo" />
        </div>
        <div class="header-title" style="width:78%;float:right;">
            <div style="width:78%; float:left;">
                <h3>INSTALASI FARMASI</h3>
                <p>{{ \App\Setting::find(\App\Setting::SETTING_FIRST)->address }}</p>
                <p>Apoteker : {{ \App\Setting::find(\App\Setting::SETTING_FIRST)->apoteker }}</p>
                <p>SIK : {{ \App\Setting::find(\App\Setting::SETTING_FIRST)->sik }}</p>
            </div>
            <div style="width:22%;float:right;">
                <img src="{{ asset('files/rsmm-logo.png') }}" class="rsmm-logo" />
            </div>
        </div>
    </div>
    <div class="content">
        <p>Dokter : {{ $model->mmDoctor->nama_dokter }}</p>
        <div style="width:50%;float:left;">
            <p>No Resep : {{ $model->receipt_number }}</p>
        </div>
        <div style="width:50%;float:right;text-align: right;">
            <p>Tgl  {{ $model->getFormattedMedicineDate() }}</p>
        </div>
        <p>Nama : {{ $model->getName() }}</p>
        <div style="width:50%;float:left;">
            <p>Tgl Lahir : {{ $model->getDob() }}</p>
        </div>
        <div style="width:50%;float:right;text-align: right;">
            <p>No RM : {{ $model->medical_record_number }}</p>
        </div>
        <div style="width:80%;float:left;">
            <p>Nama Obat / Exp : {{ $detail->getMedicineNameAndExp() }}</p>
        </div>
        <div style="width:20%;float:right;text-align: right;">
            <p>Qty : {{ $detail->quantity }}</p>
        </div>
        
        <br/><br/><br/>
        <p class="text-center">Sehari {{ $detail->how_to_use }} {{ $detail->getItemSmallName() }}</p>
        <p class="text-center">Sebelum / sesudah makan</p>
        <br/>
        <p style="text-align:center">................................................</p>
    </div>
</div>
@endforeach