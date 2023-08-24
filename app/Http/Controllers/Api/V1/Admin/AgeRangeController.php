<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgeRange;
use Illuminate\Http\Request;

class AgeRangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json(AgeRange::all());
        }
        catch (\Exception $exception) {

        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //>
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AgeRange $ageRange)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AgeRange $ageRange)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AgeRange $ageRange)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AgeRange $ageRange)
    {
        //
    }
}
