import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Drivers\Pages\ViewDriver::__invoke
* @see app/Filament/Resources/Drivers/Pages/ViewDriver.php:7
* @route '/admin/drivers/{record}'
*/
const ViewDriver = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ViewDriver.url(args, options),
    method: 'get',
})

ViewDriver.definition = {
    methods: ["get","head"],
    url: '/admin/drivers/{record}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\ViewDriver::__invoke
* @see app/Filament/Resources/Drivers/Pages/ViewDriver.php:7
* @route '/admin/drivers/{record}'
*/
ViewDriver.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    if (Array.isArray(args)) {
        args = {
            record: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        record: args.record,
    }

    return ViewDriver.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\ViewDriver::__invoke
* @see app/Filament/Resources/Drivers/Pages/ViewDriver.php:7
* @route '/admin/drivers/{record}'
*/
ViewDriver.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ViewDriver.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\ViewDriver::__invoke
* @see app/Filament/Resources/Drivers/Pages/ViewDriver.php:7
* @route '/admin/drivers/{record}'
*/
ViewDriver.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ViewDriver.url(args, options),
    method: 'head',
})

export default ViewDriver