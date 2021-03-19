class RegisterRepository {

    /**
     *
     * @returns {*}
     */
    getAccountType() {
        return cy.get('#register_personal_customer_type');
    }

    /**
     *
     * @returns {*}
     */
    getSalutation() {
        return cy.get('#salutation');
    }

    /**
     *
     * @returns {*}
     */
    getFirstname() {
        return cy.get('#firstname');
    }

    /**
     *
     * @returns {*}
     */
    getLastname() {
        return cy.get('#lastname');
    }

    /**
     *
     * @returns {*}
     */
    getEmail() {
        return cy.get('#register_personal_email');
    }

    /**
     *
     * @returns {*}
     */
    getPassword() {
        return cy.get('#register_personal_password');
    }

    /**
     *
     * @returns {*}
     */
    getStreet() {
        return cy.get('#street');
    }

    /**
     *
     * @returns {*}
     */
    getZipcode() {
        return cy.get('#zipcode');
    }

    /**
     *
     * @returns {*}
     */
    getCity() {
        return cy.get('#city');
    }

    /**
     *
     * @returns {*}
     */
    getCountry() {
        return cy.get('#country');
    }

    /**
     *
     * @returns {*}
     */
    getRegisterButton() {
        return cy.get('.register--submit')
    }

}

export default RegisterRepository;
