<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\CreateScheduleRequest;
use App\Http\Requests\Driver\DailyScheduleRequest;
use App\Http\Requests\Driver\WeeklyScheduleRequest;
use App\Http\Resources\Driver\DriverScheduleResource;
use App\Models\Driver;
use App\Models\DriverSchedule;
use App\Services\Driver\DriverScheduleService;
use App\Services\Driver\DriverService;
use Illuminate\Http\JsonResponse;

class DriverScheduleController extends Controller
{
    public function __construct(
        protected DriverScheduleService $driverScheduleService,
        protected DriverService $driverService,
    ) {
    }

    public function store(Driver $driver, CreateScheduleRequest $request): JsonResponse
    {
        $schedule = $this
            ->driverScheduleService
            ->add($driver->id, $request->input('startsAt'), $request->input('endsAt'));

        return new DriverScheduleResource($schedule)->toResponse($request);
    }

    public function weekly(Driver $driver, WeeklyScheduleRequest $request): JsonResponse
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $schedule = $this->driverScheduleService->weekly($driver->id, $date);

        return DriverScheduleResource::collection($schedule)->response();
    }

    public function delete(Driver $driver, DriverSchedule $schedule): JsonResponse
    {
        $this->driverScheduleService->delete($schedule);
        return response()->json([]);
    }

    public function city(Driver $driver,DailyScheduleRequest $request): JsonResponse
    {
        $schedules = $this->driverScheduleService->daily($request->input('date'), $driver->city_id);

        return DriverScheduleResource::collection($schedules)->toResponse($request);
    }
}
