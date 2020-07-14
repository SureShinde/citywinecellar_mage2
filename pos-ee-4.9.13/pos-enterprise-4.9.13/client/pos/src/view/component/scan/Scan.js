import CoreComponent from "../../../framework/component/CoreComponent";
import CoreContainer from "../../../framework/container/CoreContainer";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import ComponentFactory from '../../../framework/factory/ComponentFactory';
import ScanConstant from "../../constant/ScanConstant";
import ScanAction from "../../action/ScanAction";
import $ from "jquery";

export class ScanComponent extends CoreComponent {
    static className = 'ScanComponent';
    scanString = "";
    lastCharacter = "";
    currentStringLength = 0;
    lastScanTimeStamp = Date.now();
    removeScanStringKeys = ['backspace', 'delete'];
    nonScanKeys = ['control'];
    shiftMapKeys = {
        "1": "!",
        "2": "@",
        "3": "#",
        "4": "$",
        "5": "%",
        "6": "^",
        "7": "&",
        "8": "*",
        "9": "(",
        "0": ")",
        "-": "_",
        "=": "+",
        "[": "{",
        "]": "}",
        ";": ":",
        "'": '"',
        ",": '<',
        ".": '>',
        "/": '?',
        "`": '~'
    };

    componentWillMount() {
        document.addEventListener('keyup', event => this.scanBarcode(event));
    }

    scanBarcode(event) {
        if (!this.isSwipeCardScreen()) {
            if (!this.props.currentScanPage) {
                return this;
            }
            let key = event.key;
            if (!key) {
                return this;
            }
            key = key.toString();
            let lowerCaseKey = key.toLowerCase();
            if (this.nonScanKeys.includes[lowerCaseKey]) {
                return this;
            }
            if (lowerCaseKey === 'shift') {
                this.lastCharacter = lowerCaseKey;
                if (!this.currentStringLength) {
                    this.lastScanTimeStamp = Date.now();
                }
                this.currentStringLength++;
                return this;
            }
            let currentTime = Date.now();
            let diffTime = currentTime - this.lastScanTimeStamp;
            this.currentStringLength++;
            if (this.removeScanStringKeys.includes(lowerCaseKey)) {
                this.resetScanString();
                return this;
            }
            if (diffTime / this.currentStringLength < ScanConstant.MAX_DIFF_TIME_WITH_SCAN_BARCODE) {
                if (lowerCaseKey === 'enter' && this.scanString) {
                    this.props.actions.setBarcodeString(this.scanString);
                    this.resetScanString();
                } else {
                    if (lowerCaseKey === 'enter') {
                        this.resetScanString();
                    } else {
                        if (this.lastCharacter === 'shift') {
                            if (key.length === 1) {
                                if (this.shiftMapKeys[key]) {
                                    key = this.shiftMapKeys[key];
                                } else {
                                    key = key.toUpperCase();
                                }
                            } else {
                                this.lastCharacter = lowerCaseKey;
                                return this;
                            }
                        }
                        this.lastCharacter = lowerCaseKey;
                        this.scanString = this.scanString + key;
                    }
                }
            } else {
                this.lastScanTimeStamp = Date.now();
                if (lowerCaseKey === 'enter') {
                    this.resetScanString();
                } else {
                    if (this.lastCharacter === 'shift') {
                        this.currentStringLength = 1;
                        key = key.toUpperCase();
                    }
                    this.currentStringLength = 1;
                    this.scanString = key;
                }
                this.lastCharacter = lowerCaseKey;
            }
        }
    }

    resetScanString() {
        this.scanString = "";
        this.lastCharacter = "";
        this.currentStringLength = 0;
        this.lastScanTimeStamp = Date.now();
    }

    /**
     *
     * @returns {*|jQuery}
     */
    isSwipeCardScreen() {
        return $('#name-on-card').is(':visible');
    }
}

class ScanComponentContainer extends CoreContainer {
    static className = 'ScanComponentContainer';

    /**
     * map state to props
     * @param state
     * @return {{payments: *}}
     */
    static mapState(state) {
        let {scanPage} = state.core.scan;
        return {currentScanPage: scanPage};
    }

    static mapDispatch(dispatch) {
        return {
            actions: {
                setBarcodeString: barcodeString => dispatch(ScanAction.setBarcodeString(barcodeString))
            }
        }
    }

}

export default ContainerFactory.get(ScanComponentContainer).getConnect(
    ComponentFactory.get(ScanComponent)
);