import {combineEpics} from 'redux-observable';
import EpicFactory from "../../../../framework/factory/EpicFactory";
import PaymentOfflineEpic from "./payment/type/PaymentOfflineEpic";

export default combineEpics(
  EpicFactory.get(PaymentOfflineEpic)
);
