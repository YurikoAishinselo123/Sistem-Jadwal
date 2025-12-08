<?php

namespace App\Http\Controllers;

use App\Models\PeriodeTahun;
use App\Http\Requests\StorePeriodeTahunRequest;
use App\Http\Requests\UpdatePeriodeTahunRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PeriodeTahunController extends Controller implements HasMiddleware
{
    public static function middleware(){
        return[
            new Middleware('auth:sanctum', except:['index', 'show'])
        ];
    }   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PeriodeTahun::orderBy('tanggal_mulai')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields=$request->validate([
            'periode' => 'required',
            'status' => 'required',
            'tanggal_mulai' => 'required'
        ]);//

        $input = PeriodeTahun::create($fields);
        return ['Periode' => $input];//
        ////
    }

    /**
     * Display the specified resource.
     */
    public function show(PeriodeTahun $periodeTahun)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PeriodeTahun $periodeTahun)
    {
        $fields=$request->validate([
            'periode' => 'required',
            'status' => 'required',
            'tanggal_mulai' => 'required'
        ]);//

        $input = $periodeTahun->update($fields);
        return ['Periode' => $input];//
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PeriodeTahun $periodeTahun)
    {
        if($periodeTahun->jadwal()->exists()){
            return response("Data periode tidak bisa dihapus karena terdapat di data jadwal", 422);
        }else{
            $periodeTahun->delete();//
            return[
                "messsage"=>"Data periode berhasil dihapus"
            ];
        }
    }
}
