import PaymentOfflineService from "../PaymentOfflineService";
import ProductService from "../../../../service/catalog/ProductService";

describe('PaymentOfflineService-checkExistedPayLater', () => {
    let data = [
        {
            testCaseId: 'POS01',
            title: '',
            paymentData: [
                {
                    is_pay_later: true,
                    code: 'Offline Method 1'
                },
                {
                    is_pay_later: true,
                    code: 'Offline Method 2'
                }
            ],
            paymentsSelected: [
                {
                    method: 'Offline Method 1'
                }
            ],
            expect: true,
        },
        {
            testCaseId: 'POS02',
            title: '',
            paymentData: [
                {
                    is_pay_later: false,
                    code: 'Offline Method 1'
                },
                {
                    is_pay_later: true,
                    code: 'Offline Method 2'
                }
            ],
            paymentsSelected: [
                {
                    method: 'Offline Method 1'
                }
            ],
            expect: false,
        },
        {
            testCaseId: 'POS02',
            title: '',
            paymentData: [
                {
                    is_pay_later: true,
                    code: 'Offline Method 2'
                },
                {
                    is_pay_later: true,
                    code: 'Offline Method 3'
                }
            ],
            paymentsSelected: [
                {
                    method: 'Offline Method 1'
                }
            ],
            expect: false,
        }
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
            expect(Boolean(PaymentOfflineService.checkExistedPayLater(testCase.paymentData, testCase.paymentsSelected))).toEqual(testCase.expect);
        })
    })
});