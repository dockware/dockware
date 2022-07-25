export default class NavigationRepository {

    /**
     *
     * @returns {*}
     */
    getClothingMenuItem() {
        return cy.get(':nth-child(3) > .navigation--link > span');
    }

    /**
     *
     * @returns {*}
     */
    getMountainAirAdventure() {
        return cy.get('.navigation--list-wrapper > .navigation--list > :nth-child(2) > .navigation--link > span')
    }

}
