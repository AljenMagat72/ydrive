import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Driver\DriverAuthController::login
* @see app/Http/Controllers/Driver/DriverAuthController.php:24
* @route '/api/v1/auth/driver/sms/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

login.definition = {
    methods: ["post"],
    url: '/api/v1/auth/driver/sms/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Driver\DriverAuthController::login
* @see app/Http/Controllers/Driver/DriverAuthController.php:24
* @route '/api/v1/auth/driver/sms/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Driver\DriverAuthController::login
* @see app/Http/Controllers/Driver/DriverAuthController.php:24
* @route '/api/v1/auth/driver/sms/login'
*/
login.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Driver\DriverAuthController::verify
* @see app/Http/Controllers/Driver/DriverAuthController.php:34
* @route '/api/v1/auth/driver/sms/verify'
*/
export const verify = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(options),
    method: 'post',
})

verify.definition = {
    methods: ["post"],
    url: '/api/v1/auth/driver/sms/verify',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Driver\DriverAuthController::verify
* @see app/Http/Controllers/Driver/DriverAuthController.php:34
* @route '/api/v1/auth/driver/sms/verify'
*/
verify.url = (options?: RouteQueryOptions) => {
    return verify.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Driver\DriverAuthController::verify
* @see app/Http/Controllers/Driver/DriverAuthController.php:34
* @route '/api/v1/auth/driver/sms/verify'
*/
verify.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Driver\DriverAuthController::logout
* @see app/Http/Controllers/Driver/DriverAuthController.php:56
* @route '/api/v1/auth/driver/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/api/v1/auth/driver/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Driver\DriverAuthController::logout
* @see app/Http/Controllers/Driver/DriverAuthController.php:56
* @route '/api/v1/auth/driver/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Driver\DriverAuthController::logout
* @see app/Http/Controllers/Driver/DriverAuthController.php:56
* @route '/api/v1/auth/driver/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

const DriverAuthController = { login, verify, logout }

export default DriverAuthController