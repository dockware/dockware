import Devices from "Services/Devices";
import Session from "Actions/utils/Session"
// ------------------------------------------------------
import Register from 'Actions/sw5/storefront/Register';
import Login from 'Actions/sw5/storefront/Login';


const devices = new Devices();
const session = new Session();

const register = new Register();
const login = new Login();


beforeEach(() => {

    // always try to register, so that we have an account.
    // theres no assertion if it worked or not, so it can
    // be used over and over again.
    // remarks: ENV variables are not found in github - pretty weird
    // lets just do it in here! :)
    register.doRegister(
        "dev@localhost.de",
        "DockwareDockware111"
    );

    // we just try to register above which might work or might not work.
    // then simply reset our session, so that we can do a plain login ;)
    session.resetSession();
})


describe('Account Login', () => {

    devices.getDevices().forEach(device => {

        context(devices.getDescription(device), () => {

            beforeEach(() => {
                devices.setDevice(device);
            });

            it('Login with existing Account', () => {

                cy.visit('/');

                // remarks: ENV variables are not found in github - pretty weird
                // lets just do it in here! :
                login.doLogin(
                    "dev@localhost.de",
                    "DockwareDockware111"
                );

                cy.contains('Willkommen');
            })

        })
    })
})

