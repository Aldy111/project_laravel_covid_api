<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\Statuses;
use Illuminate\Http\Request;

class PatientsController extends Controller
{
    public function getStatusId($input)
    {
        $status = Statuses::all();

        $data = [];

        foreach ($status as $status) {

            $name = $status->name;
            $id = $status->id;

            $data[$name] = $id;
        }

        return $data[strtolower($input)] ?? "";
    }
    public function formatPatient($patient)
    {
        return [
            'id' => $patient->id,
            'name' => $patient->name,
            'phone' => $patient->phone,
            'address' => $patient->address,
            'status' => ucwords($patient->status->name),
            'in_date_at' => $patient->in_date_at,
            'out_date_at' => $patient->out_date_at,
            'created_at' => $patient->created_at,
            'updated_at' => $patient->updated_at

        ];
    }

    public function status($status)
    {
        $id_status = $this->getStatusId($status);

        if ($id_status) {
            $patients = Patients::where('status_id' . $id_status)->get();

            if ($patients->isNotEmpty()) {
                $patients = $patients->map(
                    function ($patient) {
                        return $this->formatPatient($patient);
                    }
                );

                $messages =  [
                    'message' => 'Get Status Resource',
                    'total' => count($patients),
                    'data' => $patients
                ];
                return response()->json($messages, 200);
            } else {

                $messages = [
                    'message' => 'resource not found'
                ];
                return response()->json($messages, 404);
            }
        } else {

            $messages = [
                'message' => 'status not found'
            ];
            return response()->json($messages, 204);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $patients = Patients::all();

        return response()->json($patients, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'status' => 'required',
            'in_date_at' => 'required'
        ]);
        $status = $validated['status'];
        $validated['status_id'] = $this->getStatusId($status);

        $patient = Patients::create($validated);
        $patient = $this->formatPatient($patient);

        $messages = [
            'message' => 'create patient is successfully',
            'patient' => $patient
        ];
        return response()->json($messages, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Patients  $patients
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $patient = Patients::find($id);

        if ($patient) {
            $patient = $patient = $this->formatPatient($patient);
            $messages = [
                'message' => 'get detail student',
                'data' => $patient
            ];

            return response()->json($messages, 200);
        } else {
            $messages = [
                'message' => 'patient by id not found',
                'data' => $patient
            ];
            return response()->json($messages, 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Patients  $patients
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $patient = Patients::find($id);

        if ($patient) {
            if ($request->status) {
                $status = $request->status;

                $request['status_id'] = $this->getStatusId($status);
            }

            $patient->update($request->all());
            $patient = $this->formatPatient($patient);

            $messages = [
                'message' => 'update is successfully',
                'data' => $patient
            ];
            return response()->json($messages, 200);
        } else {

            $messages = [
                'message' => 'resource not found'
            ];
            return response()->json($messages, 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Patients  $patients
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
