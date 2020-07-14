import EarningPointProcessor from "../EarningPointProcessor";
import {RewardPointHelper} from "../../../../helper/RewardPointHelper";
import RewardPointService from "../../../RewardPointService";
import QuoteItemService from "../../../../../../service/checkout/quote/ItemService";

describe('EarningPointProcessor-setEarningPoints', () => {
    let mocks = {};
    let canEarnWhenSpend = 0;

    beforeAll(() => {
        // Mock functions
        mocks.canEarnWhenSpend = RewardPointHelper.canEarnWhenSpend;
        RewardPointHelper.canEarnWhenSpend = jest.fn(function () {
            return canEarnWhenSpend;
        });

        mocks._updateEarningPoints = EarningPointProcessor._updateEarningPoints;
        EarningPointProcessor._updateEarningPoints = jest.fn(function (quote) {
            return quote;
        });

        mocks.getEarnPointForQuote = RewardPointService.getEarnPointForQuote;
        RewardPointService.getEarnPointForQuote = jest.fn((quote) => quote.initPointEarn);
    });
    afterAll(() => {
        // Unmock functions
        RewardPointHelper.canEarnWhenSpend = mocks.canEarnWhenSpend;
        EarningPointProcessor._updateEarningPoints = mocks._updateEarningPoints;
        RewardPointService.getEarnPointForQuote = mocks.getEarnPointForQuote;
    });

    let data = [
        {
            testCaseId: 'REP-01',
            title: 'When reward point spent is greater than 0 and can NOT earn when spend',
            data: {
                address: {
                    rewardpoints_earn: 0
                },
                quote: {
                    rewardpoints_earn: 0,
                    rewardpoints_spent: 5,
                    initPointEarn: 10
                },
                canEarnWhenSpend: 0
            },
            expect: {
                address: 0,
                quote: 0
            }
        },
        {
            testCaseId: 'REP-02',
            title: 'When reward point spent is greater than 0 and can earn when spend while earn point for quote is 0',
            data: {
                address: {
                    rewardpoints_earn: 0
                },
                quote: {
                    rewardpoints_earn: 0,
                    rewardpoints_spent: 5,
                    initPointEarn: 0
                },
                canEarnWhenSpend: 1
            },
            expect: {
                address: 0,
                quote: 0
            }
        },
        {
            testCaseId: 'REP-03',
            title: 'When reward point spent is greater than 0 and can earn when spend while earn point for quote is greater than 0 and current earn point in quote is undefined',
            data: {
                address: {
                    rewardpoints_earn: 0
                },
                quote: {
                    rewardpoints_spent: 5,
                    initPointEarn: 10
                },
                canEarnWhenSpend: 1
            },
            expect: {
                address: 10,
                quote: 10
            }
        },
        {
            testCaseId: 'REP-04',
            title: 'When reward point spent is greater than 0 and can earn when spend while earn point for quote is greater than 0 and current earn point in quote is greater than 0',
            data: {
                address: {
                    rewardpoints_earn: 0
                },
                quote: {
                    rewardpoints_earn: 1,
                    rewardpoints_spent: 5,
                    initPointEarn: 10
                },
                canEarnWhenSpend: 1
            },
            expect: {
                address: 10,
                quote: 11
            }
        },
    ];

    data.forEach((testCase) => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, function () {
            canEarnWhenSpend = testCase.data.canEarnWhenSpend;
            let address = testCase.data.address;
            let quote = testCase.data.quote;

            EarningPointProcessor.setEarningPoints(address, quote);

            expect(address.rewardpoints_earn).toEqual(testCase.expect.address);
            expect(quote.rewardpoints_earn).toEqual(testCase.expect.quote);
        });
    });
});

describe('EarningPointProcessor-_updateEarningPoints', () => {
    let mocks = {};
    let canEarnByShipping = 0;

    beforeAll(() => {
        // Mock functions
        mocks.canEarnByShipping = RewardPointHelper.canEarnByShipping;
        RewardPointHelper.canEarnByShipping = jest.fn(function () {
            return canEarnByShipping;
        });
        mocks.round = RewardPointHelper.round;
        RewardPointHelper.round = jest.fn(function (num) {
            return num;
        });

        mocks.isChildrenCalculated = QuoteItemService.isChildrenCalculated;
        QuoteItemService.isChildrenCalculated = jest.fn(function (item, quote) {
            return item.isChildrenCalculated;
        });

        mocks.getChildrenItems = QuoteItemService.getChildrenItems;
        QuoteItemService.getChildrenItems = jest.fn(function (quote, item) {
            let children = [];
            quote.items.forEach(datum => {
                if(datum.parent_item_id === item.id) {
                    children.push(datum);
                }
            });
            return children;
        });
    });
    afterAll(() => {
        // Unmock functions
        RewardPointHelper.canEarnByShipping = mocks.canEarnByShipping;
        RewardPointHelper.round = mocks.round;
        QuoteItemService.isChildrenCalculated = mocks.isChildrenCalculated;
        QuoteItemService.getChildrenItems = mocks.getChildrenItems;
    });

    let data = [
            {
                testCaseId: 'REP-05',
                title: 'Quote does not have any item',
                data: {
                    canEarnByShipping: 0,
                    quote: {
                        items: [],
                        rewardpoints_earn: 10,
                        base_shipping_amount: 0,
                        base_shipping_tax_amount: 0,
                        magestore_base_discount_for_shipping: 0
                    }
                },
                expect: {
                    1: undefined,
                    2: undefined
                }
            },
            {
                testCaseId: 'REP-06',
                title: 'Quote does not have reward point earn',
                data: {
                    canEarnByShipping: 0,
                    quote: {
                        items: [
                            {
                                id: 1,
                                parent_item_id: 0,
                                has_children: 1,
                                product: {id: 1000},
                                qty: 0,
                                base_price_incl_tax: 0,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 2,
                                parent_item_id: 1,
                                has_children: 0,
                                product: {id: 1000},
                                qty: 0,
                                base_price_incl_tax: 0,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                        ],
                        rewardpoints_earn: 0,
                        base_shipping_amount: 0,
                        base_shipping_tax_amount: 0,
                        magestore_base_discount_for_shipping: 0
                    }
                },
                expect: {
                    1: undefined,
                    2: undefined
                }
            },
            {
                testCaseId: 'REP-07',
                title: 'Quote have 1 simple product item, 1 item without product data, 2 configurable item with its child\n' +
                    'All product do not have discount and base price include tax is 0',
                data: {
                    canEarnByShipping: 0,
                    quote: {
                        items: [
                            {
                                id: 1,
                                parent_item_id: 0,
                                has_children: 0,
                                product: {id: 1000},
                                qty: 1,
                                base_price_incl_tax: 0,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 2,
                                parent_item_id: 0,
                                has_children: 1,
                                product: {id: 10},
                                qty: 1,
                                base_price_incl_tax: 0,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 3,
                                parent_item_id: 2,
                                has_children: 0,
                                product: {id: 20},
                                qty: 1,
                                base_price_incl_tax: 0,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 4,
                                parent_item_id: 0,
                                has_children: 0,
                                qty: 1,
                                base_price_incl_tax: 5,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 5,
                                parent_item_id: 0,
                                has_children: 1,
                                product: {id: 30},
                                qty: 1,
                                base_price_incl_tax: 5,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 1,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 6,
                                parent_item_id: 5,
                                has_children: 0,
                                product: {id: 40},
                                qty: 1,
                                base_price_incl_tax: 0,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                        ],
                        rewardpoints_earn: 15,
                        base_shipping_amount: 0,
                        base_shipping_tax_amount: 0,
                        magestore_base_discount_for_shipping: 0
                    }
                },
                expect: {
                    1: 5,
                    2: 5,
                    3: undefined,
                    4: undefined,
                    5: undefined,
                    6: 5,
                }
            },
            {
                testCaseId: 'REP-08',
                title: 'Quote have 1 simple product item, 1 item without product data, 2 configurable item with its child\n' +
                    'All product have discount and magestore discount',
                data: {
                    canEarnByShipping: 1,
                    quote: {
                        items: [
                            {
                                id: 1,
                                parent_item_id: 0,
                                has_children: 0,
                                product: {id: 1000},
                                qty: 1,
                                base_price_incl_tax: 10,
                                base_discount_amount: 5,
                                magestore_base_discount: 5,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 2,
                                parent_item_id: 0,
                                has_children: 1,
                                product: {id: 10},
                                qty: 1,
                                base_price_incl_tax: 20,
                                base_discount_amount: 5,
                                magestore_base_discount: 5,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 3,
                                parent_item_id: 2,
                                has_children: 0,
                                product: {id: 20},
                                qty: 1,
                                base_price_incl_tax: 20,
                                base_discount_amount: 5,
                                magestore_base_discount: 5,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 4,
                                parent_item_id: 0,
                                has_children: 0,
                                qty: 1,
                                base_price_incl_tax: 5,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 5,
                                parent_item_id: 0,
                                has_children: 1,
                                product: {id: 30},
                                qty: 1,
                                base_price_incl_tax: 30,
                                base_discount_amount: 10,
                                magestore_base_discount: 10,
                                isChildrenCalculated: 1,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 6,
                                parent_item_id: 5,
                                has_children: 0,
                                product: {id: 40},
                                qty: 1,
                                base_price_incl_tax: 30,
                                base_discount_amount: 10,
                                magestore_base_discount: 10,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                        ],
                        rewardpoints_earn: 15,
                        base_shipping_amount: 0,
                        base_shipping_tax_amount: 0,
                        magestore_base_discount_for_shipping: 0
                    }
                },
                expect: {
                    1: 0,
                    2: 7.5,
                    3: undefined,
                    4: undefined,
                    5: undefined,
                    6: 7.5,
                }
            },
            {
                testCaseId: 'REP-09',
                title: 'Quote have 1 simple product item, 1 item without product data, 2 configurable item with its child\n' +
                    'All product have discount and magestore discount\n' +
                    'Reward point can earn by shipping',
                data: {
                    canEarnByShipping: 1,
                    quote: {
                        items: [
                            {
                                id: 1,
                                parent_item_id: 0,
                                has_children: 0,
                                product: {id: 1000},
                                qty: 1,
                                base_price_incl_tax: 20,
                                base_discount_amount: 5,
                                magestore_base_discount: 5,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 2,
                                parent_item_id: 0,
                                has_children: 1,
                                product: {id: 10},
                                qty: 1,
                                base_price_incl_tax: 20,
                                base_discount_amount: 5,
                                magestore_base_discount: 5,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 3,
                                parent_item_id: 2,
                                has_children: 0,
                                product: {id: 20},
                                qty: 1,
                                base_price_incl_tax: 20,
                                base_discount_amount: 5,
                                magestore_base_discount: 5,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 4,
                                parent_item_id: 0,
                                has_children: 0,
                                qty: 1,
                                base_price_incl_tax: 5,
                                base_discount_amount: 0,
                                magestore_base_discount: 0,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 5,
                                parent_item_id: 0,
                                has_children: 1,
                                product: {id: 30},
                                qty: 1,
                                base_price_incl_tax: 40,
                                base_discount_amount: 10,
                                magestore_base_discount: 10,
                                isChildrenCalculated: 1,
                                extension_attributes: {
                                }
                            },
                            {
                                id: 6,
                                parent_item_id: 5,
                                has_children: 0,
                                product: {id: 40},
                                qty: 1,
                                base_price_incl_tax: 40,
                                base_discount_amount: 10,
                                magestore_base_discount: 10,
                                isChildrenCalculated: 0,
                                extension_attributes: {
                                }
                            },
                        ],
                        rewardpoints_earn: 15,
                        base_shipping_amount: 10,
                        base_shipping_tax_amount: 2,
                        magestore_base_discount_for_shipping: 2
                    }
                },
                expect: {
                    1: 3,
                    2: 3,
                    3: undefined,
                    4: undefined,
                    5: undefined,
                    6: 6,
                }
            },
        ];

    data.forEach((testCase) => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, function () {
            canEarnByShipping = testCase.data.canEarnByShipping;

            let quote = testCase.data.quote;

            EarningPointProcessor._updateEarningPoints(quote);

            let result = {};
            quote.items.forEach(item => {
                result[item.id] = item.extension_attributes.rewardpoints_earn;
            });

            expect(result).toEqual(testCase.expect);
        });
    });
});
