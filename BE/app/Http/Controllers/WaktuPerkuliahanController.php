<?php

namespace App\Http\Controllers;

use App\Models\WaktuPerkuliahan;
use App\Http\Requests\StoreWaktuPerkuliahanRequest;
use App\Http\Requests\UpdateWaktuPerkuliahanRequest;
use Illuminate\Http\Request;

class WaktuPerkuliahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return WaktuPerkuliahan::all();//
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields=$request->validate([
            'jam' => 'required',
        ]);//

        $input = WaktuPerkuliahan::create($fields);
        return ['Waktu' => $input];//
        ////
    }

    /**
     * Display the specified resource.
     */
    public function show(WaktuPerkuliahan $waktuPerkuliahan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WaktuPerkuliahan $waktuPerkuliahan)
    {
        $fields=$request->validate([
            'jam' => 'required',
        ]);//

        $waktuPerkuliahan->update($fields);
        return ['Waktu' => $waktuPerkuliahan];//
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WaktuPerkuliahan $waktuPerkuliahan)
    {
        $waktuPerkuliahan->delete();//
    }

    public function jadwalPagi(){
        return WaktuPerkuliahan::select('jam')->where('sesi','Pagi')->get();
    }
    
    public function jadwalMalam(){
        return WaktuPerkuliahan::select('jam')->where('sesi','Malam')->get();
    }
}
