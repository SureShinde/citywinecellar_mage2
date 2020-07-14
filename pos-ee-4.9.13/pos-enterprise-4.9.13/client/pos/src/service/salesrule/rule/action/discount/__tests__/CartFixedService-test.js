import CartFixedService from "../CartFixedService";
import ValidatorService from '../../../../ValidatorService';
import CurrencyHelper from '../../../../../../helper/CurrencyHelper';

describe('Test calculate function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.getRuleItemTotalsInfo = ValidatorService.getRuleItemTotalsInfo;

    mocks.getItemPrice = ValidatorService.getItemPrice;
    ValidatorService.getItemPrice = jest.fn((item) => item.price);

    mocks.getItemBasePrice = ValidatorService.getItemBasePrice;
    ValidatorService.getItemBasePrice = jest.fn((item) => item.base_price);

    mocks.getItemOriginalPrice = ValidatorService.getItemOriginalPrice;
    ValidatorService.getItemOriginalPrice = jest.fn((item) => item.original_price);

    mocks.getItemBaseOriginalPrice = ValidatorService.getItemBaseOriginalPrice;
    ValidatorService.getItemBaseOriginalPrice = jest.fn((item) => item.base_original_price);

    mocks.decrementRuleItemTotalsCount = ValidatorService.decrementRuleItemTotalsCount;
    ValidatorService.decrementRuleItemTotalsCount = jest.fn((ruleId) => ruleId);

    mocks.convert = CurrencyHelper.convert;
    CurrencyHelper.convert = jest.fn((value) => value);
  });

  afterAll(() => {
    // Unmock functions
    ValidatorService.getRuleItemTotalsInfo = mocks.getRuleItemTotalsInfo;
    ValidatorService.getItemPrice = mocks.getItemPrice;
    ValidatorService.getItemBasePrice = mocks.getItemBasePrice;
    ValidatorService.getItemOriginalPrice = mocks.getItemOriginalPrice;
    ValidatorService.getItemBaseOriginalPrice = mocks.getItemBaseOriginalPrice;
    ValidatorService.decrementRuleItemTotalsCount = mocks.decrementRuleItemTotalsCount;

    CurrencyHelper.convert = mocks.convert;

  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-CFS-01',
      title: 'calculate(quote, address, rule, item, qty) ' +
      'with ruleTotals.item_count = 1, ruleTotals.base_items_price = 20, ' +
      'rule.discount_amount = 20, address.cart_fixed_rules[1] = 10, item.price = 20',

      qty : 1,
      address: {
        cart_fixed_rules: {
          1: 10
        }
      },
      item: {
        price: 20,
        base_price: 20,
        original_price: 21,
        base_original_price: 21
      },
      quote: {},
      rule: {
        rule_id: 1,
        discount_amount: 20
      },
      ruleTotals: {
        items_count: 1,
        base_items_price: 20
      },
      expect: {
        amount : 10,
        base_amount: 10,
        original_amount: 10,
        base_original_amount: 21
      },
    },
    {
      testCaseId: 'DIS-CFS-02',
      title: 'calculate(quote, address, rule, item, qty) ' +
      'with ruleTotals.item_count = 1, ruleTotals.base_items_price = 20, ' +
      'rule.discount_amount = 10, address.cart_fixed_rules = [], item.price = 20',

      qty : 1,
      address: {
        cart_fixed_rules: []
      },
      item: {
        price: 20,
        base_price: 20,
        original_price: 21,
        base_original_price: 21
      },
      quote: {},
      rule: {
        rule_id: 1,
        discount_amount: 10
      },
      ruleTotals: {
        items_count: 1,
        base_items_price: 20
      },
      expect: {
        amount : 10,
        base_amount: 10,
        original_amount: 10,
        base_original_amount: 21
      },
    },
    {
      testCaseId: 'DIS-CFS-03',
      title: 'calculate(quote, address, rule, item, qty) ' +
      'with ruleTotals.item_count = 1, ruleTotals.base_items_price = 20, ' +
      'rule.discount_amount = 100, address.cart_fixed_rules= [], item.price = 20',

      qty : 1,
      address: {
        cart_fixed_rules: []
      },
      item: {
        price: 20,
        base_price: 20,
        original_price: 21,
        base_original_price: 21
      },
      quote: { },
      rule: {
        rule_id: 1,
        discount_amount: 100
      },
      ruleTotals: {
        items_count: 1,
        base_items_price: 20
      },
      expect: {
        amount : 20,
        base_amount: 20,
        original_amount: 21,
        base_original_amount: 21
      },
    },
    {
      testCaseId: 'DIS-CFS-04',
      title: 'calculate(quote, address, rule, item, qty) ' +
      'with ruleTotals.item_count = 2, ruleTotals.base_items_price = 0, ' +
      'rule.discount_amount = 10, address.cart_fixed_rules = [], item.price = 0',

      qty : 1,
      address: {
        cart_fixed_rules: []
      },
      item: {
        price: 0,
        base_price: 0,
        original_price: 0,
        base_original_price: 0
      },
      quote: { },
      rule: {
        rule_id: 1,
        discount_amount: 10
      },
      ruleTotals: {
        items_count: 2,
        base_items_price: 0
      },
      expect: {
        amount : 0,
        base_amount: 0,
        original_amount: 0,
        base_original_amount: 0
      },
    },
    {
      testCaseId: 'DIS-CFS-05',
      title: 'calculate(quote, address, rule, item, qty) ' +
      'with ruleTotals.item_count = 2, ruleTotals.base_items_price = 40, ' +
      'rule.discount_amount = 10, address.cart_fixed_rules = [], item.price = 20',

      qty : 1,
      address: {
        cart_fixed_rules: []
      },
      item: {
        price: 20,
        base_price: 20,
        original_price: 21,
        base_original_price: 21
      },
      quote: { },
      rule: {
        rule_id: 1,
        discount_amount: 10
      },
      ruleTotals: {
        items_count: 2,
        base_items_price: 40
      },
      expect: {
        amount : 5,
        base_amount: 5,
        original_amount: 5,
        base_original_amount: 21
      },
    },
    {
      testCaseId: 'DIS-CFS-06',
      title: 'calculate(quote, address, rule, item, qty) ' +
      'with ruleTotals.item_count = 2, ruleTotals.base_items_price = 40, ' +
      'rule.discount_amount = 100, address.cart_fixed_rules = [], item.price = 20',

      qty : 1,
      address: {
        cart_fixed_rules: []
      },
      item: {
        price: 20,
        base_price: 20,
        original_price: 21,
        base_original_price: 21
      },
      quote: { },
      rule: {
        rule_id: 1,
        discount_amount: 100
      },
      ruleTotals: {
        items_count: 2,
        base_items_price: 40
      },
      expect: {
        amount : 20,
        base_amount: 20,
        original_amount: 21,
        base_original_amount: 21
      },
    },
    {
      testCaseId: 'DIS-CFS-07',
      title: 'calculate(quote, address, rule, item, qty) ' +
      'with ruleTotals.item_count = 2, ruleTotals.base_items_price = 40.28, ' +
      'rule.discount_amount = 10, address.cart_fixed_rules = [], item.price = 20.28',

      qty : 1,
      address: {
        cart_fixed_rules: []
      },
      item: {
        price: 20.28,
        base_price: 20.28,
        original_price: 21.28,
        base_original_price: 21.28
      },
      quote: { },
      rule: {
        rule_id: 1,
        discount_amount: 10
      },
      ruleTotals: {
        items_count: 2,
        base_items_price: 40.28
      },
      expect: {
        amount : 5.03,
        base_amount: 5.03,
        original_amount: 5.03,
        base_original_amount: 21.28
      },
    },
    {
      testCaseId: 'DIS-CFS-08',
      title: 'calculate(quote, address, rule, item, qty) ' +
      'with ruleTotals.item_count = 2, ruleTotals.base_items_price = 40.28, ' +
      'rule.discount_amount = 0, address.cart_fixed_rules = [], item.price = 20.28',

      qty : 1,
      address: {
        cart_fixed_rules: []
      },
      item: {
        price: 20.28,
        base_price: 20.28,
        original_price: 21.28,
        base_original_price: 21.28
      },
      quote: { },
      rule: {
        rule_id: 1,
        discount_amount: 0
      },
      ruleTotals: {
        items_count: 2,
        base_items_price: 40.28
      },
      expect: {},
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      ValidatorService.getRuleItemTotalsInfo = jest.fn(() => data[i].ruleTotals);

      let result = CartFixedService.calculate(data[i].quote, data[i].address, data[i].rule, data[i].item, data[i].qty);

      expect(result).toEqual(data[i].expect);
    });
  }
  /* End Test */

});

describe('Test setCartFixedRuleUsedForAddress function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.cartFixedRuleUsedForAddress = CartFixedService.cartFixedRuleUsedForAddress;
  });

  afterAll(() => {
    // Unmock functions
    CartFixedService.cartFixedRuleUsedForAddress = mocks.cartFixedRuleUsedForAddress;
  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-CFS-9',
      title: 'setCartFixedRuleUsedForAddress(ruleId, itemId) with ruleId = 1, itemId =1',

      ruleId : 1,
      itemId: 1,
      expect: {
        1: 1
      },
    },
    {
      testCaseId: 'DIS-CFS-10',
      title: 'setCartFixedRuleUsedForAddress(ruleId, itemId) with ruleId = null, itemId =1',

      ruleId : null,
      itemId: 1,
      expect: {},
    },
    {
      testCaseId: 'DIS-CFS-11',
      title: 'setCartFixedRuleUsedForAddress(ruleId, itemId) with ruleId = undefined, itemId =1',

      ruleId : undefined,
      itemId: 1,
      expect: {},
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      CartFixedService.cartFixedRuleUsedForAddress = {};

      CartFixedService.setCartFixedRuleUsedForAddress(data[i].ruleId, data[i].itemId);

      expect(CartFixedService.cartFixedRuleUsedForAddress).toEqual(data[i].expect);
    });
  }
  /* End Test */

});

describe('Test getCartFixedRuleUsedForAddress function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.cartFixedRuleUsedForAddress = CartFixedService.cartFixedRuleUsedForAddress;
  });

  afterAll(() => {
    // Unmock functions
    CartFixedService.cartFixedRuleUsedForAddress = mocks.cartFixedRuleUsedForAddress;
  });

  //Run test
  let data = [
    {
      testCaseId: 'DIS-CFS-12',
      title: 'getCartFixedRuleUsedForAddress(ruleId) with existed ruleId',

      cartFixedRuleUsedForAddress: {
        1: 1
      },
      ruleId : 1,
      expect: 1,
    },
    {
      testCaseId: 'DIS-CFS-13',
      title: 'getCartFixedRuleUsedForAddress(ruleId) with not existed ruleId',

      cartFixedRuleUsedForAddress: {
        1: 1
      },
      ruleId : 2,
      expect: null,
    },
    {
      testCaseId: 'DIS-CFS-14',
      title: 'getCartFixedRuleUsedForAddress(ruleId) with  ruleId = null',

      cartFixedRuleUsedForAddress: {
        1: 1
      },
      ruleId : null,
      expect: null,
    },
  ];

  /* Begin test */
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      CartFixedService.cartFixedRuleUsedForAddress = data[i].cartFixedRuleUsedForAddress;

      let result = CartFixedService.getCartFixedRuleUsedForAddress(data[i].ruleId);

      expect(result).toEqual(data[i].expect);
    });
  }
  /* End Test */

});
