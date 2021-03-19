import NavigationRepository from 'Repositories/sw5/storefront/navigation/NavigationRepository';

class TopMenu {

    /**
     *
     */
    clickOnClothing() {

        const repo = new NavigationRepository();

        repo.getClothingMenuItem().click();
    }

}

export default TopMenu;
