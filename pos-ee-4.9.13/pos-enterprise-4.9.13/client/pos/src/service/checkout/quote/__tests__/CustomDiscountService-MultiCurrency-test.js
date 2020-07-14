import CustomDiscountService from '../CustomDiscountService';
import Config from "../../../../config/Config";
import QuoteService from '../../QuoteService';

describe('Test integration applyCustomRule with multiple currency', () => {
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
      max_discount_percent: 100,
    };
    Config.location_address = {};
  });

  let data = [
    {
      testCaseId: 'DIS-CUS-08',
      title: 'applyCustomRule with subtotal = 0.02, base_subtotal = 7, discount = $0.02',
      currency_rate: "0.00286",
      product_price: 7,
      qty: 1,
      discountType: '$',
      discountAmount: 0.02,
      expect: {
        base_discount_amount: -6.99,
        discount_amount: -0.02,
        base_grand_total: 0.01,
        grand_total: 0,
      },
    },
    {
      testCaseId: 'DIS-CUS-09',
      title: 'applyCustomRule with subtotal = 0.02, base_subtotal = 7, discount = 100%',
      currency_rate: "0.00286",
      product_price: 7,
      qty: 1,
      discountType: '%',
      discountAmount: 100,
      expect: {
        base_discount_amount: -7,
        discount_amount: -0.02,
        base_grand_total: 0,
        grand_total: 0,
      },
    },
    {
      testCaseId: 'DIS-CUS-10',
      title: 'applyCustomRule with subtotal = 21, base_subtotal = 7, discount = $21',
      currency_rate: "3",
      product_price: 7,
      qty: 1,
      discountType: '$',
      discountAmount: 21,
      expect: {
        base_discount_amount: -7,
        discount_amount: -21,
        base_grand_total: 0,
        grand_total: 0,
      },
    },
  ];

  data.forEach(testCase => {
    it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
      Config.config.currencies[0].currency_rate = testCase.currency_rate;

      // Apply custom rule discount
      let quote = {
        addresses: [],
        items: [{
          item_id: 1,
          product: {price: testCase.product_price},
          qty: testCase.qty,
        }],
      };
      CustomDiscountService.applyCustomRule(quote, testCase.discountType, testCase.discountAmount, '');

      // Check output
      quote = QuoteService.collectTotals(quote);
      expect(quote).toMatchObject(testCase.expect);
    });
  });
});
