import { when } from 'jest-when';
import ToPercentService from "../ToPercentService";

describe('Test calculate function', () => {
  let data = [
    {
      testCaseId: 'DIS-TPS-01',
      title: 'calculate(quote, address, rule, item, qty) with rule.discount_amount = 0',

      qty : 2,
      address: {},
      item: {
        baseItemPrice: 2.48,
        itemPrice: 2.48,
        base_discount_amount: 1.24,
        discount_amount: 1.24,
        itemOriginalPrice: 2.48,
        baseItemOriginalPrice: 2.48
      },
      quote: {},
      rule: {
        discount_amount: 0,
        discount_qty: 2
      },
      rulePercent: 100,
      expect: {
        amount : 3.72,
        base_amount: 3.72,
        original_amount: 3.72,
        base_original_amount: 3.72
      },
    },
    {
      testCaseId: 'DIS-TPS-02',
      title: 'calculate(quote, address, rule, item, qty) with rule.discount_amount = 100',

      qty : 2,
      address: {},
      item: {
        baseItemPrice: 2.48,
        itemPrice: 2.48,
        base_discount_amount: 1.24,
        discount_amount: 1.24,
        itemOriginalPrice: 2.48,
        baseItemOriginalPrice: 2.48
      },
      quote: {},
      rule: {
        discount_amount: 100,
        discount_qty: 2
      },
      rulePercent: 0,
      expect: {
        amount : 0,
        base_amount: 0,
        original_amount: 0,
        base_original_amount: 0
      },
    },
    {
      testCaseId: 'DIS-TPS-03',
      title: 'calculate(quote, address, rule, item, qty) with rule.discount_amount = 101',

      qty : 2,
      address: {},
      item: {
        baseItemPrice: 2.48,
        itemPrice: 2.48,
        base_discount_amount: 1.24,
        discount_amount: 1.24,
        itemOriginalPrice: 2.48,
        baseItemOriginalPrice: 2.48
      },
      quote: {},
      rule: {
        discount_amount: 101,
        discount_qty: 2
      },
      rulePercent: 0,
      expect: {
        amount : 0,
        base_amount: 0,
        original_amount: 0,
        base_original_amount: 0
      },
    },
  ];

  /* Begin test calculate function*/
  for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
      ToPercentService._calculate = jest.fn();
      when(ToPercentService._calculate)
        .calledWith(data[i].quote, data[i].rule, data[i].item, data[i].qty, data[i].rulePercent)
        .mockReturnValue(data[i].expect);
      expect(ToPercentService.calculate(data[i].quote, data[i].address, data[i].rule, data[i].item, data[i].qty))
        .toEqual(data[i].expect);
    });
  }
  /* End Test calculate function*/

});

