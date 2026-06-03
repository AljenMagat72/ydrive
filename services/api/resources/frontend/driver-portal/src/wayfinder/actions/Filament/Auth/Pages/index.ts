import Login from './Login'
import PasswordReset from './PasswordReset'
import EditProfile from './EditProfile'

const Pages = {
    Login: Object.assign(Login, Login),
    PasswordReset: Object.assign(PasswordReset, PasswordReset),
    EditProfile: Object.assign(EditProfile, EditProfile),
}

export default Pages