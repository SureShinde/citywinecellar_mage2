import React from "react";

export default {
    block_refund_adjustments_after: [
        function templateRefundPoint(component) {
            const {order} = component.props;
            let AdjustPointComponent = require("../../../../view/component/order/order-detail/create-creditmemo/step-adjustment/CreateCreditmemoStepAdjustPointAdjustments").default;
            return (order.extension_attributes.rewardpoints_spent || order.extension_attributes.rewardpoints_earn ) ?
                <div className="block-refund-point" key="block-refund-point">
                    <AdjustPointComponent order={component.props.order}
                                          creditmemo={component.props.creditmemo}
                                          adjustments={component.props.adjustments}
                                          changeAdjustment={component.props.changeAdjustment}
                                          setCreditmemo={component.props.setCreditmemo}
                                          getCreditmemo={component.props.getCreditmemo}
                    />
                </div>
                : '';
        }
    ],
}
