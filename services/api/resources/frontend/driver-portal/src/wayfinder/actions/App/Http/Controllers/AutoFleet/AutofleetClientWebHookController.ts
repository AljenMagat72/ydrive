import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\AutoFleet\AutofleetClientWebHookController::onboarded
* @see app/Http/Controllers/AutoFleet/AutofleetClientWebHookController.php:18
* @route '/api/webhook/clients/onboarded'
*/
export const onboarded = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: onboarded.url(options),
    method: 'post',
})

onboarded.definition = {
    methods: ["post"],
    url: '/api/webhook/clients/onboarded',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AutoFleet\AutofleetClientWebHookController::onboarded
* @see app/Http/Controllers/AutoFleet/AutofleetClientWebHookController.php:18
* @route '/api/webhook/clients/onboarded'
*/
onboarded.url = (options?: RouteQueryOptions) => {
    return onboarded.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AutoFleet\AutofleetClientWebHookController::onboarded
* @see app/Http/Controllers/AutoFleet/AutofleetClientWebHookController.php:18
* @route '/api/webhook/clients/onboarded'
*/
onboarded.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: onboarded.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AutoFleet\AutofleetClientWebHookController::updated
* @see app/Http/Controllers/AutoFleet/AutofleetClientWebHookController.php:24
* @route '/api/webhook/clients/updated'
*/
export const updated = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updated.url(options),
    method: 'post',
})

updated.definition = {
    methods: ["post"],
    url: '/api/webhook/clients/updated',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AutoFleet\AutofleetClientWebHookController::updated
* @see app/Http/Controllers/AutoFleet/AutofleetClientWebHookController.php:24
* @route '/api/webhook/clients/updated'
*/
updated.url = (options?: RouteQueryOptions) => {
    return updated.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AutoFleet\AutofleetClientWebHookController::updated
* @see app/Http/Controllers/AutoFleet/AutofleetClientWebHookController.php:24
* @route '/api/webhook/clients/updated'
*/
updated.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updated.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AutoFleet\AutofleetClientWebHookController::deleted
* @see app/Http/Controllers/AutoFleet/AutofleetClientWebHookController.php:31
* @route '/api/webhook/clients/deleted'
*/
export const deleted = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleted.url(options),
    method: 'post',
})

deleted.definition = {
    methods: ["post"],
    url: '/api/webhook/clients/deleted',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AutoFleet\AutofleetClientWebHookController::deleted
* @see app/Http/Controllers/AutoFleet/AutofleetClientWebHookController.php:31
* @route '/api/webhook/clients/deleted'
*/
deleted.url = (options?: RouteQueryOptions) => {
    return deleted.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AutoFleet\AutofleetClientWebHookController::deleted
* @see app/Http/Controllers/AutoFleet/AutofleetClientWebHookController.php:31
* @route '/api/webhook/clients/deleted'
*/
deleted.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleted.url(options),
    method: 'post',
})

const AutofleetClientWebHookController = { onboarded, updated, deleted }

export default AutofleetClientWebHookController