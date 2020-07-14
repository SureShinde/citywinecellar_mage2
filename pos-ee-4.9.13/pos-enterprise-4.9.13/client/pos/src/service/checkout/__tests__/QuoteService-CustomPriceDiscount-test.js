import QuoteService from '../QuoteService';
import Config from "../../../config/Config";

describe('Integration test calculate custom price discount', () => {

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
                    code: "USD",
                    currency_name: "US Dollar",
                    currency_rate: 1,
                    currency_symbol: "$",
                    is_default: 1,
                },
            ],
            current_currency_code: "USD",
        };
        Config.location_address = {};
    });

    let data = [
        {
            testCaseId: 'CDP-001',
            title: '',
            input: {
                qty: 3,
                customPriceDiscountValue: 100,
                customPriceDiscountType: '%',
                reason: 'Test CDP-001',
                unitPrice: 7,
                changeQty: true,
            },
            expect: {
                customPrice: 0,
                originalPrice: 7,
                customPriceDiscountValue: 100,
                customPriceDiscountType: '%',
                reason: 'Test CDP-001',
                unitCustomPriceDiscount: 7
            },
        },
        {
            testCaseId: 'CDP-002',
            title: '',
            input: {
                qty: 3,
                customPriceDiscountValue: 7,
                customPriceDiscountType: '$',
                reason: 'Test CDP-002',
                unitPrice: 7,
                changeQty: true,
            },
            expect: {
                customPrice: 0,
                originalPrice: 7,
                customPriceDiscountValue: 7,
                customPriceDiscountType: '$',
                reason: 'Test CDP-002',
                unitCustomPriceDiscount: 7
            },
        },
        {
            testCaseId: 'CDP-003',
            title: '',
            input: {
                qty: 3,
                customPriceDiscountValue: 50,
                customPriceDiscountType: '%',
                reason: 'Test CDP-003',
                unitPrice: 3.5,
                changeQty: true,
            },
            expect: {
                customPrice: 3.5,
                originalPrice: 7,
                customPriceDiscountValue: 50,
                customPriceDiscountType: '%',
                reason: 'Test CDP-003',
                unitCustomPriceDiscount: 3.5
            },
        },
        {
            testCaseId: 'CDP-004',
            title: '',
            input: {
                qty: 3,
                customPriceDiscountValue: 2,
                customPriceDiscountType: '$',
                reason: 'Test CDP-004',
                unitPrice: 2,
                changeQty: true,
            },
            expect: {
                customPrice: 5,
                originalPrice: 7,
                customPriceDiscountValue: 2,
                customPriceDiscountType: '$',
                reason: 'Test CDP-004',
                unitCustomPriceDiscount: 2
            },
        },
        {
            testCaseId: 'CDP-005',
            title: '',
            input: {
                qty: 1,
                customPriceDiscountValue: 110,
                customPriceDiscountType: '%',
                reason: 'Test CDP-005',
                unitPrice: null,
                changeQty: false,
            },
            expect: {
                customPrice: 0,
                originalPrice: 7,
                customPriceDiscountValue: 100,
                customPriceDiscountType: '%',
                reason: 'Test CDP-005',
                unitCustomPriceDiscount: 7
            },
        },
        {
            testCaseId: 'CDP-006',
            title: '',
            input: {
                qty: 3,
                customPriceDiscountValue: 2,
                customPriceDiscountType: '$',
                reason: 'Test CDP-006',
                unitPrice: null,
                changeQty: false,
            },
            expect: {
                customPrice: 5,
                originalPrice: 7,
                customPriceDiscountValue: 2,
                customPriceDiscountType: '$',
                reason: 'Test CDP-006',
                unitCustomPriceDiscount: 2
            },
        },
        {
            testCaseId: 'CDP-007',
            title: '',
            input: {
                qty: 1,
                customPriceDiscountValue: 50,
                customPriceDiscountType: '%',
                reason: 'Test CDP-007',
                unitPrice: null,
                changeQty: false,
            },
            expect: {
                customPrice: 3.5,
                originalPrice: 7,
                customPriceDiscountValue: 50,
                customPriceDiscountType: '%',
                reason: 'Test CDP-007',
                unitCustomPriceDiscount: 3.5
            },
        },
        {
            testCaseId: 'CDP-008',
            title: '',
            input: {
                qty: 3,
                customPriceDiscountValue: 10,
                customPriceDiscountType: '$',
                reason: 'Test CDP-008',
                unitPrice: null,
                changeQty: false,
            },
            expect: {
                customPrice: 0,
                originalPrice: 7,
                customPriceDiscountValue: 7,
                customPriceDiscountType: '$',
                reason: 'Test CDP-008',
                unitCustomPriceDiscount: 7
            },
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
            let item = {
                product: {price: 7,},
                qty: testCase.input.qty,
            };
            let quote = {
                addresses: [],
                items: [item],
            };
            let result = QuoteService.calculateCustomPriceDiscount(
                quote,
                item,
                testCase.input.qty,
                testCase.input
            );

            Object.keys(testCase.expect).forEach(key => {
                expect(result[key]).toBe(testCase.expect[key])
            })

        });
    });
});
