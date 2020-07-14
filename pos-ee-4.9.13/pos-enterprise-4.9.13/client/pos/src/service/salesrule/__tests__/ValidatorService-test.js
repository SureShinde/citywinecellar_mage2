import { when } from 'jest-when';
import SalesRuleValidatorService from '../ValidatorService';
import UtilityService from "../UtilityService";
import RulesApplierService from "../RulesApplierService";
import CurrencyHelper from "../../../helper/CurrencyHelper";
import QuoteItemService from "../../checkout/quote/ItemService";
import CatalogDataService from "../../catalog/CatalogDataService";

describe('Test reset function', () => {
    let data = [
        {
            testCaseId: 'DIS-VS-16',
            title: 'Function reset ',
            input: {
                quote: {},
                address : {}
            },
            expect: {
                quote: {
                    applied_rule_ids: ""
                },
                address : {
                    applied_rule_ids: ""
                }
            }
        }
    ];

    /* Begin test reset function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let quote = data[i].input.quote;
            let address = data[i].input.address;
            SalesRuleValidatorService.reset(quote, address);
            expect(quote).toEqual(data[i].expect.quote);
            expect(address).toEqual(data[i].expect.address);
        });
    }
    /* End test reset function*/
});

describe('Test sortSalesRuleByPriority function', () => {
    let data = [
        {
            testCaseId: 'DIS-VS-17',
            title: 'Function sortSalesRuleByPriority - valid_salesrule is null',
            input: {
                quote: {
                    valid_salesrule: []
                }
            },
            expect: {
                quote: {
                    valid_salesrule: []
                },
            }
        },
        {
            testCaseId: 'DIS-VS-18',
            title: 'Function sortSalesRuleByPriority - valid_salesrule is not null',
            input: {
                quote: {
                    valid_salesrule: [
                        {
                            rule_id: 2,
                            sort_order: 3
                        },
                        {
                            rule_id: 4,
                            sort_order: 1
                        },
                        {
                            rule_id: 1,
                            sort_order: 3
                        },
                        {
                            rule_id: 3,
                            sort_order: 2
                        }
                    ]
                }
            },
            expect: {
                quote: {
                    valid_salesrule: [
                        {
                            rule_id: 4,
                            sort_order: 1
                        },
                        {
                            rule_id: 3,
                            sort_order: 2
                        },
                        {
                            rule_id: 1,
                            sort_order: 3
                        },
                        {
                            rule_id: 2,
                            sort_order: 3
                        }
                    ]
                },
            }
        }
    ];

    /* Begin test sortSalesRuleByPriority function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let quote = data[i].input.quote;
            SalesRuleValidatorService.sortSalesRuleByPriority(quote);
            expect(quote.valid_salesrule).toEqual(data[i].expect.quote.valid_salesrule);
        });
    }
    /* End test sortSalesRuleByPriority function*/
});

describe('Test initTotals function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.getItemQty = UtilityService.getItemQty;
        UtilityService.getItemQty = jest.fn();
        mocksBackup.getItemPrice = SalesRuleValidatorService.getItemPrice;
        SalesRuleValidatorService.getItemPrice = jest.fn();
        mocksBackup.getItemBasePrice = SalesRuleValidatorService.getItemBasePrice;
        SalesRuleValidatorService.getItemBasePrice = jest.fn();
    });
    afterAll(() => {
        UtilityService.getItemQty = mocksBackup.getItemQty;
        SalesRuleValidatorService.getItemPrice = mocksBackup.getItemPrice;
        SalesRuleValidatorService.getItemBasePrice = mocksBackup.getItemBasePrice;
    });

    let data = [
        {
            testCaseId: 'DIS-VS-19',
            title: 'Function initTotal - valid_salesrule is null',
            input: {
                quote: {
                    valid_salesrule: []
                },
                address: {},
                quoteItems: [
                    {
                        item_id: 1,
                        parent_item_id: ''
                    },
                ]
            },
            expect: {
                address: {
                    cart_fixed_rules: {}
                },
                dataReturn: {}
            }
        },
        {
            testCaseId: 'DIS-VS-20',
            title: 'Function initTotal - valid_salesrule is not null and valid_item_ids is null',
            input: {
                quote: {
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            valid_item_ids: ''
                        }
                    ]
                },
                address: {},
                quoteItems: [
                    {
                        item_id: 1,
                        parent_item_id: ''
                    },
                ]
            },
            expect: {
                address: {
                    cart_fixed_rules: {}
                },
                dataReturn: {
                    1: {
                        items_price: 0,
                        base_items_price: 0,
                        items_count: 0
                    }
                }
            }
        },
        {
            testCaseId: 'DIS-VS-21',
            title: 'Function initTotal - has item has parent_item_id is not null',
            input: {
                quote: {
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            valid_item_ids: 1
                        }
                    ]
                },
                address: {},
                quoteItems: [
                    {
                        item_id: 1,
                        parent_item_id: 1
                    },
                ]
            },
            expect: {
                address: {
                    cart_fixed_rules: {}
                },
                dataReturn: {
                    1: {
                        items_price: 0,
                        base_items_price: 0,
                        items_count: 0
                    }
                }
            }
        },
        {
            testCaseId: 'DIS-VS-22',
            title: 'Function initTotal - has item has item_id is not exist (and exist) in valid_item_ids of rule',
            input: {
                quote: {
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            valid_item_ids: '1'
                        }
                    ]
                },
                address: {},
                quoteItems: [
                    {
                        item_id: 1,
                        parent_item_id: ''
                    },
                    {
                        item_id: 2,
                        parent_item_id: ''
                    },
                ]
            },
            mockInput: {
                item: {
                    qty: 2,
                    discount_calculation_price: 1,
                    base_discount_calculation_price: 1
                },
            },
            expect: {
                address: {
                    cart_fixed_rules: {}
                },
                dataReturn: {
                    1: {
                        items_price: 2,
                        base_items_price: 2,
                        items_count: 1
                    }
                }
            }
        },
        {
            testCaseId: 'DIS-VS-43',
            title: 'Function initTotal - quoteItems is undefined',
            input: {
                quote: {
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            valid_item_ids: '1'
                        }
                    ]
                },
                address: {}
            },
            mockInput: {
                item: {
                    qty: 2,
                    discount_calculation_price: 1,
                    base_discount_calculation_price: 1
                },
            },
            expect: {
                address: {
                    cart_fixed_rules: {}
                },
                dataReturn: {
                }
            }
        }
    ];

    /* Begin test initTotals function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let quote = data[i].input.quote;
            let address = data[i].input.address;
            let quoteItems = data[i].input.quoteItems;

            if(data[i].mockInput) {
                let item = (typeof quoteItems !== 'undefined' && quoteItems) ? quoteItems[0] : {};
                when(UtilityService.getItemQty)
                    .calledWith(item, quote, quote.valid_salesrule[0])
                    .mockReturnValue(data[i].mockInput.item.qty);
                when(SalesRuleValidatorService.getItemPrice)
                    .calledWith(item)
                    .mockReturnValue(data[i].mockInput.item.discount_calculation_price);
                when(SalesRuleValidatorService.getItemBasePrice)
                    .calledWith(item)
                    .mockReturnValue(data[i].mockInput.item.base_discount_calculation_price);
            }

            let result = SalesRuleValidatorService.initTotals(quote, address, quoteItems);
            expect(address.cart_fixed_rules).toEqual(data[i].expect.address.cart_fixed_rules);
            expect(result).toEqual(data[i].expect.dataReturn);
        });
    }
    /* End test initTotals function*/
});

describe('Test process function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.getItemPrice = SalesRuleValidatorService.getItemPrice;
        SalesRuleValidatorService.getItemPrice = jest.fn();

        mocksBackup.applyRules = RulesApplierService.applyRules;
        mocksBackup.setAppliedRuleIds = RulesApplierService.setAppliedRuleIds;
    });
    afterAll(() => {
        SalesRuleValidatorService.getItemPrice = mocksBackup.getItemPrice;
        RulesApplierService.applyRules = mocksBackup.applyRules;
        RulesApplierService.setAppliedRuleIds = mocksBackup.setAppliedRuleIds;
    });

    let data = [
        {
            testCaseId: 'DIS-VS-23',
            title: 'Function process - item price is equal or greater than 0',
            input: {
                quote: {},
                address: {},
                item: {
                    item_price: 1
                }
            },
            mockInput: {
                item: {
                    discount_calculation_price: 1
                },
            },
            expect: {
                timeCalled: {
                    applyRules: 1,
                    setAppliedRuleIds: 1
                }
            }
        },
        {
            testCaseId: 'DIS-VS-24',
            title: 'Function process - item price is less than 0',
            input: {
                quote: {},
                address: {},
                item: {
                    item_price: -1
                }
            },
            mockInput: {
                item: {
                    discount_calculation_price: 1
                },
            },
            expect: {
                timeCalled: {
                    applyRules: 0,
                    setAppliedRuleIds: 0
                }
            }
        }
    ];

    /* Begin test process function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            RulesApplierService.applyRules = jest.fn();
            RulesApplierService.setAppliedRuleIds = jest.fn();

            let quote = data[i].input.quote;
            let address = data[i].input.address;
            let item = data[i].input.item;

            when(SalesRuleValidatorService.getItemPrice).calledWith(item).mockReturnValue(item.item_price);

            SalesRuleValidatorService.process(quote, address, item);

            expect(RulesApplierService.applyRules.mock.calls.length).toBe(data[i].expect.timeCalled.applyRules);
            expect(RulesApplierService.setAppliedRuleIds.mock.calls.length)
                .toBe(data[i].expect.timeCalled.setAppliedRuleIds);
        });
    }
    /* End test process function*/
});

describe('Test processShippingAmount function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.addDiscountDescription = RulesApplierService.addDiscountDescription;
        RulesApplierService.addDiscountDescription = jest.fn();
        mocksBackup.convert = CurrencyHelper.convert;
        CurrencyHelper.convert = jest.fn();
    });
    afterAll(() => {
        RulesApplierService.addDiscountDescription = mocksBackup.addDiscountDescription;
        CurrencyHelper.convert = mocksBackup.convert;
    });

    let data = [
        {
            testCaseId: 'DIS-VS-01',
            title: 'Function processShippingAmount - Shipping amount is 0',
            input: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 0,
                    base_shipping_amount: 0,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: ''
                },
                quote: {
                    applied_rule_ids: '',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 5
            },
            expect: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 0,
                    base_shipping_amount: 0,
                    applied_rule_ids: '1',
                    base_shipping_discount_amount: 0,
                    shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-02',
            title: 'Function processShippingAmount - Shipping amount is 0' +
                ' when shipping_amount_for_discount and shipping amount are 0',
            input: {
                address: {
                    shipping_amount_for_discount: 0,
                    base_shipping_amount_for_discount: 0,
                    shipping_amount: 0,
                    base_shipping_amount: 0,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: ''
                },
                quote: {
                    applied_rule_ids: '',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 5
            },
            expect: {
                address: {
                    shipping_amount_for_discount: 0,
                    base_shipping_amount_for_discount: 0,
                    shipping_amount: 0,
                    base_shipping_amount: 0,
                    applied_rule_ids: '1',
                    base_shipping_discount_amount: 0,
                    shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-03',
            title: 'Function processShippingAmount - Shipping amount is 0 ' +
                'when shipping_amount_for_discount is 0 and shipping amount is greater than 0',
            input: {
                address: {
                    shipping_amount_for_discount: 0,
                    base_shipping_amount_for_discount: 0,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: ''
                },
                quote: {
                    applied_rule_ids: '',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 5
            },
            expect: {
                address: {
                    shipping_amount_for_discount: 0,
                    base_shipping_amount_for_discount: 0,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '1',
                    base_shipping_discount_amount: 0,
                    shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-04',
            title: 'Function processShippingAmount - Discount amount of rule (any rule) is 0',
            input: {
                address: {
                    shipping_amount_for_discount: 5.1,
                    base_shipping_amount_for_discount: 5.1,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 2,
                    base_shipping_discount_amount: 2,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: ''
                },
                quote: {
                    applied_rule_ids: '',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 0,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 0
            },
            expect: {
                address: {
                    shipping_amount_for_discount: 5.1,
                    base_shipping_amount_for_discount: 5.1,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '1',
                    shipping_discount_amount: 2,
                    base_shipping_discount_amount: 2,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 0,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-05',
            title: 'Function processShippingAmount - Rule is not apply for shipping',
            input: {
                address: {
                    shipping_amount_for_discount: 5.1,
                    base_shipping_amount_for_discount: 5.1,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 2,
                    base_shipping_discount_amount: 2,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: ''
                },
                quote: {
                    applied_rule_ids: '',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'by_fixed',
                            apply_to_shipping: false
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 0
            },
            expect: {
                address: {
                    shipping_amount_for_discount: 5.1,
                    base_shipping_amount_for_discount: 5.1,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '',
                    shipping_discount_amount: 2,
                    base_shipping_discount_amount: 2,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'by_fixed',
                            apply_to_shipping: false
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-06',
            title: 'Function processShippingAmount - Not have any sales rule',
            input: {
                address: {
                    shipping_amount_for_discount: 5.1,
                    base_shipping_amount_for_discount: 5.1,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 2,
                    base_shipping_discount_amount: 2,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: ''
                },
                quote: {
                    applied_rule_ids: '',
                    valid_salesrule: [
                    ]
                }
            },
            mockOutput: {
                convert_amount: 0
            },
            expect: {
                address: {
                    shipping_amount_for_discount: 5.1,
                    base_shipping_amount_for_discount: 5.1,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '',
                    shipping_discount_amount: 2,
                    base_shipping_discount_amount: 2,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '',
                    valid_salesrule: [
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-07',
            title: 'Function processShippingAmount  \n' +
            '- Action ""By fixed"" \n' +
            '- Applied rule ids of quote and address is 1,2\n' +
            '- Shipping_discount_amount is 0\n' +
            '- Discount amount is greater than shipping amount',
            input: {
                address: {
                    shipping_amount_for_discount: 5.1,
                    base_shipping_amount_for_discount: 5.1,
                    shipping_amount: 0,
                    base_shipping_amount: 0,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: '1,2'
                },
                quote: {
                    applied_rule_ids: '1,2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 6
            },
            expect: {
                address: {
                    shipping_amount_for_discount: 5.1,
                    base_shipping_amount_for_discount: 5.1,
                    shipping_amount: 0,
                    base_shipping_amount: 0,
                    applied_rule_ids: '1,2',
                    shipping_discount_amount: 5.1,
                    base_shipping_discount_amount: 5.1,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '1,2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-08',
            title: 'Function processShippingAmount  \n' +
            '- Action "By fixed" \n' +
            '- Applied rule ids of quote and address is 2\n' +
            '- Shipping_discount_amount is 0\n' +
            '- Discount amount is equal or less than shipping amount',
            input: {
                address: {
                    shipping_amount_for_discount: 8.1,
                    base_shipping_amount_for_discount: 8.1,
                    shipping_amount: 9,
                    base_shipping_amount: 9,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: '2'
                },
                quote: {
                    applied_rule_ids: '2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 6
            },
            expect: {
                address: {
                    shipping_amount_for_discount: 8.1,
                    base_shipping_amount_for_discount: 8.1,
                    shipping_amount: 9,
                    base_shipping_amount: 9,
                    applied_rule_ids: '2,1',
                    shipping_discount_amount: 6,
                    base_shipping_discount_amount: 6,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '2,1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-09',
            title: 'Function processShippingAmount  \n' +
            '- Action "By fixed" \n' +
            '- Applied rule ids of quote and address is 2\n' +
            '- Shipping_discount_amount is 1.48\n' +
            '- Discount amount is greater than shipping amount',
            input: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 1.48,
                    base_shipping_discount_amount: 1.48,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: '2'
                },
                quote: {
                    applied_rule_ids: '2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 6
            },
            expect: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '2,1',
                    shipping_discount_amount: 5.1,
                    base_shipping_discount_amount: 5.1,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '2,1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'by_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-10',
            title: 'Function processShippingAmount  \n' +
            '- Action "By percent" \n' +
            '- Applied rule ids of quote and address is 2\n' +
            '- Shipping_discount_amount is 0\n' +
            '- Discount amount is greater than shipping amount',
            input: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: '2'
                },
                quote: {
                    applied_rule_ids: '2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 10,
                            simple_action: 'by_percent',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 6
            },
            expect: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '2,1',
                    shipping_discount_amount: 0.51,
                    base_shipping_discount_amount: 0.51,
                    shipping_discount_percent:10,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '2,1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 10,
                            simple_action: 'by_percent',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-11',
            title: 'Function processShippingAmount  \n' +
            '- Action "By cart fixed" \n' +
            '- Applied rule ids of quote and address is 2\n' +
            '- Cart fixed rule is []',
            input: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: '2'
                },
                quote: {
                    applied_rule_ids: '2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'cart_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 6
            },
            expect: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '2,1',
                    shipping_discount_amount: 5.1,
                    base_shipping_discount_amount: 5.1,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {1: 0.9}
                },
                quote: {
                    applied_rule_ids: '2,1',
                        valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'cart_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-12',
            title: 'Function processShippingAmount  \n' +
            '- Action "By cart fixed" \n' +
            '- Applied rule ids of quote and address is 2\n' +
            '- Cart fixed rule has amount is 0',
            input: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {1: 0},
                    applied_rule_ids: '2'
                },
                quote: {
                    applied_rule_ids: '2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'cart_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 6
            },
            expect: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '2,1',
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {1: 0}
                },
                quote: {
                    applied_rule_ids: '2,1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'cart_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-13',
            title: 'Function processShippingAmount  \n' +
            '- Action "By cart fixed" \n' +
            '- Applied rule ids of quote and address is 2\n' +
            '- Cart fixed rule has amount is less than shipping amount',
            input: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {1: 3},
                    applied_rule_ids: '2'
                },
                quote: {
                    applied_rule_ids: '2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'cart_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 3
            },
            expect: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '2,1',
                    shipping_discount_amount: 3,
                    base_shipping_discount_amount: 3,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {1: 0}
                },
                quote: {
                    applied_rule_ids: '2,1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'cart_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-14',
            title: 'Function processShippingAmount  \n' +
            '- Action "By cart fixed" \n' +
            '- Applied rule ids of quote and address is 2\n' +
            '- Cart fixed rule has amount is greater than shipping amount ',
            input: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {1: 5.5},
                    applied_rule_ids: '2'
                },
                quote: {
                    applied_rule_ids: '2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'cart_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 5.5
            },
            expect: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 5.1,
                    base_shipping_amount: 5.1,
                    applied_rule_ids: '2,1',
                    shipping_discount_amount: 5.1,
                    base_shipping_discount_amount: 5.1,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {1: 0.4}
                },
                quote: {
                    applied_rule_ids: '2,1',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6,
                            simple_action: 'cart_fixed',
                            apply_to_shipping: true
                        }
                    ]
                }
            }
        },
        {
            testCaseId: 'DIS-VS-15',
            title: 'Function processShippingAmount - sale rule is custom discount',
            input: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 0,
                    base_shipping_amount: 0,
                    shipping_discount_amount: 0,
                    base_shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {},
                    applied_rule_ids: '2'
                },
                quote: {
                    applied_rule_ids: '2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'POS_CUSTOM_DISCOUNT',
                            apply_to_shipping: false
                        }
                    ]
                }
            },
            mockOutput: {
                convert_amount: 5
            },
            expect: {
                address: {
                    shipping_amount_for_discount: null,
                    base_shipping_amount_for_discount: null,
                    shipping_amount: 0,
                    base_shipping_amount: 0,
                    applied_rule_ids: '2',
                    base_shipping_discount_amount: 0,
                    shipping_discount_amount: 0,
                    shipping_discount_percent: 0,
                    cart_fixed_rules: {}
                },
                quote: {
                    applied_rule_ids: '2',
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5,
                            simple_action: 'POS_CUSTOM_DISCOUNT',
                            apply_to_shipping: false
                        }
                    ]
                }
            }
        },
    ];

    /* Begin test processShippingAmount function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let quote = data[i].input.quote;
            let address = data[i].input.address;

            when(CurrencyHelper.convert)
                .calledWith(data[i].mockOutput.convert_amount)
                .mockReturnValue(data[i].mockOutput.convert_amount);

            SalesRuleValidatorService.processShippingAmount(quote, address);

            expect(quote).toEqual(data[i].expect.quote);
            expect(address).toEqual(data[i].expect.address);
        });
    }
    /* End test processShippingAmount function*/
});

describe('Test getItemPrice function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.getCalculationPrice = QuoteItemService.getCalculationPrice;
        QuoteItemService.getCalculationPrice = jest.fn();
    });
    afterAll(() => {
        QuoteItemService.getCalculationPrice = mocksBackup.getCalculationPrice;
    });

    let data = [
        {
            testCaseId: 'DIS-VS-25',
            title: 'Function getItemPrice - item discount_calculation_price is null',
            input: {
                item: {
                    discount_calculation_price: null,
                    calPrice: 2
                }
            },
            expect: {
                price: 2
            }
        },
        {
            testCaseId: 'DIS-VS-26',
            title: 'Function getItemPrice - item discount_calculation_price is null',
            input: {
                item: {
                    calPrice: 2
                }
            },
            expect: {
                price: 2
            }
        },
        {
            testCaseId: 'DIS-VS-27',
            title: 'Function getItemPrice - item discount_calculation_price is null',
            input: {
                item: {
                    discount_calculation_price: 1,
                    calPrice: 2
                }
            },
            expect: {
                price: 1
            }
        },
    ];

    /* Begin test getItemPrice function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let item = data[i].input.item;

            when(QuoteItemService.getCalculationPrice).calledWith(item).mockReturnValue(item.calPrice);

            let result = SalesRuleValidatorService.getItemPrice(item);

            expect(result).toEqual(data[i].expect.price)
        });
    }
    /* End test getItemPrice function*/
});

describe('Test getItemBasePrice function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.getBaseCalculationPrice = QuoteItemService.getBaseCalculationPrice;
        QuoteItemService.getBaseCalculationPrice = jest.fn();
    });
    afterAll(() => {
        QuoteItemService.getBaseCalculationPrice = mocksBackup.getBaseCalculationPrice;
    });

    let data = [
        {
            testCaseId: 'DIS-VS-28',
            title: 'Function getBaseItemPrice - item base_discount_calculation_price is null',
            input: {
                item: {
                    base_discount_calculation_price: null,
                    calPrice: 2
                }
            },
            expect: {
                price: 2
            }
        },
        {
            testCaseId: 'DIS-VS-29',
            title: 'Function getBaseItemPrice - item base_discount_calculation_price is undefined',
            input: {
                item: {
                    calPrice: 2
                }
            },
            expect: {
                price: 2
            }
        },
        {
            testCaseId: 'DIS-VS-30',
            title: 'Function getBaseItemPrice - item base_discount_calculation_price is indentified',
            input: {
                item: {
                    base_discount_calculation_price: 1,
                    calPrice: 2
                }
            },
            expect: {
                price: 1
            }
        },
    ];

    /* Begin test getItemBasePrice function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let item = data[i].input.item;

            when(QuoteItemService.getBaseCalculationPrice).calledWith(item).mockReturnValue(item.calPrice);

            let result = SalesRuleValidatorService.getItemBasePrice(item);

            expect(result).toEqual(data[i].expect.price)
        });
    }
    /* End test getItemBasePrice function*/
});

describe('Test getItemOriginalPrice function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.getTaxPrice = CatalogDataService.getTaxPrice;
        mocksBackup.getOriginalPrice = QuoteItemService.getOriginalPrice;
    });
    afterAll(() => {
        CatalogDataService.getTaxPrice = mocksBackup.getTaxPrice;
        QuoteItemService.getOriginalPrice = mocksBackup.getOriginalPrice;
    });

    let data = [
        {
            testCaseId: 'DIS-VS-31',
            title: 'Function getItemOriginalPrice',
            input: {
            },
            expect: {
                calledTime: 1
            }
        }
    ];

    /* Begin test getItemOriginalPrice function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let item = {};
            let quote = {};
            CatalogDataService.getTaxPrice = jest.fn();
            QuoteItemService.getOriginalPrice = jest.fn();

            SalesRuleValidatorService.getItemOriginalPrice(item, quote);

            expect(CatalogDataService.getTaxPrice.mock.calls.length).toBe(data[i].expect.calledTime)
        });
    }
    /* End test getItemOriginalPrice function*/
});

describe('Test getItemOriginalPrice function', () => {
    let mocksBackup = {};
    beforeAll(() => {
        mocksBackup.getTaxPrice = CatalogDataService.getTaxPrice;
        mocksBackup.getOriginalPrice = QuoteItemService.getOriginalPrice;
    });
    afterAll(() => {
        CatalogDataService.getTaxPrice = mocksBackup.getTaxPrice;
        QuoteItemService.getOriginalPrice = mocksBackup.getOriginalPrice;
    });

    let data = [
        {
            testCaseId: 'DIS-VS-32',
            title: 'Function getItemBaseOriginalPrice ',
            input: {
            },
            expect: {
                calledTime: 1
            }
        }
    ];

    /* Begin test getItemBaseOriginalPrice function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let item = {};
            let quote = {};
            CatalogDataService.getTaxPrice = jest.fn();
            QuoteItemService.getOriginalPrice = jest.fn();

            SalesRuleValidatorService.getItemBaseOriginalPrice(item, quote);

            expect(CatalogDataService.getTaxPrice.mock.calls.length).toBe(data[i].expect.calledTime)
        });
    }
    /* End test getItemBaseOriginalPrice function*/
});

describe('Test sortItemsByPriority function', () => {
    let data = [
        {
            testCaseId: 'DIS-VS-40',
            title: 'Function sortItemsByPriority - quote valid_salesrule is null',
            input: {
                quote: {
                    valid_salesrule: []
                },
                items: [
                    {item_id: 1},
                    {item_id: 2},
                    {item_id: 3}
                ]
            },
            expect: {
                result:  [
                    {item_id: 1},
                    {item_id: 2},
                    {item_id: 3}
                ]
            }
        },
        {
            testCaseId: 'DIS-VS-41',
            title: 'Function sortItemsByPriority - quote valid_salesrule is not null',
            input: {
                quote: {
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            sort_order: 2,
                            valid_item_ids: '2,3'
                        },
                        {
                            rule_id: 2,
                            sort_order: 1,
                            valid_item_ids: '1,3'
                        }
                    ]
                },
                items: [
                    {item_id: 1},
                    {item_id: 2},
                    {item_id: 3},
                    {item_id: 4}
                ]
            },
            expect: {
                result:  [
                    {item_id: 1},
                    {item_id: 3},
                    {item_id: 2},
                    {item_id: 4}
                ]
            }
        },
        {
            testCaseId: 'DIS-VS-42',
            title: 'Function sortItemsByPriority - items is null',
            input: {
                quote: {
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            sort_order: 2,
                            valid_item_ids: '2,3'
                        },
                        {
                            rule_id: 2,
                            sort_order: 1,
                            valid_item_ids: '1,3'
                        }
                    ]
                },
                items: []
            },
            expect: {
                result:  []
            }
        },
    ];

    /* Begin test sortItemsByPriority function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            let quote = data[i].input.quote;
            let items = data[i].input.items;
            SalesRuleValidatorService.rulesItemTotals = data[i].input.mock_data;
            let result = SalesRuleValidatorService.sortItemsByPriority(quote, items);
            expect(result).toEqual(data[i].expect.result);
        });
    }
    /* End test sortItemsByPriority function*/
});

describe('Test getRuleItemTotalsInfo function', () => {
    let data = [
        {
            testCaseId: 'DIS-VS-33',
            title: 'Function getRuleItemTotalsInfo - key is null',
            input: {
                key: '',
                mock_data: {
                    1: {items_count: 1}
                }
            },
            expect: {
                result: {}
            }
        },
        {
            testCaseId: 'DIS-VS-34',
            title: 'Function getRuleItemTotalsInfo - key is not exist on data',
            input: {
                key: 2,
                mock_data: {
                    1: {items_count: 1}
                }
            },
            expect: {
                result: {}
            }
        },
        {
            testCaseId: 'DIS-VS-35',
            title: 'Function getRuleItemTotalsInfo - key is exist on data',
            input: {
                key: 1,
                mock_data: {
                    1: {items_count: 1}
                }
            },
            expect: {
                result: {items_count: 1}
            }
        },
    ];

    /* Begin test getRuleItemTotalsInfo function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            SalesRuleValidatorService.rulesItemTotals = data[i].input.mock_data;
            let result = SalesRuleValidatorService.getRuleItemTotalsInfo(data[i].input.key);
            expect(result).toEqual(data[i].expect.result);
        });
    }
    /* End test getRuleItemTotalsInfo function*/
});

describe('Test decrementRuleItemTotalsCount function', () => {
    let data = [
        {
            testCaseId: 'DIS-VS-36',
            title: 'Function decrementRuleItemTotalsCount - key is null',
            input: {
                key: '',
                mock_data: {
                    1: {items_count: 3}
                }
            },
            expect: {
                mock_data: {
                    1: {
                        items_count: 3
                    }
                }
            }
        },
        {
            testCaseId: 'DIS-VS-37',
            title: 'Function decrementRuleItemTotalsCount - key is exist on data',
            input: {
                key: 1,
                mock_data: {
                    1: {items_count: 3}
                }
            },
            expect: {
                mock_data: {
                    1: {
                        items_count: 2
                    }
                }
            }
        },
        {
            testCaseId: 'DIS-VS-38',
            title: 'Function decrementRuleItemTotalsCount - key is not exist on data',
            input: {
                key: 2,
                mock_data: {
                    1: {items_count: 3}
                }
            },
            expect: {
                mock_data: {
                    1: {
                        items_count: 3
                    }
                }
            }
        },
        {
            testCaseId: 'DIS-VS-39',
            title: 'Function decrementRuleItemTotalsCount - key is exist on data but data not have items_count',
            input: {
                key: 2,
                mock_data: {
                    1: {}
                }
            },
            expect: {
                mock_data: {
                    1: {}
                }
            }
        },
    ];

    /* Begin test decrementRuleItemTotalsCount function*/
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            SalesRuleValidatorService.rulesItemTotals = data[i].input.mock_data;

            SalesRuleValidatorService.decrementRuleItemTotalsCount(data[i].input.key);

            expect(SalesRuleValidatorService.rulesItemTotals).toEqual(data[i].expect.mock_data);
        });
    }
    /* End test decrementRuleItemTotalsCount function*/
});