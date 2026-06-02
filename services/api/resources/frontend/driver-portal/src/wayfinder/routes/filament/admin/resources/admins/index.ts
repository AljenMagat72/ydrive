import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Admins\Pages\ManageAdmins::__invoke
* @see app/Filament/Resources/Admins/Pages/ManageAdmins.php:7
* @route '/admin/admins'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/admins',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Admins\Pages\ManageAdmins::__invoke
* @see app/Filament/Resources/Admins/Pages/ManageAdmins.php:7
* @route '/admin/admins'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Admins\Pages\ManageAdmins::__invoke
* @see app/Filament/Resources/Admins/Pages/ManageAdmins.php:7
* @route '/admin/admins'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Admins\Pages\ManageAdmins::__invoke
* @see app/Filament/Resources/Admins/Pages/ManageAdmins.php:7
* @route '/admin/admins'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

const admins = {
    index: Object.assign(index, index),
}

export default admins