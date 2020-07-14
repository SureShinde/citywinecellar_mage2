import {combineEpics} from 'redux-observable';
import EpicFactory from "../../../../framework/factory/EpicFactory";
import CreateShipmentEpic from './order/CreateShipmentEpic'
import PrepareCreateShipmentEpic from './order/PrepareCreateShipmentEpic'

export default combineEpics(
    EpicFactory.get(CreateShipmentEpic),
    EpicFactory.get(PrepareCreateShipmentEpic)
)