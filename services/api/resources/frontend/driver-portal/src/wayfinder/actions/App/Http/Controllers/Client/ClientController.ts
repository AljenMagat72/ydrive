import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Client\ClientController::search
* @see app/Http/Controllers/Client/ClientController.php:156
* @route '/api/v1/admin/client/find'
*/
export const search = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: search.url(options),
    method: 'post',
})

search.definition = {
    methods: ["post"],
    url: '/api/v1/admin/client/find',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Client\ClientController::search
* @see app/Http/Controllers/Client/ClientController.php:156
* @route '/api/v1/admin/client/find'
*/
search.url = (options?: RouteQueryOptions) => {
    return search.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\ClientController::search
* @see app/Http/Controllers/Client/ClientController.php:156
* @route '/api/v1/admin/client/find'
*/
search.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: search.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Client\ClientController::ridesById
* @see app/Http/Controllers/Client/ClientController.php:187
* @route '/api/v1/admin/client/{id}/rides'
*/
export const ridesById = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ridesById.url(args, options),
    method: 'get',
})

ridesById.definition = {
    methods: ["get","head"],
    url: '/api/v1/admin/client/{id}/rides',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Client\ClientController::ridesById
* @see app/Http/Controllers/Client/ClientController.php:187
* @route '/api/v1/admin/client/{id}/rides'
*/
ridesById.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return ridesById.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Client\ClientController::ridesById
* @see app/Http/Controllers/Client/ClientController.php:187
* @route '/api/v1/admin/client/{id}/rides'
*/
ridesById.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ridesById.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Client\ClientController::ridesById
* @see app/Http/Controllers/Client/ClientController.php:187
* @route '/api/v1/admin/client/{id}/rides'
*/
ridesById.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ridesById.url(args, options),
    method: 'head',
})

const ClientController = { search, ridesById }

export default ClientController