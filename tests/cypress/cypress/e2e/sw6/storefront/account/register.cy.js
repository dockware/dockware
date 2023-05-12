import Devices from "Services/Devices";
import Random from 'Services/utils/Random';
// ------------------------------------------------------
import Register from 'Actions/sw6/storefront/Register';


const devices = new Devices();
const random = new Random();

const register = new Register();


describe('Account Register', () => {

    devices.getDevices().forEach(device => {

        context(devices.getDescription(device), () => {

            beforeEach(() => {
                devices.setDevice(device);
            });

            it('Register new Account', () => {

                cy.visit('/');

                const email = random.generateString(5) + "@localhost.de";

                register.doRegister(email, "DockwareDockware111");

                cy.contains('Overview');
            })

        })
    })
})
