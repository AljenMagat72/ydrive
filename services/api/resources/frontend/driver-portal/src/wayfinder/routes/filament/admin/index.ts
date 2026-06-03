import auth from './auth'
import resources from './resources'

const admin = {
    auth: Object.assign(auth, auth),
    resources: Object.assign(resources, resources),
}

export default admin