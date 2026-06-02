import admins from './admins'
import drivers from './drivers'
import queueMonitors from './queue-monitors'

const resources = {
    admins: Object.assign(admins, admins),
    drivers: Object.assign(drivers, drivers),
    queueMonitors: Object.assign(queueMonitors, queueMonitors),
}

export default resources