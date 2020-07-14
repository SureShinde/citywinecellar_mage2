import {combineEpics} from 'redux-observable';
import EpicFactory from "../../framework/factory/EpicFactory";
import GetListCategoryEpic from './category/GetListCategoryEpic';

/**
 * Combine all category epic
 * @type {Epic<Action, any, any, T> | any}
 */
const categoryEpic = combineEpics(
    EpicFactory.get(GetListCategoryEpic),
);

export default categoryEpic;
