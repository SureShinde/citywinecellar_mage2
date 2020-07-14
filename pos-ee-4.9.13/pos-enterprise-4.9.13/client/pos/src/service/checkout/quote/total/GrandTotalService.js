import {AbstractTotalService} from "./AbstractService";
import ServiceFactory from "../../../../framework/factory/ServiceFactory";
import CurrencyHelper from "../../../../helper/CurrencyHelper";
import NumberHelper from "../../../../helper/NumberHelper";

export class QuoteTotalGrandTotalService extends AbstractTotalService {
    static className = 'QuoteTotalGrandTotalService';

    code = "grand_total";

    /**
     * Collect grand total for address
     *
     * @param {object} quote
     * @param {object} address
     * @param {object} total
     * @return {QuoteTotalGrandTotalService}
     */
    collect(quote, address, total) {
        super.collect(quote, address, total);
        let grandTotal = total.grand_total ? total.grand_total : 0;
        let baseGrandTotal = total.base_grand_total ? total.base_grand_total : 0;
        /* Add all total amounts of total object  */
        let totals = Object.values(this._getTotal().totalAmounts).reduce((a, b) => NumberHelper.addNumber(a, b));
        /* Add all total base amounts of total object  */
        let baseTotals = Object.values(this._getTotal().baseTotalAmounts)
            .reduce((a, b) => NumberHelper.addNumber(a, b));
        grandTotal = CurrencyHelper.roundToFloat(grandTotal + totals);
        baseGrandTotal = CurrencyHelper.roundToFloat(baseGrandTotal + baseTotals);

        total.grand_total = grandTotal > 0 ? grandTotal : 0;
        total.base_grand_total = baseGrandTotal > 0 ? baseGrandTotal : 0;
        return this;
    }
}

let quoteTotalGrandTotalService = ServiceFactory.get(QuoteTotalGrandTotalService);

export default quoteTotalGrandTotalService;
