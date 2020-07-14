import CategoryConstant from '../constant/CategoryConstant';
import LogoutPopupConstant from "../constant/LogoutPopupConstant";

const initialState = {
    categories: [],
    parentCategory: null,
};

/**
 * receive action from Category Action
 * @param state = {categories: []}
 * @param action
 * @returns {*}
 */
const categoryReducer = function (state = initialState, action) {
    switch (action.type) {
        case CategoryConstant.GET_LIST_CATEGORY_RESULT:
            const {
                parentCategory,
                categories,
                search_criteria,
                total_count,
                parent_id,
                requestMode} = action;
            return {
                ...state,
                parentCategory: parentCategory,
                categories: categories,
                search_criteria: search_criteria,
                total_count: total_count,
                parent_id: parent_id,
                requestMode: requestMode
            };
        case LogoutPopupConstant.FINISH_LOGOUT_REQUESTING:
            return initialState;
        default:
            return state
    }
};

export default categoryReducer;
