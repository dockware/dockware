class Session {

    /**
     * This does completely reset the browser session
     * including the reset of cookies and whatever
     * needs to be done.
     */
    resetSession() {

        cy.clearCookies()

        cy.clearLocalStorage()

        cy.visit('/', {
            onBeforeLoad: (win) => {
                win.sessionStorage.clear()
            }
        });
    }

}

export default Session;
