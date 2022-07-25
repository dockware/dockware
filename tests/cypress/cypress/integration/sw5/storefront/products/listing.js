import Devices from "Services/Devices";
// ------------------------------------------------------
import TopMenu from "Actions/sw5/storefront/navigation/TopMenu";


const devices = new Devices();
const topMenu = new TopMenu();


describe('Listing', () => {

    beforeEach(() => {
        devices.setDevice(devices.getFirstDevice());
    });

    it('Products exist in Listing', () => {

        cy.visit('/');

        topMenu.clickOnFirstCategory();

        cy.get('.product--box').should('be.visible');
    })

})
