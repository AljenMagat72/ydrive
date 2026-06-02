import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListQueueMonitors::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListQueueMonitors.php:7
* @route '/admin/queue-monitors'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/queue-monitors',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListQueueMonitors::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListQueueMonitors.php:7
* @route '/admin/queue-monitors'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListQueueMonitors::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListQueueMonitors.php:7
* @route '/admin/queue-monitors'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListQueueMonitors::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListQueueMonitors.php:7
* @route '/admin/queue-monitors'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListPendingJobs::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListPendingJobs.php:7
* @route '/admin/queue-monitors/pending'
*/
export const pending = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pending.url(options),
    method: 'get',
})

pending.definition = {
    methods: ["get","head"],
    url: '/admin/queue-monitors/pending',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListPendingJobs::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListPendingJobs.php:7
* @route '/admin/queue-monitors/pending'
*/
pending.url = (options?: RouteQueryOptions) => {
    return pending.definition.url + queryParams(options)
}

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListPendingJobs::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListPendingJobs.php:7
* @route '/admin/queue-monitors/pending'
*/
pending.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pending.url(options),
    method: 'get',
})

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListPendingJobs::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListPendingJobs.php:7
* @route '/admin/queue-monitors/pending'
*/
pending.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pending.url(options),
    method: 'head',
})

const queueMonitors = {
    index: Object.assign(index, index),
    pending: Object.assign(pending, pending),
}

export default queueMonitors