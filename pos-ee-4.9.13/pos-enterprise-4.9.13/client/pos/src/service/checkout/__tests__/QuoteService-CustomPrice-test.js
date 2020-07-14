import QuoteService from '../QuoteService';
import Config from "../../../config/Config";
import ItemConstant from "../../../view/constant/checkout/cart/ItemConstant";

describe('Integration test apply custom price', () => {
  beforeAll(() => {
    // Mock config for test env
    Config.config = {
      guest_customer: {},
      settings: [
        {path: "tax/calculation/algorithm", value: "TOTAL_BASE_CALCULATION"},
        {path: "customer/create_account/default_group", value: "1"},
      ],
      customer_groups: [
        {id: 0, code: "NOT LOGGED IN", tax_class_id: 3},
        {id: 1, code: "General", tax_class_id: 3},
      ],
      currencies: [
        {
          code: "EUR",
          currency_name: "Euro",
          currency_rate: 1,
          currency_symbol: "€",
          is_default: 0
        },
        {
          code: "USD",
          currency_name: "US Dollar",
          currency_rate: 1,
          currency_symbol: "$",
          is_default: 1,
        },
      ],
      current_currency_code: "EUR",
    };
    Config.location_address = {};
  });

  let data = [
    {
      testCaseId: 'CUP-001',
      title: 'Check custom price with currency convert rate 3, qty 1',
      currency_rate: "3",
      custom_price: 0.01,
      qty: 1,
      expect: {
        custom_price: 0.01,
        row_total: 0.01,
        base_row_total: 0.00,
      },
    },
    {
      testCaseId: 'CUP-002',
      title: 'Check custom price with currency convert rate 3, qty 10',
      currency_rate: "3",
      custom_price: 0.01,
      qty: 10,
      expect: {
        custom_price: 0.01,
        row_total: 0.10,
        base_row_total: 0.00,
      },
    },
    {
      testCaseId: 'CUP-003',
      title: 'Check custom price with currency convert rate 0.00286, qty 1',
      currency_rate: "0.00286",
      custom_price: 0.02,
      qty: 1,
      expect: {
        custom_price: 0.02,
        row_total: 0.02,
        base_row_total: 6.99,
      },
    },
    {
      testCaseId: 'CUP-004',
      title: 'Check custom price with currency convert rate 0.00286, qty 10',
      currency_rate: "0.00286",
      custom_price: 0.02,
      qty: 10,
      expect: {
        custom_price: 0.02,
        row_total: 0.20,
        base_row_total: 69.90,
      },
    },
  ];

  data.forEach(testCase => {
    it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
      Config.config.currencies[0].currency_rate = testCase.currency_rate;

      let item = {
        product: {},
        qty: testCase.qty,
      };
      let data = {
          customPrice: testCase.custom_price,
          customPriceDiscountValue: null,
          customPriceDiscountType: null,
          reason: '',
          unitCustomPriceDiscount: null,
          customPriceType: ItemConstant.STATE_CUSTOM_PRICE
      }
      let quote = {
        addresses: [],
        items: [item],
      };
      QuoteService.updateCustomPriceCartItem(quote, item, data);

      expect(item.custom_price).toBe(testCase.expect.custom_price);
      expect(item.row_total).toBe(testCase.expect.row_total);
      expect(item.base_row_total).toBe(testCase.expect.base_row_total);
    });
  });
});
