class CartConfirmRepository {

    /**
     *
     * @returns {*}
     */
    getSwitchPaymentMethodsButton() {
        return cy.get('.panel--actions > .btn');
    }

}

export default CartConfirmRepository;
