import {combineEpics} from 'redux-observable';
import EpicFactory from "../../framework/factory/EpicFactory";
import SyncDataEpic from "./sync/SyncDataEpic";
import SetDefaultSyncDBEpic from "./sync/SetDefaultSyncDBEpic";
import SyncDataWithTypeEpic from "./sync/SyncDataWithTypeEpic";
import InternetEpic from "./sync/InternetEpic";
import SyncActionLogEpic from "./sync/SyncActionLogEpic";
import ScheduleUpdateDataEpic from "./sync/ScheduleUpdateDataEpic";
import UpdateDataEpic from "./sync/UpdateDataEpic";
import ExecuteUpdateDataEpic from "./sync/ExecuteUpdateDataEpic";
import UpdateDataWithTypeEpic from "./sync/UpdateDataWithTypeEpic";
import UpdateProductLocationEpic from "./sync/UpdateProductLocationEpic";
import ReloadPageEpic from "./sync/ReloadPageEpic";

/**
 * Combine all epic sync
 * @type {Epic<Action, any, any, T> | any}
 */
const syncEpic = combineEpics(
    EpicFactory.get(SetDefaultSyncDBEpic),
    EpicFactory.get(SyncDataEpic),
    EpicFactory.get(SyncDataWithTypeEpic),
    EpicFactory.get(InternetEpic),
    EpicFactory.get(SyncActionLogEpic),
    EpicFactory.get(ScheduleUpdateDataEpic),
    EpicFactory.get(UpdateDataEpic),
    EpicFactory.get(ExecuteUpdateDataEpic),
    EpicFactory.get(UpdateDataWithTypeEpic),
    EpicFactory.get(UpdateProductLocationEpic),
    EpicFactory.get(ReloadPageEpic)
);

export default syncEpic;
