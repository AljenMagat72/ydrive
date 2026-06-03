import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import schedule7539d9 from './schedule'
/**
* @see routes/frontend/driver.php:5
* @route '/driver/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/driver/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/frontend/driver.php:5
* @route '/driver/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see routes/frontend/driver.php:5
* @route '/driver/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see routes/frontend/driver.php:5
* @route '/driver/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see routes/frontend/driver.php:8
* @route '/driver'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/driver',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/frontend/driver.php:8
* @route '/driver'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see routes/frontend/driver.php:8
* @route '/driver'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see routes/frontend/driver.php:8
* @route '/driver'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see routes/frontend/driver.php:9
* @route '/driver/schedule'
*/
export const schedule = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: schedule.url(options),
    method: 'get',
})

schedule.definition = {
    methods: ["get","head"],
    url: '/driver/schedule',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/frontend/driver.php:9
* @route '/driver/schedule'
*/
schedule.url = (options?: RouteQueryOptions) => {
    return schedule.definition.url + queryParams(options)
}

/**
* @see routes/frontend/driver.php:9
* @route '/driver/schedule'
*/
schedule.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: schedule.url(options),
    method: 'get',
})

/**
* @see routes/frontend/driver.php:9
* @route '/driver/schedule'
*/
schedule.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: schedule.url(options),
    method: 'head',
})

const driver = {
    login: Object.assign(login, login),
    dashboard: Object.assign(dashboard, dashboard),
    schedule: Object.assign(schedule, schedule7539d9),
}

export default driver