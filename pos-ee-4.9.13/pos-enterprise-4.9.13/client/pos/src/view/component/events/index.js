import React, {Component, Fragment} from 'react';
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import ForceSignOut from "./ForceSignOut"
import Creditmemo from "./Creditmemo"
import ActionLog from "./ActionLog"
import Quote from "./Quote"
import Session from "./Session"

/**
 * Add events listener by components
 */
class EventIndex extends Component {
    static className = 'EventIndex';
    render() {
        return (
            <Fragment>
                <ForceSignOut/>
                <Creditmemo/>
                <ActionLog/>
                <Quote/>
                <Session/>
            </Fragment>
        )
    }
}

export default ComponentFactory.get(EventIndex)
