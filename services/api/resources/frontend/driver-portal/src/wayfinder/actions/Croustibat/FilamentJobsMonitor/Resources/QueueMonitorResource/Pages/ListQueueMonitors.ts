import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListQueueMonitors::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListQueueMonitors.php:7
* @route '/admin/queue-monitors'
*/
const ListQueueMonitors = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListQueueMonitors.url(options),
    method: 'get',
})

ListQueueMonitors.definition = {
    methods: ["get","head"],
    url: '/admin/queue-monitors',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListQueueMonitors::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListQueueMonitors.php:7
* @route '/admin/queue-monitors'
*/
ListQueueMonitors.url = (options?: RouteQueryOptions) => {
    return ListQueueMonitors.definition.url + queryParams(options)
}

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListQueueMonitors::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListQueueMonitors.php:7
* @route '/admin/queue-monitors'
*/
ListQueueMonitors.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListQueueMonitors.url(options),
    method: 'get',
})

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListQueueMonitors::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListQueueMonitors.php:7
* @route '/admin/queue-monitors'
*/
ListQueueMonitors.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListQueueMonitors.url(options),
    method: 'head',
})

export default ListQueueMonitors