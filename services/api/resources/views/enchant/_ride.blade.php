<div>
  <a href="https://control.autofleet.io/6FnkvuL1DSM3pe847fDhCX/ride/{{ $ride['id'] }}">
    {{ $title }}
  </a>

  @if(!empty($ride['priceAmount']))
    <p><b>Price:</b> ${{ $ride['priceAmount'] }}</p>
  @endif

  @if(!empty($ride['createdAt']))
    <p><b>Created At:</b> {{ \Carbon\Carbon::parse($ride['createdAt'])->format('M d, Y g:i A') }}</p>
  @endif

  @if(!empty($ride['scheduledTo']))
    <p><b>Scheduled At:</b> {{ \Carbon\Carbon::parse($ride['scheduledTo'])->format('M d, Y g:i A') }}</p>
  @endif

  @if(!empty($ride['dispatchedAt']))
    <p><b>Dispatched At:</b> {{ \Carbon\Carbon::parse($ride['dispatchedAt'])->format('M d, Y g:i A') }}</p>
  @endif

  @if(!empty($ride['finalizedAt']))
    <p><b>Completed At:</b> {{ \Carbon\Carbon::parse($ride['finalizedAt'])->format('M d, Y g:i A') }}</p>
  @endif

  @if(!empty($ride['driver']))
    <p>
      <b>Driver:</b> {{ $ride['driver']['firstName'] ?? '' }}
      {{ $ride['driver']['lastName'] ?? '' }}
    </p>
  @endif

  @if(!empty($ride['stopPoints']))
    <p><b>Stop Points:</b></p>
    <ol>
      @foreach ($ride['stopPoints'] as $stopPoint)
        <li>{{ $stopPoint['description'] ?? '' }}</li>
      @endforeach
    </ol>
  @endif
</div>