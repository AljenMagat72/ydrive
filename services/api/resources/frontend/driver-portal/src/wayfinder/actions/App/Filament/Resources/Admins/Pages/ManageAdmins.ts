import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\Admins\Pages\ManageAdmins::__invoke
* @see app/Filament/Resources/Admins/Pages/ManageAdmins.php:7
* @route '/admin/admins'
*/
const ManageAdmins = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ManageAdmins.url(options),
    method: 'get',
})

ManageAdmins.definition = {
    methods: ["get","head"],
    url: '/admin/admins',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\Admins\Pages\ManageAdmins::__invoke
* @see app/Filament/Resources/Admins/Pages/ManageAdmins.php:7
* @route '/admin/admins'
*/
ManageAdmins.url = (options?: RouteQueryOptions) => {
    return ManageAdmins.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\Admins\Pages\ManageAdmins::__invoke
* @see app/Filament/Resources/Admins/Pages/ManageAdmins.php:7
* @route '/admin/admins'
*/
ManageAdmins.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ManageAdmins.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\Admins\Pages\ManageAdmins::__invoke
* @see app/Filament/Resources/Admins/Pages/ManageAdmins.php:7
* @route '/admin/admins'
*/
ManageAdmins.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ManageAdmins.url(options),
    method: 'head',
})

export default ManageAdmins