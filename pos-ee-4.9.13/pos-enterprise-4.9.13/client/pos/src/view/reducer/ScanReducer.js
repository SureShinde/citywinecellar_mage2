import ScanConstant from '../constant/ScanConstant';

const initialState = {barcodeString: "", scanPage: ""};

/**
 * Receive action from Scan Action
 *
 * @param state = {barcodeString: ""}
 * @param action
 * @returns {*}
 */
const configReducer = function (state = initialState, action) {
    switch (action.type) {
        case ScanConstant.SET_BARCODE_STRING:
            return {...state, barcodeString: action.barcodeString};
        case ScanConstant.SET_SCAN_PAGE:
            return {...state, scanPage: action.scanPage};
        default:
            return state
    }
};

export default configReducer;