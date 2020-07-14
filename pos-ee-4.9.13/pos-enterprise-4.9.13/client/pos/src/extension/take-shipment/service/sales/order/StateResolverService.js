import {AbstractOrderService} from "../../../../../service/sales/AbstractService";
import ServiceFactory from "../../../../../framework/factory/ServiceFactory";
import StatusConstant from "../../../../../view/constant/order/StatusConstant";
import OrderService from "../../../../../service/sales/OrderService";

const IN_PROGRESS = 'order_in_progress';
const FORCED_CREDITMEMO = 'forced_creditmemo';

export class StateResolverService extends AbstractOrderService {
    static className = 'StateResolverService';

    /**
     * Check if order should be in complete state
     *
     * @param order
     * @return boolean
     */
    isOrderComplete(order) {
        /** @var order Order|*/
        if (0 === order.base_grand_total || OrderService.canCreditmemo(order)) {
            return true;
        }
        return false;
    }

    /**
     * Check if order should be in closed state
     *
     * @param {Object} order
     * @param {Array} Arguments
     * @return boolean
     */
    isOrderClosed(order, Arguments = []) {
        /** @var order Order|*/
        let forceCreditmemo = Arguments.includes(FORCED_CREDITMEMO);
        if (order.total_refunded) {
            return true;
        }
        return !order.total_refunded && forceCreditmemo;

    }

    /**
     * Check if order is processing
     *
     * @param order
     * @param {Array} Arguments
     * @return boolean
     */
    isOrderProcessing(order, Arguments = []) {
        /** @var order Order|*/
        if (order.state === StatusConstant.STATE_NEW && Arguments.includes(IN_PROGRESS)) {
            return true;
        }
        return false;
    }

    /**
     * Returns initial state for order
     *
     * @param order
     * @return string
     */
    getInitialOrderState(order) {
        return order.state === StatusConstant.STATE_PROCESSING ? StatusConstant.STATE_PROCESSING : StatusConstant.STATE_NEW;
    }

    /**
     * @param order
     * @param {Array} Arguments
     * @return string
     */
    getStateForOrder(order, Arguments = []) {
        /** @var order Order|*/
        let orderState = this.getInitialOrderState(order);
        if (
            !OrderService.isCanceled(order)
            && !OrderService.canUnhold(order)
            && !OrderService.canInvoice(order)
            && !OrderService.canShip(order)
        ) {
            if (this.isOrderComplete(order)) {
                orderState = StatusConstant.STATE_COMPLETE;
            } else if (this.isOrderClosed(order, Arguments)) {
                orderState = StatusConstant.STATE_CLOSED;
            }
        }
        if (this.isOrderProcessing(order, Arguments)) {
            orderState = StatusConstant.STATE_PROCESSING;
        }
        return orderState;
    }

    /**
     * @param {string} state
     * @return string
     */
    getStatusForOrderByState(state) {
        let stateKey = Object.keys(StatusConstant).find(key => StatusConstant[key] === state);
        let status = StatusConstant.STATUS_PENDING;

        if (!stateKey) {
            return state;
        }

        let statusKey = stateKey.replace('STATE', 'STATUS');
        if (StatusConstant.hasOwnProperty(statusKey)) {
            status = StatusConstant[statusKey];
        }

        return status;
    }
}

/** @type StateResolverService */
let stateResolverService = ServiceFactory.get(StateResolverService);

export default stateResolverService;