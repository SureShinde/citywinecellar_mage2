import {OverlayTrigger} from "react-bootstrap";

export class PosOverlayTrigger extends OverlayTrigger {
    handleHide(event) {
        let className = event.target.className;
        if (
            className &&
            (typeof className.indexOf === "function") &&
            (className.indexOf("modal-backdrop") !== -1) &&
            (className.indexOf("in") !== -1) &&
            (className.indexOf("popover-backdrop") !== -1)
        ) {
            this.hide();
        }
    };
}

export default PosOverlayTrigger;