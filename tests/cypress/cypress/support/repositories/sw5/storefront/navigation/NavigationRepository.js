class NavigationRepository {

    /**
     *
     * @returns {*}
     */
    getClothingMenuItem() {
        return cy.get(':nth-child(3) > .navigation--link > span');
    }

}

export default NavigationRepository;
