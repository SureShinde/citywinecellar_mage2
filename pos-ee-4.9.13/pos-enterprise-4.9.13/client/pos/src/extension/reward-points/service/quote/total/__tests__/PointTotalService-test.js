import PointTotalService from "../PointTotalService";
import {RewardPointHelper} from "../../../../helper/RewardPointHelper";
import RewardPointService from "../../../RewardPointService";
import SalesRuleUtilityService from "../../../../../../service/salesrule/UtilityService";
import QuoteItemService from "../../../../../../service/checkout/quote/ItemService";
import CurrencyHelper from "../../../../../../helper/CurrencyHelper";

describe('PointTotalService-collect', () => {
    let mocks = {};
    let rate = {
        customer_group_ids: "0,1,2,3",
        direction: 1,
        max_price_spended_type: "by_price",
        max_price_spended_value: 200,
        money: 1.0000,
        points: 5,
        rate_id: 3,
        sort_order: 1,
        status: 1,
        website_ids: 1
    };
    let maxPointSpendPerOrder = {};
    let usedPoint = 0;

    beforeAll(() => {
        // Mock functions
        mocks.getSpendMaxPointPerOrder = RewardPointHelper.getSpendMaxPointPerOrder;
        RewardPointHelper.getSpendMaxPointPerOrder = jest.fn(() => maxPointSpendPerOrder);

        mocks.getCustomerPointBalance = RewardPointService.getCustomerPointBalance;
        RewardPointService.getCustomerPointBalance = jest.fn((customer) => customer.point_balance);
        mocks.customerCanSpendPoint = RewardPointService.customerCanSpendPoint;
        RewardPointService.customerCanSpendPoint = jest.fn((customer) => customer.can_spend_point);
        mocks.getQuoteBaseTotal = RewardPointService.getQuoteBaseTotal;
        RewardPointService.getQuoteBaseTotal = jest.fn((quote) => quote.base_total);
        mocks.getActiveSpendingRate = RewardPointService.getActiveSpendingRate;
        RewardPointService.getActiveSpendingRate = jest.fn(() => rate);
        mocks.getUsedPoint = RewardPointService.getUsedPoint;
        RewardPointService.getUsedPoint = jest.fn(() => usedPoint);

        mocks.processDiscount = PointTotalService.processDiscount;
        PointTotalService.processDiscount = jest.fn(function (quote, address, total, ruleDiscount, points) {
            quote.rule_discount = ruleDiscount;
            quote.points = points;
        });
        mocks.setBaseDiscount = PointTotalService.setBaseDiscount;
        PointTotalService.setBaseDiscount = jest.fn(function (baseDiscount, total, quote, pointUsed) {
            quote.base_discount = baseDiscount;
            quote.point_used = pointUsed;
        });


    });
    afterAll(() => {
        // Unmock functions
        RewardPointHelper.getSpendMaxPointPerOrder = mocks.getSpendMaxPointPerOrder;

        RewardPointService.getCustomerPointBalance = mocks.getCustomerPointBalance;
        RewardPointService.customerCanSpendPoint = mocks.customerCanSpendPoint;
        RewardPointService.getQuoteBaseTotal = mocks.getQuoteBaseTotal;
        RewardPointService.getActiveSpendingRate = mocks.getActiveSpendingRate;
        RewardPointService.getUsedPoint = mocks.getUsedPoint;

        PointTotalService.processDiscount = mocks.processDiscount;
        PointTotalService.setBaseDiscount = mocks.setBaseDiscount;
    });

    let data = [
        {
            testCaseId: 'RQT-01',
            title: 'Quote does not have customer',
            data: {
                maxPointSpendPerOrder: 0,
                usedPoint: 0,
                quote: {
                    base_total: 0,
                    is_virtual: 0,
                },
                address: {
                    address_type: 'billing'
                },
                total: {},
            },
            expect: {
                rule_discount: undefined,
                points: undefined,
                base_discount: undefined,
                point_used: undefined
            }
        },
        {
            testCaseId: 'RQT-02',
            title: 'Quote is not virtual and address is billing',
            data: {
                maxPointSpendPerOrder: 0,
                usedPoint: 0,
                quote: {
                    base_total: 0,
                    is_virtual: 0,
                    customer: {
                        point_balance: 0,
                        can_spend_point: 0
                    }
                },
                address: {
                    address_type: 'billing'
                },
                total: {},
            },
            expect: {
                rule_discount: undefined,
                points: undefined,
                base_discount: undefined,
                point_used: undefined
            }
        },
        {
            testCaseId: 'RQT-03',
            title: 'Quote is virtual and address is shipping',
            data: {
                maxPointSpendPerOrder: 0,
                usedPoint: 0,
                quote: {
                    base_total: 0,
                    is_virtual: 1,
                    customer: {
                        point_balance: 0,
                        can_spend_point: 0
                    }
                },
                address: {
                    address_type: 'shipping'
                },
                total: {},
            },
            expect: {
                rule_discount: undefined,
                points: undefined,
                base_discount: undefined,
                point_used: undefined
            }
        },
        {
            testCaseId: 'RQT-04',
            title: 'Customer has point_balance and can_spend_point is 0',
            data: {
                maxPointSpendPerOrder: 0,
                usedPoint: 0,
                quote: {
                    base_total: 0,
                    is_virtual: 1,
                    customer: {
                        point_balance: 0,
                        can_spend_point: 0
                    }
                },
                address: {
                    address_type: 'billing'
                },
                total: {},
            },
            expect: {
                rule_discount: undefined,
                points: undefined,
                base_discount: undefined,
                point_used: undefined
            }
        },
        {
            testCaseId: 'RQT-05',
            title: 'Point using is 0',
            data: {
                maxPointSpendPerOrder: 150,
                usedPoint: 50,
                quote: {
                    base_total: 10,
                    is_virtual: 1,
                    customer: {
                        point_balance: 100,
                        can_spend_point: 1
                    }
                },
                address: {
                    address_type: 'billing'
                },
                total: {},
            },
            expect: {
                rule_discount: 10,
                points: 50,
                base_discount: 10,
                point_used: 50
            }
        },
        {
            testCaseId: 'RQT-06',
            title: 'Using point to discount part of order',
            data: {
                maxPointSpendPerOrder: 150,
                usedPoint: 20,
                quote: {
                    base_total: 10,
                    is_virtual: 1,
                    customer: {
                        point_balance: 100,
                        can_spend_point: 1
                    }
                },
                address: {
                    address_type: 'billing'
                },
                total: {},
            },
            expect: {
                rule_discount: 4,
                points: 20,
                base_discount: 4,
                point_used: 20
            }
        },
    ];

    data.forEach((testCase) => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, function () {
            maxPointSpendPerOrder = testCase.data.maxPointSpendPerOrder;
            usedPoint = testCase.data.usedPoint;

            let quote = testCase.data.quote;
            PointTotalService.collect(quote, testCase.data.address, testCase.data.total);

            expect(quote.rule_discount).toEqual(testCase.expect.rule_discount);
            expect(quote.points).toEqual(testCase.expect.points);
            expect(quote.base_discount).toEqual(testCase.expect.base_discount);
            expect(quote.point_used).toEqual(testCase.expect.point_used);
        });
    });
});

describe('PointTotalService-calculateDiscountItem', () => {
    let mocks = {};

    beforeAll(() => {
        // Mock functions
        mocks.getItemPrice = SalesRuleUtilityService.getItemPrice;
        SalesRuleUtilityService.getItemPrice = jest.fn((item) => item.price);
        mocks.getItemBasePrice = SalesRuleUtilityService.getItemBasePrice;
        SalesRuleUtilityService.getItemBasePrice = jest.fn((item) => item.base_price);

        mocks.getTotalQty = QuoteItemService.getTotalQty;
        QuoteItemService.getTotalQty = jest.fn((item, quote) => item.qty);

        mocks.convert = CurrencyHelper.convert;
        CurrencyHelper.convert = jest.fn((num) => num);
        mocks.round = CurrencyHelper.round;
        CurrencyHelper.round = jest.fn((num) => num);
    });
    afterAll(() => {
        // Unmock functions
        SalesRuleUtilityService.getItemPrice = mocks.getItemPrice;
        SalesRuleUtilityService.getItemBasePrice = mocks.getItemBasePrice;

        QuoteItemService.getTotalQty = mocks.getTotalQty;

        CurrencyHelper.convert = mocks.convert;
        CurrencyHelper.round = mocks.round;
    });

    let data = [
        {
            testCaseId: 'RQT-07',
            title: 'Calculate discount point function',
            data: {
                quote: {},
                total: {},
                item: {
                    price: 10,
                    base_price: 10,
                    qty: 1,
                    discount_amount: 5,
                    base_discount_amount: 5,
                    extension_attributes: {}
                },
                baseTotalWithoutShipping: 50,
                maxDiscountItems: 100,
                points: 20
            },
            expect: {
                extension_attributes: {
                    rewardpoints_base_discount: 5,
                    rewardpoints_discount: 5,
                    rewardpoints_spent: 4,
                },
                magestore_base_discount: 5,
                magestore_discount: 5,
                discount_amount: 10,
                base_discount_amount: 10,
            }
        },
    ];

    data.forEach((testCase) => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, function () {
            let quote = testCase.data.quote;
            let total = testCase.data.total;
            let item = testCase.data.item;
            let baseTotalWithoutShipping = testCase.data.baseTotalWithoutShipping;
            let maxDiscountItems = testCase.data.maxDiscountItems;
            let points = testCase.data.points;
            PointTotalService.calculateDiscountItem(quote, total, item, baseTotalWithoutShipping, maxDiscountItems, points);

            expect(item.extension_attributes).toEqual(testCase.expect.extension_attributes);
            expect(item.magestore_base_discount).toEqual(testCase.expect.magestore_base_discount);
            expect(item.magestore_discount).toEqual(testCase.expect.magestore_discount);
            expect(item.discount_amount).toEqual(testCase.expect.discount_amount);
            expect(item.base_discount_amount).toEqual(testCase.expect.base_discount_amount);
        });
    });
});

describe('PointTotalService-calculateDiscountShipping', () => {
    let mocks = {};
    let amountResult, baseAmountResult;

    beforeAll(() => {
        // Mock functions
        mocks._addAmount = PointTotalService._addAmount;
        PointTotalService._addAmount = jest.fn(function (amount, code) {
            amountResult = amount;
        });
        mocks._addBaseAmount = PointTotalService._addBaseAmount;
        PointTotalService._addBaseAmount = jest.fn(function(amount, code){
            baseAmountResult = amount;
        });
        mocks.convert = CurrencyHelper.convert;
        CurrencyHelper.convert = jest.fn((num) => num);
    });
    afterAll(() => {
        // Unmock functions
        PointTotalService._addAmount = mocks._addAmount;
        PointTotalService._addBaseAmount = mocks._addBaseAmount;
        CurrencyHelper.convert = mocks.convert;
    });

    let data = [
        {
            testCaseId: 'RQT-08',
            title: 'Discount for items is greater or equal than rule discount amount',
            data: {
                address: {},
                total: {},
                ruleDiscount: 30,
                maxDiscountItems: 50,
            },
            expect: {
                amount: undefined,
                base_amount: undefined,
                total: {}
            }
        },
        {
            testCaseId: 'RQT-09',
            title: 'Total does not have extension attributes ',
            data: {
                address: {
                    shipping_amount_for_discount: 5,
                    base_shipping_amount: 5,
                    base_shipping_discount_amount: 0,
                    base_shipping_amount_for_discount: 10
                },
                total: {
                    magestore_base_discount_for_shipping: 0,
                    magestore_discount_for_shipping: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_amount: 0
                },
                ruleDiscount: 50,
                maxDiscountItems: 40,
            },
            expect: {
                amount: -10,
                base_amount: -10,
                total: {
                    magestore_base_discount_for_shipping: 10,
                    magestore_discount_for_shipping: 10,
                    base_shipping_discount_amount: 10,
                    shipping_discount_amount: 10,
                    extension_attributes: {
                        rewardpoints_base_discount_for_shipping: 10,
                        rewardpoints_discount_for_shipping: 10
                    }
                }
            }
        },
        {
            testCaseId: 'RQT-10',
            title: 'Total have extension attributes with data',
            data: {
                address: {
                    shipping_amount_for_discount: 0,
                    base_shipping_amount: 5,
                    base_shipping_discount_amount: 0,
                    base_shipping_amount_for_discount: 10
                },
                total: {
                    magestore_base_discount_for_shipping: 1,
                    magestore_discount_for_shipping: 1,
                    base_shipping_discount_amount: 3,
                    shipping_discount_amount: 3,
                    extension_attributes: {
                        rewardpoints_base_discount_for_shipping: 5,
                        rewardpoints_discount_for_shipping: 5
                    }
                },
                ruleDiscount: 50,
                maxDiscountItems: 40,
            },
            expect: {
                amount: -5,
                base_amount: -5,
                total: {
                    magestore_base_discount_for_shipping: 6,
                    magestore_discount_for_shipping: 6,
                    base_shipping_discount_amount: 8,
                    shipping_discount_amount: 8,
                    extension_attributes: {
                        rewardpoints_base_discount_for_shipping: 10,
                        rewardpoints_discount_for_shipping: 10
                    }
                }
            }
        },
    ];

    data.forEach((testCase) => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, function () {
            let address = testCase.data.address;
            let total = testCase.data.total;
            let ruleDiscount = testCase.data.ruleDiscount;
            let maxDiscountItems = testCase.data.maxDiscountItems;

            PointTotalService.calculateDiscountShipping(address, total, ruleDiscount, maxDiscountItems);

            expect(amountResult).toEqual(testCase.expect.amount);
            expect(baseAmountResult).toEqual(testCase.expect.base_amount);
            expect(total).toEqual(testCase.expect.total);
        });
    });
});

describe('PointTotalService-setBaseDiscount', () => {
    let mocks = {};

    beforeAll(() => {
        // Mock functions
        mocks.convert = CurrencyHelper.convert;
        CurrencyHelper.convert = jest.fn((num) => num);
    });
    afterAll(() => {
        // Unmock functions
        CurrencyHelper.convert = mocks.convert;
    });

    let data = [
        {
            testCaseId: 'RQT-11',
            title: 'Set base discount with pure quote and total',
            data: {
                baseDiscount: 5,
                total: {
                },
                quote: {},
                pointUsed: 10
            },
            expect: {
                total: {
                    extension_attributes: {
                        rewardpoints_spent: 10,
                        rewardpoints_base_discount: 5,
                        rewardpoints_discount: 5
                    },
                    discount_amount: -5,
                    base_discount_amount: -5,
                    magestore_base_discount: 5,
                    magestore_discount: 5,
                    base_subtotal_with_discount: -5,
                    subtotal_with_discount: -5
                },
                quote: {
                    rewardpoints_spent: 10,
                    rewardpoints_base_discount: 5,
                    rewardpoints_discount: 5,
                    magestore_base_discount: 5,
                    magestore_discount: 5,
                    magestore_base_discount_for_shipping: 0,
                    magestore_discount_for_shipping: 0,
                    rewardpoints_base_discount_for_shipping: 0,
                    rewardpoints_discount_for_shipping: 0
                }
            }
        },
        {
            testCaseId: 'RQT-12',
            title: '',
            data: {
                baseDiscount: 5,
                total: {
                    discount_amount: -3,
                    base_discount_amount: -3,
                    extension_attributes: {
                        rewardpoints_spent: 10,
                        rewardpoints_base_discount: 5,
                        rewardpoints_discount: 5,
                        rewardpoints_base_discount_for_shipping: 1,
                        rewardpoints_discount_for_shipping: 1
                    },
                    magestore_base_discount: 5,
                    magestore_discount: 5,
                    base_subtotal_with_discount: 5,
                    subtotal_with_discount: 5,
                    magestore_discount_for_shipping: 1,
                    magestore_base_discount_for_shipping: 1
                },
                quote: {
                    rewardpoints_spent: 10,
                    rewardpoints_base_discount: 5,
                    rewardpoints_discount: 5,
                    magestore_base_discount: 5,
                    magestore_discount: 5,
                    magestore_base_discount_for_shipping: 0,
                    magestore_discount_for_shipping: 0,
                    rewardpoints_base_discount_for_shipping: 0,
                    rewardpoints_discount_for_shipping: 0
                },
                pointUsed: 10
            },
            expect: {
                total: {
                    extension_attributes: {
                        rewardpoints_spent: 20,
                        rewardpoints_base_discount: 10,
                        rewardpoints_discount: 10,
                        rewardpoints_base_discount_for_shipping: 1,
                        rewardpoints_discount_for_shipping: 1
                    },
                    discount_amount: -8,
                    base_discount_amount: -8,
                    magestore_base_discount: 10,
                    magestore_discount: 10,
                    base_subtotal_with_discount: 0,
                    subtotal_with_discount: 0,
                    magestore_discount_for_shipping: 1,
                    magestore_base_discount_for_shipping: 1
                },
                quote: {
                    rewardpoints_spent: 20,
                    rewardpoints_base_discount: 10,
                    rewardpoints_discount: 10,
                    magestore_base_discount: 10,
                    magestore_discount: 10,
                    magestore_base_discount_for_shipping: 1,
                    magestore_discount_for_shipping: 1,
                    rewardpoints_base_discount_for_shipping: 1,
                    rewardpoints_discount_for_shipping: 1
                }
            }
        },
    ];

    data.forEach((testCase) => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, function () {
            let baseDiscount = testCase.data.baseDiscount;
            let total = testCase.data.total;
            let quote = testCase.data.quote;
            let pointUsed = testCase.data.pointUsed;

            PointTotalService.setBaseDiscount(baseDiscount, total, quote, pointUsed);
            expect(total).toEqual(testCase.expect.total);
            expect(quote).toEqual(testCase.expect.quote);
        });
    });
});
