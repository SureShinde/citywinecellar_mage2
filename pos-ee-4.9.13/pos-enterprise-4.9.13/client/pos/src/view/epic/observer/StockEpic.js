import {combineEpics} from 'redux-observable';
import EpicFactory from "../../../framework/factory/EpicFactory";
import PlaceOrderAfterEpic from "./stock/PlaceOrderAfterEpic";
import CreateCreditmemoAfterEpic from "./stock/CreateCreditmemoAfterEpic";
import CancelOrderAfterEpic from "./stock/CancelOrderAfterEpic";
import CancelOnholdOrderAfterEpic from "./stock/CancelOnholdOrderAfterEpic";
/**
 * Combine all stock epic
 * @type {Epic<Action, any, any, T> | any}
 */
const stockEpic = combineEpics(
    EpicFactory.get(PlaceOrderAfterEpic),
    EpicFactory.get(CreateCreditmemoAfterEpic),
    EpicFactory.get(CancelOrderAfterEpic),
    EpicFactory.get(CancelOnholdOrderAfterEpic)
);

export default stockEpic;


