import Config from "../../../config/Config";
import QuoteService from "../QuoteService";
import PaymentService from "../../payment/PaymentService";

describe('Integration test total paid, total due, total change', () => {
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
                    currency_rate: 0.5,
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
        };
        Config.location_address = {};
    });

    /* Data for test */
    let data = [
        {
            testCaseId: 'PCD-001',
            title: 'Check total paid, total due, total change with currency convert rate 3 ' +
                'and paid is greater than grand total',
            input: {
                quote: {
                    grand_total: 0.01,
                    base_grand_total: 0.00,
                    payments: [
                        {
                            method: "cashforpos",
                            amount_paid: 5,
                            base_amount_paid: 1.67,
                            amount_change: 0,
                            base_amount_change: 0,
                            referenceNo: '',
                            is_pay_later: 0
                        }
                    ]
                },

                currency_rate: "3"
            },

            expect: {
                base_total_paid: 0.00,
                total_paid: 0.01,
                base_total_change: 1.66,
                total_change: 4.99,
                base_total_due: 0,
                total_due: 0,
            }
        },

        {
            testCaseId: 'PCD-002',
            title: 'Check total paid, total due, total change with currency convert rate 3 and ' +
                'paid is smaller than grand total',
            input: {
                quote: {
                    grand_total: 6,
                    base_grand_total: 2,
                    payments: [
                        {
                            method: "cashforpos",
                            amount_paid: 5,
                            base_amount_paid: 1.67,
                            amount_change: 0,
                            base_amount_change: 0,
                            referenceNo: '',
                            is_pay_later: 0
                        }
                    ]
                },

                currency_rate: "3"
            },

            expect: {
                base_total_paid: 1.67,
                total_paid: 5,
                base_total_change: 0.00,
                total_change: 0.00,
                base_total_due: 0.33,
                total_due: 1,
            },
        },

        {
            testCaseId: 'PCD-003',
            title: 'Check total paid, total due, total change with currency convert rate 3 ' +
                'and paid is equal grand total',
            input: {
                quote: {
                    grand_total: 6,
                    base_grand_total: 2,
                    payments: [
                        {
                            method: "cashforpos",
                            amount_paid: 6,
                            base_amount_paid: 2,
                            amount_change: 0,
                            base_amount_change: 0,
                            referenceNo: '',
                            is_pay_later: 0
                        }
                    ]
                },

                currency_rate: "3"
            },

            expect: {
                base_total_paid: 2,
                total_paid: 6,
                base_total_change: 0,
                total_change: 0,
                base_total_due: 0,
                total_due: 0,
            }
        }
    ];
    /* Begin test */
    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            Config.config.currencies[0].currency_rate = data[i].input.currency_rate;
            PaymentService.recalculatePaymentDataInQuote(data[i].input.quote);

            expect(QuoteService.getBaseTotalPaid(data[i].input.quote)).toEqual(data[i].expect.base_total_paid);
            expect(QuoteService.getTotalPaid(data[i].input.quote)).toEqual(data[i].expect.total_paid);
            expect(QuoteService.getBasePosChange(data[i].input.quote)).toEqual(data[i].expect.base_total_change);
            expect(QuoteService.getPosChange(data[i].input.quote)).toEqual(data[i].expect.total_change);
            expect(QuoteService.getBaseTotalDue(data[i].input.quote)).toEqual(data[i].expect.base_total_due);
            expect(QuoteService.getTotalDue(data[i].input.quote)).toEqual(data[i].expect.total_due);
        });
    }
    /* End: Test */
});

