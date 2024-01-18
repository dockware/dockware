it('Categories show products', () => {

    cy.visit('/');

    cy.get('[href="http://localhost/Clothing/"] > .main-navigation-link-text > span').click();

    // check if we see a product box
    cy.get(':nth-child(1) > .card > .card-body').should('be.visible');
})

