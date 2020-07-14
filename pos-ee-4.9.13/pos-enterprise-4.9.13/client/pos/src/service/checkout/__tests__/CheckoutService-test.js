import CheckoutService from '../CheckoutService'
import StatusConstant from "../../../view/constant/order/StatusConstant";

/* Data Input Output */
let data = [
    {
        testCaseId: 'ODS-01',
        title: 'Order Status - When Create Invoice and Create Shipment',
        input: {
            create_shipment: true,
            create_invoice: true,
            is_virtual: false
        },
        expect: {
            state: StatusConstant.STATE_COMPLETE,
            status: StatusConstant.STATUS_COMPLETE,
        }
    },
    {
        testCaseId: 'ODS-02',
        title: 'Order Status - Create Invoice and not create shipment',
        input: {
            create_shipment: false,
            create_invoice: true,
            is_virtual: false
        },
        expect: {
            state: StatusConstant.STATE_PROCESSING,
            status: StatusConstant.STATUS_PROCESSING,
        }
    },
    {
        testCaseId: 'ODS-03',
        title: 'Order Status - Create Invoice and Is Virtual',
        input: {
            create_shipment: false,
            create_invoice: true,
            is_virtual: true
        },
        expect: {
            state: StatusConstant.STATE_COMPLETE,
            status: StatusConstant.STATUS_COMPLETE,
        }
    },
    {
        testCaseId: 'ODS-04',
        title: 'Order Status - Not Create Invoice - Create shipment',
        input: {
            create_shipment: true,
            create_invoice: false,
            is_virtual: false
        },
        expect: {
            state: StatusConstant.STATE_PROCESSING,
            status: StatusConstant.STATUS_PROCESSING,
        }
    },
    {
        testCaseId: 'ODS-05',
        title: 'Order Status - Not Create Invoice - Not Create shipment',
        input: {
            create_shipment: false,
            create_invoice: false,
            is_virtual: false
        },
        expect: {
            state: StatusConstant.STATE_NEW,
            status: StatusConstant.STATUS_PENDING,
        }
    },
];
/* Begin test */
for (let i = 0; i < data.length; i++) {
    it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
        expect(CheckoutService.getOrderStatus(data[i].input.is_virtual, data[i].input.create_shipment,
            data[i].input.create_invoice)).toEqual(data[i].expect);
    });
}
/* End: Test */