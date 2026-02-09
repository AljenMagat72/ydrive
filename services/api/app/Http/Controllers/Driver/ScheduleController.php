<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\DriverSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Add new split schedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'starts_at' => 'required|date_format:Y-m-d H:i:s',
            'ends_at'   => 'required|date_format:Y-m-d H:i:s',
        ]);

        $validated['driver_id'] = $user->id;

        $startsAt = Carbon::parse($validated['starts_at']);
        $endsAt   = Carbon::parse($validated['ends_at']);

        try {
            $newScheduleIds = [];

            if ($startsAt->isSameDay($endsAt)) {
                $newSchedule = DriverSchedule::create($validated);
                array_push($newScheduleIds, $newSchedule->id);
            } else {
                $newSchedule = DriverSchedule::create([
                    'driver_id' => $user->id,
                    'starts_at' => $startsAt->toDateTimeString(),
                    'ends_at'   => $startsAt->copy()->addDay()->startOfDay()->toDateTimeString(),
                ]);
                array_push($newScheduleIds, $newSchedule->id);

                $newSchedule2 = DriverSchedule::create([
                    'driver_id' => $user->id,
                    'starts_at' => $endsAt->copy()->startOfDay()->toDateTimeString(),
                    'ends_at'   => $endsAt->toDateTimeString(),
                ]);

                array_push($newScheduleIds, $newSchedule2->id);
            }

            return response()->json([
                'message' => 'Schedule added successfully',
                'success' => true,
                'slot_id' => $newScheduleIds,
            ], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
