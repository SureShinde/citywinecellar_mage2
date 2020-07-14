import RewardPointCustomerService from "../RewardPointCustomerService";
import {RewardPointHelper} from "../../../helper/RewardPointHelper";
import CustomerResourceModel from "../../../../../resource-model/customer/CustomerResourceModel";
import Config from "../../../../../config/Config";

describe('RewardPointCustomerService-rewardCustomerWithPoint', () => {
    let mocks = {};

    beforeAll(() => {
        Config.dataTypeMode = {'customer': 'offline'};
        // Mock functions
        mocks.updateCustomerByLoyalty = RewardPointCustomerService.updateCustomerByLoyalty;
        RewardPointCustomerService.updateCustomerByLoyalty = jest.fn(customer => customer);

        mocks.getById = CustomerResourceModel.prototype.getById;
        CustomerResourceModel.prototype.getById = jest.fn(function (id) {
            if(id !== '' && typeof id !== 'undefined' && id !== 0) {
                return {
                    id: id,
                    extension_attributes: {
                        point_balance: 0
                    }
                };
            }

            return false;
        });

        mocks.getEarningMaxBalance = RewardPointHelper.getEarningMaxBalance;
        RewardPointHelper.getEarningMaxBalance = jest.fn(() => 50);
    });
    afterAll(() => {
        // Unmock functions
        RewardPointCustomerService.updateCustomerByLoyalty = mocks.updateCustomerByLoyalty;
        CustomerResourceModel.prototype.getById = mocks.getById;
        RewardPointHelper.getEarningMaxBalance = mocks.getEarningMaxBalance;
    });

    let data = [
        {
            testCaseId: 'RCS-01',
            title: 'When customer id does not match with any customer which exist',
            data: {
                id: 0,
                point: 10
            },
            expect: false
        },
        {
            testCaseId: 'RCS-02',
            title: 'When point is 0 or undefined',
            data: {
                id: 1,
                point: undefined
            },
            expect: false
        },
        {
            testCaseId: 'RCS-03',
            title: 'New point balance is less than earning max balance',
            data: {
                id: 1,
                point: 10
            },
            expect: {
                id: 1,
                extension_attributes: {
                    point_balance: 10
                }
            }
        },
        {
            testCaseId: 'RCS-04',
            title: 'New point balance is greater than earning max balance',
            data: {
                id: 1,
                point: 60
            },
            expect: {
                id: 1,
                extension_attributes: {
                    point_balance: 0
                }
            }
        },
    ];

    data.forEach((testCase) => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, async function () {
            let result = await RewardPointCustomerService.rewardCustomerWithPoint(testCase.data.id, testCase.data.point);
            expect(result).toEqual(testCase.expect);
        });
    });
});
