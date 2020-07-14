import SimpleProductService from "../SimpleProductService";
import Config from "../../../../config/Config";
import ConfigConstant from "../../../../view/constant/ConfigConstant";

describe('Simple Product Service Unit test', () => {
    /**
     * Data Input Output
     *
     *  detail: https://docs.google.com/spreadsheets/d/1grOCxoigkTHOe0fKqmXO30E8RrgydvhfNs0tNVg9qSQ/edit#gid=247762231
     *
     *
     * @type {[]}
     */
    let data = [
        {
            testCaseId: 'SPROS-01',
            title: "",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 0,
                        use_config_min_qty: false,
                        min_qty: 1,
                        qty: 1,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: false
        },
        {
            testCaseId: 'SPROS-02',
            title: "VerifyStock has:\n" +
                "Backorders= No\n" +
                "Out of stock Threshold = 1\n" +
                "Product Salable Qty = 2",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 0,
                        use_config_min_qty: false,
                        min_qty: 1,
                        qty: 2,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-03',
            title: "VerifyStock has:\n" +
                "Backorders= No\n" +
                "Out of stock Threshold = 1\n" +
                "Product Salable Qty = 0",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 0,
                        use_config_min_qty: false,
                        min_qty: 1,
                        qty: 0,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: false
        },
        {
            testCaseId: 'SPROS-04',
            title: "VerifyStock has:\n" +
                "Backorders= No\n" +
                "Out of stock Threshold = -1\n" +
                "Product Salable Qty = 1",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 0,
                        use_config_min_qty: false,
                        min_qty: -1,
                        qty: 1,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-05',
            title: "VerifyStock has:\n" +
                "Backorders= No\n" +
                "Out of stock Threshold = -1\n" +
                "Product Salable Qty = 0",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 0,
                        use_config_min_qty: false,
                        min_qty: -1,
                        qty: 0,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: false
        },
        {
            testCaseId: 'SPROS-06',
            title: "VerifyStock has:\n" +
                "Backorders= No\n" +
                "Out of stock Threshold = 0\n" +
                "Product Salable Qty = 1",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 0,
                        use_config_min_qty: false,
                        min_qty: 0,
                        qty: 1,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-07',
            title: "VerifyStock has:\n" +
                "Backorders= No\n" +
                "Out of stock Threshold = 0\n" +
                "Product Salable Qty = 0",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 0,
                        use_config_min_qty: false,
                        min_qty: 0,
                        qty: 0,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: false
        },
        {
            testCaseId: 'SPROS-08',
            title: "VerifyStock has:\n" +
                "Backorders= Yes\n" +
                "Out of stock Threshold = 1\n" +
                "Product Salable Qty = 1",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 1,
                        use_config_min_qty: false,
                        min_qty: 1,
                        qty: 1,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-09',
            title: "VerifyStock has:\n" +
                "Backorders= Yes\n" +
                "Out of stock Threshold = 1\n" +
                "Product Salable Qty = 2",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 1,
                        use_config_min_qty: false,
                        min_qty: 1,
                        qty: 2,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-10',
            title: "VerifyStock has:\n" +
                "Backorders= Yes\n" +
                "Out of stock Threshold = 1\n" +
                "Product Salable Qty = 0",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 1,
                        use_config_min_qty: false,
                        min_qty: 1,
                        qty: 0,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-11',
            title: "VerifyStock has:\n" +
                "Backorders= Yes\n" +
                "Out of stock Threshold = -1\n" +
                "Product Salable Qty = 1",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 1,
                        use_config_min_qty: false,
                        min_qty: -1,
                        qty: 1,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-12',
            title: "VerifyStock has:\n" +
                "Backorders= Yes\n" +
                "Out of stock Threshold = -1\n" +
                "Product Salable Qty = 2",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 1,
                        use_config_min_qty: false,
                        min_qty: -1,
                        qty: 2,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-13',
            title: "VerifyStock has:\n" +
                "Backorders= Yes\n" +
                "Out of stock Threshold = -1\n" +
                "Product Salable Qty = -1",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 1,
                        use_config_min_qty: false,
                        min_qty: -1,
                        qty: -1,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: false
        },
        {
            testCaseId: 'SPROS-14',
            title: "VerifyStock has:\n" +
                "Backorders= Yes\n" +
                "Out of stock Threshold = 0\n" +
                "Product Salable Qty = 1",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 1,
                        use_config_min_qty: false,
                        min_qty: 0,
                        qty: 1,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-15',
            title: "VerifyStock has:\n" +
                "Backorders= Yes\n" +
                "Out of stock Threshold = 0\n" +
                "Product Salable Qty = 0",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 1,
                        use_config_min_qty: false,
                        min_qty: 0,
                        qty: 0,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'SPROS-16',
            title: "VerifyStock has:\n" +
                "Backorders= Yes\n" +
                "Out of stock Threshold = 0\n" +
                "Product Salable Qty = -1",
            input: {
                stocks: [
                    {
                        use_config_backorders: false,
                        backorders: 1,
                        use_config_min_qty: false,
                        min_qty: 0,
                        qty: -1,

                    }
                ],
            },
            settings: [
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },

    ];

    Config.config = {
        magento_version: '2.3.0'
    };

    /** begin test */
    data.forEach((test) => {
        it(`[${test.testCaseId}] ${test.title}`, () => {
            Config.config.settings = test.settings;
            expect(Boolean(SimpleProductService.verifyStock(test.input))).toEqual(test.expect);
        });
    });
    /** end test */
});
