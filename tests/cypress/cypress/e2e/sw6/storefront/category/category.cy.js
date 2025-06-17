import Shopware from "Services/Shopware";

const shopware = new Shopware();

it('Categories show products', () => {

    cy.visit('/');

    if (shopware.isVersionGreaterEqual('6.7')) {
        cy.get('.nav-item-a515ae260223466f8e37471d279e6406-link > .main-navigation-link-text').click();
    } else {
        cy.get('nav.main-navigation-menu a[href*="Clothing"]').click();
    }

    // check if we see a product box
    cy.get(':nth-child(1) > .card > .card-body').should('be.visible');
})

