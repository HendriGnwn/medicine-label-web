<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    /**
     * @param Request $request
     * @return type
     */
	public function findDoctor(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return response()->json([]);
        }
        
        $doctors = \App\MmDoctor::where('nama_dokter', 'like', "%$term%")
                ->orWhere('id_unit', 'like', "%$term%")
                ->limit(20)
                ->get();
        $results = [];
        foreach ($doctors as $doctor) {
            $results[] = [
                'id' => $doctor->id_dokter,
                'text' => $doctor->id_unit . ' - ' . $doctor->nama_dokter,
            ];
        }
        
        return response()->json($results);
    }
    
    /**
     * @param Request $request
     * @return type
     */
	public function findMedicine(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return response()->json([]);
        }
        
        $items = \App\MmItem::where('nama_barang', 'like', "%$term%")
                ->orWhere('kode_barang', 'like', "%$term%")
                ->where('id_barang_group', "1")
                ->where('id_barang_group', 1) //obat
                ->limit(20)
                ->get();
        $results = [];
        foreach ($items as $item) {
            $results[] = [
                'id' => $item->id_barang,
                'text' => $item->kode_barang . ' - ' . $item->nama_barang,
            ];
        }
        
        return response()->json($results);
    }
    
    /**
     * @param Request $request
     * @return type
     */
	public function findPatient(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return response()->json([]);
        }
        
        $patients = \App\MmPatient::where('nama', 'like', "%$term%")
                ->orWhere('no_rekam_medis', 'like', "%$term%")
                ->limit(20)
                ->get();
        $results = [];
        foreach ($patients as $patient) {
            $results[] = [
                'id' => $patient->no_rekam_medis,
                'text' => $patient->no_rekam_medis . ' - ' . $patient->nama,
            ];
        }
        
        return response()->json($results);
    }
    
    /**
     * @param Request $request
     * @return type
     */
	public function findMedicineHowToUse(Request $request)
    {
        $term = trim($request->term);
        if (empty($term)) {
            return response()->json([]);
        }
        
        $medicineId = trim($request->medicine_id, 0);
        $item = \App\MmItem::where('id_barang', $medicineId)->first();
        if (!$item) {
            $howToUses = \App\MmHowToUse::where('nama', 'like', "%$term%")->get();
        } else {
            $howToUses = \App\MmHowToUse::where('nama', 'like', "%$term%")
                    ->where('id_barang_satuan_kecil', $item->id_barang_satuan_kecil)
                    ->get();
        }
        
        $results = [];
        foreach ($howToUses as $howToUse) {
            $results[] = [
                'id' => $howToUse->nama,
                'value' => $howToUse->nama
            ];
        }
        
        return response()->json($results);
    }
}
