import React from 'react';
import PropTypes from 'prop-types';
import {CoreComponent} from "../../../../framework/component/index";
import ContainerFactory from "../../../../framework/factory/ContainerFactory";
import CoreContainer from "../../../../framework/container/CoreContainer";
import ComponentFactory from "../../../../framework/factory/ComponentFactory";
import CurrencyHelper from "../../../../helper/CurrencyHelper";
import PaymentConstant from "../../../constant/PaymentConstant";
import PaymentHelper from "../../../../helper/PaymentHelper";
import NumberHelper from "../../../../helper/NumberHelper";

class CompleteOrderPaymentItem extends CoreComponent {
    static className = 'CompleteOrderPaymentItem';

    /**
     * Get display value
     * @param value
     * @returns {*}
     */
    getDisplayValue(value) {
        return CurrencyHelper.format(value);
    }

    /**
     * Render template
     *
     * @returns {*}
     */
    template() {
        let {payment, paymentData, deletePayment, editPayment} = this.props;
        const isWaiting = [
            PaymentConstant.PROCESS_PAYMENT_PENDING, PaymentConstant.PROCESS_PAYMENT_PROCESSING
        ].indexOf(payment.status) !== -1;
        const isSuccess = payment.status === PaymentConstant.PROCESS_PAYMENT_SUCCESS;
        const preventEdit = isWaiting || isSuccess;
        let amountPaid = payment.amount_change ?
            NumberHelper.addNumber(payment.amount_paid, payment.amount_change) : payment.amount_paid;
        return (
            <div className="payment-full-amount">
                <div className="info" onClick={() => !preventEdit && editPayment(paymentData)}>
                    {
                        paymentData.icon ?
                            <span className = {"img"}><img alt={"payment-icon"} className={"img payment-offline-icon"} src={paymentData.icon}/></span>
                        :
                            <span className={"img image-default image-" + payment.method}/>
                    }
                    <div className="price">
                        <div className="box">
                            <span className="label">{paymentData.title}</span>
                            {
                                !paymentData.is_pay_later ?
                                    <span className="value">{this.getDisplayValue(amountPaid)}</span>
                                : ''
                            }
                        </div>
                        {
                            payment.reference_number ? (<div className="box reference">
                                <span className="label">{this.props.t('Reference No')}</span>
                                <span className="value">{payment.reference_number}</span>
                            </div>) : ''
                        }
                        {
                            payment.last4Digit ? (<div className="box reference">
                                <span className="label">{this.props.t('Card number')}</span>
                                <span className="value">{payment.last4Digit}</span>
                            </div>) : ''
                        }
                        {
                            payment.email ? (<div className="box reference">
                                <span className="label">{this.props.t('Email')}</span>
                                <span className="value">{payment.email}</span>
                            </div>) : ''
                        }
                        {
                            payment.errorMessage ? (<div className="box reference">
                                <span className="error value">{payment.errorMessage}</span>
                            </div>) : ''
                        }
                    </div>
                </div>
                {
                    isWaiting && !isSuccess && `${paymentData.type}` !== PaymentConstant.PAYMENT_TYPE_OFFLINE ?
                        <div className="loader-product loader">&nbsp;&nbsp;</div> :
                        isSuccess && !PaymentHelper.isFlatPayment(payment.method) ? '' :
                            <span className="remove-cash" onClick={() => deletePayment(paymentData.index)}/>
                }

            </div>
        )
    }
}

CompleteOrderPaymentItem.propTypes = {
    payment: PropTypes.object.isRequired,
    paymentData: PropTypes.object.isRequired,
    deletePayment: PropTypes.func.isRequired,
    editPayment: PropTypes.func.isRequired,
};

class CompleteOrderPaymentItemContainer extends CoreContainer {
    static className = 'CompleteOrderPaymentItemContainer';
}

export default ContainerFactory.get(CompleteOrderPaymentItemContainer).withRouter(
    ComponentFactory.get(CompleteOrderPaymentItem)
)
