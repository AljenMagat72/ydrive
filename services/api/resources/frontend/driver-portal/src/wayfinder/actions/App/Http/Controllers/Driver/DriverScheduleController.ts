import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::weekly
* @see app/Http/Controllers/Driver/DriverScheduleController.php:33
* @route '/api/v1/driver/{driver}/schedule/weekly'
*/
export const weekly = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: weekly.url(args, options),
    method: 'get',
})

weekly.definition = {
    methods: ["get","head"],
    url: '/api/v1/driver/{driver}/schedule/weekly',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::weekly
* @see app/Http/Controllers/Driver/DriverScheduleController.php:33
* @route '/api/v1/driver/{driver}/schedule/weekly'
*/
weekly.url = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { driver: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'uuid' in args) {
        args = { driver: args.uuid }
    }

    if (Array.isArray(args)) {
        args = {
            driver: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        driver: typeof args.driver === 'object'
        ? args.driver.uuid
        : args.driver,
    }

    return weekly.definition.url
            .replace('{driver}', parsedArgs.driver.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::weekly
* @see app/Http/Controllers/Driver/DriverScheduleController.php:33
* @route '/api/v1/driver/{driver}/schedule/weekly'
*/
weekly.get = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: weekly.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::weekly
* @see app/Http/Controllers/Driver/DriverScheduleController.php:33
* @route '/api/v1/driver/{driver}/schedule/weekly'
*/
weekly.head = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: weekly.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::city
* @see app/Http/Controllers/Driver/DriverScheduleController.php:47
* @route '/api/v1/driver/{driver}/schedule/city'
*/
export const city = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: city.url(args, options),
    method: 'get',
})

city.definition = {
    methods: ["get","head"],
    url: '/api/v1/driver/{driver}/schedule/city',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::city
* @see app/Http/Controllers/Driver/DriverScheduleController.php:47
* @route '/api/v1/driver/{driver}/schedule/city'
*/
city.url = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { driver: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'uuid' in args) {
        args = { driver: args.uuid }
    }

    if (Array.isArray(args)) {
        args = {
            driver: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        driver: typeof args.driver === 'object'
        ? args.driver.uuid
        : args.driver,
    }

    return city.definition.url
            .replace('{driver}', parsedArgs.driver.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::city
* @see app/Http/Controllers/Driver/DriverScheduleController.php:47
* @route '/api/v1/driver/{driver}/schedule/city'
*/
city.get = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: city.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::city
* @see app/Http/Controllers/Driver/DriverScheduleController.php:47
* @route '/api/v1/driver/{driver}/schedule/city'
*/
city.head = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: city.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::store
* @see app/Http/Controllers/Driver/DriverScheduleController.php:24
* @route '/api/v1/driver/{driver}/schedule'
*/
export const store = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/driver/{driver}/schedule',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::store
* @see app/Http/Controllers/Driver/DriverScheduleController.php:24
* @route '/api/v1/driver/{driver}/schedule'
*/
store.url = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { driver: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'uuid' in args) {
        args = { driver: args.uuid }
    }

    if (Array.isArray(args)) {
        args = {
            driver: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        driver: typeof args.driver === 'object'
        ? args.driver.uuid
        : args.driver,
    }

    return store.definition.url
            .replace('{driver}', parsedArgs.driver.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::store
* @see app/Http/Controllers/Driver/DriverScheduleController.php:24
* @route '/api/v1/driver/{driver}/schedule'
*/
store.post = (args: { driver: string | { uuid: string } } | [driver: string | { uuid: string } ] | string | { uuid: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::deleteMethod
* @see app/Http/Controllers/Driver/DriverScheduleController.php:41
* @route '/api/v1/driver/{driver}/schedule/{schedule}'
*/
export const deleteMethod = (args: { driver: string | { uuid: string }, schedule: string | { uuid: string } } | [driver: string | { uuid: string }, schedule: string | { uuid: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/api/v1/driver/{driver}/schedule/{schedule}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::deleteMethod
* @see app/Http/Controllers/Driver/DriverScheduleController.php:41
* @route '/api/v1/driver/{driver}/schedule/{schedule}'
*/
deleteMethod.url = (args: { driver: string | { uuid: string }, schedule: string | { uuid: string } } | [driver: string | { uuid: string }, schedule: string | { uuid: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            driver: args[0],
            schedule: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        driver: typeof args.driver === 'object'
        ? args.driver.uuid
        : args.driver,
        schedule: typeof args.schedule === 'object'
        ? args.schedule.uuid
        : args.schedule,
    }

    return deleteMethod.definition.url
            .replace('{driver}', parsedArgs.driver.toString())
            .replace('{schedule}', parsedArgs.schedule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Driver\DriverScheduleController::deleteMethod
* @see app/Http/Controllers/Driver/DriverScheduleController.php:41
* @route '/api/v1/driver/{driver}/schedule/{schedule}'
*/
deleteMethod.delete = (args: { driver: string | { uuid: string }, schedule: string | { uuid: string } } | [driver: string | { uuid: string }, schedule: string | { uuid: string } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

const DriverScheduleController = { weekly, city, store, deleteMethod, delete: deleteMethod }

export default DriverScheduleController