class ListingRepository {

    /**
     *
     * @returns {*}
     */
    getFirstProduct() {
        return cy.get(':nth-child(1) > .product--box > .box--content > .product--info > .product--image > .image--element > .image--media > img');
    }

}

export default ListingRepository;
