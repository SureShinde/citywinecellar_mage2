import CoreContainer from "../../../framework/container/CoreContainer";
import CoreComponent from "../../../framework/component/CoreComponent";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import {listen} from "../../../event-bus";
import CreditmemoAction from "../../action/order/CreditmemoAction";

export class CreditmemoComponent extends CoreComponent {
    static className = 'CreditmemoComponent';

    constructor(props) {
        super(props);
        listen('resource_model_order_refund', eventData => {
            this.props.createCreditmemo(eventData.creditmemo);
        }, 'CreditmemoComponent');
        listen('service_creditmemo_refund_operation_refund_after', eventData => {
            this.props.refundOperationRefundAfter(eventData.creditmemo, eventData.order);
        }, 'CreditmemoComponent');
        listen('service_creditmemo_init_total_collectors', eventData => {
            this.props.creditmemoInitTotalCollectors(eventData.service);
        }, 'CreditmemoComponent');
    }

    render() {
        return (null)
    }
}

class CreditmemoContainer extends CoreContainer {
    static className = 'CreditmemoContainer';

    static mapDispatch(dispatch) {
        return {
            createCreditmemo: (creditmemo) => dispatch(
                CreditmemoAction.creditmemoCreateActionLogBefore(creditmemo)
            ),
            refundOperationRefundAfter: (creditmemo, order) => dispatch(
                CreditmemoAction.refundOperationRefundAfter(creditmemo, order)
            ),
            creditmemoInitTotalCollectors: (service) => dispatch(
                CreditmemoAction.salesOrderCreditmemoInitTotalCollectors(service)
            ),
        }
    }
}

export default ContainerFactory.get(CreditmemoContainer).withRouter(
    ComponentFactory.get(CreditmemoComponent)
);
