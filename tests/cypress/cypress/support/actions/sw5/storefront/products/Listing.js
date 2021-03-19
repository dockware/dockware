import ListingRepository from 'Repositories/sw5/storefront/products/ListingRepository';

class Listing {

    /**
     *
     */
    clickOnFirstProduct() {

        const repo = new ListingRepository();

        repo.getFirstProduct().click();
    }

}

export default Listing;
