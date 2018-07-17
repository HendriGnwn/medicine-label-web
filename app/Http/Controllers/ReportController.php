<?php

namespace App\Http\Controllers;

use App\MmTransactionAddMedicine;
use App\TransactionAddMedicineAdditional;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
    
    public function showList(Request $request) 
    {
        $datePeriod = $request->get('date_period', '01/01/2018');
        $query = $this->getQuery($datePeriod);
        $models = $query['models'];
        $medicines = $query['medicines'];
        
        return view('report.list', compact('models', 'medicines'));
    }
    
    /**
     * get query
     * 
     * @param type $datePeriod
     * @return array
     */
    private function getQuery($datePeriod) 
    {
        \DB::beginTransaction();
        $startDate = Carbon::parse($datePeriod)->toDateString() . ' 00:00:00';
        $endDate = Carbon::parse($datePeriod)->toDateString() . ' 23:59:59';
        
        $models = Cache\App\MmPatientRegistration::withCacheCooldownSeconds(600)->leftJoin('mm_transaksi_add_obat', 'mm_transaksi_add_obat.id_pendaftaran', '=', 'mm_pasien_pendaftaran.id_pendaftaran')
             ->whereBetween('mm_transaksi_add_obat.created_date', [$startDate, $endDate])
             ->groupBy('mm_transaksi_add_obat.id_pendaftaran', 'mm_transaksi_add_obat.no_resep')
             ->get();
        $medicines = \App\MmTransactionAddMedicine::withCacheCooldownSeconds(600)->whereBetween('created_date', [$startDate, $endDate])
            ->select(['*', DB::raw('SUM(jml_permintaan) as total_jml_permintaan')])
            ->groupBy('id_barang')
            ->get();
        \DB::commit();
        
        return [
            'models' => $models,
            'medicines' => $medicines
        ];
    }
    
    /**
     * @param Request $request
     */
    public function exportToExcel(Request $request) 
    {
        $datePeriod = $request->get('date_period', '01/01/2018');
        $query = $this->getQuery($datePeriod);
        $models = $query['models'];
        $medicines = $query['medicines'];

        $objPHPExcel = new \Maatwebsite\Excel\Classes\PHPExcel();

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
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.'2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('A2:A'.($lastDataIndex+1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3:'.$lastColumn.($lastDataIndex+1))->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D3:'.$lastColumn.($lastDataIndex+1))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle('D'.($lastDataIndex+2).':'.$lastColumn.($lastDataIndex+2))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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

        \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
        \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::ZIPARCHIVE);
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=laporan-harian-".Carbon::parse($datePeriod)->format('d-m-Y').".xlsx");
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
    
    public function monthlyPrintLabelByCategory(Request $request)
    {
        
    }
}
