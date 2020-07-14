import {combineEpics} from 'redux-observable';
import EpicFactory from "../../../../framework/factory/EpicFactory";
import UpdateLoyaltyEpic from "./loyalty/UpdateLoyaltyEpic";
import PlaceOrderAfterEpic from "./order/PlaceOrderAfterEpic";

export default combineEpics(
  EpicFactory.get(UpdateLoyaltyEpic),
  EpicFactory.get(PlaceOrderAfterEpic)
);
