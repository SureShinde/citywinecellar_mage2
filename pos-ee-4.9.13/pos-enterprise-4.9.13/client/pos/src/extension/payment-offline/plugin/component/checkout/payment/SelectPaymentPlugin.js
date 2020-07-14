import "../../../../view/style/css/PaymentOffline.css";
import {toast} from "react-toastify";

export default {
    template: {
        payment_offline: {
            sortOrder: 100,
            disabled: false,
            before: function () {

            },
        }
    },
    /**
     * plugin to check an order, customer can select only one Payment Later method
     */
    selectPayment: {
        payment_offline: {
            sortOrder: 100,
            disabled: false,
            around: function (proceed, payment) {
                if (payment.is_pay_later) {
                    let quote = this.props.quote;
                    let paymentsSelected = quote.payments;
                    if (paymentsSelected.length > 0) {
                        if (this.existedPayLater(paymentsSelected)) {
                            return toast.error(
                                this.props.t('You can select only one Pay Later method for an order. Please choose another payment method.'),
                                {
                                    className: 'wrapper-messages messages-warning',
                                    autoClose: 3000
                                }
                            );
                        }
                    }
                }
                this.props.selectPayment(payment, this.props.remain);
            },
        }
    }
};
