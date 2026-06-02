import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Drivers\Pages\OverviewDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/OverviewDrivers.php:7
* @route '/admin/drivers'
*/
const OverviewDrivers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: OverviewDrivers.url(options),
    method: 'get',
})

OverviewDrivers.definition = {
    methods: ["get","head"],
    url: '/admin/drivers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\OverviewDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/OverviewDrivers.php:7
* @route '/admin/drivers'
*/
OverviewDrivers.url = (options?: RouteQueryOptions) => {
    return OverviewDrivers.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\OverviewDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/OverviewDrivers.php:7
* @route '/admin/drivers'
*/
OverviewDrivers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: OverviewDrivers.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\OverviewDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/OverviewDrivers.php:7
* @route '/admin/drivers'
*/
OverviewDrivers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: OverviewDrivers.url(options),
    method: 'head',
})

export default OverviewDrivers