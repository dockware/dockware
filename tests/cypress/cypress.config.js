const {defineConfig} = require('cypress')

module.exports = defineConfig({
    chromeWebSecurity: false,
    retries: {
        "runMode": 3,
        "openMode": 0
    },
    watchForFileChanges: false,
    trashAssetsBeforeRuns: true,
    screenshotOnRunFailure: true,
    video: false,
    videoCompression: 50,
    e2e: {
        experimentalSessionAndOrigin: true,
        testIsolation: true,
        experimentalWebKitSupport: true,
        // We've imported your old cypress plugins here.
        // You may want to clean this up later by importing these.
        setupNodeEvents(on, config) {
            return require('./cypress/plugins/index.js')(on, config)
        },
    },
})
