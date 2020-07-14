import QuoteConstant from '../../../constant/checkout/QuoteConstant';
import QuoteCustomDiscountService from '../../../../service/checkout/quote/CustomDiscountService';
import QuoteAction from '../../../action/checkout/QuoteAction';
import {Observable} from 'rxjs';
import CheckoutAction from "../../../action/CheckoutAction";

/**
 *
 * @param action$
 * @param store
 * @return {Observable<any>}
 * @constructor
 */
export default function SetCartCustomDiscountEpic(action$, store) {
    return action$.ofType(QuoteConstant.SET_CUSTOM_DISCOUNT)
        .mergeMap(action => {
            return Observable.from(QuoteCustomDiscountService.applyCustomRule(
                store.getState().core.checkout.quote,
                action.discountType,
                action.discountAmount,
                action.discountReason)
            ).mergeMap((response) => {
                let quote = response.quote;
                let actions = [QuoteAction.setQuote(quote)];
                if (!quote.grand_total) {
                    actions.push(CheckoutAction.checkoutToSelectPayments(quote));
                }
                return actions;
            });
        });
}


