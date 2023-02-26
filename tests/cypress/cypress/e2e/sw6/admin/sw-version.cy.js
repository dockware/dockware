import Shopware from "Services/Shopware";


const shopware = new Shopware();


it('Verify installed Shopware Version: ' + shopware.getVersion(), () => {

    cy.viewport(1800, 1200);

    cy.visit('/admin');

    cy.get('#sw-field--username').type('admin');
    cy.get('#sw-field--password').type('shopware');

    cy.get('.sw-button').click();

    cy.contains('.sw-version__info', shopware.getVersion());
})

