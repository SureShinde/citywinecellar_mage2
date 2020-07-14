import { when } from 'jest-when';
import ValidatorService from "../../../../ValidatorService";
import ByPercentService from "../ByPercentService";

describe('Test calculate function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup._calculate = ByPercentService._calculate;
        ByPercentService._calculate = jest.fn();
    });
    afterAll(() => {
        ByPercentService._calculate = mocksBackup._calculate;
    });
    let data = [
        {
            testCaseId: 'DIS-BPC-01',
            title: 'calculate function - Discount Amount > 100',

            input: {
                qty : 2,
                address: {},
                item: {},
                quote: {},
                rule: {
                    discount_amount: 110,
                },
            },

            mockInput: {
                rulePercent: 100
            },

            expect: {
                amount : 3.72,
                base_amount: 3.72,
                original_amount: 3.72,
                base_original_amount: 3.72
            }
        },

        {
            testCaseId: 'DIS-BPC-02',
            title: 'calculate function - 0 < Discount Amount < 100',
            input: {
                qty : 2,
                address: {},
                item: {},
                quote: {},
                rule: {
                    discount_amount: 50
                },
            },

            mockInput: {
                rulePercent: 50,
            },
            expect: {
                amount : 1.86,
                base_amount: 1.86,
                original_amount: 1.86,
                base_original_amount: 1.86
            }
        },

        {
            testCaseId: 'DIS-BPC-03',
            title: 'calculate function - Discount Amount = 100',
            input: {
                qty : 2,
                address: {},
                item: {},
                quote: {},
                rule: {
                    discount_amount: 100,
                },
            },
            mockInput: {
                rulePercent: 100,
            },
            expect: {
                amount : 3.72,
                base_amount: 3.72,
                original_amount: 3.72,
                base_original_amount: 3.72
            }
        },
    ];

    /* Begin test calculate function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            when(ByPercentService._calculate).calledWith(data[i].input.quote,
                data[i].input.rule, data[i].input.item,
                data[i].input.qty, data[i].mockInput.rulePercent)
                .mockReturnValue(data[i].expect);
            expect(ByPercentService.calculate(data[i].input.quote,
                data[i].input.address, data[i].input.rule,
                data[i].input.item, data[i].input.qty))
                .toEqual(data[i].expect);
        });
    }
    /* End Test calculate function*/

});

describe('Test fix quantity function', () => {
    let data = [
        {
            testCaseId: 'DIS-BPS-01',
            title: 'Fix quantity - step = 0',
            input: {
                qty : 2,
                rule: {
                    discount_step: 0,
                },
            },
            expect: 2
        },

        {
            testCaseId: 'DIS-BPS-02',
            title: 'Fix quantity - step > 0 and 3/2',
            input: {
                qty : 3,
                rule: {
                    discount_step: 2,
                },
            },
            expect: 2
        },

        {
            testCaseId: 'DIS-BPS-03',
            title: 'Fix quantity - step > 0 and 2/2',
            input: {
                qty : 2,
                rule: {
                    discount_step: 2,
                },
            },
            expect: 2
        }
    ];

    /* Begin test fix quantity function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            expect(ByPercentService.fixQuantity(data[i].input.qty, data[i].input.rule))
                .toEqual(data[i].expect);
        });
    }
    /* End Test fix quantity function*/
});

describe('Test _calculate function', () => {
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
            testCaseId: 'DIS-BPS-01',
            title: '_calculate function - rule.discount_qty  > 0',
            input: {
                quote: {

                },
                rule: {
                    discount_qty: 2
                },
                item: {
                    base_discount_amount: 1.24,
                    discount_amount: 1.24,
                    discount_percent: 50
                },
                qty : 2,
                rulePercent : 50
            },
            mockInput: {
                item: {
                    baseItemPrice: 2.48,
                    itemPrice: 2.48,
                    itemOriginalPrice: 2.48,
                    baseItemOriginalPrice: 2.48,
                },
            },
            expect: {
                discount_data: {
                    amount : 1.86,
                    base_amount: 1.86,
                    original_amount: 1.86,
                    base_original_amount: 1.86
                },
                item: {
                    discount_percent: 50
                }
            }
        },

        {
            testCaseId: 'DIS-BPS-02',
            title: '_calculate function - rule.discount_qty  = 0',
            input: {
                rulePercent : 50,
                qty : 2,
                item: {
                    base_discount_amount: 1.24,
                    discount_amount: 1.24,
                    discount_percent: 50
                },
                quote: {

                },
                rule: {
                    discount_qty: 0
                },
            },
            mockInput: {
                item: {
                    baseItemPrice: 2.48,
                    itemPrice: 2.48,
                    itemOriginalPrice: 2.48,
                    baseItemOriginalPrice: 2.48,
                },
            },
            expect: {
                discount_data: {
                    amount : 1.86,
                    base_amount: 1.86,
                    original_amount: 1.86,
                    base_original_amount: 1.86
                },
                item: {
                    discount_percent: 100
                }
            }
        },

        {
            testCaseId: 'DIS-BPS-03',
            title: '_calculate function - rule.discount_qty > qty',
            input: {
                rulePercent : 50,
                qty : 2,
                item: {
                    base_discount_amount: 1.24,
                    discount_amount: 1.24,
                    discount_percent: 50
                },
                quote: {

                },
                rule: {
                    discount_qty: 3
                },
            },
            mockInput: {
                item: {
                    baseItemPrice: 2.48,
                    itemPrice: 2.48,
                    itemOriginalPrice: 2.48,
                    baseItemOriginalPrice: 2.48,
                },
            },
            expect: {
                discount_data: {
                    amount : 1.86,
                    base_amount: 1.86,
                    original_amount: 1.86,
                    base_original_amount: 1.86
                },
                item: {
                    discount_percent: 100
                }
            }
        },

        {
            testCaseId: 'DIS-BPS-04',
            title: '_calculate function - rule.discount_qty < qty',
            input: {
                rulePercent : 50,
                qty : 2,
                item: {
                    base_discount_amount: 1.24,
                    discount_amount: 1.24,
                    discount_percent: 50
                },
                quote: {

                },
                rule: {
                    discount_qty: 1
                },
            },
            mockInput: {
                item: {
                    baseItemPrice: 2.48,
                    itemPrice: 2.48,
                    itemOriginalPrice: 2.48,
                    baseItemOriginalPrice: 2.48,
                },
            },
            expect: {
                discount_data: {
                    amount : 1.86,
                    base_amount: 1.86,
                    original_amount: 1.86,
                    base_original_amount: 1.86
                },
                item: {
                    discount_percent: 50
                }
            }
        },

        {
            testCaseId: 'DIS-BPS-05',
            title: '_calculate function - rule.discount_qty  > 0 and calculate 2.48*2-1.24 (error by browser)',
            input: {
                quote: {

                },
                rule: {
                    discount_qty: 2
                },
                item: {
                    base_discount_amount: 1.24,
                    discount_amount: 1.24,
                    discount_percent: 50
                },
                qty : 2,
                rulePercent : 50
            },
            mockInput: {
                item: {
                    baseItemPrice: 2.48,
                    itemPrice: 2.48,
                    itemOriginalPrice: 2.48,
                    baseItemOriginalPrice: 2.48,
                },
            },
            expect: {
                discount_data: {
                    amount : 1.86,
                    base_amount: 1.86,
                    original_amount: 1.86,
                    base_original_amount: 1.86
                },
                item: {
                    discount_percent: 50
                }
            }
        },

        {
            testCaseId: 'DIS-BPS-06',
            title: '_calculate function - rule.discount_qty  > 0 and calculate 2.48*2-1.27 (not error by browser)',
            input: {
                quote: {

                },
                rule: {
                    discount_qty: 2
                },
                item: {
                    base_discount_amount: 1.27,
                    discount_amount: 1.27,
                    discount_percent: 50
                },
                qty : 2,
                rulePercent : 50
            },
            mockInput: {
                item: {
                    baseItemPrice: 2.48,
                    itemPrice: 2.48,
                    itemOriginalPrice: 2.48,
                    baseItemOriginalPrice: 2.48,
                },
            },
            expect: {
                discount_data: {
                    amount : 1.845,
                    base_amount: 1.845,
                    original_amount: 1.845,
                    base_original_amount: 1.845
                },
                item: {
                    discount_percent: 50
                }
            }
        },

        {
            testCaseId: 'DIS-BPS-07',
            title: '_calculate function - rule.discount_qty  > 0 and calculate item.discount_amount > qty*item.price',
            input: {
                quote: {

                },
                rule: {
                    discount_qty: 2
                },
                item: {
                    base_discount_amount: 10,
                    discount_amount: 10,
                    discount_percent: 50
                },
                qty : 2,
                rulePercent : 50
            },
            mockInput: {
                item: {
                    baseItemPrice: 2.48,
                    itemPrice: 2.48,
                    itemOriginalPrice: 2.48,
                    baseItemOriginalPrice: 2.48,
                },
            },
            expect: {
                discount_data: {
                    amount : 0,
                    base_amount: 0,
                    original_amount: 0,
                    base_original_amount: 0
                },
                item: {
                    discount_percent: 50
                }
            }
        },

        {
            testCaseId: 'DIS-BPS-08',
            title: '_calculate function - rule.discount_qty  > 0 and calculate item.discount_amount undefined',
            input: {
                quote: {

                },
                rule: {
                    discount_qty: 2
                },
                item: {
                    discount_percent: 50
                },
                qty : 2,
                rulePercent : 50
            },
            mockInput: {
                item: {
                    baseItemPrice: 2.48,
                    itemPrice: 2.48,
                    itemOriginalPrice: 2.48,
                    baseItemOriginalPrice: 2.48,
                },
            },
            expect: {
                discount_data: {
                    amount : 2.48,
                    base_amount: 2.48,
                    original_amount: 2.48,
                    base_original_amount: 2.48
                },
                item: {
                    discount_percent: 50
                }
            }
        },

        {
            testCaseId: 'DIS-BPS-09',
            title: '_calculate function - rule.discount_qty  = 0 and item.discount_percent undefined',
            input: {
                quote: {

                },
                rule: {
                    discount_qty: 0
                },
                item: {

                },
                qty : 2,
                rulePercent : 50
            },
            mockInput: {
                item: {
                    baseItemPrice: 2.48,
                    itemPrice: 2.48,
                    itemOriginalPrice: 2.48,
                    baseItemOriginalPrice: 2.48,
                },
            },
            expect: {
                discount_data: {
                    amount : 2.48,
                    base_amount: 2.48,
                    original_amount: 2.48,
                    base_original_amount: 2.48
                },
                item: {
                    discount_percent: 50
                }
            }
        },

    ];

    /* Begin test _calculate function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            when(ValidatorService.getItemPrice).calledWith(data[i].input.item)
                .mockReturnValue(data[i].mockInput.item.itemPrice);
            when(ValidatorService.getItemBasePrice).calledWith(data[i].input.item)
                .mockReturnValue(data[i].mockInput.item.baseItemPrice);
            when(ValidatorService.getItemOriginalPrice).calledWith(data[i].input.item, data[i].input.quote)
                .mockReturnValue(data[i].mockInput.item.itemOriginalPrice);
            when(ValidatorService.getItemBaseOriginalPrice).calledWith(data[i].input.item, data[i].input.quote)
                .mockReturnValue(data[i].mockInput.item.baseItemOriginalPrice);

            let results = ByPercentService._calculate(data[i].input.quote, data[i].input.rule,
                data[i].input.item, data[i].input.qty, data[i].input.rulePercent);

            expect(results).toEqual(data[i].expect.discount_data);
            expect(data[i].input.item.discount_percent).toEqual(data[i].expect.item.discount_percent);
        });
    }
    /* End Test _calculate function*/

});
