import StateResolverService from "../StateResolverService";

/**
 *  link https://docs.google.com/spreadsheets/d/1t9OxHRxFMlXNrcccrYIb5JCPZhIMT_Sd1Tnv-5PQnBA/edit#gid=0
 */
describe('StateResolverService-test-isOrderComplete', () => {
    let data = [
        {
            testCaseId: 'SOSRSIOC01',
            title: '',
            order: {
                state: 'complete',
                base_grand_total: 0,
                total_paid: 0,
                total_refunded: 0,
                adjustment_negative: 0,
                extension_attributes: {}
            },
            expect: true,
        },
        {
            testCaseId: 'SOSRSIOC02',
            title: '',
            order: {
                state: 'canceled',
                base_grand_total: 0,
                total_paid: 0,
                total_refunded: 0,
                adjustment_negative: 0,
                extension_attributes: {},
            },
            expect: true,
        },
        {
            testCaseId: 'SOSRSIOC03',
            title: '',
            order: {
                state: 'closed',
                base_grand_total: 1,
                total_paid: 1,
                total_refunded: 0,
                adjustment_negative: 0,
                extension_attributes: {},
            },
            expect: false,
        },
        {
            testCaseId: 'SOSRSIOC04',
            title: '',
            order: {
                state: 'holded',
                base_grand_total: 1,
                total_paid: 0,
                total_refunded: 0,
                adjustment_negative: 0,
                extension_attributes: {},
            },
            expect: false,
        },
        {
            testCaseId: 'SOSRSIOC05',
            title: '',
            order: {
                state: 'payment_review',
                base_grand_total: 1,
                total_paid: 0,
                total_refunded: 0,
                adjustment_negative: 0,
                extension_attributes: {},
            },
            expect: false,
        },
        {
            testCaseId: 'SOSRSIOC06',
            title: '',
            order: {
                state: 'complete',
                base_grand_total: 1,
                total_paid: 1,
                total_refunded: 1,
                adjustment_negative: 0,
                extension_attributes: {},
            },
            expect: false,
        },
        {
            testCaseId: 'SOSRSIOC07',
            title: '',
            order: {
                state: 'complete',
                base_grand_total: 2,
                total_paid: 2,
                total_refunded: 1,
                adjustment_negative: 0,
                extension_attributes: {},
            },
            expect: true,
        },
        {
            testCaseId: 'SOSRSIOC08',
            title: '',
            order: {
                state: 'complete',
                base_grand_total: 2,
                total_paid: 2,
                total_refunded: 1,
                adjustment_negative: 1,
                extension_attributes: {},
            },
            expect: false,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
            let isOrderComplete = StateResolverService.isOrderComplete(testCase.order);
            expect(isOrderComplete).toBe(testCase.expect)
        })
    })
});