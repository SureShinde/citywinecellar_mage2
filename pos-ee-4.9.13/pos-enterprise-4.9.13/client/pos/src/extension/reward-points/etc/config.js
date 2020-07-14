import ModuleConfigAbstract from "../../ModuleConfigAbstract";
import CartTotalsComponentPlugin from "../plugin/CartTotalsComponent";
import CartTotalsDiscountComponentPlugin from "../plugin/CartTotalsDiscountComponent";
import QuoteServicePlugin from "../plugin/service/QuoteService";
import CheckoutServicePlugin from "../plugin/service/CheckoutService";
import CreditmemoServicePlugin from "../plugin/service/CreditmemoService";
import CustomerServicePlugin from "../plugin/service/CustomerService";
import CheckoutReducerObserver from "../observer/CheckoutReducer";
import PointTotalServiceInitObserver from "../observer/service/quote/total/PointTotalServiceInit";
import CreditmemoPointTotalObserver from "../observer/service/order/creditmemo/total/PointTotalServiceInit";
import ChangeCustomerEpicAfterObserver from "../observer/epic/ChangeCustomerEpicAfter";
import RefundCreateGetCreditmemoDataObserver from "../observer/view/component/order/order-detail/RefundCreateGetCreditmemoDataObserver";
import MenuComponentPlugin from "../plugin/MenuComponent";
import Customer from "../layout/Customer";
import { listen } from "../../../event-bus";
import OrderDetailLayout from "../layout/order/OrderDetail";
import OrderDetailPlugin from "../plugin/component/order/OrderDetailPlugin";
import ViewCartTotalPrepareAfter from "../observer/view/component/checkout/cart/cart-total/ViewCartTotalPrepareAfter";
import ViewCartTotalPrepareBefore from "../observer/view/component/checkout/cart/cart-total/ViewCartTotalPrepareBefore";
import OrderCreateCreditmemoObserver from "../observer/view/component/order/order-detail/OrderCreateCreditmemoObserver";
import CreateCreditmemoStepAdjustmentLayout from "../layout/order/order-detail/create-creditmemo/CreateCreditmemoStepAdjustmentLayout";
import CreateCreditmemoStepAdjustmentTotalsPlugin
  from "../plugin/component/order/order-detail/create-creditmemo/step-adjustment/CreateCreditmemoStepAdjustmentTotalsPlugin";
import CreateCreditmemoStepAdjustmentTotalsObserver
  from "../observer/view/component/order/order-detail/create-creditmemo/step-adjustment/CreateCreditmemoStepAdjustmentTotalsObserver";
import ViewCartTotalInitAfter from "../observer/view/component/checkout/cart/cart-total/ViewCartTotalInitAfter";
import CreditmemoFactoryServicePlugin from "../plugin/service/sales/order/CreditmemoFactoryServicePlugin";
import RefundOperationServiceObserver from "../observer/service/sales/order/creditmemo/RefundOperationServiceObserver";
import OrderServiceObserver from "../observer/service/sales/OrderServiceObserver";
import PrintComponentObserver from "../observer/view/component/print/PrintComponentObserver";
import QuoteServiceObserver from "../observer/service/checkout/QuoteServiceObserver";
import TemplateReceiptLayout from "../layout/print/print-component/TemplateReceiptLayout";
import CheckoutServiceObserver from "../observer/service/checkout/CheckoutServiceObserver";
import TakePaymentServiceObserver from "../observer/service/sales/order/TakePaymentServiceObserver";

class Config extends ModuleConfigAbstract{
  module = ['reward-points'];
  menu = {};
  observer = (() => {
    // Listen event by pure function
    listen('reducer_checkout_index_before', CheckoutReducerObserver, 'rewardpoints');
    listen('service_quote_init_total_collectors', PointTotalServiceInitObserver, 'rewardpoints');
    listen('service_creditmemo_init_total_collectors', CreditmemoPointTotalObserver, 'rewardpoints');
    listen('epic_change_customer_after', ChangeCustomerEpicAfterObserver, 'rewardpoints');
    listen('refund_creditmemo_get_creditmemo_data_middle', RefundCreateGetCreditmemoDataObserver, 'rewardpoints');
    // Listen event by Object
    new ViewCartTotalPrepareAfter();
    new ViewCartTotalPrepareBefore();
    new OrderCreateCreditmemoObserver();
    new CreateCreditmemoStepAdjustmentTotalsObserver();
    new ViewCartTotalInitAfter();
    new RefundOperationServiceObserver();
    new OrderServiceObserver();
    new PrintComponentObserver();
    new QuoteServiceObserver();
    new CheckoutServiceObserver();
    new TakePaymentServiceObserver();
  })();
  reducer = {};
  rewrite = {
  };
  plugin = {
    component: {
      CartTotalsComponent: CartTotalsComponentPlugin,
      CartTotalsDiscountComponent: CartTotalsDiscountComponentPlugin,
      MenuComponent: MenuComponentPlugin,
      OrderDetail: OrderDetailPlugin,
      CreateCreditmemoStepAdjustmentTotalsComponent: CreateCreditmemoStepAdjustmentTotalsPlugin,
    },
    service: {
      CustomerService: CustomerServicePlugin,
      CheckoutService: CheckoutServicePlugin,
      QuoteService: QuoteServicePlugin,
      CreditmemoService: CreditmemoServicePlugin,
      CreditmemoFactoryService: CreditmemoFactoryServicePlugin,
    },
  };
  layout = {
    customer: Customer,
    order: {
      order_detail_layout: OrderDetailLayout,
      order_detail: {
        create_creditmemo: {
          step_adjustment_layout: CreateCreditmemoStepAdjustmentLayout,
        }
      }
    },
    print: {
      template_receipt_layout: TemplateReceiptLayout,
    }
  };
}

export default (new Config());
