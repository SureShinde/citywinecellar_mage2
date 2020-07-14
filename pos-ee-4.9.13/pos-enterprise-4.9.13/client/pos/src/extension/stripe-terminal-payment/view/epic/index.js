import {combineEpics} from 'redux-observable';
import EpicFactory from "../../../../framework/factory/EpicFactory";
import StripeTerminalEpic from './payment/type/StripeTerminalEpic'

export default combineEpics(
    EpicFactory.get(StripeTerminalEpic)
)