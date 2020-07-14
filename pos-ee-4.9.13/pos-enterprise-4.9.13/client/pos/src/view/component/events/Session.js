import CoreContainer from "../../../framework/container/CoreContainer";
import CoreComponent from "../../../framework/component/CoreComponent";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import {listen} from "../../../event-bus";
import SessionAction from "../../action/SessionAction"

export class EventSessionComponent extends CoreComponent {
    static className = 'EventSessionComponent';

    constructor(props) {
        super(props);
        listen('service_session_remove_after', () => {
            this.props.redirectToManageSession();
        }, 'EventSessionComponent');
    }

    render() {
        return (null)
    }
}

class EventSessionContainer extends CoreContainer {
    static className = 'EventSessionContainer';

    static mapDispatch(dispatch) {
        return {
            redirectToManageSession: () => dispatch(
                SessionAction.redirectToManageSession()
            ),
        }
    }
}

export default ContainerFactory.get(EventSessionContainer).withRouter(
    ComponentFactory.get(EventSessionComponent)
);
