import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Filament\Pages\CreateAccount::__invoke
* @see app/Filament/Pages/CreateAccount.php:7
* @route '/admin/verify'
*/
const CreateAccount = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateAccount.url(options),
    method: 'get',
})

CreateAccount.definition = {
    methods: ["get","head"],
    url: '/admin/verify',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Pages\CreateAccount::__invoke
* @see app/Filament/Pages/CreateAccount.php:7
* @route '/admin/verify'
*/
CreateAccount.url = (options?: RouteQueryOptions) => {
    return CreateAccount.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Pages\CreateAccount::__invoke
* @see app/Filament/Pages/CreateAccount.php:7
* @route '/admin/verify'
*/
CreateAccount.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateAccount.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Pages\CreateAccount::__invoke
* @see app/Filament/Pages/CreateAccount.php:7
* @route '/admin/verify'
*/
CreateAccount.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateAccount.url(options),
    method: 'head',
})

export default CreateAccount