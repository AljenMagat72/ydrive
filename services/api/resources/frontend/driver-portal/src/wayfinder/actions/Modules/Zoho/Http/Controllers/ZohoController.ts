import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::show
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:31
* @route '/api/driver-details'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/driver-details',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::show
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:31
* @route '/api/driver-details'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::show
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:31
* @route '/api/driver-details'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::show
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:31
* @route '/api/driver-details'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::getDocuments
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:0
* @route '/api/driver-documents'
*/
export const getDocuments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDocuments.url(options),
    method: 'get',
})

getDocuments.definition = {
    methods: ["get","head"],
    url: '/api/driver-documents',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::getDocuments
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:0
* @route '/api/driver-documents'
*/
getDocuments.url = (options?: RouteQueryOptions) => {
    return getDocuments.definition.url + queryParams(options)
}

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::getDocuments
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:0
* @route '/api/driver-documents'
*/
getDocuments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDocuments.url(options),
    method: 'get',
})

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::getDocuments
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:0
* @route '/api/driver-documents'
*/
getDocuments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getDocuments.url(options),
    method: 'head',
})

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::viewAttachment
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:126
* @route '/api/view-attachment/{fileId}'
*/
export const viewAttachment = (args: { fileId: string | number } | [fileId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewAttachment.url(args, options),
    method: 'get',
})

viewAttachment.definition = {
    methods: ["get","head"],
    url: '/api/view-attachment/{fileId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::viewAttachment
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:126
* @route '/api/view-attachment/{fileId}'
*/
viewAttachment.url = (args: { fileId: string | number } | [fileId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { fileId: args }
    }

    if (Array.isArray(args)) {
        args = {
            fileId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        fileId: args.fileId,
    }

    return viewAttachment.definition.url
            .replace('{fileId}', parsedArgs.fileId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::viewAttachment
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:126
* @route '/api/view-attachment/{fileId}'
*/
viewAttachment.get = (args: { fileId: string | number } | [fileId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewAttachment.url(args, options),
    method: 'get',
})

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::viewAttachment
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:126
* @route '/api/view-attachment/{fileId}'
*/
viewAttachment.head = (args: { fileId: string | number } | [fileId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewAttachment.url(args, options),
    method: 'head',
})

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::updateDocument
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:91
* @route '/api/zoho/update-document'
*/
export const updateDocument = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateDocument.url(options),
    method: 'post',
})

updateDocument.definition = {
    methods: ["post"],
    url: '/api/zoho/update-document',
} satisfies RouteDefinition<["post"]>

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::updateDocument
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:91
* @route '/api/zoho/update-document'
*/
updateDocument.url = (options?: RouteQueryOptions) => {
    return updateDocument.definition.url + queryParams(options)
}

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::updateDocument
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:91
* @route '/api/zoho/update-document'
*/
updateDocument.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateDocument.url(options),
    method: 'post',
})

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::updateProfile
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:139
* @route '/api/zoho/update-profile'
*/
export const updateProfile = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateProfile.url(options),
    method: 'post',
})

updateProfile.definition = {
    methods: ["post"],
    url: '/api/zoho/update-profile',
} satisfies RouteDefinition<["post"]>

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::updateProfile
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:139
* @route '/api/zoho/update-profile'
*/
updateProfile.url = (options?: RouteQueryOptions) => {
    return updateProfile.definition.url + queryParams(options)
}

/**
* @see \Modules\Zoho\Http\Controllers\ZohoController::updateProfile
* @see modules/Zoho/app/Http/Controllers/ZohoController.php:139
* @route '/api/zoho/update-profile'
*/
updateProfile.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateProfile.url(options),
    method: 'post',
})

const ZohoController = { show, getDocuments, viewAttachment, updateDocument, updateProfile }

export default ZohoController