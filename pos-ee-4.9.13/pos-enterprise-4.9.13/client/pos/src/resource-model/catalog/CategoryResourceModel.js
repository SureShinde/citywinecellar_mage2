import AbstractResourceModel from "../AbstractResourceModel";
import SyncConstant from "../../view/constant/SyncConstant";

export default class CategoryResourceModel extends AbstractResourceModel {
    static className = 'CategoryResourceModel';

    /**
     * Constructor
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            resourceName : 'Category',
            dataType: SyncConstant.TYPE_CATEGORY
        };
    }

}
