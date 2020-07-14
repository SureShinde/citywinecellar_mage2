import { when } from 'jest-when';
import SalesRuleByFixedService from '../ByFixedService';
import CurrencyHelper from "../../../../../../helper/CurrencyHelper";

describe('Test calculate function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.convert = CurrencyHelper.convert;
        CurrencyHelper.convert = jest.fn();
    });
    afterAll(() => {
        CurrencyHelper.convert = mocksBackup.convert;
    });
    let data = [
        {
            testCaseId: 'DIS-BFS-01',
            title: 'Function calculate - when discount amount is 0',
            input: {
                rule: {
                    discount_amount: 0
                },
                qty : 1
            },
            mockInput: {
                rule: {
                    discount_amount: 0
                },
            },
            expect: {
                discount_data: {
                    amount : 0,
                    base_amount: 0
                }
            }
        },
        {
            testCaseId: 'DIS-BFS-02',
            title: 'Function calculate - when qty is 0',
            input: {
                rule: {
                    discount_amount: 1
                },
                qty : 0
            },
            mockInput: {
                rule: {
                    discount_amount: 1
                },
            },
            expect: {
                discount_data: {
                    amount : 0,
                    base_amount: 0
                }
            }
        },
        {
            testCaseId: 'DIS-BFS-03',
            title: 'Function calculate - when qty and discount amount is not 0',
            input: {
                rule: {
                    discount_amount: 0.5
                },
                qty : 1.5
            },
            mockInput: {
                rule: {
                    discount_amount: 0.5
                },
            },
            expect: {
                discount_data: {
                    amount : 0.75,
                    base_amount: 0.75
                }
            }
        }
    ];

    /* Begin test calculate function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            when(CurrencyHelper.convert)
                .calledWith(data[i].input.rule.discount_amount)
                .mockReturnValue(data[i].mockInput.rule.discount_amount);

            let result = SalesRuleByFixedService.calculate({}, {}, data[i].input.rule, {}, data[i].input.qty);
            expect(result).toEqual(data[i].expect.discount_data);
        });
    }
    /* End test calculate function*/
});

describe('Test fixQuantity function', () => {
    let data = [
        {
            testCaseId: 'DIS-BFS-04',
            title: 'Function fixQuantity - when qty is 0',
            input: {
                rule: {
                    discount_step: 1
                },
                qty : 0
            },
            expect: {
                qty: 0
            }
        },
        {
            testCaseId: 'DIS-BFS-05',
            title: 'Function fixQuantity - when discount step is 0',
            input: {
                rule: {
                    discount_step: 0
                },
                qty : 1
            },
            expect: {
                qty: 1
            }
        },
        {
            testCaseId: 'DIS-BFS-06',
            title: 'Function fixQuantity - when qty is 1 and discount step is 1',
            input: {
                rule: {
                    discount_step: 1
                },
                qty : 1
            },
            expect: {
                qty: 1
            }
        },
        {
            testCaseId: 'DIS-BFS-07',
            title: 'Function fixQuantity - when qty is 1.5 and discount step is 2',
            input: {
                rule: {
                    discount_step: 2
                },
                qty : 1.5
            },
            expect: {
                qty: 0
            }
        },
        {
            testCaseId: 'DIS-BFS-08',
            title: 'Function fixQuantity - when qty is 1.5 and discount step is 1',
            input: {
                rule: {
                    discount_step: 1
                },
                qty : 1.5
            },
            expect: {
                qty: 1
            }
        },
    ];

    /* Begin test fixQuantity function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let result = SalesRuleByFixedService.fixQuantity(data[i].input.qty, data[i].input.rule);
            expect(result).toEqual(data[i].expect.qty);
        });
    }
    /* End test fixQuantity function*/
});