class Devices {

    /**
     *
     * @returns {*}
     */
    getDevices() {
        const configDevices = Cypress.config('devices');
        let envDevice = Cypress.env('device');
        
        var list = [];
        
        // we don't have any tags
        // then leave the test as it is
        configDevices.forEach(device => {

            // if no env device is set
            // always add all devices
            if (!envDevice) {
                list.push(device);
            }
            
            // if we have an env device
            // then only use this
            if (envDevice === device.key) {
                list.push(device);
            }
        });
        
        return list;
    }

    /**
     *
     * @returns {*}
     */
    getFirstDevice() {
        return this.getDevices()[0];
    }

    /**
     *
     * @param device
     * @returns {string}
     */
    getDescription(device) {
        return `${device.name} (${device.width} x ${device.height})`;
    }

    /**
     *
     * @param device
     */
    setDevice(device) {
        cy.viewport(device.width, device.height);
    }

}

export default Devices;
