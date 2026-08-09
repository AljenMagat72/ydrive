<div class="flex flex-col space-y-4">
    <div class="z-20">
        {{ $this->form }}
    </div>

    <div class="flex-1 relative">
        <div
            class="text-sm absolute inset-0 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-900 shadow-sm overflow-scroll"
            x-data="timeIndicator()"
        >
            <div class="w-full min-w-max h-full min-h-max flex flex-col">
                <div
                    class="grid grid-cols-24 auto-rows-auto sticky top-0 py-2 z-10 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-white/10 text-zinc-500 dark:text-zinc-400 font-medium">
                    @foreach($this->timeSlots as $slot)
                        <div class="text-center px-1 text-xs">{{ $slot }}</div>
                    @endforeach
                </div>

                <div class="relative bg-striped fi-color-gray grow">
                    <template
                        x-if="new Date().toLocaleString('en-CA').slice(0, 10) === '{{ \Carbon\Carbon::parse($this->date)->toDateString() }}'"
                    >
                        <div
                            class="absolute top-0 bottom-0 w-0.5 bg-primary-600 dark:bg-primary-500 z-10 pointer-events-none"
                            :style="`left: ${position}%`"
                        ></div>
                    </template>

                    @foreach($schedules as $schedule)
                        <div class="relative h-8 my-2">
                            @foreach($schedule as $shift)
                                <a
                                    class="block bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 text-center h-full absolute rounded-md content-center overflow-hidden truncate px-2 text-xs font-medium transition"
                                    style="left: {{ $shift['left'] }}%; width: {{ $shift['width'] }}%"
                                    target="_blank"
                                    href="{{ \App\Filament\Resources\Drivers\DriverResource::getUrl('view', ['record' => $shift['driver']['uuid']]) }}"
                                >
                                    {{ $shift['driver']['first_name'] }} {{ $shift['driver']['last_name'] }}
                                </a>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function timeIndicator() {
            return {
                position: 0,
                init() {
                    const tick = () => {
                        const now = new Date();
                        const minutes = now.getHours() * 60 + now.getMinutes() + now.getSeconds() / 60;
                        this.position = (minutes / 1440) * 100;
                        requestAnimationFrame(tick);
                    };
                    tick();
                }
            }
        }
    </script>
@endpush
