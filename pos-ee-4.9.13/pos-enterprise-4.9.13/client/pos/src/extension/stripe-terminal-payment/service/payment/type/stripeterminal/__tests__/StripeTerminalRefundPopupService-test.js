import "../__mocks__/Config";
import StripeTerminalPaymentService from "../__mocks__/StripeTerminalPaymentService";
import StripeTerminalRefundPopupService from "../StripeTerminalRefundPopupService";

let reinitStripeTerminalRefundPopupService = () => {
    StripeTerminalRefundPopupService.modal = undefined;
    StripeTerminalRefundPopupService.cssClassName = undefined;
    StripeTerminalRefundPopupService.dialogTitle = undefined;
    StripeTerminalRefundPopupService.dialogLoader = undefined;
    StripeTerminalRefundPopupService.dialogFooter = undefined;
    StripeTerminalRefundPopupService.dialogCancelButton = undefined;
    StripeTerminalRefundPopupService.closeTimeout = undefined;
    StripeTerminalRefundPopupService.dialogRetryButton = undefined;
};

describe('StripeTerminalRefundPopupService-getModalFooter', () => {
    afterEach(reinitStripeTerminalRefundPopupService);

    let data = [
        {
            testCaseId: 'STRPSGMF-01',
            setup: () => {
            },
            afterGetResult: () => {
                StripeTerminalRefundPopupService.dialogRetryButton.onclick();
            },
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalRefundPopupService.getModalFooter(() => {
            }, () => {
            });
            testCase.afterGetResult(result);
            expect(result).toBe(StripeTerminalRefundPopupService.dialogFooter);
        })
    })
});

describe('StripeTerminalRefundPopupService-startProcessTransaction', () => {
    afterEach(reinitStripeTerminalRefundPopupService);

    let data = [
        {
            testCaseId: 'STRPSSPT-01',
            setup: () => {
                StripeTerminalRefundPopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
            },
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalRefundPopupService.startProcessTransaction();
            expect(result).toBe(StripeTerminalRefundPopupService);
        })
    })
});

describe('StripeTerminalRefundPopupService-showError', () => {
    afterEach(reinitStripeTerminalRefundPopupService);

    let data = [
        {
            testCaseId: 'STRPSSM-01',
            message: 'lorem',
            setup: () => {
            },
            expect: false
        },
        {
            testCaseId: 'STRPSSM-02',
            message: 'lorem',
            setup: () => {
                StripeTerminalRefundPopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalRefundPopupService.dialogTitle = false;
            },
            expect: false
        },
        {
            testCaseId: 'STRPSSM-03',
            message: 'lorem',
            setup: () => {
                StripeTerminalRefundPopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                })
            },
            expect: StripeTerminalRefundPopupService
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalRefundPopupService.showError(testCase.message);
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalRefundPopupService-closePopup', () => {
    afterEach(reinitStripeTerminalRefundPopupService);

    let data = [
        {
            testCaseId: 'STRPSCP-01',
            setup: () => {
            },
            expect: undefined
        },
        {
            testCaseId: 'STRPSCP-02',
            setup: () => {
                StripeTerminalRefundPopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                })
            },
            expect: undefined
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalRefundPopupService.closePopup(() => {
            });
            expect(result).toBe(testCase.expect);
        })
    })
});




