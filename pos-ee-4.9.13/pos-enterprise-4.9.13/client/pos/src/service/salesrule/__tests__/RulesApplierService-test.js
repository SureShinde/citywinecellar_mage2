import RulesApplierService from "../RulesApplierService";
import { CustomDiscountService } from '../../checkout/quote/CustomDiscountService';
import UtilityService from '../UtilityService';
import ByPercentService from '../rule/action/discount/ByPercentService';

describe('Test applyRules function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.applyRule = RulesApplierService.applyRule;
    RulesApplierService.applyRule = jest.fn();
  });

  afterAll(() => {
    // Unmock functions
    RulesApplierService.applyRule = mocks.applyRule;
  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-RAS-01',
      title: 'applyRules(quote, address, item)',

      quote: {
        valid_salesrule: [
          {
            rule_id: 1,
            valid_item_ids: [4, 6]
          },
          {
            rule_id: 2,
            valid_item_ids: [10]
          },
        ],
        items: [
          {
            item_id: 1,
          },
          {
            item_id: 2,
            parent_item_id: 1
          },
          {
            item_id: 3,
            parent_item_id: 1
          },
          {
            item_id: 4,
          },
          {
            item_id: 5,
          },
          {
            item_id: 6,
            parent_item_id: 5
          },
          {
            item_id: 7,
            parent_item_id: 5
          },
        ]
      },
      address: [],
      item: {
        item_id: 1,
      },
      expect: [],
    },
    {
      testCaseId: 'DIS-RAS-02',
      title: 'applyRules(quote, address, item)',

      quote: {
        valid_salesrule: [
          {
            rule_id: 1,
            valid_item_ids: [4, 6]
          },
          {
            rule_id: 2,
            valid_item_ids: [10]
          },
        ],
        items: [
          {
            item_id: 1,
          },
          {
            item_id: 2,
            parent_item_id: 1
          },
          {
            item_id: 3,
            parent_item_id: 1
          },
          {
            item_id: 4,
          },
          {
            item_id: 5,
          },
          {
            item_id: 6,
            parent_item_id: 5
          },
          {
            item_id: 7,
            parent_item_id: 5
          },
        ]
      },
      address: [],
      item: {
        item_id: 4,
      },
      expect: [1],
    },
    {
      testCaseId: 'DIS-RAS-03',
      title: 'applyRules(quote, address, item)',

      quote: {
        valid_salesrule: [
          {
            rule_id: 1,
            valid_item_ids: [4, 6]
          },
          {
            rule_id: 2,
            valid_item_ids: [10]
          },
        ],
        items: [
          {
            item_id: 1,
          },
          {
            item_id: 2,
            parent_item_id: 1
          },
          {
            item_id: 3,
            parent_item_id: 1
          },
          {
            item_id: 4,
          },
          {
            item_id: 5,
          },
          {
            item_id: 6,
            parent_item_id: 5
          },
          {
            item_id: 7,
            parent_item_id: 5
          },
        ]
      },
      address: [],
      item: {
        item_id: 5,
      },
      expect: [1],
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      let result = RulesApplierService.applyRules(data[i].quote, data[i].address, data[i].item);

      expect(result).toEqual(data[i].expect);
    });
  }
  /* End Test */

});

describe('Test applyRule function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.getDiscountData = RulesApplierService.getDiscountData;
    mocks.setDiscountData = RulesApplierService.setDiscountData;
    mocks.maintainAddressCouponCode = RulesApplierService.maintainAddressCouponCode;
    mocks.addDiscountDescription = RulesApplierService.addDiscountDescription;
  });

  afterAll(() => {
    // Unmock functions
    RulesApplierService.getDiscountData = mocks.getDiscountData;
    RulesApplierService.setDiscountData = mocks.setDiscountData;
    RulesApplierService.maintainAddressCouponCode = mocks.maintainAddressCouponCode;
    RulesApplierService.addDiscountDescription = mocks.addDiscountDescription;
  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-RAS-04',
      title: 'applyRule(quote, item, rule, address)',

      quote: {
        os_pos_custom_discount_reason: 'abc',
        os_pos_custom_discount_type: '2',
        os_pos_custom_discount_amount: 10
      },
      item: { },
      rule: {
        rule_id: CustomDiscountService.DISCOUNT_RULE_ID
      },
      address: [],
      expect: {
        quote: {
          os_pos_custom_discount_reason: 'abc',
          os_pos_custom_discount_type: '2',
          os_pos_custom_discount_amount: 10
        }
      },
    },
    {
      testCaseId: 'DIS-RAS-05',
      title: 'applyRule(quote, item, rule, address)',

      quote: {
        os_pos_custom_discount_reason: 'abc',
        os_pos_custom_discount_type: '2',
        os_pos_custom_discount_amount: 10
      },
      item: { },
      rule: {
        rule_id: '1'
      },
      address: [],
      expect: {
        quote: {
          os_pos_custom_discount_reason: '',
          os_pos_custom_discount_type: '',
          os_pos_custom_discount_amount: 0
        },
      },
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      /* Mock functions */
      RulesApplierService.getDiscountData = jest.fn();
      RulesApplierService.setDiscountData = jest.fn();
      RulesApplierService.maintainAddressCouponCode = jest.fn();
      RulesApplierService.addDiscountDescription = jest.fn();

      RulesApplierService.applyRule(data[i].quote, data[i].item, data[i].rule, data[i].address);

      expect(data[i].quote).toEqual(data[i].expect.quote);
      expect(RulesApplierService.getDiscountData.mock.calls.length).toBe(1);
      expect(RulesApplierService.setDiscountData.mock.calls.length).toBe(1);
      expect(RulesApplierService.maintainAddressCouponCode.mock.calls.length).toBe(1);
      expect(RulesApplierService.addDiscountDescription.mock.calls.length).toBe(1);
    });
  }
  /* End Test */

});

describe('Test addDiscountDescription function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions

  });

  afterAll(() => {
    // Unmock functions

  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-RAS-06',
      title: 'addDiscountDescription(address, rule)',

      address: {
        discount_description: {},
        coupon_code: ''
      },
      rule: {
        rule_id: 1,
        store_labels: [ ]
      },
      expect: {
        discount_description: {}
      },
    },
    {
      testCaseId: 'DIS-RAS-07',
      title: 'addDiscountDescription(address, rule)',

      address: {
        discount_description: { },
        coupon_code: 'coupon'
      },
      rule: {
        rule_id: 1,
        store_labels: [ ]
      },
      expect: {
        discount_description: {
          1: "coupon"
        }
      },
    },
    {
      testCaseId: 'DIS-RAS-08',
      title: 'addDiscountDescription(address, rule)',

      address: {
        discount_description: { },
        coupon_code: 'coupon'
      },
      rule: {
        rule_id: 1,
        store_labels: [ "Store Label" ]
      },
      expect: {
        discount_description: {
          1: "Store Label"
        }
      },
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      RulesApplierService.addDiscountDescription(data[i].address, data[i].rule);

      expect(data[i].address.discount_description).toEqual(data[i].expect.discount_description);
    });
  }
  /* End Test */

});

describe('Test getDiscountData function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.getItemQty = UtilityService.getItemQty;
    UtilityService.getItemQty = jest.fn(() => 1);

    mocks.minFix = UtilityService.minFix;
    UtilityService.minFix = jest.fn((discountData) => discountData);

    mocks.fixQuantity = ByPercentService.fixQuantity;
    ByPercentService.fixQuantity = jest.fn(() => 1);

    mocks.calculate = ByPercentService.calculate;
  });

  afterAll(() => {
    // Unmock functions
    UtilityService.getItemQty = mocks.getItemQty;
    UtilityService.minFix = mocks.minFix;

    ByPercentService.fixQuantity = mocks.fixQuantity;
    ByPercentService.calculate = mocks.calculate;
  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-RAS-09',
      title: 'getDiscountData(quote, item, rule, address)',

      quote: {},
      item: {},
      address: {},
      rule: {
        simple_action: 'not_valid_action'
      },
      expect: {
        amount : 0,
        base_amount : 0
      },
    },
    {
      testCaseId: 'DIS-RAS-10',
      title: 'getDiscountData(quote, item, rule, address)',

      quote: { },
      item: { },
      address: { },
      rule: {
        simple_action: 'by_percent'
      },
      expect: {
        amount : 10,
        base_amount : 10,
        original_amount: 10,
        base_original_amount: 10
      },
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      /* Mock function */
      ByPercentService.calculate = jest.fn(() => data[i].expect);

      let result = RulesApplierService.getDiscountData(data[i].quote, data[i].item, data[i].rule, data[i].address);

      expect(result).toEqual(data[i].expect);
    });
  }
  /* End Test */

});

describe('Test setDiscountData function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions

  });

  afterAll(() => {
    // Unmock functions

  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-RAS-11',
      title: 'setDiscountData(discountData, item)',

      discountData: {
        amount : 10.4545,
        base_amount : 10.4545,
        original_amount: 10.4545,
        base_original_amount: 10.4545
      },
      item: {},
      expect: {
        item: {
          discount_amount : 10.45,
          base_discount_amount : 10.45,
          original_discount_amount: 10.45,
          base_original_discount_amount: 10.45
        }
      },
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      RulesApplierService.setDiscountData(data[i].discountData, data[i].item);

      expect(data[i].item).toEqual(data[i].expect.item);
    });
  }
  /* End Test */

});

describe('Test maintainAddressCouponCode function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions

  });

  afterAll(() => {
    // Unmock functions

  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-RAS-12',
      title: 'maintainAddressCouponCode(address, rule, couponCode)',

      address: {},
      rule: {
        coupon_type: '1'
      },
      couponCode: 'coupon',
      expect: {
        address: {}
      },
    },
    {
      testCaseId: 'DIS-RAS-13',
      title: 'maintainAddressCouponCode(address, rule, couponCode)',

      address: {},
      rule: {
        coupon_type: '2'
      },
      couponCode: 'coupon',
      expect: {
        address: {
          coupon_code: 'coupon'
        }
      },
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      RulesApplierService.maintainAddressCouponCode(data[i].address, data[i].rule, data[i].couponCode);

      expect(data[i].address).toEqual(data[i].expect.address);
    });
  }
  /* End Test */

});

describe('Test setAppliedRuleIds function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions

  });

  afterAll(() => {
    // Unmock functions

  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-RAS-14',
      title: 'setAppliedRuleIds(quote, address, item, appliedRuleIds)',

      quote: {
        applied_rule_ids: '1,2'
      },
      address: {
        applied_rule_ids: '1,2'
      },
      item: {},
      appliedRuleIds: [3, 4],
      expect: {
        quote: {
          applied_rule_ids: '1,2,3,4'
        },
        address: {
          applied_rule_ids: '1,2,3,4'
        },
        item: {
          applied_rule_ids: '3,4'
        }
      },
    },
    {
      testCaseId: 'DIS-RAS-15',
      title: 'setAppliedRuleIds(quote, address, item, appliedRuleIds)',

      quote: {
        applied_rule_ids: 'POS_CUSTOM_DISCOUNT'
      },
      address: {
        applied_rule_ids: 'POS_CUSTOM_DISCOUNT'
      },
      item: {},
      appliedRuleIds: ['POS_CUSTOM_DISCOUNT'],
      expect: {
        quote: {
          applied_rule_ids: 'POS_CUSTOM_DISCOUNT'
        },
        address: {
          applied_rule_ids: 'POS_CUSTOM_DISCOUNT'
        },
        item: {
          applied_rule_ids: 'POS_CUSTOM_DISCOUNT'
        }
      },
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      RulesApplierService.setAppliedRuleIds(data[i].quote, data[i].address, data[i].item, data[i].appliedRuleIds);

      expect(data[i].quote).toEqual(data[i].expect.quote);
      expect(data[i].address).toEqual(data[i].expect.address);
      expect(data[i].item).toEqual(data[i].expect.item);
    });
  }
  /* End Test */

});
