class LoginRepository {

  /**
   *
   * @returns {*}
   */
  getEmail() {
    return cy.get('#email');
  }

  /**
   *
   * @returns {*}
   */
  getPassword() {
    return cy.get('#passwort');
  }

  /**
   *
   * @returns {*}
   */
  getSubmitButton() {
    return cy.get('.register--login-btn')
  }

}

export default LoginRepository;
