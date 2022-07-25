import NavigationRepository from 'Repositories/sw5/storefront/navigation/NavigationRepository';
import Shopware from "Services/Shopware";


const shopware = new Shopware();

export default class TopMenu {

    /**
     *
     */
    clickOnFirstCategory() {

        const repo = new NavigationRepository();

        if (shopware.isVersionGreaterEqual('5.3')) {
            repo.getClothingMenuItem().click();
        } else {
            repo.getMountainAirAdventure().click();
        }
    }

}
