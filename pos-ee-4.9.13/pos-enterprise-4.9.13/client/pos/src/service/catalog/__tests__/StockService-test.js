import StockService from "../StockService";
import Config from "../../../config/Config";
import ConfigConstant from "../../../view/constant/ConfigConstant";

describe('Stock Service Unit test', () => {
    /**
     * Data Input Output
     *
     *  detail: https://docs.google.com/spreadsheets/d/1grOCxoigkTHOe0fKqmXO30E8RrgydvhfNs0tNVg9qSQ/edit#gid=871708895
     *
     *
     * @type {[]}
     */
    let data = [
        {
            testCaseId: 'STOS-01',
            title: "Get product salable qty simple product:\n" +
            "manage_stock = Yes\n" +
            "backorders= No\n" +
            "min_qty=1\n" +
            "qty=2",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '0',
                use_config_min_qty: false,
                min_qty: '1',
                is_in_stock: true,
                qty: 2,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 1
        },
        {
            testCaseId: 'PROS-02',
            title: "Get product salable qty simple product:\n" +
            "manage_stock = Yes\n" +
            "backorders= No\n" +
            "min_qty=1\n" +
            "qty=1",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '0',
                use_config_min_qty: false,
                min_qty: '1',
                is_in_stock: true,
                qty: 1,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 0
        },
        {
            testCaseId: 'STOS-03',
            title: "Get product salable qty simple product:\n" +
            "manage_stock = Yes\n" +
            "backorders= No\n" +
            "min_qty=-1\n" +
            "qty=2",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '0',
                use_config_min_qty: false,
                min_qty: '-1',
                is_in_stock: true,
                qty: 2,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 2
        },
        ///
        {
            testCaseId: 'STOS-04',
            title: "Get product salable qty simple product:\n" +
            "manage_stock = Yes\n" +
            "backorders= No\n" +
            "min_qty=-1\n" +
            "qty=-1",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '0',
                use_config_min_qty: false,
                min_qty: '-1',
                is_in_stock: true,
                qty: -1,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: -1
        },
        {
            testCaseId: 'STOS-05',
            title: "Get product salable qty simple product:\n" +
            "manage_stock = Yes\n" +
            "backorders= No\n" +
            "min_qty=0\n" +
            "qty=1",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '0',
                use_config_min_qty: false,
                min_qty: '0',
                is_in_stock: true,
                qty: 1,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 1
        },
        {
            testCaseId: 'STOS-06',
            title: "Check simple product is in stock: \n" +
            "manage_stock = Yes\n" +
            "backorders= No\n" +
            "min_qty=0\n" +
            "qty=0",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '0',
                use_config_min_qty: false,
                min_qty: '0',
                is_in_stock: true,
                qty: 0,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 0
        },
        {
            testCaseId: 'STOS-07',
            title: "Check simple product is in stock: \n" +
            "manage_stock = Yes\n" +
            "backorders= Yes\n" +
            "min_qty=1\n" +
            "qty=1",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '1',
                use_config_min_qty: false,
                min_qty: '1',
                is_in_stock: true,
                qty: 1,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 1
        },
        {
            testCaseId: 'STOS-08',
            title: "Check simple product is in stock: \n" +
            "manage_stock = Yes\n" +
            "backorders= Yes\n" +
            "min_qty=1\n" +
            "qty=2",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '1',
                use_config_min_qty: false,
                min_qty: '1',
                is_in_stock: true,
                qty: 2,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 2
        },
        {
            testCaseId: 'STOS-09',
            title: "\"Get product salable qty simple product: \n" +
            "manage_stock = Yes\n" +
            "backorders= Yes\n" +
            "min_qty=-1\n" +
            "qty=1\"",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '1',
                use_config_min_qty: false,
                min_qty: '-1',
                is_in_stock: true,
                qty: 1,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 2
        },
        {
            testCaseId: 'STOS-010',
            title: "Get product salable qty simple product:\n" +
            "manage_stock = Yes\n" +
            "backorders= Yes\n" +
            "min_qty=-1\n" +
            "qty=-1",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '1',
                use_config_min_qty: false,
                min_qty: '-1',
                is_in_stock: true,
                qty: -1,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 0
        },
        {
            testCaseId: 'STOS-011',
            title: "Get product salable qty simple product:\n" +
            "manage_stock = Yes\n" +
            "backorders= Yes\n" +
            "min_qty=0\n" +
            "qty=0",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '1',
                use_config_min_qty: false,
                min_qty: '0',
                is_in_stock: true,
                qty: 0,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 0
        },
        {
            testCaseId: 'STOS-012',
            title: "Get product salable qty simple product:\n" +
            "manage_stock = Yes\n" +
            "backorders= Yes\n" +
            "min_qty=0\n" +
            "qty=1",
            input: {
                use_config_manage_stock: false,
                manage_stock: true,
                use_config_backorders: false,
                backorders: '1',
                use_config_min_qty: false,
                min_qty: '0',
                is_in_stock: true,
                qty: 1,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: false
                },
                {
                    path: ConfigConstant.XML_PATH_MANAGE_STOCK,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_BACKORDERS,
                    value: '0'
                },
                {
                    path: ConfigConstant.XML_PATH_MIN_QTY,
                    value: '0'
                },
            ],
            expect: 1
        },
    ];

    Config.config = {
        magento_version: '2.3.0'
    };

    /** begin test */
    data.forEach((test) => {
        it(`[${test.testCaseId}] ${test.title}`, () => {
            Config.config.settings = test.settings;
            expect(StockService.getStockItemQty(test.input)).toEqual(test.expect);
        });
    });
    /** end test */
});
