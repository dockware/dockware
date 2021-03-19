class OffCanvasRepository {

    /**
     *
     * @returns {*}
     */
    getCheckoutButton() {
        return cy.get('.button--container > .is--primary');
    }

}

export default OffCanvasRepository;
