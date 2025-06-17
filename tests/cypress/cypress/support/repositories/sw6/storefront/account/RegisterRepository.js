import Shopware from "Services/Shopware";

const shopware = new Shopware();


class RegisterRepository {

    /**
     *
     * @returns {*}
     */
    getSalutation() {
        return cy.get('#personalSalutation');
    }

    /**
     *
     * @returns {*}
     */
    getFirstname() {

        if (shopware.isVersionGreaterEqual('6.7')) {
            return cy.get('#billingAddress-personalFirstName');
        }

        return cy.get('#personalFirstName');
    }

    /**
     *
     * @returns {*}
     */
    getLastname() {

        if (shopware.isVersionGreaterEqual('6.7')) {
            return cy.get('#billingAddress-personalLastName');
        }

        return cy.get('#personalLastName');
    }

    /**
     *
     * @returns {*}
     */
    getEmail() {

        return cy.get('#personalMail');
    }

    /**
     *
     * @returns {*}
     */
    getPassword() {

        return cy.get('#personalPassword');
    }

    /**
     *
     * @returns {*}
     */
    getStreet() {

        if (shopware.isVersionGreaterEqual('6.7')) {
            return cy.get('#billingAddress-AddressStreet');
        }

        return cy.get('#billingAddressAddressStreet');
    }

    /**
     *
     * @returns {*}
     */
    getZipcode() {

        return cy.get('#billingAddressAddressZipcode');
    }

    /**
     *
     * @returns {*}
     */
    getCity() {

        return cy.get('#billingAddressAddressCity');
    }

    /**
     *
     * @returns {*}
     */
    getCountry() {

        return cy.get('#billingAddressAddressCountry');
    }

    /**
     *
     * @returns {*}
     */
    getRegisterButton() {
        return cy.get('.register-submit > .btn');
    }

}

export default RegisterRepository;
