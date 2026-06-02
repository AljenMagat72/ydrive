import DriverAuthController from './DriverAuthController'
import DriverSettingsController from './DriverSettingsController'
import DriverController from './DriverController'
import DriverScheduleController from './DriverScheduleController'

const Driver = {
    DriverAuthController: Object.assign(DriverAuthController, DriverAuthController),
    DriverSettingsController: Object.assign(DriverSettingsController, DriverSettingsController),
    DriverController: Object.assign(DriverController, DriverController),
    DriverScheduleController: Object.assign(DriverScheduleController, DriverScheduleController),
}

export default Driver