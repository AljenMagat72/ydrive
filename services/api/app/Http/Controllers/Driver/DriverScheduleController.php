<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Driver\CreateScheduleRequest;
use App\Http\Requests\Driver\DailyScheduleRequest;
use App\Http\Requests\Driver\DeleteScheduleRequest;
use App\Http\Requests\Driver\WeeklyScheduleRequest;
use App\Http\Resources\Driver\DriverAdminResource;
use App\Http\Resources\Driver\DriverCityScheduleResource;
use App\Http\Resources\Driver\DriverDelinquentResource;
use App\Http\Resources\Driver\DriverScheduleResource;
use App\Models\Driver;
use App\Services\DriverScheduleService;
use App\Services\DriverService;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Enums\Role;

class DriverScheduleController extends Controller
{

  public function __construct(
    protected DriverScheduleService $driverScheduleService,
    protected DriverService $driverService,
  ) {}
  /**
   * Create a schedule.
   */
  public function store(CreateScheduleRequest $request): JsonResponse
  {
    $this
      ->driverScheduleService
      ->addWeek(
        $request->user()->id,
        $request->input('schedule'),
        $request->input('startDate')
      );

    return response()->json([
      'success' => true,
    ]);
  }

  /**
   * Get a list of drivers weekly schedule
   */
  public function weekly(WeeklyScheduleRequest $request): JsonResponse
  {
    try {

      // Get the authenticated user
      $user = $request->user();

      // Read query parameters
      $date = $request->query('start_date', now()->format('Y-m-d'));

      if ($user->role === Role::ADMIN) {
        $driverId = $request->query('driver_id', $user->id);
      } else {
        $driverId = $user->id;
      }

      // Fetch schedule from service
      $schedule = $this->driverScheduleService->weekly($driverId, $date);

      return response()->json([
        'user' => $user->only('id', 'name', 'email'),
        'driver_id' => $driverId,
        'date' => $date,
        'schedule' => $schedule,
        'success' => true,
      ]);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Delete a schedule.
   */
  public function delete(DeleteScheduleRequest $request): JsonResponse
  {
    try {
      return response()->json([
        'success' => $this->driverScheduleService->delete(
          $request->user()->id,
          $request->input('date')
        ),
      ]);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * Gets the daily schedule for drivers city
   */

  public function dailyCity(Request $request): JsonResponse
  {
    try {
      $city = $request->user()->role == Role::ADMIN ? $request->input('city') : $request->user()->city_id;

      $schedules = $this->driverScheduleService->daily($request->input('date'), $city);

      return response()->json([
        'success' => true,
        'schedules' => DriverCityScheduleResource::collection($schedules),
      ]);
    } catch (\Throwable $th) {
      throw $th;
    }
  }
  /**
   * List drivers schedules by day.
   */
  public function daily(DailyScheduleRequest $request): JsonResponse
  {
    $schedules = $this->driverScheduleService->daily($request->input('date'), $request->input('city'));

    return response()->json([
      'success' => true,
      'schedules' => DriverScheduleResource::collection($schedules),
    ]);
  }

  public function all(\Illuminate\Http\Request $request): JsonResponse
  {
    $startOfWeek = Carbon::now()->startOfWeek(Carbon::SUNDAY);
    $endOfWeek = Carbon::now()->endOfWeek(Carbon::SATURDAY);

    $startOfNextWeek = $startOfWeek->copy()->addWeek();
    $endOfNextWeek = $endOfWeek->copy()->addWeek();

    $drivers = Driver::withExists([
      'schedules as has_current_schedule' => function ($query) use ($startOfWeek, $endOfWeek) {
        $query->where(function ($q) use ($startOfWeek, $endOfWeek) {
          $q->whereBetween('starts_at', [$startOfWeek, $endOfWeek])
            ->orWhereBetween('ends_at', [$startOfWeek, $endOfWeek])
            ->orWhere(function ($sub) use ($startOfWeek, $endOfWeek) {
              $sub->where('starts_at', '<=', $startOfWeek)
                ->where('ends_at', '>=', $endOfWeek);
            });
        });
      },
      'schedules as has_next_schedule' => function ($query) use ($startOfNextWeek, $endOfNextWeek) {
        $query->where(function ($q) use ($startOfNextWeek, $endOfNextWeek) {
          $q->whereBetween('starts_at', [$startOfNextWeek, $endOfNextWeek])
            ->orWhereBetween('ends_at', [$startOfNextWeek, $endOfNextWeek])
            ->orWhere(function ($sub) use ($startOfNextWeek, $endOfNextWeek) {
              $sub->where('starts_at', '<=', $startOfNextWeek)
                ->where('ends_at', '>=', $endOfNextWeek);
            });
        });
      },
    ])
      ->where('city_id', 'NOT LIKE', '%NO OPPS%')
      ->get();

    return response()->json([
      'success' => true,
      'drivers' => DriverAdminResource::collection($drivers),
    ]);
  }

  public function delinquents(\Illuminate\Http\Request $request): JsonResponse
  {
    $drivers = Driver::where([
      'is_active' => true,
      'is_delinquent' => true,
    ])
      ->with('currentDelinquentPeriod')
      ->get();

    return response()->json([
      'success' => true,
      'delinquents' => DriverDelinquentResource::collection($drivers),
    ]);
  }

  public function deleteSplitSchedule($id)
  {
    $schedule = \App\Models\DriverSchedule::find($id);

    if (! $schedule) {
      return response()->json(['message' => 'Schedule not found'], 404);
    }

    $schedule->delete();

    return response()->json([
      'message' => 'Schedule deleted successfully',
      'success' => true,
    ]);
  }

  public function updateSchedule(Request $request, $id)
  {
    // Validate input
    $validated = $request->validate([
      'minimum_scheduled_hours' => 'required|numeric|min:0',
      'acceptance_rate' => 'required|numeric|min:0|max:100',
    ]);

    // Find the driver
    $driver = Driver::findOrFail($id);

    // Update the fields
    $driver->minimum_scheduled_hours = $validated['minimum_scheduled_hours'];
    $driver->acceptance_rate_needed = $validated['acceptance_rate'];
    $driver->save();

    return response()->json([
      'message' => 'Driver schedule updated successfully',
      'driver' => $driver
    ]);
  }
}
