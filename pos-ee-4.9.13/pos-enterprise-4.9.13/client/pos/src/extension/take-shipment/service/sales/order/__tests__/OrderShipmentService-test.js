import OrderCreateShipmentService from "../OrderCreateShipmentService";
import ProductTypeConstant from "../../../../../../view/constant/ProductTypeConstant";

/**
 *  https://docs.google.com/spreadsheets/d/1o3ZiBUUkBY_cE7_yxMIwyA_bUBeMWORO-HIPGixU-q8/edit#gid=0
 */
describe('OrderCreateShipmentService-prepareShipmentItemParam', () => {
    let data = [
        {
            testCaseId: 'OCSS01',
            title: '',
            order: {},
            item: {
                tmp_item_id: 1,
                parent_item_id: false,
                qty_ordered: 1,
                qty_shipped: 0,
                qty_refunded: 0,
                qty_canceled: 0,
                is_qty_decimal: false,
                product_type: ProductTypeConstant.SIMPLE,
            },
            expect: {
                order_item_id: 1,
                qty_left: 1,
                qty: 0,
                is_qty_decimal: false
            },
        },
        {
            testCaseId: 'OCSS02',
            title: '',
            order: {},
            item: {
                tmp_item_id: 1,
                parent_item_id: false,
                qty_ordered: 1,
                qty_shipped: 1,
                qty_refunded: 0,
                qty_canceled: 0,
                is_qty_decimal: false,
                product_type: ProductTypeConstant.SIMPLE,
            },
            expect: {
                order_item_id: 1,
                qty_left: 0,
                qty: 0,
                is_qty_decimal: false
            },

        },
        {
            testCaseId: 'OCSS03',
            title: '',
            order: {},
            item: {
                tmp_item_id: 1,
                parent_item_id: false,
                qty_ordered: 1,
                qty_shipped: 1,
                qty_refunded: 1,
                qty_canceled: 0,
                is_qty_decimal: false,
                product_type: ProductTypeConstant.SIMPLE,
            },
            expect: {
                order_item_id: 1,
                qty_left: 0,
                qty: 0,
                is_qty_decimal: false
            },

        },
        {
            testCaseId: 'OCSS04',
            title: '',
            order: {},
            item: {
                tmp_item_id: 1,
                parent_item_id: false,
                qty_ordered: 1,
                qty_shipped: 0,
                qty_refunded: 0,
                qty_canceled: 1,
                is_qty_decimal: false,
                product_type: ProductTypeConstant.SIMPLE,
            },
            expect: {
                order_item_id: 1,
                qty_left: 0,
                qty: 0,
                is_qty_decimal: false
            },

        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
            let result = OrderCreateShipmentService.prepareShipmentItemParam(testCase.item, testCase.order);
            Object.keys(testCase.expect).forEach(key => {
                expect(result[key]).toBe(testCase.expect[key])
            })
        })
    })
});
