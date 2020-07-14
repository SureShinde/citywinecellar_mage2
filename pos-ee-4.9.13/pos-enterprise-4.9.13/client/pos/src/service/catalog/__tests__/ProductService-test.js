import ProductService from "../ProductService";
import SimpleProductService from "../../catalog/stock/SimpleProductService";
import ProductTypeConstant from "../../../view/constant/ProductTypeConstant";
import Config from "../../../config/Config";
import ConfigConstant from "../../../view/constant/ConfigConstant";

describe('Product Service Unit test', () => {
    /**
     * Data Input Output
     *
     *  detail: https://docs.google.com/spreadsheets/d/1PAEZe-cXM_EuYgOI6q6MBUp8RGq_k8-bhK2PtlBomsg/edit#gid=126062515
     *
     *
     * @type {[]}
     */
    let data = [
        {
            testCaseId: 'PROS-01',
            title: "Check product is salable: \n" +
            "allow add OOS product = Yes\n" +
            "product_status= Enable\n" +
            "manage_stock = ANY\n" +
            "product_type: ANY\n" +
            "product is_salable= ANY\n" +
            "verifyStock= ANY",
            // input: {
            //     type_id: ProductTypeConstant.SIMPLE,
            //     status: 1,
            //     is_salable: 1,
            //     stocks: [
            //         {
            //             use_config_manage_stock: false,
            //             manage_stock: true,
            //             use_config_backorders: false,
            //             backorders: '0',
            //             use_config_min_qty: false,
            //             min_qty: '1',
            //             is_in_stock: true,
            //             qty: 2,
            //         }
            //     ]
            // },
            input: {
                type_id: ProductTypeConstant.SIMPLE,
                search_string: '',
                status: 1,
                is_salable: 1,
                stocks: [
                    {
                        use_config_manage_stock: false,
                        manage_stock: true,
                    }
                ],
                mockVerifyStock: true
            },
            settings: [
                { path: 'webpos/checkout/add_out_of_stock_product', value: '1' },
                { path: ConfigConstant.XML_PATH_MANAGE_STOCK, value: '0' },
                { path: ConfigConstant.XML_PATH_BACKORDERS, value: '0' },
                { path: ConfigConstant.XML_PATH_MIN_QTY, value: '0' },
            ],
            expect: true
        },
        {
            testCaseId: 'PROS-02',
            title: "Check product is salable: \n" +
            "allow add OOS product = No\n" +
            "product_status= ANY,\n" +
            "manage_stock = Yes,\n" +
            "product_type: SIMPLE,\n" +
            "product is_salable= ANY\n" +
            "verifyStock= False",
            input: {
                type_id: ProductTypeConstant.SIMPLE,
                search_string: '',
                status: 1,
                is_salable: 1,
                stocks: [
                    {
                        use_config_manage_stock: false,
                        manage_stock: true,
                    }
                ],
                mockVerifyStock: false
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: '0'
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
            expect: false
        },
        {
            testCaseId: 'PROS-03',
            title: "Check product is salable: \n" +
            "allow add OOS product = Yes\n" +
            "product_status= Disable,\n" +
            "manage_stock = ANY\n" +
            "product_type: ANY\n" +
            "product is_salable= ANY\n" +
            "verifyStock= ANY",
            input: {
                type_id: ProductTypeConstant.SIMPLE,
                search_string: '',
                status: 0,
                is_salable: 1,
                stocks: [
                    {
                        use_config_manage_stock: false,
                        manage_stock: true,
                    }
                ],
                mockVerifyStock: true
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: '1'
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
            expect: false
        },
        {
            testCaseId: 'PROS-04',
            title: "Check product is salable: \n" +
            "allow add OOS product = No\n" +
            "product_status= ANY,\n" +
            "manage_stock = No,\n" +
            "product_type: SIMPLE,\n" +
            "product is_salable= ANY\n" +
            "verifyStock= ANY",
            input: {
                type_id: ProductTypeConstant.SIMPLE,
                search_string: '',
                status: 1,
                is_salable: 1,
                stocks: [
                    {
                        use_config_manage_stock: false,
                        manage_stock: false,
                    }
                ],
                mockVerifyStock: false
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: '0'
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
            expect: true
        },
        {
            testCaseId: 'PROS-05',
            title: "Check product is salable: \n" +
            "allow add OOS product = No\n" +
            "product_status= ANY,\n" +
            "manage_stock = Yes,\n" +
            "product_type: SIMPLE,\n" +
            "product is_salable= ANY\n" +
            "verifyStock= True",
            input: {
                type_id: ProductTypeConstant.SIMPLE,
                search_string: '',
                status: 1,
                is_salable: 1,
                stocks: [
                    {
                        use_config_manage_stock: false,
                        manage_stock: true,
                    }
                ],
                mockVerifyStock: true,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: '0'
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
            expect: true
        },
        {
            testCaseId: 'PROS-06',
            title: "Check product is salable: \n" +
            "allow add OOS product = No,\n" +
            "product_status= ANY,\n" +
            "manage_stock = ANY,\n" +
            "product_type: !SIMPLE,\n" +
            "product is_salable= 1,\n" +
            "verifyStock= ANY,",
            input: {
                type_id: ProductTypeConstant.CONFIGURABLE,
                search_string: '',
                status: 1,
                is_salable: 1,
                stocks: [
                    {
                        use_config_manage_stock: false,
                        manage_stock: true,
                    }
                ],
                mockVerifyStock: true,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: '0'
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
            expect: true
        },
        {
            testCaseId: 'PROS-07',
            title: "Check product is salable: \n" +
            "allow add OOS product = No\n" +
            "product_status= ANY,\n" +
            "manage_stock = ANY,\n" +
            "product_type: !SIMPLE,\n" +
            "product is_salable= 0,\n" +
            "verifyStock= ANY,",
            input: {
                type_id: ProductTypeConstant.CONFIGURABLE,
                search_string: '',
                status: 1,
                is_salable: 0,
                stocks: [
                    {
                        use_config_manage_stock: false,
                        manage_stock: true,
                    }
                ],
                mockVerifyStock: true,
            },
            settings: [
                {
                    path: 'webpos/checkout/add_out_of_stock_product',
                    value: '0'
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
            expect: false
        },

    ];

    Config.config = {
        magento_version: '2.3.0'
    };

    /** begin test */
    data.forEach((test) => {
        it(`[${test.testCaseId}] ${test.title}`, () => {
            Config.config.settings = test.settings;
            SimpleProductService.verifyStock = jest.fn(() => test.input.mockVerifyStock);
            expect(Boolean(ProductService.isSalable(test.input))).toEqual(test.expect);
        });
    });
    /** end test */
});
