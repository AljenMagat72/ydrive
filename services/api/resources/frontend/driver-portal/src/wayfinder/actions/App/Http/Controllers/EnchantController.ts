import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\EnchantController::customerView
* @see app/Http/Controllers/EnchantController.php:15
* @route '/api/enchant/customer'
*/
export const customerView = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: customerView.url(options),
    method: 'post',
})

customerView.definition = {
    methods: ["post"],
    url: '/api/enchant/customer',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\EnchantController::customerView
* @see app/Http/Controllers/EnchantController.php:15
* @route '/api/enchant/customer'
*/
customerView.url = (options?: RouteQueryOptions) => {
    return customerView.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EnchantController::customerView
* @see app/Http/Controllers/EnchantController.php:15
* @route '/api/enchant/customer'
*/
customerView.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: customerView.url(options),
    method: 'post',
})

const EnchantController = { customerView }

export default EnchantController