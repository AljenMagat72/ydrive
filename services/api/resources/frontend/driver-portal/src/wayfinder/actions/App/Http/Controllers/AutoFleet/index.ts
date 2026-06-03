import AutoFleetWebHookController from './AutoFleetWebHookController'
import AutofleetClientWebHookController from './AutofleetClientWebHookController'

const AutoFleet = {
    AutoFleetWebHookController: Object.assign(AutoFleetWebHookController, AutoFleetWebHookController),
    AutofleetClientWebHookController: Object.assign(AutofleetClientWebHookController, AutofleetClientWebHookController),
}

export default AutoFleet