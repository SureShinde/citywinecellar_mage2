import React, {Fragment} from "react";

/**
 * Plugin to add more Due payment method to receipt
 */
export default {
    getTemplatePaymentArea: {
        payment_offline: {
            sortOrder: 100,
            disabled: false,
            around: function (proceed, order) {
                let grandTotal = order.grand_total;
                if (grandTotal) {
                    return (
                        <Fragment>
                            <tr>
                                <td colSpan="4">
                                    <hr/>
                                </td>
                            </tr>
                            {this.getTemplateTotalPaid(order)}
                            {this.getTemplatePayments(order)}
                            {this.getTemplateTotalDue(order)}
                            {this.getTemplateDuePayments(order)}
                            {this.getTemplateTotalChage(order)}
                            {this.getTemplateTotalRefunded(order)}
                        </Fragment>
                    )
                }
            },
        }
    }
};
