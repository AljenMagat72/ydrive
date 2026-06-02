import ZohoController from './ZohoController'
import AdminZohoController from './AdminZohoController'

const Controllers = {
    ZohoController: Object.assign(ZohoController, ZohoController),
    AdminZohoController: Object.assign(AdminZohoController, AdminZohoController),
}

export default Controllers