it('Categories show products', () => {

    cy.visit('/');

    cy.get('nav.main-navigation-menu a[href*="Clothing"]').click();
 
    // check if we see a product box
    cy.get(':nth-child(1) > .card > .card-body').should('be.visible');
})

