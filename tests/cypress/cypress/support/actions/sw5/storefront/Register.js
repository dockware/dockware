import RegisterRepository from 'Repositories/sw5/storefront/account/RegisterRepository';

class Register {

    /**
     *
     * @param email
     * @param password
     */
    doRegister(email, password) {

        cy.visit('/account');

        const repo = new RegisterRepository();

        repo.getAccountType().select('Privatkunde');
        repo.getSalutation().select('Herr');

        repo.getFirstname().clear().type('Dockware');
        repo.getLastname().clear().type('Dockware');

        repo.getEmail().clear().type(email);
        repo.getPassword().clear().type(password);

        repo.getStreet().clear().type('Dockware');
        repo.getZipcode().clear().type('Dockware');
        repo.getCity().clear().type('Dockware');

        repo.getCountry().select('Deutschland');

        repo.getRegisterButton().click();
    }
}

export default Register;
