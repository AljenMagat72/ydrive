import ServiceController from './ServiceController'
import StripeController from './StripeController'

const Admin = {
    ServiceController: Object.assign(ServiceController, ServiceController),
    StripeController: Object.assign(StripeController, StripeController),
}

export default Admin