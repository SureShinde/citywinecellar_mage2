import SignOutConstant from "../constant/SignoutConstant";
import LogoutPopupConstant from "../constant/LogoutPopupConstant";
import PosService from "../../service/PosService";
import UserService from "../../service/user/UserService";
import ApiResponseConstant from "../constant/ApiResponseConstant";

const  initialState = {};
/**
 * receive action from DataAbstract
 *
 * @param state
 * @param action
 * @returns {*}
 */
const signoutreducer = function (state = initialState, action) {
    switch (action.type) {
        case LogoutPopupConstant.FINISH_LOGOUT_REQUESTING:
            if(action.response.code === ApiResponseConstant.EXCEPTION_CODE_FORCE_CHANGE_POS) {
                PosService.removeCurrentPos();
                return { ...state,
                    page: '/location',
                    message: action.response.message
                };
            }
            if(action.response.code === ApiResponseConstant.EXCEPTION_CODE_FORCE_SIGN_OUT) {
                UserService.removeStaff();
                UserService.removeSession();
                PosService.removeCurrentPos();
                return { ...state,
                    page: '/login',
                    message: action.response.message
                };
            }

            return state;
        case SignOutConstant.FORCE_SIGN_OUT_SUCCESS:
            return { ...state,
                page: '',
                message: ''
            };
        case LogoutPopupConstant.LOGOUT_RE_AUTHORIZE:
            UserService.resetAllData();
            window.location.reload();
            return { ...state,
                page: '',
                message: ''
            };
        default:
            return state;
    }
};

export default signoutreducer;
