import QuoteService from '../QuoteService'

/* Data for test case */
let data = [
    {
        testCaseId: 'TP-01',
        title: 'All Payments are pay later.',
        quote: {
            payments: [
                {
                    is_pay_later: 1,
                    base_amount_paid: 2.48,
                    amount_paid: 2.48
                },

                {
                    is_pay_later: 1,
                    base_amount_paid: 2,
                    amount_paid: 2
                }
            ],
            base_grand_total: 4.96,
            grand_total: 4.96
        },
        expect: {
            base_total_paid: 0,
            total_paid: 0,
        }
    },
    {
        testCaseId: 'TP-02',
        title: 'One payment method is pay later. One payment method is not pay later. ' +
            'Sum Total Paid Amount is smaller than grand total.',
        quote: {
            payments: [
                {
                    is_pay_later: 1,
                    base_amount_paid: 2.48,
                    amount_paid: 2.48
                },

                {
                    is_pay_later: 0,
                    base_amount_paid: 2,
                    amount_paid: 2
                }
            ],
            base_grand_total: 4.96,
            grand_total: 4.96
        },
        expect: {
            base_total_paid: 2,
            total_paid: 2,
        }
    },
    {
        testCaseId: 'TP-03',
        title: 'One payment method is pay later. One payment method is not pay later. ' +
            'Sum Total Paid Amount is greater  than grand total.',
        quote: {
            payments: [
                {
                    is_pay_later: 1,
                    base_amount_paid: 2.48,
                    amount_paid: 2.48
                },

                {
                    is_pay_later: 0,
                    base_amount_paid: 5,
                    amount_paid: 5
                }
            ],
            base_grand_total: 4.96,
            grand_total: 4.96
        },
        expect: {
            base_total_paid: 4.96,
            total_paid: 4.96,
        }
    },
    {
        testCaseId: 'TP-04',
        title: 'All payment methods are not pay later. Sum total paid amount is equal grand total.',
        quote: {
            payments: [
                {
                    is_pay_later: 0,
                    base_amount_paid: 2.48,
                    amount_paid: 2.48
                },

                {
                    is_pay_later: 0,
                    base_amount_paid: 2.48,
                    amount_paid: 2.48
                }
            ],
            base_grand_total: 4.96,
            grand_total: 4.96
        },
        expect: {
            base_total_paid: 4.96,
            total_paid: 4.96,
        }
    },
    {
        testCaseId: 'TP-05',
        title: 'All payment methods are not pay later. Sum total paid amount is equal grand total. Both types are float: 0.1 & 0.2',
        quote: {
            payments: [
                {
                    is_pay_later: 0,
                    base_amount_paid: 0.1,
                    amount_paid: 0.1
                },

                {
                    is_pay_later: 0,
                    base_amount_paid: 0.2,
                    amount_paid: 0.2
                }
            ],
            base_grand_total: 0.3,
            grand_total: 0.3
        },
        expect: {
            base_total_paid: 0.3,
            total_paid: 0.3,
        }
    },
    {
        testCaseId: 'TP-06',
        title: 'All payment methods are not pay later. Sum total paid amount is equal grand total. Both types are float: 0.1 & 0.7',
        quote: {
            payments: [
                {
                    is_pay_later: 0,
                    base_amount_paid: 0.1,
                    amount_paid: 0.1
                },

                {
                    is_pay_later: 0,
                    base_amount_paid: 0.7,
                    amount_paid: 0.7
                }
            ],
            base_grand_total: 0.8,
            grand_total: 0.8
        },
        expect: {
            base_total_paid: 0.8,
            total_paid: 0.8,
        }
    }
];

/* Begin Test */
for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
        expect(QuoteService.getBaseTotalPaid(data[i].quote)).toEqual(data[i].expect.base_total_paid);
        expect(QuoteService.getTotalPaid(data[i].quote)).toEqual(data[i].expect.total_paid);
    });
}

/* Test for total due calculation */
let dataTotalDue = [
    {
        testCaseId: 'TD-01',
        title: 'Grand total is greater than total paid and both types are integer: 2 & 1',
        quote: {
            grand_total: 2,
            base_grand_total: 2,
            payments: [{
                is_pay_later: 0,
                base_amount_paid: 1,
                amount_paid: 1
            }]
        },
        expect: {
            base_total_due: 1,
            total_due: 1
        }
    },
    {
        testCaseId: 'TD-02',
        title: 'Grand total is greater than total paid and both types are float: 0.4 & 0.3',
        quote: {
            grand_total: 0.4,
            base_grand_total: 0.4,
            payments: [{
                is_pay_later: 0,
                base_amount_paid: 0.3,
                amount_paid: 0.3
            }]
        },
        expect: {
            base_total_due: 0.1,
            total_due: 0.1
        }
    },
    {
        testCaseId: 'TD-03',
        title: 'Grand total is greater than total paid and both types are float: 0.5 & 0.4',
        quote: {
            grand_total: 0.5,
            base_grand_total: 0.5,
            payments: [{
                is_pay_later: 0,
                base_amount_paid: 0.4,
                amount_paid: 0.4
            }]
        },
        expect: {
            base_total_due: 0.1,
            total_due: 0.1
        }
    },
    {
        testCaseId: 'TD-04',
        title: 'Total paid is 0',
        quote: {
            grand_total: 0.5,
            base_grand_total: 0.5,
            payments: [{
                is_pay_later: 1,
                base_amount_paid: 0.5,
                amount_paid: 0.5
            }]
        },
        expect: {
            base_total_due: 0.5,
            total_due: 0.5
        }
    },
    {
        testCaseId: 'TD-05',
        title: 'Grand total is less than total paid and both types are float: 1 & 2',
        quote: {
            grand_total: 1,
            base_grand_total: 1,
            payments: [{
                is_pay_later: 0,
                base_amount_paid: 2,
                amount_paid: 2
            }]
        },
        expect: {
            base_total_due: 0,
            total_due: 0
        }
    },
];
/* Begin test */
for (let i = 0; i < dataTotalDue.length; i++) {
    it(`[${dataTotalDue[i].testCaseId}] ${dataTotalDue[i].title}`, () => {
        expect(QuoteService.getBaseTotalDue(dataTotalDue[i].quote)).toEqual(dataTotalDue[i].expect.base_total_due);
        expect(QuoteService.getTotalDue(dataTotalDue[i].quote)).toEqual(dataTotalDue[i].expect.total_due);
    });
}
/* End: Test for total change calculation */

/* Test for total change calculation */
let dataTotalChange = [
    {
        testCaseId: 'TC-01',
        title: 'Total Paid is greater than grand total and both types are float 0.4 and 0.3',
        quote: {
            grand_total: 0.6,
            base_grand_total: 0.3,
            payments: [{
                is_pay_later: 0,
                base_amount_paid: 0.3,
                amount_paid: 0.6,
                base_amount_change: 0.1,
                amount_change: 0.2,
            }]
        },
        expect: {
            base_total_change: 0.1,
            total_change: 0.2
        }
    },
    {
        testCaseId: 'TC-02',
        title: 'Total Paid is greater than grand total and both types are float 0.5 and 0.4',
        quote: {
            grand_total: 0.8,
            base_grand_total: 0.4,
            payments: [{
                is_pay_later: 0,
                base_amount_paid: 0.4,
                amount_paid: 0.8,
                base_amount_change: 0.1,
                amount_change: 0.2,
            }]
        },
        expect: {
            base_total_change: 0.1,
            total_change: 0.2
        }
    },
    {
        testCaseId: 'TC-03',
        title: 'Total Paid is smaller grand total',
        quote: {
            grand_total: 1.6,
            base_grand_total: 0.8,
            payments: [{
                is_pay_later: 0,
                base_amount_paid: 0.4,
                amount_paid: 0.8,
                base_amount_change: 0,
                amount_change: 0,
            }]
        },
        expect: {
            base_total_change: 0,
            total_change: 0
        }
    },
    {
        testCaseId: 'TC-04',
        title: 'Total Paid is equal grand total',
        quote: {
            grand_total: 0.8,
            base_grand_total: 0.4,
            payments: [{
                is_pay_later: 0,
                base_amount_paid: 0.4,
                amount_paid: 0.8,
                base_amount_change: 0,
                amount_change: 0,
            }]
        },
        expect: {
            base_total_change: 0,
            total_change: 0
        }
    }
];
/* Begin test */
for (let i = 0; i < dataTotalChange.length; i++) {
    it(`[${dataTotalChange[i].testCaseId}] ${dataTotalChange[i].title}`, () => {
        expect(QuoteService.getBasePosChange(dataTotalChange[i].quote)).toEqual(dataTotalChange[i].expect.base_total_change);
        expect(QuoteService.getPosChange(dataTotalChange[i].quote)).toEqual(dataTotalChange[i].expect.total_change);
    });
}
/* End: Test for total change calculation */
