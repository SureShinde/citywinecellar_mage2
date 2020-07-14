import ToFixedService from "../ToFixedService";
import ValidatorService from "../../../../ValidatorService";
import CurrencyHelper from "../../../../../../helper/CurrencyHelper";

describe('Test calculate function', () => {
  let mocks = {};
  beforeAll(() => {
    // Mock functions
    mocks.getItemPrice = ValidatorService.getItemPrice;
    mocks.getItemBasePrice = ValidatorService.getItemBasePrice;
    mocks.getItemOriginalPrice = ValidatorService.getItemOriginalPrice;
    mocks.getItemBaseOriginalPrice = ValidatorService.getItemBaseOriginalPrice;

    ValidatorService.getItemPrice = jest.fn(item => item.price);
    ValidatorService.getItemBasePrice = ValidatorService.getItemPrice;
    ValidatorService.getItemOriginalPrice = ValidatorService.getItemPrice;
    ValidatorService.getItemBaseOriginalPrice = ValidatorService.getItemPrice;

    mocks.convert = CurrencyHelper.convert;
    CurrencyHelper.convert = jest.fn(x => x);
  });
  afterAll(() => {
    // Unmock functions
    ValidatorService.getItemPrice = mocks.getItemPrice;
    ValidatorService.getItemBasePrice = mocks.getItemBasePrice;
    ValidatorService.getItemOriginalPrice = mocks.getItemOriginalPrice;
    ValidatorService.getItemBaseOriginalPrice = mocks.getItemBaseOriginalPrice;

    CurrencyHelper.convert = mocks.convert;
  });

  let data = [
    {
      testCaseId: 'DIS-TFS-01',
      title: 'calculate(quote, address, rule, item, qty) with rule.discount_amount = 0',
      price: 10.2,
      discount_amount: 0,
      qty : 1.5,
      expect: { amount : 15.3 },
    },
    {
      testCaseId: 'DIS-TFS-02',
      title: 'calculate(quote, address, rule, item, qty) with rule.discount_amount = 9',
      price: 10.2,
      discount_amount: 9,
      qty : 1.5,
      expect: { amount : 1.8 },
    },
    {
      testCaseId: 'DIS-TFS-03',
      title: 'calculate(quote, address, rule, item, qty) with rule.discount_amount = 11.2',
      price: 10.2,
      discount_amount: 11.2,
      qty : 1.5,
      expect: { amount : -1.5 },
    },
    {
      testCaseId: 'DIS-TFS-04',
      title: 'calculate(quote, address, rule, item, qty) with qty = 0',
      price: 10.2,
      discount_amount: 9,
      qty : 0,
      expect: { amount : 0 },
    },
  ];

  /* Begin test calculate function*/
  data.forEach((testCase) => {
    it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
      let discountData = ToFixedService.calculate(
        {},
        {},
        {discount_amount: testCase.discount_amount},
        {price: testCase.price},
        testCase.qty
      );
      Object.keys(discountData).forEach((key) => {
        expect(discountData[key]).toBeCloseTo(testCase.expect.amount);
      });
    });
  });
  /* End Test calculate function*/
});
