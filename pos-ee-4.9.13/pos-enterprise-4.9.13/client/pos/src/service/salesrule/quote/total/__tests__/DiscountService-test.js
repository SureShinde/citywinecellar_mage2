import ValidatorService from "../../../ValidatorService";
import DiscountService from "../DiscountService";
import QuoteItemService from "../../../../checkout/quote/ItemService";
import AddressService from "../../../../checkout/quote/AddressService";

describe('DiscountService.distributeDiscount', () => {
  it('[DIS-DCS-01] Distribute discount', () => {
    let getChildrenItems = QuoteItemService.getChildrenItems;

    let item = {
      base_row_total: 3,
      discount_amount: 0.01,
      base_discount_amount: 0.05,
      children: [
        {base_row_total: 1},
        {base_row_total: 2},
      ]
    };
    QuoteItemService.getChildrenItems = jest.fn(() => item.children);

    DiscountService.distributeDiscount({}, item);
    expect(item.children).toEqual([
      {
        base_row_total: 1,
        discount_amount: 0.00,
        base_discount_amount: 0.02,
      },
      {
        base_row_total: 2,
        discount_amount: 0.01,
        base_discount_amount: 0.03,
      },
    ]);

    // Unmock function
    QuoteItemService.getChildrenItems = getChildrenItems;
  });
});

describe('DiscountService.collect', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.getChildrenItems = QuoteItemService.getChildrenItems;
    QuoteItemService.getChildrenItems = jest.fn();

    mocks.isChildrenCalculated = QuoteItemService.isChildrenCalculated;
    QuoteItemService.isChildrenCalculated = jest.fn(item => item.is_children_calculated);

    mocks.sortSalesRuleByPriority = ValidatorService.sortSalesRuleByPriority;
    ValidatorService.sortSalesRuleByPriority = jest.fn();

    mocks.initTotals = ValidatorService.initTotals;
    ValidatorService.initTotals = jest.fn();

    mocks.sortItemsByPriority = ValidatorService.sortItemsByPriority;
    ValidatorService.sortItemsByPriority = jest.fn();

    mocks.process = ValidatorService.process;
    ValidatorService.process = jest.fn();

    mocks.processShippingAmount = ValidatorService.processShippingAmount;
    ValidatorService.processShippingAmount = jest.fn((quote, address) => {
      address.base_shipping_discount_amount = address.base_shipping_discount;
      address.shipping_discount_amount = address.shipping_discount;
    });

    mocks.isVirtual = DiscountService.isVirtual;
    DiscountService.isVirtual = jest.fn(quote => quote.is_virtual);

    mocks.isBillingAddress = AddressService.isBillingAddress;
    AddressService.isBillingAddress = jest.fn(address => address.type === 'billing');

    mocks.isShippingAddress = AddressService.isShippingAddress;
    AddressService.isShippingAddress = jest.fn(address => address.type === 'shipping');
  });

  afterAll(() => {
    // Unmock functions
    QuoteItemService.getChildrenItems = mocks.getChildrenItems;
    QuoteItemService.isChildrenCalculated = mocks.isChildrenCalculated;

    ValidatorService.sortSalesRuleByPriority = mocks.sortSalesRuleByPriority;
    ValidatorService.initTotals = mocks.initTotals;
    ValidatorService.sortItemsByPriority = mocks.sortItemsByPriority;
    ValidatorService.process = mocks.process;
    ValidatorService.processShippingAmount = mocks.processShippingAmount;

    DiscountService.isVirtual = mocks.isVirtual;

    AddressService.isBillingAddress = mocks.isBillingAddress;
    AddressService.isShippingAddress = mocks.isShippingAddress;
  });

  let data = [
    {
      testCaseId: 'DIS-DCS-02',
      title: 'collect(quote, address, total) with quote.is_virtual = false and address is billing',
      quote: {is_virtual: false},
      address: {
        type: 'billing',
        shipping_amount: 0,
      },
      expect: {
        discount_amount: 0,
        base_discount_amount: 0,
      },
    },
    {
      testCaseId: 'DIS-DCS-03',
      title: 'collect(quote, address, total) with quote.is_virtual = true and address is billing',
      quote: {is_virtual: true},
      address: {
        type: 'billing',
        shipping_amount: 0,
      },
      expect: {
        discount_amount: -0.08,
        base_discount_amount: -0.08,
      },
    },
    {
      testCaseId: 'DIS-DCS-04',
      title: 'collect(quote, address, total) with quote.is_virtual = false and address is shipping',
      quote: {is_virtual: false},
      address: {
        type: 'shipping',
        shipping_amount: 1.0,
        shipping_discount: 0.03,
        base_shipping_discount: 0.03,
      },
      expect: {
        discount_amount: -0.11,
        base_discount_amount: -0.11,
      },
    },
    {
      testCaseId: 'DIS-DCS-05',
      title: 'collect(quote, address, total) with quote.is_virtual = true and address is shipping',
      quote: {is_virtual: true},
      address: {
        type: 'shipping',
        shipping_amount: 1.0,
        shipping_discount: 0,
        base_shipping_discount: 0,
      },
      expect: {
        discount_amount: 0,
        base_discount_amount: 0,
      },
    },
  ];

  data.forEach(testCase => {
    it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
      let total = {};
      let items = [
        {
          item_id: 1,
          discount_amount: 0.03,
          base_discount_amount: 0.03,
          base_row_total: 3,
          has_children: true,
          is_children_calculated: true,
        },
        {
          item_id: 2,
          parent_item_id: 1,
          base_row_total: 1,
        },
        {
          item_id: 3,
          parent_item_id: 1,
          base_row_total: 2,
        },
        {
          item_id: 4,
          discount_amount: 0.05,
          base_discount_amount: 0.05,
        },
      ];

      QuoteItemService.getChildrenItems.mockReturnValue(items.slice(1, 3));
      ValidatorService.sortItemsByPriority.mockReturnValue(items);

      DiscountService.collect(
        {is_virtual: testCase.quote.is_virtual, items: items},
        testCase.address,
        total
      );
      expect(total.discount_amount).toBeCloseTo(testCase.expect.discount_amount);
      expect(total.base_discount_amount).toBeCloseTo(testCase.expect.base_discount_amount);
    });
  });
});
