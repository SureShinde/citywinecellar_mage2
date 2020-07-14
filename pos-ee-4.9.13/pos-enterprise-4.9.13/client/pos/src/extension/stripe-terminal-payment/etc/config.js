import ModuleConfigAbstract from "../../ModuleConfigAbstract";
import PaymentFactoryObserver from "../observer/factory/PaymentFactoryObserver";
import CreditmemoServiceObserver from "../observer/service/sales/order/CreditmemoServiceObserver";
import RefundOperationServiceObserver from "../observer/service/sales/order/creditmemo/RefundOperationServiceObserver";
import SettingPlugin from "../plugin/view/component/SettingPlugin";
import PaymentDetailPlugin from "../plugin/view/component/settings/PaymentDetailPlugin";
import ToPrintComponentPlugin from "../plugin/view/component/print/ToPrintComponentPlugin";
import ContentPaymentItemPlugin from "../plugin/view/component/order/order-detail/detail-content/ContentPaymentItemPlugin";
import CreateCreditmemoStepPaymentAddPaymentPlugin from "../plugin/view/component/order/order-detail/create-creditmemo/step-payment/CreateCreditmemoStepPaymentAddPaymentPlugin";
import CreateCreditmemoStepPaymentOrderPaymentMethodComponentPlugin from "../plugin/view/component/order/order-detail/create-creditmemo/step-payment/CreateCreditmemoStepPaymentOrderPaymentMethodPlugin";
import "../view/style/css/StripeTerminalPayment.css";

class Config extends ModuleConfigAbstract{
  module = ['stripe-terminal-payment'];
  menu = {};
  observer = (() => {
    new PaymentFactoryObserver();
    new CreditmemoServiceObserver();
    new RefundOperationServiceObserver();
  })();
  plugin = {
    component: {
      Setting: SettingPlugin,
      PaymentDetail: PaymentDetailPlugin,
      ToPrintComponent: ToPrintComponentPlugin,
      ContentPaymentItem: ContentPaymentItemPlugin,
      CreateCreditmemoStepPaymentAddPaymentComponent: CreateCreditmemoStepPaymentAddPaymentPlugin,
      CreateCreditmemoStepPaymentOrderPaymentMethodComponent: CreateCreditmemoStepPaymentOrderPaymentMethodComponentPlugin,
    },
  };
}

export default (new Config());
