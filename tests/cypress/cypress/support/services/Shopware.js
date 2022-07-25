
export default class Shopware {

    /**
     *
     * @returns {*}
     */
    getVersion() {
        return Cypress.env().SHOPWARE;
    }

    /**
     *
     * @param version
     * @returns {boolean}
     */
    isVersionLower(version) {
        const diff = this._cmpVersions(this.getVersion(), version);
        return (diff < 0);
    }

    /**
     *
     * @param version
     * @returns {boolean}
     */
    isVersionLowerEqual(version) {
        const diff = this._cmpVersions(this.getVersion(), version);
        return (diff <= 0);
    }

    /**
     *
     * @param version
     * @returns {boolean}
     */
    isVersionEqual(version) {
        const diff = this._cmpVersions(this.getVersion(), version);
        return (diff === 0);
    }

    /**
     *
     * @param version
     * @returns {boolean}
     */
    isVersionGreater(version) {
        const diff = this._cmpVersions(this.getVersion(), version);
        return (diff > 0);
    }

    /**
     *
     * @param version
     * @returns {boolean}
     */
    isVersionGreaterEqual(version) {
        const diff = this._cmpVersions(this.getVersion(), version);
        return (diff >= 0);
    }


    /**
     *
     * @param a
     * @param b
     * @returns {number}
     */
    _cmpVersions(a, b) {

        a = a.toString();
        b = b.toString();

        var i, diff;
        var regExStrip0 = /(\.0+)+$/;
        var segmentsA = a.replace(regExStrip0, '').split('.');
        var segmentsB = b.replace(regExStrip0, '').split('.');
        var l = Math.min(segmentsA.length, segmentsB.length);

        for (i = 0; i < l; i++) {
            diff = parseInt(segmentsA[i], 10) - parseInt(segmentsB[i], 10);
            if (diff) {
                return diff;
            }
        }
        return segmentsA.length - segmentsB.length;
    }

}