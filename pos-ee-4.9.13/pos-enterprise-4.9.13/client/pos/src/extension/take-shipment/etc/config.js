import ModuleConfigAbstract from "../../ModuleConfigAbstract";
import CheckoutServiceObserver from "../observer/service/checkout/CheckoutServiceObserver";
import ActionLogServiceObserver from "../observer/service/sync/ActionLogServiceObserver";
import OrderDetailObserver from "../observer/view/component/order/OrderDetailObserver";
import CompleteOrderObserver from "../observer/view/component/checkout/complete-order/CompleteOrderObserver";
import OrderServiceMixin from "../mixin/service/sales/OrderServiceMixin";
import ActionLogServiceMixin from "../mixin/service/sync/ActionLogServiceMixin";
import ExpireAlertPopupLayout from "../layout/order/order-history/ExpireAlertPopupLayout";
import ActionsLayout from "../layout/order/order-detail/order-detail-layout/ActionsLayout";
import CompleteOrderLayout from "../layout/order/CompleteOrderLayout";
import orderCreateShipmentReducer from "../view/reducer/orderCreateShipmentReducer";
import checkoutCreateShipmentReducer from "../view/reducer/checkoutCreateShipmentReducer";
import OrderHistoryContainerPlugin from "../plugin/view/container/order/OrderHistoryContainerPlugin";
import OrderDetailContainerPlugin from "../plugin/view/container/order/OrderDetailContainerPlugin";
import CompleteOrderPlugin from "../plugin/view/container/checkout/complete-order/CompleteOrderPlugin";
import "../view/style/css/OrderCreateShipment.css";
import ActionLogServicePlugin from "../plugin/service/sync/ActionLogServicePlugin";

class Config extends ModuleConfigAbstract{
  module = ['take-shipment'];
  menu = {};
  observer = (() => {
    new CheckoutServiceObserver();
    new ActionLogServiceObserver();
    new OrderDetailObserver();
    new CompleteOrderObserver();
  })();
  reducer = {
    orderCreateShipmentReducer,
    checkoutCreateShipmentReducer,
  };
  rewrite = {
  };
  layout = {
    order: {
      orderHistory: {
        expire_alert_popup_layout: ExpireAlertPopupLayout,
      },
      order_detail_layout: {
        actions: ActionsLayout
      },
      complete_order_layout: CompleteOrderLayout
    },

  };
  mixin = {
    service: {
      OrderService: OrderServiceMixin,
      ActionLogService: ActionLogServiceMixin,
    },
  };
  plugin = {
    container: {
      OrderHistoryContainer: {
        mapDispatch: {
          changeMapDispatch: OrderHistoryContainerPlugin.changeMapDispatch
        },
        mapState: {
          changeMapState: OrderHistoryContainerPlugin.changeMapState
        }
      },
      OrderDetailContainer: {
        mapDispatch: {
          changeMapDispatch: OrderDetailContainerPlugin.changeMapDispatch
        }
      },
      CompleteOrderContainer: {
        mapDispatch: {
          changeMapDispatch: CompleteOrderPlugin.changeMapDispatch
        }
      }
    },
    service: {
      ActionLogService: ActionLogServicePlugin
    }
  };
}

export default (new Config());
