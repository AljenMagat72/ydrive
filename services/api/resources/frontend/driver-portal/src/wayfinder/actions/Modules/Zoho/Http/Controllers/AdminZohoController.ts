import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::show
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:26
* @route '/api/admin/driver-details/{zohoId}'
*/
export const show = (args: { zohoId: string | number } | [zohoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/admin/driver-details/{zohoId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::show
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:26
* @route '/api/admin/driver-details/{zohoId}'
*/
show.url = (args: { zohoId: string | number } | [zohoId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { zohoId: args }
    }

    if (Array.isArray(args)) {
        args = {
            zohoId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        zohoId: args.zohoId,
    }

    return show.definition.url
            .replace('{zohoId}', parsedArgs.zohoId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::show
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:26
* @route '/api/admin/driver-details/{zohoId}'
*/
show.get = (args: { zohoId: string | number } | [zohoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::show
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:26
* @route '/api/admin/driver-details/{zohoId}'
*/
show.head = (args: { zohoId: string | number } | [zohoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::viewAttachment
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:66
* @route '/api/admin/view-attachment/{zohoId}/{fileId}'
*/
export const viewAttachment = (args: { zohoId: string | number, fileId: string | number } | [zohoId: string | number, fileId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewAttachment.url(args, options),
    method: 'get',
})

viewAttachment.definition = {
    methods: ["get","head"],
    url: '/api/admin/view-attachment/{zohoId}/{fileId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::viewAttachment
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:66
* @route '/api/admin/view-attachment/{zohoId}/{fileId}'
*/
viewAttachment.url = (args: { zohoId: string | number, fileId: string | number } | [zohoId: string | number, fileId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            zohoId: args[0],
            fileId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        zohoId: args.zohoId,
        fileId: args.fileId,
    }

    return viewAttachment.definition.url
            .replace('{zohoId}', parsedArgs.zohoId.toString())
            .replace('{fileId}', parsedArgs.fileId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::viewAttachment
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:66
* @route '/api/admin/view-attachment/{zohoId}/{fileId}'
*/
viewAttachment.get = (args: { zohoId: string | number, fileId: string | number } | [zohoId: string | number, fileId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewAttachment.url(args, options),
    method: 'get',
})

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::viewAttachment
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:66
* @route '/api/admin/view-attachment/{zohoId}/{fileId}'
*/
viewAttachment.head = (args: { zohoId: string | number, fileId: string | number } | [zohoId: string | number, fileId: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewAttachment.url(args, options),
    method: 'head',
})

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::downloadZip
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:76
* @route '/api/admin/driver-documents-zip/{zohoId}'
*/
export const downloadZip = (args: { zohoId: string | number } | [zohoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: downloadZip.url(args, options),
    method: 'post',
})

downloadZip.definition = {
    methods: ["post"],
    url: '/api/admin/driver-documents-zip/{zohoId}',
} satisfies RouteDefinition<["post"]>

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::downloadZip
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:76
* @route '/api/admin/driver-documents-zip/{zohoId}'
*/
downloadZip.url = (args: { zohoId: string | number } | [zohoId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { zohoId: args }
    }

    if (Array.isArray(args)) {
        args = {
            zohoId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        zohoId: args.zohoId,
    }

    return downloadZip.definition.url
            .replace('{zohoId}', parsedArgs.zohoId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Modules\Zoho\Http\Controllers\AdminZohoController::downloadZip
* @see modules/Zoho/app/Http/Controllers/AdminZohoController.php:76
* @route '/api/admin/driver-documents-zip/{zohoId}'
*/
downloadZip.post = (args: { zohoId: string | number } | [zohoId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: downloadZip.url(args, options),
    method: 'post',
})

const AdminZohoController = { show, viewAttachment, downloadZip }

export default AdminZohoController