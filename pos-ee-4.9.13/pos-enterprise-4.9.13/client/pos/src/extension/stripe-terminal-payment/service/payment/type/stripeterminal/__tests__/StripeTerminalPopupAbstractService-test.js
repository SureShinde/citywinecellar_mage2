import "../__mocks__/Config";
import StripeTerminalPaymentService from "../__mocks__/StripeTerminalPaymentService";
import StripeTerminalPopupAbstractService from "../StripeTerminalPopupAbstractService";


let reinitStripeTerminalPopupAbstractService = () => {
    StripeTerminalPopupAbstractService.modal = undefined;
    StripeTerminalPopupAbstractService.cssClassName = undefined;
    StripeTerminalPopupAbstractService.dialogTitle = undefined;
    StripeTerminalPopupAbstractService.dialogLoader = undefined;
    StripeTerminalPopupAbstractService.dialogFooter = undefined;
    StripeTerminalPopupAbstractService.dialogCancelButton = undefined;
    StripeTerminalPopupAbstractService.closeTimeout = undefined;
};

describe('StripeTerminalPopupAbstractService-createModal', () => {
    afterEach(reinitStripeTerminalPopupAbstractService);

    let data = [
        {
            testCaseId: 'STPASCM-01',
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            let result = StripeTerminalPopupAbstractService.createModal();
            expect(result).toBeTruthy();
        })
    })
});
describe('StripeTerminalPopupAbstractService-getModalHeader', () => {
    afterEach(reinitStripeTerminalPopupAbstractService);

    let data = [
        {
            testCaseId: 'STPASGMH-01',
        },
    ];


    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            let result = StripeTerminalPopupAbstractService.getModalHeader(StripeTerminalPaymentService);
            expect(result).toBeTruthy();
        })
    })
});

describe('StripeTerminalPopupAbstractService-getModalBody', () => {
    afterEach(reinitStripeTerminalPopupAbstractService);

    let data = [
        {
            testCaseId: 'STPASGMB-01',
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            let result = StripeTerminalPopupAbstractService.getModalBody();
            expect(result).toBeTruthy();
        })
    })
});

describe('StripeTerminalPopupAbstractService-getModalFooter', () => {
    afterEach(reinitStripeTerminalPopupAbstractService);

    let data = [
        {
            testCaseId: 'STPASGMF-01',
            setup: () => {
            },
            afterGetResult: (result) => {
            },
        },
        {
            testCaseId: 'STPASGMF-02',
            setup: () => {
            },
            afterGetResult: () => {
                StripeTerminalPopupAbstractService.dialogCancelButton.onclick();
            },
        },
        {
            testCaseId: 'STPASGMF-03',
            setup: () => {
                StripeTerminalPopupAbstractService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
            },
            afterGetResult: () => {
                StripeTerminalPopupAbstractService.dialogCancelButton.onclick();
            },
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalPopupAbstractService.getModalFooter(() => {
            }, () => {
            });
            testCase.afterGetResult(result);
            expect(result).toBe(StripeTerminalPopupAbstractService.dialogFooter);
        })
    })
});

describe('StripeTerminalPopupAbstractService-startProcessTransaction', () => {
    afterEach(reinitStripeTerminalPopupAbstractService);

    let data = [
        {
            testCaseId: 'STPASSPT-01',
            setup: () => {
                StripeTerminalPopupAbstractService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
            },
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalPopupAbstractService.startProcessTransaction();
            expect(result).toBe(StripeTerminalPopupAbstractService);
        })
    })
});

describe('StripeTerminalPopupAbstractService-getMessage', () => {
    afterEach(reinitStripeTerminalPopupAbstractService);

    let data = [
        {
            testCaseId: 'STPASGM-01',
            setup: () => {
            },
            expect: ''
        },
        {
            testCaseId: 'STPASGM-02',
            setup: () => {
                StripeTerminalPopupAbstractService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalPopupAbstractService.dialogTitle = false;
            },
            expect: ''
        },
        {
            testCaseId: 'STPASGM-03',
            setup: () => {
                StripeTerminalPopupAbstractService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                })
            },
            expect: '...'
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalPopupAbstractService.getMessage();
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalPopupAbstractService-closePopup', () => {
    afterEach(reinitStripeTerminalPopupAbstractService);

    let data = [
        {
            testCaseId: 'STPASCP-01',
            setup: () => {
            },
            expect: undefined
        },
        {
            testCaseId: 'STPASCP-02',
            setup: () => {
                StripeTerminalPopupAbstractService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                })
            },
            expect: undefined
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalPopupAbstractService.closePopup(() => {
            });
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalPopupAbstractService-showMessage', () => {
    afterEach(reinitStripeTerminalPopupAbstractService);

    let data = [
        {
            testCaseId: 'STPASSM-01',
            message: 'lorem',
            setup: () => {
            },
            expect: false
        },
        {
            testCaseId: 'STPASSM-02',
            message: 'lorem',
            setup: () => {
                StripeTerminalPopupAbstractService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalPopupAbstractService.dialogTitle = false;
            },
            expect: false
        },
        {
            testCaseId: 'STPASSM-03',
            message: 'lorem',
            setup: () => {
                StripeTerminalPopupAbstractService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                })
            },
            expect: StripeTerminalPopupAbstractService
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalPopupAbstractService.showMessage(testCase.message);
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalPopupAbstractService-showPopup', () => {
    afterEach(reinitStripeTerminalPopupAbstractService);

    let data = [
        {
            testCaseId: 'STPASSP-01',
            setup: () => {
            },
            expect: StripeTerminalPopupAbstractService
        },
        {
            testCaseId: 'STPASSP-02',
            setup: () => {
                StripeTerminalPopupAbstractService.closeTimeout = 1;
            },
            expect: StripeTerminalPopupAbstractService
        },
        {
            testCaseId: 'STPASSP-03',
            setup: () => {
                StripeTerminalPopupAbstractService.modal = {
                    remove: () => {
                    }
                };
                StripeTerminalPopupAbstractService.closeTimeout = 1;
            },
            expect: StripeTerminalPopupAbstractService
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalPopupAbstractService.showPopup(StripeTerminalPaymentService, () => {
            }, () => {
            });
            expect(result).toBe(testCase.expect);
        })
    })
});




