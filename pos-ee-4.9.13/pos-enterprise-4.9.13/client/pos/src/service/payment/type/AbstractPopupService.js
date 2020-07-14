import CoreService from "../../CoreService";

export class AbstractPopupService extends CoreService{
    modal;
    /**
     *
     * @return {boolean}
     */
    isOpen() {
        return this.modal;
    }
}