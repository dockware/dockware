class PDPRepository {

    /**
     *
     * @returns {*}
     */
    getAddToCartButton() {
        return cy.get('.buybox--button');
    }

}

export default PDPRepository;
