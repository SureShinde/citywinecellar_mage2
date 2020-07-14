import React, {Fragment} from "react";
import CoreComponent from '../../../framework/component/CoreComponent';
import ComponentFactory from '../../../framework/factory/ComponentFactory';
import CoreContainer from '../../../framework/container/CoreContainer';
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import NumberHelper from "../../../helper/NumberHelper";
import StockService from "../../../service/catalog/StockService";
import ModuleHelper from "../../../helper/ModuleHelper";

export class StockLocation extends CoreComponent {
    static className = 'StockLocation';

    getQty() {
        let stockItem = this.props.stock_location;

        if (!stockItem) {
            return "";
        }

        let isManageStock = StockService.getManageStock(stockItem);
        if (!isManageStock) {
            return this.props.t("No Manage Stock");
        }
        if(!ModuleHelper.isWebposStandard() && ModuleHelper.isAllowShowExternalStockMSI()){
            return NumberHelper.formatDisplayGroupAndDecimalSeparator(stockItem.qty);
        }
        return NumberHelper.formatDisplayGroupAndDecimalSeparator(StockService.getStockItemQty(stockItem));
    }

    /**
     * Render template
     *
     * @return {*}
     */
    template() {
        let {stock_location} = this.props;
        return (
            <Fragment>
                <li className={stock_location.is_current_location === "1" ? "active" : ""}>
                    <div className="info">
                        <div className="name">{stock_location.name}</div>
                        <div className="detail">
                            {stock_location.address}
                        </div>
                        <div className={stock_location.is_in_stock === "1" ? "qty" : "qty not-available"}>
                            {this.getQty()}
                        </div>
                    </div>
                </li>
            </Fragment>
        )
    }
}

class StockLocationContainer extends CoreContainer {
    static className = 'StockLocationContainer';
}

export default ContainerFactory.get(StockLocationContainer).withRouter(
    ComponentFactory.get(StockLocation)
);
