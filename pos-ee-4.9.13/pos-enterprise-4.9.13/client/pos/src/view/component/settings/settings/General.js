import React, {Fragment} from "react";
import ComponentFactory from '../../../../framework/factory/ComponentFactory';
import CoreContainer from '../../../../framework/container/CoreContainer';
import ContainerFactory from "../../../../framework/factory/ContainerFactory";
import AbstractGrid from "../../../../framework/component/grid/AbstractGrid";
import GeneralService from "../../../../service/settings/GeneralService";
import '../../../style/css/Setting.css';

export class General extends AbstractGrid {
    static className = 'General';

    // /**
    //  *
    //  * @param props
    //  */
    // constructor(props) {
    //     super(props);
    // }

    /**
     * template
     * @returns {*}
     */
    template() {
        return (
            <Fragment>
                <div className="settings-right">
                    <div className="block-title">
                        <strong className="title"></strong>
                    </div>
                    <div className="block-content">
                        <ul className="list-lv1">
                            <li>
                                <span className="title">
                                    {this.props.t('Synchronize data')}
                                </span>
                                <span className="value">
                                    <label className="checkbox">
                                        <input type="checkbox"
                                               defaultChecked={GeneralService.isUseOfflineData()}
                                               onChange={(event) =>
                                                   GeneralService.setUseOfflineData(event.target.checked)}
                                        />
                                        <span></span>
                                    </label>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </Fragment>
        )
    }
}

class GeneralContainer extends CoreContainer {
    static className = 'GeneralContainer';
}

/**
 * @type {GeneralContainer}
 */
export default ContainerFactory.get(GeneralContainer).withRouter(
    ComponentFactory.get(General)
);