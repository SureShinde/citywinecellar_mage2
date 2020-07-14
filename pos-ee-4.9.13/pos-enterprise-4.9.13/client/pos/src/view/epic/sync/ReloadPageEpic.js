import SyncConstant from "../../constant/SyncConstant";
import ActionLogService from "../../../service/sync/ActionLogService";
import CheckoutConstant from "../../constant/CheckoutConstant";
import Action from "../../action/index";
import AppStore from "../../store/store";
import SyncAction from "../../action/SyncAction";

/**
 * Reload after sync action log
 *
 * @param action$
 * @param store
 * @returns {Observable<any>}
 */
export default function reloadPage(action$, store) {
    let countOrder = 0;
    let timeout;
    return action$.ofType(SyncConstant.RELOAD_PAGE, SyncConstant.SYNC_ACTION_LOG_SUCCESS, CheckoutConstant.CHECK_OUT_PLACE_ORDER_RESULT)
        .mergeMap(async function(action) {
            if (action.type === CheckoutConstant.CHECK_OUT_PLACE_ORDER_RESULT) {
                countOrder++;
                return Action.empty();
            }
            if (timeout) {
                clearTimeout(timeout);
            }
            let canReload = await ActionLogService.checkCanReload(countOrder, store.getState());
            if (canReload === SyncConstant.CAN_RELOAD) {
                window.location.reload();
            } else if (canReload === SyncConstant.NEED_TO_RELOAD_BUT_USER_IS_DOING_SOMETHING) {
                // wait until user doesn't do any thing on pos
                timeout = setTimeout(() => AppStore.dispatch(SyncAction.reloadPage()), 1000);
            }
            return Action.empty();
        });

}
