import ShipmentFactoryService from "../ShipmentFactoryService";

/**
 *  https://docs.google.com/spreadsheets/d/1o3ZiBUUkBY_cE7_yxMIwyA_bUBeMWORO-HIPGixU-q8/edit#gid=0
 */
describe('ShipmentFactoryService-castQty', () => {
    let data = [
        {
            testCaseId: 'SOSFS01',
            title: '',
            qty: 1.2,
            item: {
                is_qty_decimal: true
            },
            expect: 1.2,
        },
        {
            testCaseId: 'SOSFS02',
            title: '',
            qty: 1.2,
            item: {
                is_qty_decimal: false
            },
            expect: 1,
        },
        {
            testCaseId: 'SOSFS03',
            title: '',
            qty: 2,
            item: {
                is_qty_decimal: true
            },
            expect: 2,
        },
        {
            testCaseId: 'SOSFS04',
            title: '',
            qty: 2,
            item: {
                is_qty_decimal: false
            },
            expect: 2,
        },
        {
            testCaseId: 'SOSFS05',
            title: '',
            qty: -1,
            item: {
                is_qty_decimal: true
            },
            expect: 0,
        },
        {
            testCaseId: 'SOSFS06',
            title: '',
            qty: -1,
            item: {
                is_qty_decimal: false
            },
            expect: 0,
        },
        {
            testCaseId: 'SOSFS07',
            title: '',
            qty: -1.2,
            item: {
                is_qty_decimal: true
            },
            expect: 0,
        },
        {
            testCaseId: 'SOSFS08',
            title: '',
            qty: -1.2,
            item: {
                is_qty_decimal: false
            },
            expect: 0,
        },
    ];

    data.forEach(testCase => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, () => {
            let result = ShipmentFactoryService.castQty(testCase.item, testCase.qty);
            expect(result).toBe(testCase.expect)
        })
    })
});
