import DiscountService from '../DiscountService';
import Config from "../../../../../config/Config";
import AddressConstant from "../../../../../view/constant/checkout/quote/AddressConstant";

describe('Test integration apply sales rule with multiple currency', () => {
    beforeAll(() => {
        // Mock config for test env
        Config.config = {
            guest_customer: {},
            settings: [
                {path: "tax/calculation/algorithm", value: "TOTAL_BASE_CALCULATION"},
                {path: "customer/create_account/default_group", value: "1"},
            ],
            customer_groups: [
                {id: 0, code: "NOT LOGGED IN", tax_class_id: 3},
                {id: 1, code: "General", tax_class_id: 3},
            ],
            currencies: [
                {
                    code: "EUR",
                    currency_name: "Euro",
                    currency_rate: 1,
                    currency_symbol: "€",
                    is_default: 0
                },
                {
                    code: "USD",
                    currency_name: "US Dollar",
                    currency_rate: 1,
                    currency_symbol: "$",
                    is_default: 1,
                },
            ],
            current_currency_code: "EUR",
            max_discount_percent: 100,
        };
        Config.location_address = {};
    });

    /* Data for test */
    let data = [
        {
            testCaseId: 'DIS-MUL-001',
            title: 'Discount Multi Currency Rate = 3 and percent = 99.99',
            currency_rate: "3",

            input: {
                address: {
                    address_type: AddressConstant.SHIPPING_ADDRESS_TYPE
                },

                quote: {
                    applied_rule_ids: '',
                    items: [
                        {
                            item_id: 1544422386218,
                            price: 30,
                            is_virtual: 0,
                            product_type: "simple",
                            qty: 1
                        }
                    ],
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 99.99,
                            simple_action: 'by_percent',
                            valid_item_ids: [1544422386218],
                            apply_to_shipping: true
                        }
                    ]
                }
            },

            expect: {
                base_discount_amount: -30,
                discount_amount: -89.99,
            },
        },

        {
            testCaseId: 'DIS-MUL-002',
                title: 'Discount Multi Currency Rate = 3 and percent = 6.22 (Round Up)',
            currency_rate: "3",

            input: {
                address: {
                    address_type: AddressConstant.SHIPPING_ADDRESS_TYPE
                },

                quote: {
                    applied_rule_ids: '',
                    items: [
                        {
                            item_id: 1544422386218,
                            price: 30,
                            is_virtual: 0,
                            product_type: "simple",
                            qty: 1
                        }
                    ],
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 6.22,
                            simple_action: 'by_percent',
                            valid_item_ids: [1544422386218],
                            apply_to_shipping: true
                        }
                    ]
                }
            },

            expect: {
                base_discount_amount: -1.87,
                discount_amount: -5.60
            },
        },

        {
            testCaseId: 'DIS-MUL-003',
            title: 'Discount Multi Currency Rate = 0.03 and percent = 6.22 (Round Down)',
            currency_rate: "0.03",

            input: {
                address: {
                    address_type: AddressConstant.SHIPPING_ADDRESS_TYPE
                },

                quote: {
                    applied_rule_ids: '',
                    items: [
                        {
                            item_id: 1544422386218,
                            price: 30,
                            is_virtual: 0,
                            product_type: "simple",
                            qty: 1
                        }
                    ],
                    valid_salesrule: [
                        {
                            rule_id: 1,
                            discount_amount: 5.67,
                            simple_action: 'by_percent',
                            valid_item_ids: [1544422386218],
                            apply_to_shipping: true
                        }
                    ]
                }
            },

            expect: {
                base_discount_amount: -1.7,
                discount_amount: -0.05
            },
        },
    ];

    /* Begin test */
    data.forEach(testCase => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
            Config.config.currencies[0].currency_rate = testCase.currency_rate;

            let total = {};

            // Check output
            DiscountService.collect(testCase.input.quote, testCase.input.address, total);

            expect(total.base_discount_amount).toBe(testCase.expect.base_discount_amount);
            expect(total.discount_amount).toBe(testCase.expect.discount_amount);
        });
    });
    /* End: Test */
});
