import { when } from 'jest-when';
import SalesRuleBuyXGetYService from '../BuyXGetYService';
import ValidatorService from "../../../../ValidatorService";

describe('Test calculate function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.getItemPrice = ValidatorService.getItemPrice;
        ValidatorService.getItemPrice = jest.fn();
        mocksBackup.getItemBasePrice = ValidatorService.getItemBasePrice;
        ValidatorService.getItemBasePrice = jest.fn();
        mocksBackup.getItemOriginalPrice = ValidatorService.getItemOriginalPrice;
        ValidatorService.getItemOriginalPrice = jest.fn();
        mocksBackup.getItemBaseOriginalPrice = ValidatorService.getItemBaseOriginalPrice;
        ValidatorService.getItemBaseOriginalPrice = jest.fn();
    });
    afterAll(() => {
        ValidatorService.getItemPrice = mocksBackup.getItemPrice;
        ValidatorService.getItemBasePrice = mocksBackup.getItemBasePrice;
        ValidatorService.getItemOriginalPrice = mocksBackup.getItemOriginalPrice;
        ValidatorService.getItemBaseOriginalPrice = mocksBackup.getItemBaseOriginalPrice;
    });
   let data = [
       {
           testCaseId: 'DIS-XGY-01',
           title: 'Function calculate - when qty is 0',
           input: {
               quote: {},
               address: {},
               rule: {
                   discount_amount: 1,
                   discount_step: 1
               },
               item: {},
               qty : 0
           },
           mockInput: {
               item: {
                   item_price: 1,
                   base_item_price: 1,
                   item_original_price: 1,
                   base_item_original_price: 1
               },
           },
           expect: {
               discount_data: {
                   amount: 0,
                   base_amount: 0,
                   original_amount: 0,
                   base_original_amount: 0
               }
           }
       },
       {
           testCaseId: 'DIS-XGY-02',
           title: 'Function calculate - when discount amount is 0',
           input: {
               quote: {},
               address: {},
               rule: {
                   discount_amount: 0,
                   discount_step: 1
               },
               item: {},
               qty : 1
           },
           mockInput: {
               item: {
                   item_price: 1,
                   base_item_price: 1,
                   item_original_price: 1,
                   base_item_original_price: 1
               },
           },
           expect: {
               discount_data: {
                   amount: 0,
                   base_amount: 0,
                   original_amount: 0,
                   base_original_amount: 0
               }
           }
       },
       {
           testCaseId: 'DIS-XGY-03',
           title: 'Function calculate - when discount step is 0',
           input: {
               quote: {},
               address: {},
               rule: {
                   discount_amount: 1,
                   discount_step: 0
               },
               item: {},
               qty : 1
           },
           mockInput: {
               item: {
                   item_price: 1,
                   base_item_price: 1,
                   item_original_price: 1,
                   base_item_original_price: 1
               },
           },
           expect: {
               discount_data: {
               }
           }
       },
       {
           testCaseId: 'DIS-XGY-04',
           title: 'Function calculate - when discount step less than discount amount',
           input: {
               quote: {},
               address: {},
               rule: {
                   discount_amount: 2,
                   discount_step: 1
               },
               item: {},
               qty : 1
           },
           mockInput: {
               item: {
                   item_price: 1,
                   base_item_price: 1,
                   item_original_price: 1,
                   base_item_original_price: 1
               },
           },
           expect: {
               discount_data: {
               }
           }
       },
       {
           testCaseId: 'DIS-XGY-05',
           title: 'Function calculate - when qty has just enough to discount',
           input: {
               quote: {},
               address: {},
               rule: {
                   discount_amount: 2.3,
                   discount_step: 10
               },
               item: {},
               qty : 36.9
           },
           mockInput: {
               item: {
                   item_price: 1,
                   base_item_price: 1,
                   item_original_price: 1,
                   base_item_original_price: 1
               },
           },
           expect: {
               discount_data: {
                   amount: 6.9,
                   base_amount: 6.9,
                   original_amount: 6.9,
                   base_original_amount: 6.9
               }
           }
       },
       {
           testCaseId: 'DIS-XGY-06',
           title: 'Function calculate - when qty is not enough to discount',
           input: {
               quote: {},
               address: {},
               rule: {
                   discount_amount: 1,
                   discount_step: 1
               },
               item: {},
               qty : 1
           },
           mockInput: {
               item: {
                   item_price: 1,
                   base_item_price: 1,
                   item_original_price: 1,
                   base_item_original_price: 1
               },
           },
           expect: {
               discount_data: {
                   amount: 0,
                   base_amount: 0,
                   original_amount: 0,
                   base_original_amount: 0
               }
           }
       },
       {
           testCaseId: 'DIS-XGY-07',
           title: 'Function calculate - when qty is enough to partial discount',
           input: {
               quote: {},
               address: {},
               rule: {
                   discount_amount: 2.5,
                   discount_step: 3
               },
               item: {},
               qty : 5
           },
           mockInput: {
               item: {
                   item_price: 1,
                   base_item_price: 1,
                   item_original_price: 1,
                   base_item_original_price: 1
               },
           },
           expect: {
               discount_data: {
                   amount: 2,
                   base_amount: 2,
                   original_amount: 2,
                   base_original_amount: 2
               }
           }
       },
       {
           testCaseId: 'DIS-XGY-08',
           title: 'Function calculate - when qty is greater than qty for discount, ' +
               'but the balance not enough to discount more',
           input: {
               quote: {},
               address: {},
               rule: {
                   discount_amount: 1,
                   discount_step: 2
               },
               item: {},
               qty : 7
           },
           mockInput: {
               item: {
                   item_price: 1,
                   base_item_price: 1,
                   item_original_price: 1,
                   base_item_original_price: 1
               },
           },
           expect: {
               discount_data: {
                   amount: 2,
                   base_amount: 2,
                   original_amount: 2,
                   base_original_amount: 2
               }
           }
       }
   ]

    /* Begin test calculate function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            when(ValidatorService.getItemPrice)
                .calledWith(data[i].input.item)
                .mockReturnValue(data[i].mockInput.item.item_price);
            when(ValidatorService.getItemBasePrice)
                .calledWith(data[i].input.item)
                .mockReturnValue(data[i].mockInput.item.base_item_price);
            when(ValidatorService.getItemOriginalPrice)
                .calledWith(data[i].input.item, data[i].input.quote)
                .mockReturnValue(data[i].mockInput.item.item_original_price);
            when(ValidatorService.getItemBaseOriginalPrice)
                .calledWith(data[i].input.item, data[i].input.quote)
                .mockReturnValue(data[i].mockInput.item.base_item_original_price);

            let result = SalesRuleBuyXGetYService.calculate({}, {}, data[i].input.rule, {}, data[i].input.qty);
            expect(result).toEqual(data[i].expect.discount_data);
        });
    }
    /* End test calculate function*/
});