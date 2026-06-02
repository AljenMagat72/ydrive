import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\AutoFleet\AutoFleetWebHookController::rideUpdated
* @see app/Http/Controllers/AutoFleet/AutoFleetWebHookController.php:39
* @route '/api/webhook/ride-updated'
*/
export const rideUpdated = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rideUpdated.url(options),
    method: 'post',
})

rideUpdated.definition = {
    methods: ["post"],
    url: '/api/webhook/ride-updated',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AutoFleet\AutoFleetWebHookController::rideUpdated
* @see app/Http/Controllers/AutoFleet/AutoFleetWebHookController.php:39
* @route '/api/webhook/ride-updated'
*/
rideUpdated.url = (options?: RouteQueryOptions) => {
    return rideUpdated.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AutoFleet\AutoFleetWebHookController::rideUpdated
* @see app/Http/Controllers/AutoFleet/AutoFleetWebHookController.php:39
* @route '/api/webhook/ride-updated'
*/
rideUpdated.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rideUpdated.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AutoFleet\AutoFleetWebHookController::driverCreation
* @see app/Http/Controllers/AutoFleet/AutoFleetWebHookController.php:29
* @route '/api/webhook/driver-created'
*/
export const driverCreation = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: driverCreation.url(options),
    method: 'post',
})

driverCreation.definition = {
    methods: ["post"],
    url: '/api/webhook/driver-created',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AutoFleet\AutoFleetWebHookController::driverCreation
* @see app/Http/Controllers/AutoFleet/AutoFleetWebHookController.php:29
* @route '/api/webhook/driver-created'
*/
driverCreation.url = (options?: RouteQueryOptions) => {
    return driverCreation.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AutoFleet\AutoFleetWebHookController::driverCreation
* @see app/Http/Controllers/AutoFleet/AutoFleetWebHookController.php:29
* @route '/api/webhook/driver-created'
*/
driverCreation.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: driverCreation.url(options),
    method: 'post',
})

const AutoFleetWebHookController = { rideUpdated, driverCreation }

export default AutoFleetWebHookController