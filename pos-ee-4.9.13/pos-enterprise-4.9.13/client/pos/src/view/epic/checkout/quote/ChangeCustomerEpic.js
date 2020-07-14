import QuoteConstant from '../../../constant/checkout/QuoteConstant';
import QuoteService from '../../../../service/checkout/QuoteService';
import QuoteAction from "../../../action/checkout/QuoteAction";
import MultiCartService from "../../../../service/MultiCartService";
import MultiCheckoutAction from "../../../action/MultiCheckoutAction";
import StoreCreditService from "../../../../service/store-credit/StoreCreditService";
import { fire } from '../../../../event-bus';

/**
 *
 * @param action$
 * @param store
 * @return {Observable<any>}
 * @constructor
 */
export default function ChangeCustomerEpic(action$, store) {
    return action$.ofType(QuoteConstant.SET_CUSTOMER)
        .mergeMap(async action => {
                const oldQuote  = store.getState().core.checkout.quote;
                let oldCustomer = oldQuote.customer;
                let quote = QuoteService.changeCustomer(oldQuote, action.customer);
                fire('epic_change_customer_after', {oldCustomer, quote});
                // check and remove customer credit in quote
                quote = StoreCreditService.checkAndRemoveStoreCreditInQuote(quote);
                quote = QuoteService.collectTotals(quote);
                /** auto create cart if empty */
                const {activeCart} = store.getState().core.multiCheckout;
                if (!activeCart) {
                    await QuoteAction.setQuote(quote);
                    let newCartId = await MultiCartService.addCartByQuote(quote);
                    return MultiCheckoutAction.getListCart(newCartId);
                }
                return QuoteAction.setQuote(quote);
            }
        );
}


