import React, {Fragment} from "react";
import ComponentFactory from '../../../../../framework/factory/ComponentFactory';
import CoreContainer from '../../../../../framework/container/CoreContainer';
import ContainerFactory from "../../../../../framework/factory/ContainerFactory";
import CoreComponent from "../../../../../framework/component/CoreComponent";
import DateTimeHelper from "../../../../../helper/DateTimeHelper";
import moment from 'moment';
import OrderHelper from "../../../../../helper/OrderHelper";
import PaymentHelper from "../../../../../helper/PaymentHelper";

export class ContentPaymentItem extends CoreComponent {
    static className = 'ContentPaymentItem';

    /**
     * template
     * @returns {*}
     */
    template() {
        let {payment, order} = this.props;
        let date = payment.payment_date ?
            '(' +
            moment(DateTimeHelper.convertDatabaseDateTimeToLocalDate(payment.payment_date)).format('L')
            + ')'
            : '';
        return (
            <Fragment>
                <li>
                    <div className="title">{
                        PaymentHelper.isPaypalDirect(payment.method) && !payment.reference_number
                            ? PaymentHelper.paypalPayViaEmailTitle()
                            : payment.title
                    } <span>{date}</span></div>
                    <div className="value">
                        {
                            payment.amount_paid > 0 ?
                                OrderHelper.formatPrice(payment.amount_paid, order)
                            :
                                <strong>{this.props.t('Pay Later')}</strong>
                        }
                    </div>
                </li>
                {
                    (payment.reference_number && (
                        <li>
                            <div className="value">
                                {
                                    payment.reference_number
                                }
                                {
                                    payment.card_type && ` - ${payment.card_type.toUpperCase()}`
                                }
                            </div>
                        </li>
                    )) || (payment.card_type && <li>
                        <div className="value">
                            {
                                `${payment.card_type.toUpperCase()}`
                            }
                        </div>
                    </li>)
                }
            </Fragment>
        );
    }
}

class ContentPaymentItemContainer extends CoreContainer {
    static className = 'ContentPaymentItemContainer';
}

/**
 * @type {ContentPaymentItem}
 */
export default ContainerFactory.get(ContentPaymentItemContainer).withRouter(
    ComponentFactory.get(ContentPaymentItem)
);