import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Drivers\Pages\ListDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ListDrivers.php:7
* @route '/admin/drivers/list'
*/
const ListDrivers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListDrivers.url(options),
    method: 'get',
})

ListDrivers.definition = {
    methods: ["get","head"],
    url: '/admin/drivers/list',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\ListDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ListDrivers.php:7
* @route '/admin/drivers/list'
*/
ListDrivers.url = (options?: RouteQueryOptions) => {
    return ListDrivers.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\ListDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ListDrivers.php:7
* @route '/admin/drivers/list'
*/
ListDrivers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListDrivers.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\ListDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ListDrivers.php:7
* @route '/admin/drivers/list'
*/
ListDrivers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListDrivers.url(options),
    method: 'head',
})

export default ListDrivers