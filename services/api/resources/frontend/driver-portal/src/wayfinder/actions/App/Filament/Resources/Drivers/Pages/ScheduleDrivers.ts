import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Drivers\Pages\ScheduleDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ScheduleDrivers.php:7
* @route '/admin/drivers/schedule'
*/
const ScheduleDrivers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ScheduleDrivers.url(options),
    method: 'get',
})

ScheduleDrivers.definition = {
    methods: ["get","head"],
    url: '/admin/drivers/schedule',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\ScheduleDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ScheduleDrivers.php:7
* @route '/admin/drivers/schedule'
*/
ScheduleDrivers.url = (options?: RouteQueryOptions) => {
    return ScheduleDrivers.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\ScheduleDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ScheduleDrivers.php:7
* @route '/admin/drivers/schedule'
*/
ScheduleDrivers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ScheduleDrivers.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\ScheduleDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ScheduleDrivers.php:7
* @route '/admin/drivers/schedule'
*/
ScheduleDrivers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ScheduleDrivers.url(options),
    method: 'head',
})

export default ScheduleDrivers