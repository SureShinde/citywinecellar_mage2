import React from 'react';
import PropTypes from 'prop-types';
import {CoreComponent} from "../../../../../framework/component/index";
import {toast} from "react-toastify";
import ContainerFactory from "../../../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../../../framework/container/CoreContainer";
import ComponentFactory from "../../../../../framework/factory/ComponentFactory";
import PaymentConstant from "../../../../constant/PaymentConstant";
import StoreCreditService from "../../../../../service/store-credit/StoreCreditService";

export class TakePaymentItem extends CoreComponent {
    static className = 'TakePaymentItem';

    /**
     * select payment
     * @param payment
     */
    selectPayment(payment) {
        let {order, grand_total, remain} = this.props;
        if (
            (payment.code === PaymentConstant.STORE_CREDIT) &&
            !StoreCreditService.checkSpentCreditSelectPayment(null, grand_total, remain, order)
        ) {
            return toast.error(
                this.props.t('You do not have enough credit to spend for this order.'),
                {
                    className: 'wrapper-messages messages-warning',
                    autoClose: 3000
                }
            );
        }
        this.props.selectPayment(payment);
    }

    /**
     * Render template
     *
     * @return {*}
     */
    template() {
        let {payment} = this.props;
        let code = payment.code;
        let payment_title = (code === PaymentConstant.STORE_CREDIT) ? this.props.t(payment.title) : payment.title;
        let payment_subtitle = payment.sub_title;
        return (
            <li onClick={() => this.selectPayment(payment)}>
                {
                    payment.icon ?
                        <span className = {"img"}><img alt={"payment-icon"} className={"img payment-offline-icon"} src={payment.icon}/></span>
                    :
                        <span className={"img image-default image-" + code }/>
                }
                <span className="text">
                    {
                        this.props.t(payment_title) + (payment_subtitle ? ' ' + payment_subtitle : '')
                    }
                </span>
            </li>
        )
    }
}

TakePaymentItem.propTypes = {
    selectPayment: PropTypes.func
};

class TakePaymentItemContainer extends CoreContainer {
    static className = 'TakePaymentItemContainer';
}

export default ContainerFactory.get(TakePaymentItemContainer).withRouter(
    ComponentFactory.get(TakePaymentItem)
)