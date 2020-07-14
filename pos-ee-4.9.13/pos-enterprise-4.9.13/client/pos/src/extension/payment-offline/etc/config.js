import ModuleConfigAbstract from "../../ModuleConfigAbstract";
import SelectPaymentPlugin from "../plugin/component/checkout/payment/SelectPaymentPlugin";
import EditPaymentPlugin from "../plugin/component/checkout/payment/EditPaymentPlugin";
import SelectPaymentMixin from "../mixin/component/checkout/payment/SelectPaymentMixin";
import CompleteOrderPlugin from "../plugin/component/checkout/complete-order/CompleteOrderPlugin";
import ToPrintComponentPlugin from "../plugin/component/print/ToPrintComponentPlugin";
import ToPrintComponentMixin from "../mixin/component/print/ToPrintComponentMixin";
import OrderSelectPaymentPlugin from "../plugin/component/order/order-detail/take-payment/OrderSelectPaymentPlugin";

class Config extends ModuleConfigAbstract{
  module = ['payment-offline'];
  menu = {};
  observer = (() => {
  })();
  reducer = {};
  rewrite = {
    component: {
    }
  };
  plugin = {
    component: {
      SelectPayment: SelectPaymentPlugin,
      EditPayment: EditPaymentPlugin,
      ToPrintComponent: ToPrintComponentPlugin,
      CompleteOrder: CompleteOrderPlugin,
      OrderSelectPayment: OrderSelectPaymentPlugin
    },
    resource_model: {},
    service: {},
  };
  layout = {};
  mixin = {
    component: {
      SelectPayment: SelectPaymentMixin,
      ToPrintComponent: ToPrintComponentMixin
    }
  };
}

export default (new Config());
