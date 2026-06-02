import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListPendingJobs::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListPendingJobs.php:7
* @route '/admin/queue-monitors/pending'
*/
const ListPendingJobs = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListPendingJobs.url(options),
    method: 'get',
})

ListPendingJobs.definition = {
    methods: ["get","head"],
    url: '/admin/queue-monitors/pending',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListPendingJobs::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListPendingJobs.php:7
* @route '/admin/queue-monitors/pending'
*/
ListPendingJobs.url = (options?: RouteQueryOptions) => {
    return ListPendingJobs.definition.url + queryParams(options)
}

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListPendingJobs::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListPendingJobs.php:7
* @route '/admin/queue-monitors/pending'
*/
ListPendingJobs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListPendingJobs.url(options),
    method: 'get',
})

/**
* @see \Croustibat\FilamentJobsMonitor\Resources\QueueMonitorResource\Pages\ListPendingJobs::__invoke
* @see vendor/croustibat/filament-jobs-monitor/src/Resources/QueueMonitorResource/Pages/ListPendingJobs.php:7
* @route '/admin/queue-monitors/pending'
*/
ListPendingJobs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListPendingJobs.url(options),
    method: 'head',
})

export default ListPendingJobs