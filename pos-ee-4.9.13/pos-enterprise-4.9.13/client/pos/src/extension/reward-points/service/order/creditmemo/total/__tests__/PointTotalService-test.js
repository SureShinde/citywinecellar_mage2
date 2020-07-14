import PointTotalService from '../PointTotalService';
import OrderItemService from "../../../../../../../service/sales/order/OrderItemService";

describe('PointTotalService-collect', () => {
    let mocks = {};

    beforeAll(() => {
        // Mock functions
        mocks.isDummy = OrderItemService.isDummy;
        OrderItemService.isDummy = jest.fn(function (orderItem, order) {
            return orderItem.isDummy;
        });
    });
    afterAll(() => {
        // Unmock functions
        OrderItemService.isDummy = mocks.isDummy;
    });

    let data = [
        {
            testCaseId: 'RPT-01',
            title: 'Order in credit memo is null or undefined',
            data: {
                creditmemo: {
                    extension_attributes: {},
                    base_shipping_amount: 0,
                    shipping_amount: 0,
                    items: [
                        {
                            qty: 0,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0,
                                    rewardpoints_base_discount: 0
                                },
                                isDummy: false,
                                qty_invoiced: 0,
                                qty_ordered: 0
                            }
                        }
                    ]
                }
            },
            expect: {
                rewardpoints_discount: 0,
                rewardpoints_base_discount: 0
            }
        },
        {
            testCaseId: 'RPT-02',
            title: 'Reward point discount in order is less or equal than 0',
            data: {
                creditmemo: {
                    base_shipping_amount: 0,
                    shipping_amount: 0,
                    order: {
                        extension_attributes: {
                            rewardpoints_discount: 0,
                            rewardpoints_base_discount_for_shipping: 0,
                            rewardpoints_discount_for_shipping: 0
                        },
                        shipping_amount: 0,
                        base_shipping_amount: 0
                    },
                    items: [
                        {
                            qty: 0,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0,
                                    rewardpoints_base_discount: 0
                                },
                                isDummy: false,
                                qty_invoiced: 0,
                                qty_ordered: 0
                            }
                        }
                    ]
                }
            },
            expect: {
                rewardpoints_discount: 0,
                rewardpoints_base_discount: 0
            }
        },
        {
            testCaseId: 'RPT-03',
            title: 'Credit memo shipping amount is 0 or undefined',
            data: {
                creditmemo: {
                    extension_attributes: {},
                    base_shipping_amount: undefined,
                    shipping_amount: 0,
                    order: {
                        extension_attributes: {
                            rewardpoints_discount: 1,
                            rewardpoints_base_discount_for_shipping: 0,
                            rewardpoints_discount_for_shipping: 0
                        },
                        shipping_amount: 1,
                        base_shipping_amount: 1
                    },
                    items: [
                        {
                            qty: 1,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0,
                                    rewardpoints_base_discount: 0
                                },
                                isDummy: true,
                                qty_invoiced: 0,
                                qty_ordered: 3
                            }
                        },
                        {
                            qty: 2,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0,
                                    rewardpoints_base_discount: 0
                                },
                                isDummy: false,
                                qty_invoiced: 1,
                                qty_ordered: 3
                            }
                        },
                        {
                            qty: 3,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0.5,
                                    rewardpoints_base_discount: 0.5
                                },
                                isDummy: false,
                                qty_invoiced: 0,
                                qty_ordered: 3
                            }
                        },
                        {
                            qty: 3,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0.5,
                                    rewardpoints_base_discount: 0.5
                                },
                                isDummy: false,
                                qty_invoiced: 2,
                                qty_ordered: 3
                            }
                        },
                    ]
                }
            },
            expect: {
                rewardpoints_discount: 0.5,
                rewardpoints_base_discount: 0.5
            }
        },
        {
            testCaseId: 'RPT-04',
            title: 'Credit memo shipping amount is greater than 0',
            data: {
                creditmemo: {
                    extension_attributes: {},
                    base_shipping_amount: 5,
                    shipping_amount: 5,
                    order: {
                        extension_attributes: {
                            rewardpoints_discount: 1,
                            rewardpoints_base_discount_for_shipping: 0,
                            rewardpoints_discount_for_shipping: 0
                        },
                        shipping_amount: 10,
                        base_shipping_amount: 10
                    },
                    items: [
                        {
                            qty: 1,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0,
                                    rewardpoints_base_discount: 0
                                },
                                isDummy: true,
                                qty_invoiced: 0,
                                qty_ordered: 3
                            }
                        },
                        {
                            qty: 2,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0,
                                    rewardpoints_base_discount: 0
                                },
                                isDummy: false,
                                qty_invoiced: 1,
                                qty_ordered: 3
                            }
                        },
                        {
                            qty: 3,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0.5,
                                    rewardpoints_base_discount: 0.5
                                },
                                isDummy: false,
                                qty_invoiced: 0,
                                qty_ordered: 3
                            }
                        },
                        {
                            qty: 3,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0.5,
                                    rewardpoints_base_discount: 0.5
                                },
                                isDummy: false,
                                qty_invoiced: 2,
                                qty_ordered: 3
                            }
                        },
                    ]
                }
            },
            expect: {
                rewardpoints_discount: 0.5,
                rewardpoints_base_discount: 0.5
            }
        },
        {
            testCaseId: 'RPT-05',
            title: 'Credit memo shipping amount is greater than 0 while reward points discount for shipping is greater than 0',
            data: {
                creditmemo: {
                    extension_attributes: {},
                    base_shipping_amount: 5,
                    shipping_amount: 5,
                    order: {
                        extension_attributes: {
                            rewardpoints_discount: 1,
                            rewardpoints_base_discount_for_shipping: 1,
                            rewardpoints_discount_for_shipping: 1
                        },
                        shipping_amount: 10,
                        base_shipping_amount: 10
                    },
                    items: [
                        {
                            qty: 1,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0,
                                    rewardpoints_base_discount: 0
                                },
                                isDummy: true,
                                qty_invoiced: 0,
                                qty_ordered: 3
                            }
                        },
                        {
                            qty: 2,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0,
                                    rewardpoints_base_discount: 0
                                },
                                isDummy: false,
                                qty_invoiced: 1,
                                qty_ordered: 3
                            }
                        },
                        {
                            qty: 3,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0.5,
                                    rewardpoints_base_discount: 0.5
                                },
                                isDummy: false,
                                qty_invoiced: 0,
                                qty_ordered: 3
                            }
                        },
                        {
                            qty: 3,
                            order_item: {
                                extension_attributes: {
                                    rewardpoints_discount: 0.5,
                                    rewardpoints_base_discount: 0.5
                                },
                                isDummy: false,
                                qty_invoiced: 2,
                                qty_ordered: 3
                            }
                        },
                    ]
                }
            },
            expect: {
                rewardpoints_discount: 1,
                rewardpoints_base_discount: 1
            }
        },
    ];

    data.forEach((testCase) => {
        it(`[${testCase.testCaseId}] ${testCase.title}`, async function () {
            PointTotalService.collect(testCase.data.creditmemo);
            expect(testCase.data.creditmemo.extension_attributes.rewardpoints_discount).toEqual(testCase.expect.rewardpoints_discount);
            expect(testCase.data.creditmemo.extension_attributes.rewardpoints_base_discount).toEqual(testCase.expect.rewardpoints_base_discount);
        });
    });
});
