import Driver from './Driver'
import Client from './Client'
import Admin from './Admin'
import EnchantController from './EnchantController'
import AutoFleet from './AutoFleet'

const Controllers = {
    Driver: Object.assign(Driver, Driver),
    Client: Object.assign(Client, Client),
    Admin: Object.assign(Admin, Admin),
    EnchantController: Object.assign(EnchantController, EnchantController),
    AutoFleet: Object.assign(AutoFleet, AutoFleet),
}

export default Controllers