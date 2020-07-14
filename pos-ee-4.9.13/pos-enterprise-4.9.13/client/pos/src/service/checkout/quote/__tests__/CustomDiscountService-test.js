import CustomDiscountService from '../CustomDiscountService';
import Config from "../../../../config/Config";
import QuoteService from '../../QuoteService';
import CurrencyHelper from "../../../../helper/CurrencyHelper";
import CustomerGroupHelper from "../../../../helper/CustomerGroupHelper";

describe('Test applyCustomRule', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.collectTotals = QuoteService.collectTotals;

    mocks.getCurrency = CurrencyHelper.getCurrency;
    CurrencyHelper.getCurrency = jest.fn();
    mocks.convertToBase = CurrencyHelper.convertToBase;
    CurrencyHelper.convertToBase = jest.fn(amount => amount);

    mocks.getAllCustomerGroup = CustomerGroupHelper.getAllCustomerGroup;
  });
  afterAll(() => {
    // Unmock functions
    QuoteService.collectTotals = mocks.collectTotals;

    CurrencyHelper.getCurrency = mocks.getCurrency;
    CurrencyHelper.convertToBase = mocks.convertToBase;

    CustomerGroupHelper.getAllCustomerGroup = mocks.getAllCustomerGroup;
  });

  let data = [
    {
      testCaseId: 'DIS-CUS-01',
      title: 'applyCustomRule(quote, discountType, discountAmount, reason) with quote.subtotal = 0',
      quote: {
        subtotal: 0,
        items: [],
      },
      discountType: '$',
      discountAmount: 100,
      customer_groups: [],
      reason: 'test',
      maxDiscountPercent: 10,
      expect: {
        os_pos_custom_discount_reason: '',
        os_pos_custom_discount_type: '',
        os_pos_custom_discount_amount: 0,
      },
    },
    {
      testCaseId: 'DIS-CUS-02',
      title: 'applyCustomRule(quote, discountType, discountAmount, reason) with maxDiscountPercent = 0',
      quote: {
        subtotal: 10,
        items: [],
      },
      discountType: '%',
      discountAmount: 100,
      customer_groups: [],
      reason: 'test',
      maxDiscountPercent: 0,
      expect: {
        os_pos_custom_discount_reason: '',
        os_pos_custom_discount_type: '',
        os_pos_custom_discount_amount: 0,
      },
    },
    {
      testCaseId: 'DIS-CUS-03',
      title: 'applyCustomRule(quote, discountType, discountAmount, reason) with discount 101% & maxDiscountPercent = 90',
      quote: {
        subtotal: 100.99,
        items: [],
      },
      discountType: '%',
      discountAmount: 101,
      customer_groups: [],
      reason: 'test',
      maxDiscountPercent: 90,
      expect: {
        os_pos_custom_discount_reason: 'test',
        os_pos_custom_discount_type: '%',
        os_pos_custom_discount_amount: 90,
        valid_salesrule: {
          customer_group_ids: [],
          discount_amount: 90,
          simple_action: 'by_percent',
          valid_item_ids: [],
        }
      },
    },
    {
      testCaseId: 'DIS-CUS-04',
      title: 'applyCustomRule(quote, discountType, discountAmount, reason) with discount $101 & maxDiscountPercent = 90',
      quote: {
        subtotal: 100.99,
        items: [],
      },
      discountType: '$',
      discountAmount: 101,
      customer_groups: [],
      reason: 'test',
      maxDiscountPercent: 90,
      expect: {
        os_pos_custom_discount_reason: 'test',
        os_pos_custom_discount_type: '$',
        os_pos_custom_discount_amount: 90.89,
        valid_salesrule: {
          customer_group_ids: [],
          discount_amount: 90.89,
          simple_action: 'cart_fixed',
          valid_item_ids: [],
        }
      },
    },
    {
      testCaseId: 'DIS-CUS-05',
      title: 'applyCustomRule(quote, discountType, discountAmount, reason) with discount 30% & maxDiscountPercent = 90',
      quote: {
        subtotal: 100.99,
        items: [
          {item_id: 1},
          {item_id: 2, parent_item_id: 1},
        ],
      },
      discountType: '%',
      discountAmount: 30,
      customer_groups: [{id: 3}],
      reason: 'test',
      maxDiscountPercent: 90,
      expect: {
        os_pos_custom_discount_reason: 'test',
        os_pos_custom_discount_type: '%',
        os_pos_custom_discount_amount: 30,
        valid_salesrule: {
          customer_group_ids: [3],
          discount_amount: 30,
          simple_action: 'by_percent',
          valid_item_ids: [1],
        }
      },
    },
    {
      testCaseId: 'DIS-CUS-06',
      title: 'applyCustomRule(quote, discountType, discountAmount, reason) with discount $30 & maxDiscountPercent = 90',
      quote: {
        subtotal: 100.99,
        items: [],
      },
      discountType: '$',
      discountAmount: 30,
      customer_groups: null,
      reason: 'test',
      maxDiscountPercent: 90,
      expect: {
        os_pos_custom_discount_reason: 'test',
        os_pos_custom_discount_type: '$',
        os_pos_custom_discount_amount: 30,
        valid_salesrule: {
          customer_group_ids: [],
          discount_amount: 30,
          simple_action: 'cart_fixed',
          valid_item_ids: [],
        }
      },
    },
  ];

  data.forEach(testCase => {
    it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
      // Mock maxDiscountPercent
      Config.config = {
        'max_discount_percent': testCase.maxDiscountPercent
      };
      // Mock collectTotals
      QuoteService.collectTotals = jest.fn();
      // Mock getAllCustomerGroup
      CustomerGroupHelper.getAllCustomerGroup = jest.fn(() => testCase.customer_groups);

      // applyCustomRule
      CustomDiscountService.applyCustomRule(
        testCase.quote,
        testCase.discountType,
        testCase.discountAmount,
        testCase.reason
      );

      // Expect
      expect(testCase.quote.os_pos_custom_discount_reason)
        .toEqual(testCase.expect.os_pos_custom_discount_reason);
      expect(testCase.quote.os_pos_custom_discount_type)
        .toEqual(testCase.expect.os_pos_custom_discount_type);
      expect(testCase.quote.os_pos_custom_discount_amount)
        .toEqual(testCase.expect.os_pos_custom_discount_amount);
      expect(QuoteService.collectTotals.mock.calls.length).toBe(1);
      // Expect valid_salesrule
      if (!testCase.expect.valid_salesrule) {
        expect(testCase.quote.valid_salesrule).toBeUndefined();
      } else {
        expect(testCase.quote.valid_salesrule.length).toBe(1);
        for (const key in testCase.expect.valid_salesrule) {
          expect(testCase.quote.valid_salesrule[0][key])
            .toEqual(testCase.expect.valid_salesrule[key]);
        }
      }
    });
  });
});

describe('Test removeCustomRule', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.collectTotals = QuoteService.collectTotals;
  });
  afterAll(() => {
    // Unmock functions
    QuoteService.collectTotals = mocks.collectTotals;
  });

  it('[DIS-CUS-07] remove custom discount', () => {
    let quote = {
      os_pos_custom_discount_reason: 'test',
      os_pos_custom_discount_type: '$',
      os_pos_custom_discount_amount: 30,
      valid_salesrule: [{
        customer_group_ids: [],
        discount_amount: 30,
        simple_action: 'cart_fixed',
        valid_item_ids: [],
      }],
    };
    QuoteService.collectTotals = jest.fn();

    CustomDiscountService.removeCustomRule(quote);

    expect(QuoteService.collectTotals.mock.calls.length).toBe(1);
    expect(quote.os_pos_custom_discount_reason).toBe('');
    expect(quote.os_pos_custom_discount_type).toBe('');
    expect(quote.valid_salesrule).toBe('');
    expect(quote.os_pos_custom_discount_amount).toBe(0);
  });
});
