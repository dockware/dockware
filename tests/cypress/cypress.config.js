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
    devices: [
        {
            key: 'desktop',
            name: 'Desktop',
            width: 1920,
            height: 1080,
        },
    ],
    e2e: {
        testIsolation: true,
        experimentalWebKitSupport: true,
        // We've imported your old cypress plugins here.
        // You may want to clean this up later by importing these.
        setupNodeEvents(on, config) {
            return require('./cypress/plugins/index.js')(on, config)
        },
    },
})
