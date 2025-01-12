<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;

class AppointmentController extends Controller
{
    public function index()
    {
        return Appointment::query()
            ->with('client:id,first_name,last_name')
            ->when(request('status'), function ($query) {
                return $query->where('status', AppointmentStatus::from(request('status')));
            })
            ->latest()
            ->paginate()
            ->through(fn ($appointment) => [
                'id' => $appointment->id,
                'start_time' => $appointment->start_time->format('Y-m-d h:i A'),
                'end_time' => $appointment->end_time->format('Y-m-d h:i A'),
                'status' => [
                    'name' => $appointment->status->name,
                    'color' => $appointment->status->color(),
                ],
                'client' => $appointment->client,
            ]);
    }

    public function store()
    {
        $validated = request()->validate(
            [
                'title' => 'required',
                'client_id' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'description' => 'required',
            ],
            [
                'client_id.required' => 'the client name field is required.'
            ]
        );
        Appointment::create([
            'title' => $validated['title'],
            'client_id' => $validated['client_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'description' => $validated['description'],
            'status' => AppointmentStatus::SCHEDULED
        ]);
        return response()->json(['message' => 'success']);
    }

    public function edit(Appointment $appointment)
    {
        return $appointment;
    }

    public function update(Appointment $appointment)
    {
        $validated = request()->validate(
            [
                'title' => 'required',
                'client_id' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'description' => 'required',
            ],
            [
                'client_id.required' => 'the client name field is required.'
            ]
        );
        $appointment->update($validated);
        return response()->json(['success' => true]);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json(['success' => true], 200);
    }
}
