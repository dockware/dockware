describe('Apache Tests', () => {

    it('Empty Apache Exists', () => {

        cy.visit('/', { failOnStatusCode: false });

        cy.contains('Apache/2.4');
    })

})

