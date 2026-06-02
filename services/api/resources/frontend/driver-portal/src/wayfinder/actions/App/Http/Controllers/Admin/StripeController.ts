import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\StripeController::paymentIntent
* @see app/Http/Controllers/Admin/StripeController.php:18
* @route '/api/v1/admin/stripe/payment-intent/{paymentId}'
*/
export const paymentIntent = (args: { paymentId: string | number } | [paymentId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paymentIntent.url(args, options),
    method: 'get',
})

paymentIntent.definition = {
    methods: ["get","head"],
    url: '/api/v1/admin/stripe/payment-intent/{paymentId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\StripeController::paymentIntent
* @see app/Http/Controllers/Admin/StripeController.php:18
* @route '/api/v1/admin/stripe/payment-intent/{paymentId}'
*/
paymentIntent.url = (args: { paymentId: string | number } | [paymentId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { paymentId: args }
    }

    if (Array.isArray(args)) {
        args = {
            paymentId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        paymentId: args.paymentId,
    }

    return paymentIntent.definition.url
            .replace('{paymentId}', parsedArgs.paymentId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\StripeController::paymentIntent
* @see app/Http/Controllers/Admin/StripeController.php:18
* @route '/api/v1/admin/stripe/payment-intent/{paymentId}'
*/
paymentIntent.get = (args: { paymentId: string | number } | [paymentId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: paymentIntent.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\StripeController::paymentIntent
* @see app/Http/Controllers/Admin/StripeController.php:18
* @route '/api/v1/admin/stripe/payment-intent/{paymentId}'
*/
paymentIntent.head = (args: { paymentId: string | number } | [paymentId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: paymentIntent.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\StripeController::charge
* @see app/Http/Controllers/Admin/StripeController.php:43
* @route '/api/v1/admin/stripe/charge/{chargeId}'
*/
export const charge = (args: { chargeId: string | number } | [chargeId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: charge.url(args, options),
    method: 'get',
})

charge.definition = {
    methods: ["get","head"],
    url: '/api/v1/admin/stripe/charge/{chargeId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\StripeController::charge
* @see app/Http/Controllers/Admin/StripeController.php:43
* @route '/api/v1/admin/stripe/charge/{chargeId}'
*/
charge.url = (args: { chargeId: string | number } | [chargeId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { chargeId: args }
    }

    if (Array.isArray(args)) {
        args = {
            chargeId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        chargeId: args.chargeId,
    }

    return charge.definition.url
            .replace('{chargeId}', parsedArgs.chargeId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\StripeController::charge
* @see app/Http/Controllers/Admin/StripeController.php:43
* @route '/api/v1/admin/stripe/charge/{chargeId}'
*/
charge.get = (args: { chargeId: string | number } | [chargeId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: charge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\StripeController::charge
* @see app/Http/Controllers/Admin/StripeController.php:43
* @route '/api/v1/admin/stripe/charge/{chargeId}'
*/
charge.head = (args: { chargeId: string | number } | [chargeId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: charge.url(args, options),
    method: 'head',
})

const StripeController = { paymentIntent, charge }

export default StripeController