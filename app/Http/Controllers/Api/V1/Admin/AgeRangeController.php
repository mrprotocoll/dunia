<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgeRange;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
            return response()->json('Oops something went wrong', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $validatedData = $request->validate([
                'name' => ['required', 'max:50'],
                'description' => ['nullable']
            ]);
            $created = AgeRange::create($validatedData);

            return response()->json($created, 201);
        }
        catch (ValidationException $validationException) {
            return response()->json(['errors' => $validationException->errors()], 422);
        }
        catch (\Exception $exception) {
            return response()->json(['errors' => 'Oops something went wrong'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AgeRange $ageRange)
    {
        try {
            return response()->json(AgeRange::find($ageRange->id));
        }
        catch (ValidationException $validationException) {
            return response()->json(['errors' => $validationException->errors()], 422);
        }
        catch (\Exception $exception) {
            return response()->json(['errors' => 'Oops something went wrong'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AgeRange $ageRange)
    {
        try {
            $validatedData = $request->validate([
                'name' => ['required', 'max:50'],
                'description' => ['nullable']
            ]);

            $age = $ageRange->update($validatedData);
            return response()->json($age);
        }
        catch (ValidationException $validationException) {
            return response()->json(['errors' => $validationException->errors()], 422);
        }
        catch (\Exception $exception) {
            return response()->json(['errors' => 'Oops something went wrong'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AgeRange $ageRange)
    {
        try {
            return response()->json($ageRange->delete());
        }
        catch (\Exception $exception) {
            return response()->json(['errors' => 'Oops something went wrong'], 500);
        }
    }
}
