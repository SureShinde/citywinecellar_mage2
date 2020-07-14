import QuoteConstant from '../../../constant/checkout/QuoteConstant';
import QuoteCustomDiscountService from '../../../../service/checkout/quote/CustomDiscountService';
import QuoteAction from '../../../action/checkout/QuoteAction';

/**
 *
 * @param action$
 * @param store
 * @return {Observable<any>}
 * @constructor
 */
export default function RemoveCartCustomDiscountEpic(action$, store) {
    return action$.ofType(QuoteConstant.REMOVE_CUSTOM_DISCOUNT)
        .mergeMap(action => {
                return QuoteCustomDiscountService.removeCustomRule(store.getState().core.checkout.quote)
                    .map(response => {
                        return QuoteAction.setQuote(response.quote);
                    });

            }
        );
}


