import "../stripeterminal/__mocks__/Config";
import "../stripeterminal/__mocks__/sdk";
import StripeTerminalPaymentService from "../stripeterminal/__mocks__/StripeTerminalPaymentService";
import Config from "../../../../../../config/Config";
import StripeTerminalService from "../StripeTerminalService";
import sdk from "../stripeterminal/sdk";
import StripeTerminalRefundPopupService from "../stripeterminal/StripeTerminalRefundPopupService";
import StripeTerminalPurchasePopupService from "../stripeterminal/StripeTerminalPurchasePopupService";
import StripeTerminalConstant from "../../../../view/constant/payment/StripeTerminalConstant";
import PaymentConstant from "../../../../../../view/constant/PaymentConstant";
import CheckoutService from "../../../../../../service/checkout/CheckoutService";
import ConfigService from "../../../../../../service/config/ConfigService";

let simulatorReader = {
    id: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
    label: StripeTerminalConstant.DEVICE_TYPE_SIMULATED
};

let reinitStripeTerminalService = () => {
    StripeTerminalService.pendingPaymentIntentSecret = false;
    StripeTerminalService.pendingPaymentIntent = false;
    StripeTerminalService.canceled = false;
    StripeTerminalService.payment = null;
    StripeTerminalService.order = null;
    StripeTerminalService.creditmemo = null;
    StripeTerminalService.quote = null;
};

let reinitStripeTerminalPopupService = (PopupService) => {
    PopupService.modal = undefined;
    PopupService.cssClassName = undefined;
    PopupService.dialogTitle = undefined;
    PopupService.dialogLoader = undefined;
    PopupService.dialogFooter = undefined;
    PopupService.dialogCancelButton = undefined;
    PopupService.closeTimeout = undefined;
    PopupService.dialogRetryButton = undefined;
};

let reinitStripeTerminalRefundPopupService = () => {
    reinitStripeTerminalPopupService(StripeTerminalRefundPopupService);
};

let reinitStripeTerminalPurchasePopupService = () => {
    reinitStripeTerminalPopupService(StripeTerminalPurchasePopupService);
};

let reinitEnv = () => {
    reinitStripeTerminalService();
    reinitStripeTerminalRefundPopupService();
    reinitStripeTerminalPurchasePopupService();
};

describe('StripeTerminalService-closePopup', () => {
    afterEach(reinitEnv);

    let data = [
        {
            testCaseId: 'STSCP-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            popup: false,
            expect: StripeTerminalService
        },
        {
            testCaseId: 'STSCP-04',
            setup: () => {
                StripeTerminalPurchasePopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
            },
            afterGetResult: () => {
            },
            popup: StripeTerminalPurchasePopupService,
            expect: StripeTerminalService
        },
        {
            testCaseId: 'STSCP-02',
            setup: () => {
                StripeTerminalRefundPopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
            },
            afterGetResult: () => {
            },
            popup: false,
            expect: StripeTerminalService
        },
        {
            testCaseId: 'STSCP-03',
            setup: () => {
                StripeTerminalPurchasePopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
            },
            afterGetResult: () => {
            },
            popup: false,
            expect: StripeTerminalService
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalService.closePopup(testCase.popup);
            testCase.afterGetResult(result);
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalService-closePopupWithError', () => {
    afterEach(reinitEnv);

    let data = [
        {
            testCaseId: 'STSCPWE-01',
            setup: () => {
                StripeTerminalPurchasePopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                resolve: response => response,
                errorMessage: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, () => {
            testCase.setup();
            let result = StripeTerminalService.closePopupWithError(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-pollCallback', async () => {
    afterEach(reinitEnv);
    let mocks = {};

    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.doPost = StripeTerminalService.doPost;
        mocks.collectPaymentMethod = StripeTerminalService.constructor.terminal.collectPaymentMethod;
        mocks.processPayment = StripeTerminalService.constructor.terminal.processPayment;
        StripeTerminalService.constructor.terminal = sdk;
        StripeTerminalService.doPost = jest.fn(() => Promise.resolve({}));

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.constructor.terminal.collectPaymentMethod = mocks.collectPaymentMethod;
        StripeTerminalService.constructor.terminal.processPayment = mocks.processPayment;
        StripeTerminalService.doPost = mocks.doPost;
    });

    let data = [
        {
            testCaseId: 'STSPC-01',
            setup: () => {
                StripeTerminalService.canceled = true;
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: false,
                resolve: response => response,
                reject: response => response,
            },
            expect: false
        },
        {
            testCaseId: 'STSPC-02',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {error: {message: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}},
                resolve: response => response,
                reject: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}
        },
        {
            testCaseId: 'STSPC-03',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {status: false},
                resolve: response => response,
                reject: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.INVALID_PAYMENT_INTENT_EXCEPTION_MESSAGE}
        },
        {
            testCaseId: 'STSPC-04',
            setup: () => {
                StripeTerminalService.pendingPaymentIntent = {
                    charges: {
                        data: [{
                            payment_method_details: {
                                card_present: {
                                    brand: 'visa',
                                    last4: `************1111`
                                }
                            }
                        }]
                    }
                }
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.INTENT_STATUS_SUCCEEDED,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: StripeTerminalService
        },
        {
            testCaseId: 'STSPC-05',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.ERROR_STATUS,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}
        },
        {
            testCaseId: 'STSPC-06',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: PaymentConstant.TIMEOUT_STATUS,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.MESSAGE_TRANSACTION_STATUS_EXPIRED}
        },
        {
            testCaseId: 'STSPC-07',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: PaymentConstant.LOST_INTERNET_STATUS,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: StripeTerminalService
        },
        {
            testCaseId: 'STSPC-08',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.INTENT_STATUS_CANCELED,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: StripeTerminalService
        },
        {
            testCaseId: 'STSPC-09',
            setup: () => {

            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.INTENT_STATUS_REQUIRES_PAYMENT_METHOD,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: undefined,
        },
        {
            testCaseId: 'STSPC-10',
            setup: () => {
                StripeTerminalService.constructor.terminal.collectPaymentMethod = jest.fn(() => {
                    return Promise.resolve({error: {message: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}})
                });
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.INTENT_STATUS_REQUIRES_PAYMENT_METHOD,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.CONNECTION_ERROR_MESSAGE},
        },
        {
            testCaseId: 'STSPC-11',
            setup: () => {
                StripeTerminalService.constructor.terminal.processPayment = jest.fn(() => {
                    return Promise.resolve({error: {message: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}})
                });
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.INTENT_STATUS_REQUIRES_CONFIRMATION,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.CONNECTION_ERROR_MESSAGE},
        },
        {
            testCaseId: 'STSPC-12',
            setup: () => {
                StripeTerminalService.constructor.terminal.processPayment = jest.fn(() => {
                    return Promise.resolve({paymentIntent: {}})
                });
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.INTENT_STATUS_REQUIRES_CONFIRMATION,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: undefined,
        },
        {
            testCaseId: 'STSPC-13',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.INTENT_STATUS_REQUIRES_CAPTURE,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: undefined,
        },
        {
            testCaseId: 'STSPC-14',
            setup: () => {
                StripeTerminalService.doPost = jest.fn(() => {
                    return Promise.resolve({error: {message: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}})
                });
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.INTENT_STATUS_REQUIRES_CAPTURE,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.CONNECTION_ERROR_MESSAGE},
        },
        {
            testCaseId: 'STSPC-15',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                paymentIntent: {
                    status: StripeTerminalConstant.INTENT_STATUS_PROCESSING,
                    id: 1
                },
                resolve: response => response,
                reject: response => response,
            },
            expect: undefined,
        },
    ];


    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.pollCallback(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-getOrderPaymentDetail', () => {

    let data = [
        {
            testCaseId: 'STSGOPD-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                payment: {title: StripeTerminalConstant.TITLE, card_type: 'visa', cc_last4: '******1111'},
                prefix: '',
                excludePaymentMethod: false,
                excludeCardInformation: false,
            },
            expect: `${StripeTerminalConstant.TITLE} (******1111 - visa)`
        },
        {
            testCaseId: 'STSGOPD-02',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                payment: {title: StripeTerminalConstant.TITLE, card_type: 'visa', cc_last4: '******1111'},
                prefix: 'xxx',
                excludePaymentMethod: false,
                excludeCardInformation: false,
            },
            expect: `xxx${StripeTerminalConstant.TITLE} (******1111 - visa)`
        },
        {
            testCaseId: 'STSGOPD-03',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                payment: {title: StripeTerminalConstant.TITLE, card_type: 'visa', cc_last4: '******1111'},
                prefix: 'xxx',
                excludePaymentMethod: true,
                excludeCardInformation: false,
            },
            expect: `(******1111 - visa)`
        },
        {
            testCaseId: 'STSGOPD-04',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                payment: {title: StripeTerminalConstant.TITLE, card_type: 'visa', cc_last4: '******1111'},
                prefix: 'xxx',
                excludePaymentMethod: false,
                excludeCardInformation: true,
            },
            expect: `xxx${StripeTerminalConstant.TITLE}`
        },
        {
            testCaseId: 'STSGOPD-05',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                payment: {title: StripeTerminalConstant.TITLE, cc_last4: '******1111'},
                prefix: 'xxx',
                excludePaymentMethod: false,
                excludeCardInformation: false,
            },
            expect: `xxx${StripeTerminalConstant.TITLE} (******1111)`
        },
        {
            testCaseId: 'STSGOPD-06',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                payment: {title: StripeTerminalConstant.TITLE, card_type: 'visa'},
                prefix: 'xxx',
                excludePaymentMethod: false,
                excludeCardInformation: false,
            },
            expect: `xxx${StripeTerminalConstant.TITLE} (visa)`
        },
        {
            testCaseId: 'STSGOPD-07',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                payment: {title: StripeTerminalConstant.TITLE},
                prefix: 'xxx',
                excludePaymentMethod: false,
                excludeCardInformation: false,
            },
            expect: `xxx${StripeTerminalConstant.TITLE} `
        },
    ];


    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = StripeTerminalService.getOrderPaymentDetail(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalService-makeError', () => {

    let data = [
        {
            testCaseId: 'STSME-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            response: {error: {message: StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE}},
            expect: `${StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE}`
        },
        {
            testCaseId: 'STSME-02',
            setup: () => {
            },
            afterGetResult: () => {
            },
            response: {
                error: {
                    items: [{message: StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE}]
                }
            },
            expect: `${StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE}`
        },
        {
            testCaseId: 'STSME-03',
            setup: () => {
            },
            afterGetResult: () => {
            },
            response: {
                error: {
                    items: [
                        {message: StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE},
                        {message: StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE},
                    ]
                }
            },
            expect: `${StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE}`
        },
    ];


    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = StripeTerminalService.makeError(testCase.response);
            testCase.afterGetResult(result);
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalService-showError', () => {
    let data = [
        {
            testCaseId: 'STSSE-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalRefundPopupService,
                response: {error: {message: StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE}},
            },
            expect: false
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = StripeTerminalService.showError(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalService-getConfigPath', () => {

    let data = [
        {
            testCaseId: 'STSGCP-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            node: 'enable',
            expect: `${StripeTerminalConstant.CONFIG_PATH}/enable`
        },
        {
            testCaseId: 'STSGCP-02',
            setup: () => {
            },
            afterGetResult: () => {
            },
            node: false,
            expect: `${StripeTerminalConstant.CONFIG_PATH}`
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = StripeTerminalService.getConfigPath(testCase.node);
            testCase.afterGetResult(result);
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalService-isEnable', () => {
    let mocks = {};
    beforeEach(() => {
        mocks.Config = {settings: Config.config.settings};
    });

    beforeEach(() => {
        Config.config.settings = mocks.Config.settings;
    });

    let data = [
        {
            testCaseId: 'STSIE-01',
            setup: () => {
                Config.config.settings = [{path: StripeTerminalService.getConfigPath('enable'), value: '1'}]
            },
            afterGetResult: () => {
            },
            expect: true
        },
        {
            testCaseId: 'STSIE-02',
            setup: () => {
                Config.config.settings = [{path: StripeTerminalService.getConfigPath('enable'), value: '0'}]
            },
            afterGetResult: () => {
            },
            expect: false
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = StripeTerminalService.isEnable();
            testCase.afterGetResult(result);
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalService-fetchTransaction', () => {
    let mocks = {};
    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.fetchPaymentIntent = StripeTerminalService.constructor.terminal.fetchPaymentIntent;
        StripeTerminalService.constructor.terminal = sdk;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.constructor.terminal.fetchPaymentIntent = mocks.fetchPaymentIntent;
    });

    let data = [
        {
            testCaseId: 'STSFT-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            callback: response => response,
            expect: undefined
        },
        {
            testCaseId: 'STSFT-02',
            setup: () => {
                StripeTerminalService.constructor.terminal.fetchPaymentIntent = jest.fn(() => {
                    return {
                        internalPromise: Promise.resolve({error: {message: ''}}),
                    }
                });
            },
            afterGetResult: () => {
            },
            callback: response => response,
            expect: undefined
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.fetchTransaction(testCase.callback);
            testCase.afterGetResult(result);
            expect(result).toBe(testCase.expect);
        })
    })
});

describe('StripeTerminalService-cancelPendingPayment', () => {
    let mocks = {};
    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.cancelCollectPaymentMethod = StripeTerminalService.constructor.terminal.cancelCollectPaymentMethod;
        StripeTerminalService.constructor.terminal = sdk;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.constructor.terminal.cancelCollectPaymentMethod = mocks.cancelCollectPaymentMethod;
    });

    let data = [
        {
            testCaseId: 'STSCPP-01',
            setup: () => {
                StripeTerminalService.constructor.terminal.cancelCollectPaymentMethod = () => {
                    return Promise.resolve({})
                }
            },
            afterGetResult: () => {
            },
            callback: response => response,
            expect: {}
        },
        {
            testCaseId: 'STSCPP-02',
            setup: () => {
                StripeTerminalService.constructor.terminal.cancelCollectPaymentMethod = () => {
                    throw "Error"
                }
            },
            afterGetResult: () => {
            },
            callback: response => response,
            expect: false
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.cancelPendingPayment(testCase.callback);
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-updateLineItems', () => {
    let mocks = {};
    afterEach(reinitEnv);
    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.setReaderDisplay = StripeTerminalService.constructor.terminal.setReaderDisplay;
        StripeTerminalService.constructor.terminal = sdk;
        StripeTerminalService.constructor.terminal.setReaderDisplay = jest.fn(({type}) => ({type}))
    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.constructor.terminal.setReaderDisplay = mocks.setReaderDisplay;
    });

    let data = [
        {
            testCaseId: 'STSULI-01',
            setup: () => {
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
            },
            afterGetResult: () => {
            },
            items: [{name: 'Test Item'}],
            expect: {type: "cart"}
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.updateLineItems(testCase.items);
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-registerDevice', () => {
    let mocks = {};
    afterEach(reinitEnv);

    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.doPost = StripeTerminalService.doPost;
        StripeTerminalService.constructor.terminal = sdk;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.doPost = mocks.doPost;
    });

    let data = [
        {
            testCaseId: 'STSRD-01',
            setup: () => {
                StripeTerminalService.doPost = jest.fn(() => Promise.resolve(simulatorReader));
            },
            afterGetResult: () => {
            },
            params: {
                label: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                registration_code: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: simulatorReader
        },
        {
            testCaseId: 'STSRD-02',
            setup: () => {
                StripeTerminalService.doPost = jest.fn(() => {
                    throw "Error"
                });
            },
            afterGetResult: () => {
            },
            params: {
                label: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                registration_code: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: StripeTerminalConstant.ERROR_REGISTERING_EXCEPTION_MESSAGE
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.registerDevice(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-connectToReader', () => {
    let mocks = {};
    afterEach(reinitEnv);

    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.connectReader = StripeTerminalService.constructor.terminal.connectReader;
        mocks.saveConnectedReaderToPos = StripeTerminalService.saveConnectedReaderToPos;
        mocks.setConnectedReader = StripeTerminalService.setConnectedReader;
        mocks.doPost = StripeTerminalService.doPost;
        StripeTerminalService.constructor.terminal = sdk;

        StripeTerminalService.setConnectedReader = jest.fn(() => ({}));
        StripeTerminalService.doPost = jest.fn(() => Promise.resolve({}));

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.constructor.terminal.connectReader = mocks.connectReader;
        StripeTerminalService.saveConnectedReaderToPos = mocks.saveConnectedReaderToPos;
        StripeTerminalService.setConnectedReader = mocks.setConnectedReader;
        StripeTerminalService.doPost = mocks.doPost;
    });

    let data = [
        {
            testCaseId: 'STSCTR-01',
            setup: () => {
                StripeTerminalService.constructor.terminal.connectReader = jest.fn(() => {
                    return Promise.resolve({reader: simulatorReader})
                });
            },
            afterGetResult: () => {

            },
            params: {
                selectedReader: simulatorReader,
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: {reader: simulatorReader},
        },
        {
            testCaseId: 'STSCTR-02',
            setup: () => {
                StripeTerminalService.constructor.terminal.connectReader = jest.fn(() => {
                    throw "Error"
                });
            },
            afterGetResult: () => {
            },
            params: {
                selectedReader: simulatorReader,
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: StripeTerminalConstant.FAILED_TO_CONNECT_READER_EXCEPTION_MESSAGE,
        },
        {
            testCaseId: 'STSCTR-03',
            setup: () => {
                StripeTerminalService.constructor.terminal.connectReader = jest.fn(() => {
                    return Promise.resolve({error: {message: ''}})
                });
            },
            afterGetResult: () => {
            },
            params: {
                selectedReader: simulatorReader,
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: StripeTerminalConstant.FAILED_TO_CONNECT_READER_EXCEPTION_MESSAGE,
        },
        {
            testCaseId: 'STSCTR-04',
            setup: () => {
                StripeTerminalService.constructor.terminal.connectReader = jest.fn(() => {
                    return Promise.resolve({reader: simulatorReader})
                });
                StripeTerminalService.saveConnectedReaderToPos = jest.fn(() => {
                    return Promise.resolve({})
                });
            },
            afterGetResult: () => {
            },
            params: {
                selectedReader: simulatorReader,
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: {reader: simulatorReader},
        },
        {
            testCaseId: 'STSCTR-05',
            setup: () => {
                StripeTerminalService.constructor.terminal.connectReader = jest.fn(() => {
                    return Promise.resolve({reader: simulatorReader})
                });
                StripeTerminalService.saveConnectedReaderToPos = jest.fn(() => {
                    throw "Error";
                });
            },
            afterGetResult: () => {
            },
            params: {
                selectedReader: simulatorReader,
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: StripeTerminalConstant.FAILED_TO_CONNECT_READER_EXCEPTION_MESSAGE,
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.connectToReader(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-disconnectReader', () => {
    let mocks = {};
    afterEach(reinitEnv);

    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.disconnectReader = StripeTerminalService.constructor.terminal.disconnectReader;
        mocks.saveConnectedReaderToPos = StripeTerminalService.saveConnectedReaderToPos;
        StripeTerminalService.constructor.terminal = sdk;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.constructor.terminal.disconnectReader = mocks.disconnectReader;
        StripeTerminalService.saveConnectedReaderToPos = mocks.saveConnectedReaderToPos;
    });

    let data = [
        {
            testCaseId: 'STSDTR-01',
            setup: () => {
                StripeTerminalService.constructor.terminal.disconnectReader = jest.fn(() => {
                    return Promise.resolve({})
                });
                StripeTerminalService.saveConnectedReaderToPos = jest.fn(() => {
                    return Promise.resolve({})
                });
            },
            afterGetResult: () => {
            },
            params: {
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: undefined,
        },
        {
            testCaseId: 'STSDTR-02',
            setup: () => {
                StripeTerminalService.constructor.terminal.disconnectReader = jest.fn(() => {
                    throw "Error";
                });
                StripeTerminalService.saveConnectedReaderToPos = jest.fn(() => {
                    return Promise.resolve({})
                });
            },
            afterGetResult: () => {
            },
            params: {
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE,
        },
        {
            testCaseId: 'STSDTR-03',
            setup: () => {
                StripeTerminalService.constructor.terminal.disconnectReader = jest.fn(() => {
                    return Promise.resolve({})
                });
                StripeTerminalService.saveConnectedReaderToPos = jest.fn(() => {
                    throw "Error";
                });
            },
            afterGetResult: () => {
            },
            params: {
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE,
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.disconnectReader(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-registerAndConnectNewReader ', () => {
    let mocks = {};
    afterEach(reinitEnv);
    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.registerDevice = StripeTerminalService.registerDevice;
        StripeTerminalService.constructor.terminal = sdk;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.registerDevice = mocks.registerDevice;
    });

    let data = [
        {
            testCaseId: 'STSRACTR-01',
            setup: () => {
                StripeTerminalService.registerDevice = jest.fn(() => {
                    return Promise.resolve({})
                });
            },
            afterGetResult: () => {
            },
            params: {
                label: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                registration_code: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: {},
        },
        {
            testCaseId: 'STSRACTR-02',
            setup: () => {
                StripeTerminalService.registerDevice = jest.fn(() => {
                    throw "Error";
                });

            },
            afterGetResult: () => {
            },
            params: {
                label: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                registration_code: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                doneCallback: response => response,
                failCallback: response => response,
            },
            expect: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.registerAndConnectNewReader(
                ...Object.values(testCase.params),
            );
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-saveConnectedReaderToPos', () => {
    let mocks = {};
    // afterEach(reinitEnv);
    beforeAll(() => {
        // Mock functions
        mocks.doPost = StripeTerminalService.doPost;
    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.doPost = mocks.doPost;
    });


    let data = [
        {
            testCaseId: 'STSSCRTP-01',
            setup: () => {
                StripeTerminalService.doPost = jest.fn(() => Promise.resolve(simulatorReader));
            },
            afterGetResult: () => {
            },
            reader: simulatorReader,
            expect: simulatorReader,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.saveConnectedReaderToPos(
                testCase.reader,
            );
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-discoverReaders ', () => {
    let mocks = {};
    afterEach(reinitEnv);
    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.connectToReader = StripeTerminalService.connectToReader;
        mocks.getConnectedReader = StripeTerminalService.getConnectedReader;
        mocks.discoverReaders = StripeTerminalService.constructor.terminal.discoverReaders;
        StripeTerminalService.constructor.terminal = sdk;
    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.constructor.terminal.discoverReaders = mocks.discoverReaders;
        StripeTerminalService.connectToReader = mocks.connectToReader;
        StripeTerminalService.getConnectedReader = mocks.getConnectedReader;
    });

    let data = [
        {
            testCaseId: 'STSDR-01',
            setup: () => {
                StripeTerminalService.constructor.terminal.discoverReaders = jest.fn(() => {
                    return Promise.resolve({error: {message: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}})
                });
            },
            afterGetResult: () => {
            },
            params: {
                deviceType: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                doneCallback: response => response,
                failCallback: message => message,
            },
            expect: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE,
        },
        {
            testCaseId: 'STSDR-02',
            setup: () => {
                StripeTerminalService.constructor.terminal.discoverReaders = jest.fn(() => {
                    throw "Error";
                });
            },
            afterGetResult: () => {
            },
            params: {
                deviceType: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                doneCallback: response => response,
                failCallback: message => message,
            },
            expect: StripeTerminalConstant.FAILED_TO_READER_EXCEPTION_MESSAGE,
        },
        {
            testCaseId: 'STSDR-03',
            setup: () => {
                StripeTerminalService.constructor.terminal.discoverReaders = jest.fn(() => {
                    return Promise.resolve({discoveredReaders: [simulatorReader]});
                });
            },
            afterGetResult: () => {
            },
            params: {
                deviceType: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                doneCallback: response => response,
                failCallback: message => message,
            },
            expect: [simulatorReader],
        },
        {
            testCaseId: 'STSDR-04',
            setup: () => {
                StripeTerminalService.constructor.terminal.discoverReaders = jest.fn(() => {
                    return Promise.resolve({discoveredReaders: [{...simulatorReader, id: simulatorReader.id + ' '}]});
                });
                StripeTerminalService.getConnectedReader = jest.fn(() => {
                    return simulatorReader
                });
                StripeTerminalService.connectToReader = jest.fn(() => {
                    return Promise.resolve({error: {}})
                });
            },
            afterGetResult: () => {
            },
            params: {
                deviceType: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                doneCallback: response => response,
                failCallback: message => message,
            },
            expect: [{...simulatorReader, id: simulatorReader.id + ' '}],
        },
        {
            testCaseId: 'STSDR-05',
            setup: () => {
                StripeTerminalService.constructor.terminal.discoverReaders = jest.fn(() => {
                    return Promise.resolve({discoveredReaders: [{...simulatorReader, id: simulatorReader.id}]});
                });
                StripeTerminalService.getConnectedReader = jest.fn(() => {
                    return simulatorReader
                });
                StripeTerminalService.connectToReader = jest.fn(() => {
                    return Promise.resolve({reader: simulatorReader})
                });
            },
            afterGetResult: () => {
            },
            params: {
                deviceType: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
                doneCallback: response => response,
                failCallback: message => message,
            },
            expect: {reader: simulatorReader},
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.discoverReaders(
                ...Object.values(testCase.params),
            );
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-initializeBackendClientAndTerminal ', () => {
    afterEach(reinitEnv);

    let data = [
        {
            testCaseId: 'STSIBCAT-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            expect: {},
        },
        {
            testCaseId: 'STSIBCAT-02',
            setup: () => {
                StripeTerminalService.constructor.terminal = false;
            },
            afterGetResult: () => {
            },
            expect: {},
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.initializeBackendClientAndTerminal();
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-doPost ', () => {
    let mocks = {};
    let data = [
        {
            testCaseId: 'STSDP-01',
            setup: () => {
                // Mock functions
                mocks.StripeTerminalService = {
                    omc: StripeTerminalService.omc,
                    api_url: StripeTerminalService.api_url,
                };
                StripeTerminalService.omc = {
                    post: jest.fn((url, body) => {
                        return Promise.resolve({url, body});
                    })
                };

                StripeTerminalService.api_url = '';
            },
            afterGetResult: () => {
                StripeTerminalService.omc = mocks.StripeTerminalService.omc;
                StripeTerminalService.api_url = mocks.StripeTerminalService.api_url;
            },
            url: StripeTerminalConstant.END_POINT_PATH,
            body: {},
            expect: {url: StripeTerminalConstant.END_POINT_PATH, body: {}},
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.doPost(
                testCase.url,
                testCase.body
            );
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-capturePaymentIntent', () => {
    afterEach(reinitEnv);

    let mocks = {};

    beforeAll(() => {
        mocks.doPost = StripeTerminalService.doPost;
    });

    afterAll(() => {
        StripeTerminalService.doPost = mocks.doPost;
    });

    let data = [
        {
            testCaseId: 'STSCPI-01',
            setup: () => {
                StripeTerminalService.doPost = jest.fn((url, body) => body);
            },
            afterGetResult: () => {
            },
            paymentIntentId: {paymentIntentId: 1},
            expect: {
                request: {
                    payment_intent_id: 1
                }
            },
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.capturePaymentIntent(
                testCase.paymentIntentId,
            );
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-createPaymentIntent', () => {
    afterEach(reinitEnv);

    let mocks = {};

    beforeAll(() => {
        mocks.doPost = StripeTerminalService.doPost;
    });

    afterAll(() => {
        StripeTerminalService.doPost = mocks.doPost;
    });

    let data = [
        {
            testCaseId: 'STSCREPI-01',
            setup: () => {
                StripeTerminalService.doPost = jest.fn((url, body) => Promise.resolve(body));
            },
            afterGetResult: () => {
            },
            params: {amount: 1, currency: 'USD', description: ''},
            expect: {
                request: {amount: 1, currency: 'USD', description: ''}
            },
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.createPaymentIntent(
                testCase.params,
            );
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-createConnectionToken', () => {
    afterEach(reinitEnv);

    let mocks = {};

    beforeAll(() => {
        mocks.doPost = StripeTerminalService.doPost;
    });

    afterAll(() => {
        StripeTerminalService.doPost = mocks.doPost;
    });
    let data = [
        {
            testCaseId: 'STSCT-01',
            setup: () => {
                StripeTerminalService.doPost = jest.fn((url, body) => Promise.resolve(body));
            },
            afterGetResult: () => {
            },
            expect: {},
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.createConnectionToken();
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-removeConnectedReader', () => {
    let data = [
        {
            testCaseId: 'STSRCR-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            expect: undefined,
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.removeConnectedReader();
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-setConnectedReader', () => {
    let data = [
        {
            testCaseId: 'STSSCR-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {simulatorReader},
            expect: undefined,
        },

    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.setConnectedReader(...Object.values(simulatorReader));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-getConnectedReaderFromLocalStorage', () => {
    afterAll(() => {
        StripeTerminalService.setConnectedReader(false);
    });
    let data = [
        {
            testCaseId: 'STSGCRFS-01',
            setup: () => {
                StripeTerminalService.setConnectedReader(simulatorReader);
            },
            afterGetResult: () => {
            },
            expect: simulatorReader,
        },
        {
            testCaseId: 'STSGCRFS-02',
            setup: () => {
                StripeTerminalService.setConnectedReader(false);
            },
            afterGetResult: () => {
            },
            expect: false,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.getConnectedReaderFromLocalStorage();
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-getConnectedReaderFromConfig', () => {
    let data = [
        {
            testCaseId: 'STSGCRFC-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {config: {settings: []}},
            expect: false,
        },
        {
            testCaseId: 'STSGCRFC-02',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                config: {
                    settings: [{
                        path: StripeTerminalService.getConfigPath('connected_reader'),
                        value: JSON.stringify(simulatorReader)
                    }]
                }
            },
            expect: simulatorReader,
        },
        {
            testCaseId: 'STSGCRFC-03',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                config: false
            },
            expect: false,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.getConnectedReaderFromConfig(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-getConnectedReader', () => {
    afterAll(() => {
        StripeTerminalService.setConnectedReader(false);
    });
    let data = [
        {
            testCaseId: 'STSGCR-01',
            setup: () => {
                StripeTerminalService.setConnectedReader(simulatorReader);
            },
            afterGetResult: () => {
            },
            params: {config: false},
            expect: simulatorReader,
        },
        {
            testCaseId: 'STSGCR-02',
            setup: () => {
                StripeTerminalService.setConnectedReader(simulatorReader);
            },
            afterGetResult: () => {
            },
            params: {
                config: {
                    settings: [{
                        path: StripeTerminalService.getConfigPath('connected_reader'),
                        value: JSON.stringify(simulatorReader)
                    }]
                }
            },
            expect: simulatorReader,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.getConnectedReader(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-setDeviceType', () => {
    afterAll(() => {
        StripeTerminalService.setDeviceType(false);
    });
    let data = [
        {
            testCaseId: 'STSSDT-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {type: StripeTerminalConstant.DEVICE_TYPE_SIMULATED},
            expect: undefined,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.setDeviceType(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-getDeviceType', () => {
    afterAll(() => {
        StripeTerminalService.setDeviceType(false);
    });
    let data = [
        {
            testCaseId: 'STSGDT-01',
            setup: () => {
                StripeTerminalService.setDeviceType(StripeTerminalConstant.DEVICE_TYPE_SIMULATED);
            },
            afterGetResult: () => {
            },
            params: {},
            expect: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.getDeviceType();
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-tryCreateRefundRequest', () => {
    let mocks = {};
    afterEach(() => {
        reinitEnv();
    });
    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.doPost = StripeTerminalService.doPost;
        StripeTerminalService.constructor.terminal = sdk;
        StripeTerminalService.doPost = jest.fn(() => Promise.resolve(simulatorReader));

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.doPost = mocks.doPost;
    });
    let data = [
        {
            testCaseId: 'STSTCRR-01',
            setup: () => {
                StripeTerminalRefundPopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalService.doPost = jest.fn(() => {
                    return Promise.resolve({id: 1})
                });
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalRefundPopupService,
                resolve: response => response,
            },
            expect: {
                reference_number: 1,
                error: false,
            },
        },
        {
            testCaseId: 'STSTCRR-02',
            setup: () => {
                StripeTerminalRefundPopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalService.doPost = jest.fn(() => {
                    return Promise.resolve({error: {}})
                });
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalRefundPopupService,
                resolve: response => response,
            },
            expect: StripeTerminalRefundPopupService,
        },
        {
            testCaseId: 'STSTCRR-03',
            setup: () => {
                StripeTerminalRefundPopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalService.doPost = jest.fn(() => {
                    throw "Error";
                });
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalRefundPopupService,
                resolve: response => response,
            },
            expect: StripeTerminalRefundPopupService,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.tryCreateRefundRequest(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-cancelSendRefundRequest', () => {
    let data = [
        {
            testCaseId: 'STSCSRR-01',
            setup: () => {
            },
            afterGetResult: () => {
            },
            params: {
                resolve: response => response,
            },
            expect: {
                error: true,
                errorMessage: StripeTerminalConstant.MESSAGE_TRANSACTION_STATUS_CANCELLED,
            },
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.cancelSendRefundRequest(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-tryCreateSaleRequest', () => {
    let mocks = {};
    afterEach(() => {
        reinitEnv();
    });
    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.getConnectionStatus = StripeTerminalService.constructor.terminal.getConnectionStatus;
        mocks.doPost = StripeTerminalService.doPost;
        mocks.getConnectedReader = StripeTerminalService.getConnectedReader;
        mocks.tryCreatePaymentIntent = StripeTerminalService.tryCreatePaymentIntent;
        mocks.getDeviceType = StripeTerminalService.getDeviceType;
        mocks.discoverReaders = StripeTerminalService.discoverReaders;
        StripeTerminalService.constructor.terminal = sdk;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.constructor.terminal.getConnectionStatus = mocks.getConnectionStatus;
        StripeTerminalService.doPost = mocks.doPost;
        StripeTerminalService.getConnectedReader = mocks.getConnectedReader;
        StripeTerminalService.tryCreatePaymentIntent = mocks.tryCreatePaymentIntent;
        StripeTerminalService.getDeviceType = mocks.getDeviceType;
        StripeTerminalService.discoverReaders = mocks.discoverReaders;
        StripeTerminalService.setConnectedReader(false);
        StripeTerminalService.setDeviceType(false);
    });
    let data = [
        {
            testCaseId: 'STSTCSR-01',
            setup: () => {
                StripeTerminalPurchasePopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalService.setConnectedReader(false);
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                order: {},
                resolve: response => response,
                reject: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.NO_CONNECTED_READER_EXCEPTION_MESSAGE},
        },
        {
            testCaseId: 'STSTCSR-02',
            setup: () => {
                StripeTerminalPurchasePopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalService.setConnectedReader(simulatorReader);
                StripeTerminalService.constructor.terminal.getConnectionStatus = jest.fn(() => 'connected');
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
                StripeTerminalService.tryCreatePaymentIntent = jest.fn((popup, order, resolve, reject) => ({
                    popup,
                    order,
                }));
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                order: {},
                resolve: response => response,
                reject: response => response,
            },
            expect: {
                popup: StripeTerminalPurchasePopupService,
                order: {},
            },
        },
        {
            testCaseId: 'STSTCSR-03',
            setup: () => {
                StripeTerminalPurchasePopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalService.setConnectedReader(simulatorReader);
                StripeTerminalService.constructor.terminal.getConnectionStatus = jest.fn(() => 'disconnected');
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
                StripeTerminalService.setDeviceType(StripeTerminalConstant.DEVICE_TYPE_SIMULATED);
                StripeTerminalService.discoverReaders = jest.fn((deviceType) => ({
                    deviceType
                }));
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                order: {},
                resolve: response => response,
                reject: response => response,
            },
            expect: {
                deviceType: StripeTerminalConstant.DEVICE_TYPE_SIMULATED,
            },
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.tryCreateSaleRequest(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-tryCreatePaymentIntent', () => {
    let mocks = {};
    afterEach(() => {
        reinitEnv();
    });
    beforeAll(() => {
        // Mock functions
        mocks.terminal = StripeTerminalService.constructor.terminal;
        mocks.collectPaymentMethod = StripeTerminalService.constructor.terminal.collectPaymentMethod;
        mocks.createPaymentIntent = StripeTerminalService.createPaymentIntent;
        mocks.pollCallback = StripeTerminalService.pollCallback;
        StripeTerminalService.constructor.terminal = sdk;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.constructor.terminal = mocks.terminal;
        StripeTerminalService.constructor.terminal.collectPaymentMethod = mocks.collectPaymentMethod;
        StripeTerminalService.createPaymentIntent = mocks.createPaymentIntent;
        StripeTerminalService.pollCallback = mocks.pollCallback;
    });
    let data = [
        {
            testCaseId: 'STSTCPI-01',
            setup: () => {
                StripeTerminalService.canceled = true;
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
            },
            afterGetResult: () => {
                StripeTerminalService.canceled = false;
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                order: {},
                resolve: response => response,
                reject: response => response,
            },
            expect: false,
        },
        {
            testCaseId: 'STSTCPI-02',
            setup: () => {
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
                StripeTerminalService.createPaymentIntent = jest.fn(({amount, currency, description}) => {
                    throw StripeTerminalConstant.CONNECTION_ERROR_MESSAGE;
                });
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                order: {},
                resolve: response => response,
                reject: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.CONNECTION_ERROR_MESSAGE},
        },
        {
            testCaseId: 'STSTCPI-03',
            setup: () => {
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
                StripeTerminalService.createPaymentIntent = jest.fn(({amount, currency, description}) => {
                    return Promise.resolve({
                        secret: 'secret'
                    });
                });
                StripeTerminalService.constructor.terminal.collectPaymentMethod = jest.fn((secret) => {
                    return Promise.resolve({
                        error: {message: StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}
                    });
                });
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                order: {},
                resolve: response => response,
                reject: response => response,
            },
            expect: {
                "error": true,
                "errorMessage": `${StripeTerminalConstant.COLLECT_FAILED_EXCEPTION_MESSAGE} ${StripeTerminalConstant.CONNECTION_ERROR_MESSAGE}`
            },
        },
        {
            testCaseId: 'STSTCPI-04',
            setup: () => {
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
                StripeTerminalService.createPaymentIntent = jest.fn(({amount, currency, description}) => {
                    return Promise.resolve({
                        secret: 'secret'
                    });
                });
                StripeTerminalService.constructor.terminal.collectPaymentMethod = jest.fn((secret) => {
                    return Promise.resolve({
                        paymentIntent: {}
                    });
                });
                StripeTerminalService.pollCallback = jest.fn((paymentIntent, resolve, reject) => {
                    return Promise.resolve({
                        paymentIntent: {}
                    });
                });
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                order: {},
                resolve: response => response,
                reject: response => response,
            },
            expect: {
                paymentIntent: {}
            },
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.tryCreatePaymentIntent(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-cancelSendSaleRequest', () => {
    let mocks = {};
    afterEach(() => {
        reinitEnv();
    });
    beforeAll(() => {
        // Mock functions
        mocks.cancelPendingPayment = StripeTerminalService.cancelPendingPayment;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.cancelPendingPayment = mocks.cancelPendingPayment;
    });
    let data = [
        {
            testCaseId: 'STSCSSR-01',
            setup: () => {
                StripeTerminalService.pendingPaymentIntentSecret = false;
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                resolve: response => response,
            },
            expect: {"error": true, "errorMessage": StripeTerminalConstant.MESSAGE_TRANSACTION_STATUS_CANCELLED},
        },
        {
            testCaseId: 'STSCSSR-02',
            setup: () => {
                StripeTerminalService.pendingPaymentIntentSecret = true;
                StripeTerminalPurchasePopupService.showPopup(StripeTerminalPaymentService, () => {
                }, () => {
                });
                StripeTerminalService.cancelPendingPayment = jest.fn(callback => Promise.resolve({}))
            },
            afterGetResult: () => {
            },
            params: {
                popup: StripeTerminalPurchasePopupService,
                resolve: response => response,
            },
            expect: {},
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.cancelSendSaleRequest(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-getSendSaleRequestOrder', () => {
    let mocks = {};
    afterEach(() => {
        reinitEnv();
    });
    beforeAll(() => {
        // Mock functions
        mocks.getPreOrder = CheckoutService.getPreOrder

    });
    afterAll(() => {
        // Unmock functions
        CheckoutService.getPreOrder = mocks.getPreOrder;
    });
    let data = [
        {
            testCaseId: 'STSGSSRO-01',
            setup: () => {
                StripeTerminalService.order = false;
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
                CheckoutService.getPreOrder = jest.fn(() => {
                    return {
                        increment_id: 1,
                        payments: [{method: StripeTerminalConstant.CODE, increment_id: 123}]
                    }
                });
            },
            afterGetResult: () => {
            },
            params: {},
            expect: {
                "increment_id": "1",
                "payments": [{"increment_id": 123, "method": StripeTerminalConstant.CODE}]
            },
        },
        {
            testCaseId: 'STSGSSRO-02',
            setup: () => {
                StripeTerminalService.order = {
                    increment_id: 1,
                    payments: [{method: StripeTerminalConstant.CODE, increment_id: 123}]
                };
                StripeTerminalService.payment = {amount_paid: 1, orderPayment: {reference_number: 2}};
            },
            afterGetResult: () => {
            },
            params: {},
            expect: {
                "increment_id": "1",
                "payments": [{"increment_id": 123, "method": StripeTerminalConstant.CODE}]
            },
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.getSendSaleRequestOrder(...Object.values(testCase.params));
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-sendSaleRequest', () => {
    let mocks = {};

    afterEach(() => {
        reinitEnv();
    });

    beforeAll(() => {
        // Mock functions
        mocks.tryCreateSaleRequest = StripeTerminalService.tryCreateSaleRequest;
        mocks.cancelSendSaleRequest = StripeTerminalService.cancelSendSaleRequest;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.tryCreateSaleRequest = mocks.tryCreateSaleRequest;
        StripeTerminalService.cancelSendSaleRequest = mocks.cancelSendSaleRequest;
    });


    let data = [
        {
            testCaseId: 'STSSSR-01',
            setup: () => {
                StripeTerminalService.tryCreateSaleRequest = jest.fn(() => {
                    return Promise.resolve({})
                });
            },
            afterGetResult: () => {
            },
            expect: StripeTerminalPurchasePopupService
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.sendSaleRequest();
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-sendSaleRequest', () => {
    let mocks = {};

    afterEach(() => {
        reinitEnv();
    });

    beforeAll(() => {
        // Mock functions
        mocks.tryCreateRefundRequest = StripeTerminalService.tryCreateRefundRequest;
        mocks.cancelSendRefundRequest = StripeTerminalService.cancelSendRefundRequest;

    });
    afterAll(() => {
        // Unmock functions
        StripeTerminalService.tryCreateRefundRequest = mocks.tryCreateRefundRequest;
        StripeTerminalService.cancelSendRefundRequest = mocks.cancelSendRefundRequest;
    });


    let data = [
        {
            testCaseId: 'STSSRR-01',
            setup: () => {
                StripeTerminalService.tryCreateRefundRequest = jest.fn(() => {
                    return Promise.resolve({})
                });
            },
            afterGetResult: () => {
            },
            expect: StripeTerminalRefundPopupService
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.sendRefundRequest();
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});

describe('StripeTerminalService-initialize', () => {
    let mocks = {};

    afterEach(() => {
        reinitEnv();
    });

    beforeAll(() => {
        // Mock functions
        mocks.getConfigFromLocalStorage = StripeTerminalService.getConfigFromLocalStorage;

    });
    afterAll(() => {
        // Unmock functions
        ConfigService.getConfigFromLocalStorage = mocks.getConfigFromLocalStorage;
    });


    let data = [
        {
            testCaseId: 'STSI-01',
            setup: () => {
                ConfigService.getConfigFromLocalStorage = jest.fn(() => {
                    return Config.config
                });
            },
            afterGetResult: () => {
            },
            expect: undefined
        },
        {
            testCaseId: 'STSI-01',
            setup: () => {
                ConfigService.getConfigFromLocalStorage = jest.fn(() => {
                    return JSON.stringify(Config.config)
                });
            },
            afterGetResult: () => {
            },
            expect: undefined
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}]`, async () => {
            testCase.setup();
            let result = await StripeTerminalService.initialize();
            testCase.afterGetResult(result);
            expect(result).toEqual(testCase.expect);
        })
    })
});