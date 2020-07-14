import {when} from "jest-when";
import UtilityService from '../UtilityService';
import QuoteItemService from "../../checkout/quote/ItemService";

describe('Test getItemQty function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.getTotalQty = QuoteItemService.getTotalQty;
        QuoteItemService.getTotalQty = jest.fn();
    });
    afterAll(() => {
        QuoteItemService.getTotalQty = mocksBackup.getTotalQty;
    });

    let data = [
        {
            testCaseId: 'UTI-01',
            title: 'getItemQty - Return discount item qty',
            input: {
                item: {},
                quote: {},
                rule: {
                    discount_qty: 2
                },
            },
            mockInput: {
                item: {
                    qty: 1,
                    discountQty: 2,
                },
            },
            expect: 1
        },
        {
            testCaseId: 'UTI-02',
            title: 'getItemQty - Return discount item qty',
            input: {
                item: {},
                quote: {},
                rule: {
                    discount_qty: 0
                },
            },
            mockInput: {
                item: {
                    qty: 2,
                    discountQty: 0,
                },
            },
            expect: 2
        },
        {
            testCaseId: 'UTI-03',
            title: 'getItemQty - Return discount item qty',
            input: {
                item: {},
                quote: {},
                rule: {
                    discount_qty: 1
                },
            },
            mockInput: {
                item: {
                    qty: 2,
                    discountQty: 1,
                },
            },
            expect: 1
        }
    ];

    /* Begin test getItemQty function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            when(QuoteItemService.getTotalQty)
                .calledWith(data[i].input.item, data[i].input.quote)
                .mockReturnValue(data[i].mockInput.item.qty);

            expect(UtilityService.getItemQty(data[i].input.item, data[i].input.quote, data[i].input.rule))
                .toEqual(data[i].expect);
        });
    }
    /* End Test getItemQty function*/
});

describe('Test getItemPrice function', () => {
    let mocks = {};
    beforeAll(() => {
        // Mock functions
        mocks.getCalculationPrice = QuoteItemService.getCalculationPrice;
    });

    afterAll(() => {
        // Unmock functions
        QuoteItemService.getCalculationPrice = mocks.getCalculationPrice;
    });

    let data = [
        {
            testCaseId: 'UTI-05',
            title: 'getItemPrice - Return item price',
            input: {
                item: {
                    discount_calculation_price: null,
                },
            },
            mockInput: {
                item: {
                    calcPrice: 1,
                },
            },
            expect: 1
        },
        {
            testCaseId: 'UTI-06',
            title: 'getItemPrice - Return item price',
            input: {
                item: {
                    discount_calculation_price: undefined,
                },
            },
            mockInput: {
                item: {
                    calcPrice: 1,
                },
            },
            expect: 1
        },
        {
            testCaseId: 'UTI-07',
            title: 'getItemPrice - Return item price',
            input: {
                item: {
                    discount_calculation_price: 3,
                },
            },
            mockInput: {
                item: {
                    calcPrice: 1,
                },
            },
            expect: 3
        },
    ];

    /* Begin test getItemPrice function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            QuoteItemService.getCalculationPrice = jest.fn();
            when(QuoteItemService.getCalculationPrice)
                .calledWith(data[i].input.item)
                .mockReturnValue(data[i].mockInput.item.calcPrice);

            expect(UtilityService.getItemPrice(data[i].input.item))
                .toEqual(data[i].expect);
        });
    }
    /* End Test getItemPrice function*/
});

describe('Test getItemBasePrice function', () => {
    let mocks = {};
    beforeAll(() => {
        // Mock functions
        mocks.getBaseCalculationPrice = QuoteItemService.getBaseCalculationPrice;
    });

    afterAll(() => {
        // Unmock functions
        QuoteItemService.getBaseCalculationPrice = mocks.getBaseCalculationPrice;
    });

    let data = [
        {
            testCaseId: 'UTI-08',
            title: 'getItemBasePrice - Return base item price',
            input: {
                item: {
                    base_discount_calculation_price: null,
                },
            },
            mockInput: {
                item: {
                    baseCalcPrice: 2,
                },
            },
            expect: 2
        },
        {
            testCaseId: 'UTI-09',
            title: 'getItemBasePrice - Return base item price',
            input: {
                item: {
                    base_discount_calculation_price: undefined,
                },
            },
            mockInput: {
                item: {
                    baseCalcPrice: 2,
                },
            },
            expect: 2
        },
        {
            testCaseId: 'UTI-10',
            title: 'getItemBasePrice - Return base item price',
            input: {
                item: {
                    base_discount_calculation_price: 1,
                },
            },
            mockInput: {
                item: {
                    baseCalcPrice: 2,
                },
            },
            expect: 1
        },
    ];

    /* Begin test getItemBasePrice function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            QuoteItemService.getBaseCalculationPrice = jest.fn();
            when(QuoteItemService.getBaseCalculationPrice)
                .calledWith(data[i].input.item)
                .mockReturnValue(data[i].mockInput.item.baseCalcPrice);

            expect(UtilityService.getItemBasePrice(data[i].input.item))
                .toEqual(data[i].expect);
        });
    }
    /* End Test getItemBasePrice function*/
});

describe('Test mergeIds function', () => {

    let data = [
        {
            testCaseId: 'UTI-11',
            title: 'mergeIds - Merges two set of IDs',
            input: {
                item: {
                    a1: [1,3],
                    a2: [3,4],
                    asString: true,
                },
            },
            expect: "1,3,4"
        },
        {
            testCaseId: 'UTI-12',
            title: 'mergeIds - Merges two set of IDs',
            input: {
                item: {
                    a1: '1,2',
                    a2: '3,4',
                    asString: false,
                },
            },
            expect: [1, 2, 3, 4]
        },
        {
            testCaseId: 'UTI-13',
            title: 'mergeIds - Merges two set of IDs',
            input: {
                item: {
                    a1: "",
                    a2: [3,4],
                    asString: true,
                },
            },
            expect: "3,4"
        },
        {
            testCaseId: 'UTI-14',
            title: 'mergeIds - Merges two set of IDs',
            input: {
                item: {
                    a1: "1,2",
                    a2: [3,4],
                    asString: true,
                },
            },
            expect: "1,2,3,4"
        },
        {
            testCaseId: 'UTI-15',
            title: 'mergeIds - Merges two set of IDs',
            input: {
                item: {
                    a1: [],
                    a2: [3,4],
                    asString: true,
                },
            },
            expect: "3,4"
        },
    ];

    /* Begin test mergeIds function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            expect(UtilityService.mergeIds(data[i].input.item.a1, data[i].input.item.a2, data[i].input.item.asString))
                .toEqual(data[i].expect);
        });
    }
    /* End Test mergeIds function*/
});

describe('Test minFix function', () => {
    let mocks = {};
    beforeAll(() => {
        // Mock functions
        mocks.getItemPrice = UtilityService.getItemPrice;
        mocks.getItemBasePrice = UtilityService.getItemBasePrice;
    });

    afterAll(() => {
        // Unmock functions
        UtilityService.getItemPrice = mocks.getItemPrice;
        UtilityService.getItemBasePrice = mocks.getItemBasePrice;
    });

    let data = [
        {
            testCaseId: 'UTI-17',
            title: 'minFix - Get min fix',
            input: {
                discountData: {
                    amount: 1,
                    base_amount: 1,
                },
                item: {
                    discount_amount: 1,
                    base_discount_amount: 1,
                },
                qty: 0,
            },
            mockInput: {
                item: {
                    itemPrice: 2,
                    baseItemPrice: 2,
                },
            },
            expect: {
                discountData: {
                    amount: 0,
                    base_amount: 0,
                },
            }
        },
        {
            testCaseId: 'UTI-18',
            title: 'minFix - Get min fix',
            input: {
                discountData: {
                    amount: 1,
                    base_amount: 1,
                },
                item: {
                    discount_amount: 1,
                    base_discount_amount: 1,
                },
                qty: 1,
            },
            mockInput: {
                item: {
                    itemPrice: 0,
                    baseItemPrice: 0,
                },
            },
            expect: {
                discountData: {
                    amount: 0,
                    base_amount: 0,
                },
            }
        },
        {
            testCaseId: 'UTI-19',
            title: 'minFix - Get min fix',
            input: {
                discountData: {
                    amount: 1,
                    base_amount: 1,
                },
                item: {
                    discount_amount: 1,
                    base_discount_amount: 1,
                },
                qty: 1,
            },
            mockInput: {
                item: {
                    itemPrice: 1,
                    baseItemPrice: 1,
                },
            },
            expect: {
                discountData: {
                    amount: 1,
                    base_amount: 1,
                },
            }
        },
        {
            testCaseId: 'UTI-20',
            title: 'minFix - Get min fix',
            input: {
                discountData: {
                    amount: 1,
                    base_amount: 1,
                },
                item: {
                    discount_amount: 1,
                    base_discount_amount: 1,
                },
                qty: 2,
            },
            mockInput: {
                item: {
                    itemPrice: 2,
                    baseItemPrice: 2,
                },
            },
            expect: {
                discountData: {
                    amount: 2,
                    base_amount: 2,
                },
            }
        },
        {
            testCaseId: 'UTI-21',
            title: 'minFix - Get min fix',
            input: {
                discountData: {
                    amount: 1,
                    base_amount: 1,
                },
                item: {
                    discount_amount: undefined,
                    base_discount_amount: undefined,
                },
                qty: 2,
            },
            mockInput: {
                item: {
                    itemPrice: 2,
                    baseItemPrice: 2,
                },
            },
            expect: {
                discountData: {
                    amount: 1,
                    base_amount: 1,
                },
            }
        },
    ];

    /* Begin test minFix function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            UtilityService.getItemPrice = jest.fn();
            when(UtilityService.getItemPrice)
                .calledWith(data[i].input.item)
                .mockReturnValue(data[i].mockInput.item.itemPrice);

            UtilityService.getItemBasePrice = jest.fn();
            when(UtilityService.getItemBasePrice)
                .calledWith(data[i].input.item)
                .mockReturnValue(data[i].mockInput.item.baseItemPrice);

            UtilityService.minFix(data[i].input.discountData, data[i].input.item, data[i].input.qty);
            expect(data[i].input.discountData.amount).toEqual(data[i].expect.discountData.amount);
            expect(data[i].input.discountData.base_amount).toEqual(data[i].expect.discountData.base_amount);
        });
    }
    /* End Test minFix function*/
});
