<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgeRange;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @group Age Range
 *
 * The age range management
 */
class AgeRangeController extends Controller
{

    /**
     *
     * Retrieve a list of all age ranges.
     *
     * @authenticated
     *
     * @response {
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Age Range Name",
     *             "description": "Age range description",
     *             "created_at": "2023-08-11T12:34:56Z",
     *             "updated_at": "2023-08-11T12:34:56Z"
     *         },
     *         ...
     *     ]
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Store a new age range.
     * @authenticated
     * @bodyParam name string required The name of the age range.
     * @bodyParam description string The description of the age range.
     *
     * @response 201 {
     *     "id": 1,
     *     "name": "Age Range Name",
     *     "description": "Age range description",
     *     "created_at": "2023-08-11T12:34:56Z",
     *     "updated_at": "2023-08-11T12:34:56Z"
     * }
     * @response 422 {
     *     "errors": {...}
     * }
     * @response 500 {
     *     "errors": "Oops something went wrong"
     * }
     *
     * @param Request $request The request instance.
     * @return \Illuminate\Http\JsonResponse
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
     * Retrieve details of a specific age range.
     * @authenticated
     * @param \App\Models\AgeRange $ageRange The age range to retrieve details for.
     *
     * @response {
     *     "id": 1,
     *     "name": "Age Range Name",
     *     "description": "Age range description",
     *     "created_at": "2023-08-11T12:34:56Z",
     *     "updated_at": "2023-08-11T12:34:56Z"
     * }
     * @response 422 {
     *     "errors": {...}
     * }
     * @response 500 {
     *     "errors": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Update an existing age range.
     * @authenticated
     * @bodyParam name string required The name of the age range.
     * @bodyParam description string The description of the age range.
     *
     * @param \Illuminate\Http\Request $request The request instance.
     * @param AgeRange $ageRange The age range to update.
     *
     * @response {
     *     "id": 1,
     *     "name": "Updated Age Range Name",
     *     "description": "Updated age range description",
     *     "created_at": "2023-08-11T12:34:56Z",
     *     "updated_at": "2023-08-11T12:34:56Z"
     * }
     * @response 422 {
     *     "errors": {...}
     * }
     * @response 500 {
     *     "errors": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Delete an age range.
     *
     * @param AgeRange $ageRange The age range to delete.
     *
     * @response 200 {
     *     "message": "Age range deleted successfully"
     * }
     * @response 500 {
     *     "message": "Oops something went wrong"
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(AgeRange $ageRange)
    {
        try {
            $ageRange->delete();
            return response()->json(['message' => 'Age range deleted successfully'], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Oops something went wrong'], 500);
        }
    }
}
