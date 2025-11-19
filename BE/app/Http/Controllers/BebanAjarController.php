<?php

namespace App\Http\Controllers;

use App\Models\BebanAjar;
use App\Http\Requests\StoreBebanAjarRequest;
use App\Http\Requests\UpdateBebanAjarRequest;
use Illuminate\Http\Request;

class BebanAjarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        BebanAjar::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields=$request->validate([
            'id_dosen'=> 'required',
            'beban_sesi'=> 'required',
            'beban_sks'=> 'required'            
        ]);//

        $bebanAjar = BebanAjar::create($fields);
        return ['bebanAjar' => $bebanAjar]; //
    }

    /**
     * Display the specified resource.
     */
    public function show(BebanAjar $bebanAjar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BebanAjar $bebanAjar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BebanAjar $bebanAjar)
    {
        //
    }
}
