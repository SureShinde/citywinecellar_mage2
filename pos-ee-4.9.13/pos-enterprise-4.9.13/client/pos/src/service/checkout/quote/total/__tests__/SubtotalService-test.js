import SubtotalService from '../SubtotalService';
import AddressService from "../../AddressService";
import QuoteItemService from "../../ItemService";
import PriceService from "../../../../catalog/product/PriceService";

describe('QuoteTotalSubtotalService-collect', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.isVirtual = SubtotalService.isVirtual;
    SubtotalService.isVirtual = jest.fn(quote => quote.is_virtual);

    mocks._initItem = SubtotalService._initItem;
    SubtotalService._initItem = jest.fn((quote, address, item) => !!item.product);

    mocks.isBillingAddress = AddressService.isBillingAddress;
    AddressService.isBillingAddress = jest.fn(address => address.type === 'billing');

    mocks.isShippingAddress = AddressService.isShippingAddress;
    AddressService.isShippingAddress = jest.fn(address => address.type === 'shipping');
  });
  afterAll(() => {
    // Unmock functions
    SubtotalService.isVirtual = mocks.isVirtual;
    SubtotalService._initItem = mocks._initItem;

    AddressService.isBillingAddress = mocks.isBillingAddress;
    AddressService.isShippingAddress = mocks.isShippingAddress;
  });
  // Run test
  let data = [
    {
      testCaseId: 'STT-CLT-01',
      title: 'Collect total for virtual quote and billing address',
      data: {
        quote_is_virtual: true,
        address: 'billing',
      },
      expect: {
        virtual_amount: 5,
        base_virtual_amount: 10,
        items: [2, 3],
      }
    },
    {
      testCaseId: 'STT-CLT-02',
      title: 'Collect total for physical quote and billing address',
      data: {
        quote_is_virtual: false,
        address: 'billing',
      },
      expect: {
        virtual_amount: 0,
        base_virtual_amount: 0,
        items: [1, 2, 3],
      }
    },
    {
      testCaseId: 'STT-CLT-03',
      title: 'Collect total for virtual quote and shipping address',
      data: {
        quote_is_virtual: true,
        address: 'shipping',
      },
      expect: {
        virtual_amount: 0,
        base_virtual_amount: 0,
        items: [1, 2, 3],
      }
    },
    {
      testCaseId: 'STT-CLT-04',
      title: 'Collect total for physical quote and shipping address',
      data: {
        quote_is_virtual: false,
        address: 'shipping',
      },
      expect: {
        virtual_amount: 3.5,
        base_virtual_amount: 7.0,
        items: [2, 3],
      }
    },
  ];
  data.forEach((testCase) => {
    it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
      let quote = {
        is_virtual: testCase.data.quote_is_virtual,
        items: [
          {
            item_id: 1,
            product: {is_virtual: true},
            qty: 0,
            row_total: 1,
            base_row_total: 2,
          },
          {
            item_id: 2,
            product: {is_virtual: testCase.data.quote_is_virtual},
            qty: 2,
            row_total: 1.5,
            base_row_total: 3.0,
          },
          {
            item_id: 3,
            product: {is_virtual: true},
            qty: 1,
            row_total: 3.5,
            base_row_total: 7.0,
          },
        ],
      };
      let address = {
        type: testCase.data.address
      };
      let total = {};

      SubtotalService.collect(quote, address, total);

      expect(quote.items.length).toEqual(testCase.expect.items.length);
      for (let i = 0; i < quote.items.length; i++) {
        expect(quote.items[i].item_id).toEqual(testCase.expect.items[i]);
      }
      expect(total.virtual_amount).toEqual(testCase.expect.virtual_amount);
      expect(total.base_virtual_amount).toEqual(testCase.expect.base_virtual_amount);
    });
  });
});

describe('QuoteTotalSubtotalService-_initItem', () => {
  let mocks = {};
  let items = [
    {
      item_id: 0,
      qty: 1,
    },
    {
      item_id: 1,
      product: {price: 1.0},
      product_type: 'simple',
      qty: 1,
    },
    {
      item_id: 2,
      product: {price: 2.0},
      product_type: 'configurable',
      qty: 1,
    },
    {
      item_id: 3,
      product: {price: 3.0},
      product_type: 'configurable',
      qty: 1,
    },
    {
      item_id: 4,
      product: {price: 4.0},
      product_type: 'simple',
      parent_item_id: 3,
      qty: 1,
    },
    {
      item_id: 5,
      product: {price: 13.0},
      product_type: 'bundle',
      qty: 1,
    },
    {
      item_id: 6,
      product: {price: 6.0},
      product_type: 'simple',
      parent_item_id: 5,
      qty: 1,
      is_children_calculated: true,
    },
    {
      item_id: 7,
      product: {price: 7.0},
      product_type: 'simple',
      parent_item_id: 5,
      qty: 1,
      is_children_calculated: true,
    },
    {
      item_id: 8,
      product: {price: 0},
      product_type: 'simple',
      qty: 1,
    },
  ];
  beforeAll(() => {
    // Mock functions
    mocks.isChildrenCalculated = QuoteItemService.isChildrenCalculated;
    QuoteItemService.isChildrenCalculated = jest.fn((item) => item.is_children_calculated);

    mocks.getParentItem = QuoteItemService.getParentItem;
    QuoteItemService.getParentItem = jest.fn((quote, item) => items[item.parent_item_id]);

    mocks.getOriginalPrice = QuoteItemService.getOriginalPrice;
    QuoteItemService.getOriginalPrice = jest.fn(item => item.product.price);

    mocks.calcRowTotal = QuoteItemService.calcRowTotal;
    QuoteItemService.calcRowTotal = jest.fn((item, quote) => {
      item.row_total = item.price ? item.price : item.product.price;
      item.base_row_total = item.row_total;
    });

    // getPriceService
    mocks.getPriceService = PriceService.getPriceService;
    PriceService.getPriceService = jest.fn(() => {
      return {
        getChildFinalPrice: (pProduct, pQty, product) => product.price,
        getFinalPrice: (qty, product) => product.price,
      }
    });
  });
  afterAll(() => {
    // Unmock functions
    QuoteItemService.isChildrenCalculated = mocks.isChildrenCalculated;
    QuoteItemService.getParentItem = mocks.getParentItem;
    QuoteItemService.getOriginalPrice = mocks.getOriginalPrice;
    QuoteItemService.calcRowTotal = mocks.calcRowTotal;

    PriceService.getPriceService = mocks.getPriceService;
  });
  it('[STT-INI-01...STT-INI-09] Test _initItem in sequence', () => {
    let quote = {items: items};
    let address = {customer_id: 10};
    expect(SubtotalService._initItem(quote, address, items[0])).toEqual(false);
    let expected = [
      false,
      {total_qty: 1, subtotal: 1.0, base_subtotal: 1.0},
      {total_qty: 2, subtotal: 3.0, base_subtotal: 3.0},
      {total_qty: 3, subtotal: 6.0, base_subtotal: 6.0},
      {total_qty: 3, subtotal: 6.0, base_subtotal: 6.0},
      {total_qty: 4, subtotal: 19.0, base_subtotal: 19.0},
      {total_qty: 4, subtotal: 19.0, base_subtotal: 19.0},
      {total_qty: 4, subtotal: 19.0, base_subtotal: 19.0},
      {total_qty: 5, subtotal: 19.0, base_subtotal: 19.0},
    ];
    let total = SubtotalService._getTotal();
    for (let i = 1; i < items.length; i++) {
      let item = items[i];
      expect(SubtotalService._initItem(quote, address, item)).toEqual(true);
      expect(item.product.customer_group_id).toEqual(address.customer_id);
      expect(address.total_qty).toEqual(expected[i].total_qty);
      expect(total.subtotal).toEqual(expected[i].subtotal);
      expect(total.base_subtotal).toEqual(expected[i].base_subtotal);
    }
  });
});
