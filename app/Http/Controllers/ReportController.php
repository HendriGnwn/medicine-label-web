<?php

namespace App\Http\Controllers;

use App\MmPatientRegistration;
use App\MmTransactionAddMedicine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Classes\PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Settings;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;

class ReportController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('report.index');
    }
    
	/**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function daily()
    {
        return view('report.daily');
    }
    
    public function showList(Request $request) 
    {
        $datePeriod = $request->get('date_period', Carbon::now()->subDay()->format('m/d/Y'));
        $query = $this->getQuery($datePeriod);
        $models = $query['models'];
        $medicines = $query['medicines'];
        
        return view('report.list', compact('models', 'medicines'));
    }
    
    /**
     * get query for laporan harian obat
     * 
     * @param type $datePeriod
     * @return array
     */
    private function getQuery($datePeriod) 
    {
        \DB::beginTransaction();
        $startDate = Carbon::parse($datePeriod)->toDateString() . ' 00:00:00';
        $endDate = Carbon::parse($datePeriod)->toDateString() . ' 23:59:59';
        
        $models = Cache::remember('reportMmPatientRegistration', 2*60, function() use ($startDate, $endDate) {
            return MmPatientRegistration::withCacheCooldownSeconds(600)->leftJoin('mm_transaksi_add_obat', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
             ->whereBetween('mm_transaksi_add_obat.created_date', [$startDate, $endDate])
             ->groupBy('mm_transaksi_add_obat.id_pendaftaran', 'mm_transaksi_add_obat.no_resep')
             ->get();
        });
        $medicines = Cache::remember('reportMmPatientRegistration', 2*60, function() use ($startDate, $endDate) {
            return MmTransactionAddMedicine::withCacheCooldownSeconds(600)->whereBetween('created_date', [$startDate, $endDate])
                ->select(['*', DB::raw('SUM(jml_permintaan) as total_jml_permintaan')])
                ->groupBy('id_barang')
                ->get();
        });
        \DB::commit();
        
        return [
            'models' => $models,
            'medicines' => $medicines
        ];
    }
    
    /**
     * get query for laporan harian obat
     * 
     * @param Request $request
     */
    public function exportToExcel(Request $request) 
    {
        $datePeriod = $request->get('date_period', Carbon::now()->subDay()->format('m/d/Y'));
        $query = $this->getQuery($datePeriod);
        $models = $query['models'];
        $medicines = $query['medicines'];

        $objPHPExcel = new PHPExcel();

        $arrayData['head'][]='No';
        $arrayData['head'][]='No Pendaftaran';
        $arrayData['head'][]='No RM';
        $arrayData['head'][]='Nama Pasien';
        $arrayData['head'][]='Tanggal Pendaftaran';
        $arrayData['head'][]='No Resep';
        foreach ($medicines as $medicine) {
            $arrayData['head'][] = $medicine->mmItem->nama_barang;
        }
        $no = 0;
        foreach ($models as $model) {
            $arrayData[$no][] = $no+1;
            $arrayData[$no][] = $model->no_pendaftaran;
            $arrayData[$no][] = $model->no_rekam_medis;
            $arrayData[$no][] = $model->mmPatient->nama;
            $arrayData[$no][] = \Carbon\Carbon::parse($model->tanggal_pendaftaran)->format('d-M-Y H:i:s');
            $arrayData[$no][] = $model->no_resep;
            foreach ($medicines as $medicine) {
                $arrayData[$no][] = $model->getItemQuantity($medicine->id_barang, $model->no_resept);
            }
            $no++;
        }
        $arrayData[$no][] = "";
        $arrayData[$no][] = "Jml Keseluruhan";
        $arrayData[$no][] = "";
        $arrayData[$no][] = "";
        $arrayData[$no][] = "";
        $arrayData[$no][] = "";
        foreach ($medicines as $medicine) {
            $arrayData[$no][] = $medicine->total_jml_permintaan;
        }
        
        $lastDataIndex = count($arrayData) - 1;

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Sistem Labeling Obat RSMM");
		$objPHPExcel->getProperties()->setLastModifiedBy("Sistem Labeling Obat RSMM");
		$objPHPExcel->getProperties()->setTitle("Laporan Harian Obat " . $datePeriod);
		$objPHPExcel->getProperties()->setSubject("Laporan Harian Obat " . $datePeriod);
        $objPHPExcel->getProperties()->setDescription("Laporan Harian Obat " . $datePeriod);
        
        $objPHPExcel->getActiveSheet()->setCellValue("A1", "Laporan Harian Obat Tanggal " . $datePeriod);
        $objPHPExcel->getActiveSheet()->getRowDimension("A1")->setRowHeight(25);
        $objPHPExcel->getActiveSheet()->mergeCells("A1:E1");

        $objPHPExcel->getActiveSheet()->fromArray($arrayData, NULL, 'A2');
        
        $starRow = 'A';
		$lastColumn = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $objPHPExcel->getActiveSheet()->getStyle('D2:'.$lastColumn.'2')->getAlignment()->setTextRotation(90);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A2:A'.($lastDataIndex+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3:'.$lastColumn.($lastDataIndex+1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D3:'.$lastColumn.($lastDataIndex+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getFont()->setBold(true);
        
        /**autosize*/
		for ($col = $starRow; $col != $lastColumn; $col++) {
            if ($col == 'A' || $col == 'B' || $col == 'C') {
                $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            } else {
                $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(7);
            }
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);

        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=laporan-harian-".Carbon::parse($datePeriod)->format('d-m-Y').".xlsx");
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
    
	/**
     * for per periode
     *
     * @return View
     */
    public function period()
    {
        return view('report.period.index');
    }
    
    public function periodList(Request $request)
    {
        $datePeriod = $request->get('date_period', Carbon::now()->subDay()->format('m/d/Y') . ' - ' . Carbon::now()->format('m/d/Y'));
        $query = $this->getPeriodQuery($datePeriod);
        $models = $query['models'];
        
        return view('report.period.list', compact('models'));
    }
    
    public function periodListDetail(Request $request)
    {
        $receiptNumber = $request->get('no_resep');
        $registeredId = $request->get('id_pendaftaran');
        
        $model = MmPatientRegistration::findOrFail($registeredId);
        $medicines = MmTransactionAddMedicine::where('id_pendaftaran', $registeredId)
                ->where('no_resep', $receiptNumber)
                ->get();
        
        return view('report.period.list-detail', compact('model', 'medicines'));
    }
    
    public function periodExportToExcel(Request $request)
    {
        $datePeriod = $request->get('date_period', Carbon::now()->subDay()->format('m/d/Y') . ' - ' . Carbon::now()->format('m/d/Y'));
        $datePeriodLabel = str_replace('/', '-', $datePeriod);
        $query = $this->getPeriodQuery($datePeriod);
        $models = $query['models'];

        $objPHPExcel = new PHPExcel();

        $arrayData['head'][]='No';
        $arrayData['head'][]='Pasien';
        $arrayData['head'][]='No RM';
        $arrayData['head'][]='No Resep';
        $arrayData['head'][]='No Pendaftaran';
        $arrayData['head'][]='Nilai Transaksi';

        $no = 0;
        $subTotal = 0;
        foreach ($models as $model) {
            $arrayData[$no][] = $no+1;
            $arrayData[$no][] = $model->mmPatientRegistration->mmPatient->nama;
            $arrayData[$no][] = $model->no_rekam_medis;
            $arrayData[$no][] = $model->no_resep;
            $arrayData[$no][] = $model->no_pendaftaran;
            $arrayData[$no][] = $model->getCalculatePriceTotal();
            $no++;
            $subTotal += $model->getCalculatePriceTotal();
        }
        $arrayData[$no][] = "";
        $arrayData[$no][] = "";
        $arrayData[$no][] = "";
        $arrayData[$no][] = "";
        $arrayData[$no][] = "Jml Keseluruhan";
        $arrayData[$no][] = $subTotal;
        $lastDataIndex = count($arrayData) - 1;

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Sistem Labeling Obat RSMM");
		$objPHPExcel->getProperties()->setLastModifiedBy("Sistem Labeling Obat RSMM");
		$objPHPExcel->getProperties()->setTitle("Laporan Obat Periode " . $datePeriodLabel);
		$objPHPExcel->getProperties()->setSubject("Laporan Obat Periode " . $datePeriodLabel);
        $objPHPExcel->getProperties()->setDescription("Laporan Obat Periode " . $datePeriodLabel);
        
        $objPHPExcel->getActiveSheet()->setCellValue("A1", "Laporan Obat Periode " . $datePeriodLabel);
        $objPHPExcel->getActiveSheet()->getRowDimension("A1")->setRowHeight(25);
        $objPHPExcel->getActiveSheet()->mergeCells("A1:E1");

        $objPHPExcel->getActiveSheet()->fromArray($arrayData, NULL, 'A2');
        
        $starRow = 'A';
		$lastColumn = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A2:A'.($lastDataIndex+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3:'.$lastColumn.($lastDataIndex+1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D3:'.$lastColumn.($lastDataIndex+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getFont()->setBold(true);
        
        /**autosize*/
		for ($col = $starRow; $col != $lastColumn; $col++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);

        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=laporan-obat-periode-".$datePeriodLabel.".xlsx");
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
    
    /**
     * get query for laporan harian obat
     * 
     * @param type $datePeriod
     * @return array
     */
    private function getPeriodQuery($datePeriod) 
    {
        \DB::beginTransaction();
        $start = explode(' - ', $datePeriod)[0];
        $end = explode(' - ', $datePeriod)[1];
        $startDate = Carbon::parse($start)->toDateString() . ' 00:00:00';
        $endDate = Carbon::parse($end)->toDateString() . ' 23:59:59';
        
        $models = MmTransactionAddMedicine::withCacheCooldownSeconds(100)->leftJoin('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
             ->whereBetween('mm_transaksi_add_obat.created_date', [$startDate, $endDate])
             ->groupBy('mm_transaksi_add_obat.id_pendaftaran', 'mm_transaksi_add_obat.no_resep')
             ->get();
        \DB::commit();
        
        return [
            'models' => $models,
        ];
    }
    
	/**
     * for per periode
     *
     * @return View
     */
    public function transactionType()
    {
        return view('report.transaction-type.index');
    }
    
    public function transactionTypeList(Request $request)
    {
        $datePeriod = $request->get('date_period', Carbon::now()->subDay()->format('m/d/Y') . ' - ' . Carbon::now()->format('m/d/Y'));
        $transactionType = $request->get('transaction_type');
        $query = $this->getTransactionTypeQuery($datePeriod, $transactionType);
        $models = $query['models'];
        
        return view('report.transaction-type.list', compact('models'));
    }
    
    public function transactionTypeListDetail(Request $request)
    {
        $receiptNumber = $request->get('no_resep');
        $registeredId = $request->get('id_pendaftaran');
        
        $model = MmPatientRegistration::findOrFail($registeredId);
        $medicines = MmTransactionAddMedicine::where('id_pendaftaran', $registeredId)
                ->where('no_resep', $receiptNumber)
                ->get();
        
        return view('report.transaction-type.list-detail', compact('model', 'medicines'));
    }
    
    public function transactionTypeExportToExcel(Request $request)
    {
        $datePeriod = $request->get('date_period', Carbon::now()->subDay()->format('m/d/Y') . ' - ' . Carbon::now()->format('m/d/Y'));
        $datePeriodLabel = str_replace('/', '-', $datePeriod);
        $transactionType = $request->get('transaction_type');
        $query = $this->getTransactionTypeQuery($datePeriod, $transactionType);
        $models = $query['models'];

        $objPHPExcel = new PHPExcel();

        $arrayData['head'][]='No';
        $arrayData['head'][]='Nama Obat';
        $arrayData['head'][]='Jumlah';

        $no = 0;
        $subTotal = 0;
        foreach ($models as $model) {
            $arrayData[$no][] = $no+1;
            $arrayData[$no][] = $model->mmItem->nama_barang;
            $arrayData[$no][] = $model->medicine_qty;
            $no++;
            $subTotal += $model->medicine_qty;
        }
        $arrayData[$no][] = "";
        $arrayData[$no][] = "Jml Keseluruhan";
        $arrayData[$no][] = $subTotal;
        $lastDataIndex = count($arrayData) - 1;

		// Set properties
		$objPHPExcel->getProperties()->setCreator("Sistem Labeling Obat RSMM");
		$objPHPExcel->getProperties()->setLastModifiedBy("Sistem Labeling Obat RSMM");
		$objPHPExcel->getProperties()->setTitle("Laporan Obat Jenis Transaksi " . $datePeriodLabel);
		$objPHPExcel->getProperties()->setSubject("Laporan Obat Jenis Transaksi " . $datePeriodLabel);
        $objPHPExcel->getProperties()->setDescription("Laporan Obat Jenis Transaksi " . $datePeriodLabel);
        
        $objPHPExcel->getActiveSheet()->setCellValue("A1", "Laporan Obat Jenis Transaksi " . $datePeriodLabel);
        $objPHPExcel->getActiveSheet()->getRowDimension("A1")->setRowHeight(25);
        $objPHPExcel->getActiveSheet()->mergeCells("A1:E1");

        $objPHPExcel->getActiveSheet()->fromArray($arrayData, NULL, 'A2');
        
        $starRow = 'A';
		$lastColumn = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A2:A'.($lastDataIndex+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3:'.$lastColumn.($lastDataIndex+1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D3:'.$lastColumn.($lastDataIndex+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getFont()->setBold(true);
        
        /**autosize*/
		for ($col = $starRow; $col != $lastColumn; $col++) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);

        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=laporan-jenis-transaksi-".$datePeriodLabel.".xlsx");
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
    
    /**
     * get query for laporan harian obat
     * 
     * @param type $datePeriod
     * @return array
     */
    private function getTransactionTypeQuery($datePeriod, $transactionType) 
    {
        \DB::beginTransaction();
        $start = explode(' - ', $datePeriod)[0];
        $end = explode(' - ', $datePeriod)[1];
        $startDate = Carbon::parse($start)->toDateString() . ' 00:00:00';
        $endDate = Carbon::parse($end)->toDateString() . ' 23:59:59';
        
        $models = MmTransactionAddMedicine::select(['*', \DB::raw('SUM(mm_transaksi_add_obat.id_barang) as medicine_qty')])
//             ->withCacheCooldownSeconds(100)
             //->leftJoin('mm_pasien_pendaftaran', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
             ->transactionType($transactionType)
             ->whereBetween('mm_transaksi_add_obat.created_date', [$startDate, $endDate])
             ->groupBy('mm_transaksi_add_obat.id_barang')
             ->get();
        \DB::commit();
        
        return [
            'models' => $models,
        ];
    }
    
    
}
