import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Drivers\Pages\OverviewDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/OverviewDrivers.php:7
* @route '/admin/drivers'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/drivers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\OverviewDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/OverviewDrivers.php:7
* @route '/admin/drivers'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\OverviewDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/OverviewDrivers.php:7
* @route '/admin/drivers'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\OverviewDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/OverviewDrivers.php:7
* @route '/admin/drivers'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\ListDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ListDrivers.php:7
* @route '/admin/drivers/list'
*/
export const list = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: list.url(options),
    method: 'get',
})

list.definition = {
    methods: ["get","head"],
    url: '/admin/drivers/list',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\ListDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ListDrivers.php:7
* @route '/admin/drivers/list'
*/
list.url = (options?: RouteQueryOptions) => {
    return list.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\ListDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ListDrivers.php:7
* @route '/admin/drivers/list'
*/
list.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: list.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\ListDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ListDrivers.php:7
* @route '/admin/drivers/list'
*/
list.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: list.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\ScheduleDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ScheduleDrivers.php:7
* @route '/admin/drivers/schedule'
*/
export const schedule = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: schedule.url(options),
    method: 'get',
})

schedule.definition = {
    methods: ["get","head"],
    url: '/admin/drivers/schedule',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\ScheduleDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ScheduleDrivers.php:7
* @route '/admin/drivers/schedule'
*/
schedule.url = (options?: RouteQueryOptions) => {
    return schedule.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\ScheduleDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ScheduleDrivers.php:7
* @route '/admin/drivers/schedule'
*/
schedule.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: schedule.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\ScheduleDrivers::__invoke
* @see app/Filament/Resources/Drivers/Pages/ScheduleDrivers.php:7
* @route '/admin/drivers/schedule'
*/
schedule.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: schedule.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\DriverCMS::__invoke
* @see app/Filament/Resources/Drivers/Pages/DriverCMS.php:7
* @route '/admin/drivers/cms'
*/
export const cms = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cms.url(options),
    method: 'get',
})

cms.definition = {
    methods: ["get","head"],
    url: '/admin/drivers/cms',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\DriverCMS::__invoke
* @see app/Filament/Resources/Drivers/Pages/DriverCMS.php:7
* @route '/admin/drivers/cms'
*/
cms.url = (options?: RouteQueryOptions) => {
    return cms.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\DriverCMS::__invoke
* @see app/Filament/Resources/Drivers/Pages/DriverCMS.php:7
* @route '/admin/drivers/cms'
*/
cms.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cms.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\DriverCMS::__invoke
* @see app/Filament/Resources/Drivers/Pages/DriverCMS.php:7
* @route '/admin/drivers/cms'
*/
cms.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: cms.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\DriverSettings::__invoke
* @see app/Filament/Resources/Drivers/Pages/DriverSettings.php:7
* @route '/admin/drivers/settings'
*/
export const settings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})

settings.definition = {
    methods: ["get","head"],
    url: '/admin/drivers/settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\DriverSettings::__invoke
* @see app/Filament/Resources/Drivers/Pages/DriverSettings.php:7
* @route '/admin/drivers/settings'
*/
settings.url = (options?: RouteQueryOptions) => {
    return settings.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\DriverSettings::__invoke
* @see app/Filament/Resources/Drivers/Pages/DriverSettings.php:7
* @route '/admin/drivers/settings'
*/
settings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: settings.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\DriverSettings::__invoke
* @see app/Filament/Resources/Drivers/Pages/DriverSettings.php:7
* @route '/admin/drivers/settings'
*/
settings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: settings.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\ViewDriver::__invoke
* @see app/Filament/Resources/Drivers/Pages/ViewDriver.php:7
* @route '/admin/drivers/{record}'
*/
export const view = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/admin/drivers/{record}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Drivers\Pages\ViewDriver::__invoke
* @see app/Filament/Resources/Drivers/Pages/ViewDriver.php:7
* @route '/admin/drivers/{record}'
*/
view.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return view.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\Drivers\Pages\ViewDriver::__invoke
* @see app/Filament/Resources/Drivers/Pages/ViewDriver.php:7
* @route '/admin/drivers/{record}'
*/
view.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Drivers\Pages\ViewDriver::__invoke
* @see app/Filament/Resources/Drivers/Pages/ViewDriver.php:7
* @route '/admin/drivers/{record}'
*/
view.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

const drivers = {
    index: Object.assign(index, index),
    list: Object.assign(list, list),
    schedule: Object.assign(schedule, schedule),
    cms: Object.assign(cms, cms),
    settings: Object.assign(settings, settings),
    view: Object.assign(view, view),
}

export default drivers