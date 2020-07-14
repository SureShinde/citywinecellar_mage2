import ScanConstant from '../constant/ScanConstant';

export default {
    /**
     * Set barcode string
     *
     * @param barcodeString
     * @returns {{type: string, barcodeString: string}}
     */
    setBarcodeString (barcodeString = "") {
        return {
            type: ScanConstant.SET_BARCODE_STRING,
            barcodeString: barcodeString
        }
    },
    setScanPage(scanPage = "") {
        return {
            type: ScanConstant.SET_SCAN_PAGE,
            scanPage: scanPage
        }
    }
}