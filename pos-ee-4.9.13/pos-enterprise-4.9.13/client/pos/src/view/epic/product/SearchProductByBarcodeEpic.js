import ProductConstant from '../../constant/ProductConstant';
import {Observable} from 'rxjs';
import ProductService from "../../../service/catalog/ProductService";
import QuoteService from "../../../service/checkout/QuoteService";
import {fire} from "../../../event-bus";

/**
 * Receive action type(SEARCH_BY_BARCODE) and request, response list product
 * @param action$
 * @param store
 * @returns {Observable<Observable<any> | * | type | products>}
 * @constructor
 */
export default function SearchProductByBarcodeEpic(action$, store) {
    return action$.ofType(ProductConstant.SEARCH_BY_BARCODE)
        .mergeMap(action => {
            return Observable.from(
                    ProductService.processBarcode(action.code, store)
                ).map(response => {
                    fire('search_barcode_result_after', {
                        products: response
                    });
                    if (response && response.items && response.items.length === 1) {
                        let product = response.items[0];
                        QuoteService.addProductToCurrentQuote(store, product);
                    }
                    // require to return an object action (have key "type")
                    return {type: ""};
                }).catch(error => Observable.empty())
            }
        );
}
