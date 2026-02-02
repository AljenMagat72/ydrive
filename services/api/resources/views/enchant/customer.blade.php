@if ($activeRide !== null)
  @include('enchant._ride', [
    'ride' => $activeRide,
    'title' => 'Active Ride',
  ])
@endif

@if ($latestRide !== null)
  @include('enchant._ride', [
    'ride' => $latestRide,
    'title' => 'Latest Ride',
  ])
@endif
@if ($upcomingRide !== null)
  @include('enchant._ride', [
    'ride' => $upcomingRide,
    'title' => 'Upcoming Ride',
  ])
@endif

@if($activeRide === null && $latestRide === null && $upcomingRide === null)
  <div>No Rides Found</div>
@endif