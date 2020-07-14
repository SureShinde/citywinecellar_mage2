import {Observable} from "rxjs";
import CategoryConstant from "../../constant/CategoryConstant";
import CategoryAction from '../../action/CategoryAction';
import CategoryService from "../../../service/catalog/CategoryService";
import Config from "../../../config/Config";
import SyncConstant from "../../constant/SyncConstant";

/**
 * Receive action type(GET_LIST_CATEGORY) and request, response list category
 * @param action$
 */

export default function getListCategory(action$) {
    return action$.ofType(CategoryConstant.GET_LIST_CATEGORY)
        .mergeMap((action) => {
            let requestMode = Config.dataTypeMode[SyncConstant.TYPE_CATEGORY];
            return Observable.from(CategoryService.getListCategory(action.parent_id))
                .mergeMap((response) => {
                    return Observable.of(CategoryAction.getListCategoryResult(
                        response.parentCategory,
                        response.items,
                        response.search_criteria,
                        response.total_count,
                        action.parent_id,
                        requestMode
                    ));
                }).catch(() => Observable.of(CategoryAction.getListCategoryResult([])))
        });
}
