import service, {RewardPointService} from "../RewardPointService";
import Config from "../../../../config/Config";
import UserService from "../../../../service/user/UserService";
import QuoteItemService from "../../../../service/checkout/quote/ItemService";
import {RewardPointHelper} from "../../helper/RewardPointHelper";

/**
 * Test Case Documentation
 *
 * https://docs.google.com/spreadsheets/d/1-qIyHM1y_A3LIu96kJUwFhCbbW38GiRW2DNdwgBGsOA/edit#gid=1240763378
 *
 */
describe('Reward Point Service', () => {
  describe('getEarnPointForQuote', () => {
    let data = [
      {
        testCaseId: 'RPS-01',
        title: "Don't have earning rate",
        input: {
          earn_by_tax: 1,
          earn_by_shipping: 1,
        },
        expect: 0
      },
      {
        testCaseId: 'RPS-02',
        title: "Have earning rate $10 ~ 1 point",
        input: {
          rate: {money: 10, points: 1},
          earn_by_tax: 1,
          earn_by_shipping: 1,
        },
        expect: 10
      },
      {
        testCaseId: 'RPS-03',
        title: "Have earning rate $10 ~ 1 point, can't earn by Tax",
        input: {
          rate: {money: 10, points: 1},
          earn_by_tax: 0,
          earn_by_shipping: 1,
        },
        expect: 8
      },
      {
        testCaseId: 'RPS-04',
        title: "Have earning rate $10 ~ 1 point, can't earn by Shipping",
        input: {
          rate: {money: 10, points: 1},
          earn_by_tax: 1,
          earn_by_shipping: 0,
        },
        expect: 7
      },
      {
        testCaseId: 'RPS-05',
        title: "Have earning rate $10 ~ 1 point, can't earn by Tax and Shipping",
        input: {
          rate: {money: 10, points: 1},
          earn_by_tax: 0,
          earn_by_shipping: 0,
        },
        expect: 6
      },
    ];

    let quote = {
      base_grand_total: 99,
      base_shipping_amount: 20,
      base_shipping_tax_amount: 6,
      base_tax_amount: 19,
      customer_id: 1,
      customer_group_id: 1,
    };

    let getWebsiteId = UserService.getWebsiteId;

    data.forEach((test) => {
      it(`[${test.testCaseId}] ${test.title}`, () => {
        Config.config = {
          settings: [
            {path: "rewardpoints/earning/by_tax", value: test.input.earn_by_tax},
            {path: "rewardpoints/earning/by_shipping", value: test.input.earn_by_shipping},
          ],
          extension_attributes: {rewardpoints_rate: []}
        };
        if (test.input.rate) {
          Config.config.extension_attributes.rewardpoints_rate = [{
            status: 1,
            direction: 2, // Earning Rate
            customer_group_ids: '1,2,3',
            website_ids: '1',
            money: test.input.rate.money,
            points: test.input.rate.points,
          }];
        }
        UserService.getWebsiteId = jest.fn(() => '1');
        expect(service.getEarnPointForQuote(quote)).toEqual(test.expect);
        UserService.getWebsiteId = getWebsiteId;
      });
    });
  });

  describe('getDiscountAmountByPoint', () => {
    let data = [
      {
        testCaseId: 'RPS-06',
        title: "Don't have spending rate",
        input: {
          points: 99,
        },
        expect: 0
      },
      {
        testCaseId: 'RPS-07',
        title: "Rate 10 Points ~ $1 Discount",
        input: {
          rate: {money: 1, points: 10},
          points: 99,
        },
        expect: 9.9
      },
    ];

    let quote = {
      customer_id: 1,
      customer_group_id: 1,
      items: [],
    };
    let getWebsiteId = UserService.getWebsiteId;

    data.forEach((test) => {
      it(`[${test.testCaseId}] ${test.title}`, () => {
        Config.config = {
          settings: [],
          extension_attributes: {rewardpoints_rate: []}
        };
        if (test.input.rate) {
          Config.config.extension_attributes.rewardpoints_rate = [{
            status: 1,
            direction: 1, // Spending Rate
            customer_group_ids: '1,2,3',
            website_ids: '1',
            money: test.input.rate.money,
            points: test.input.rate.points,
          }];
        }
        UserService.getWebsiteId = jest.fn(() => '1');
        expect(service.getDiscountAmountByPoint(test.input.points, quote)).toEqual(test.expect);
        UserService.getWebsiteId = getWebsiteId;
      });
    });
  });

  describe('getQuoteBaseTotalWithoutShippingFee', () => {
    let data = [
      {
        testCaseId: 'RPS-08',
        title: "Discount applied after tax",
        input: {
          discount_calculation_price: 9,
          base_discount_amount: 1,
          extension_attributes: {rewardpoints_base_discount: 1},
          base_tax_amount: 2,
          isApplyAfterTax: true
        },
        expect: 11
      },
      {
        testCaseId: 'RPS-09',
        title: "Discount applied before tax",
        input: {
          base_calculation_price: 9,
          base_discount_amount: 1,
          extension_attributes: {},
          isApplyAfterTax: false
        },
        expect: 8
      },
    ];

    let getTotalQty = QuoteItemService.getTotalQty;
    data.forEach((test) => {
      it(`[${test.testCaseId}] ${test.title}`, () => {
        QuoteItemService.getTotalQty = jest.fn(() => 1);
        expect(service.getQuoteBaseTotalWithoutShippingFee({
          items: [test.input]
        }, test.input.isApplyAfterTax)).toEqual(test.expect);
        QuoteItemService.getTotalQty = getTotalQty;
      });
    });
  });

  describe('getQuoteBaseTotal', () => {
    let data = [
      {
        testCaseId: 'RPS-10',
        title: "Don't spend for shipping",
        input: {
          allowSpendForShippingFee: false,
          totalWithoutShip: 9,
        },
        expect: 9
      },
      {
        testCaseId: 'RPS-11',
        title: "Have shipping with discount",
        input: {
          allowSpendForShippingFee: true,
          totalWithoutShip: 9,
          shipping_amount_for_discount: 2,
          base_shipping_amount_for_discount: 2,
          base_shipping_discount_amount: 1,
          rewardpoints_base_discount_for_shipping: 1,
          base_shipping_tax_amount: 1,
        },
        expect: 12
      },
      {
        testCaseId: 'RPS-12',
        title: "Have shipping without discount",
        input: {
          allowSpendForShippingFee: true,
          totalWithoutShip: 9,
          base_shipping_amount: 3,
          base_shipping_discount_amount: 1,
          base_shipping_tax_amount: 1,
        },
        expect: 12
      },
    ];

    let getQuoteBaseTotalWithoutShippingFee = service.getQuoteBaseTotalWithoutShippingFee;
    let allowSpendForShippingFee = RewardPointHelper.allowSpendForShippingFee;
    data.forEach((test) => {
      it(`[${test.testCaseId}] ${test.title}`, () => {
        service.getQuoteBaseTotalWithoutShippingFee = jest.fn(() => test.input.totalWithoutShip);
        RewardPointHelper.allowSpendForShippingFee = jest.fn(() => test.input.allowSpendForShippingFee);
        expect(service.getQuoteBaseTotal({
          is_virtual: true,
          addresses: [{
            address_type: 'billing',
            shipping_amount_for_discount: test.input.shipping_amount_for_discount,
            base_shipping_amount_for_discount: test.input.base_shipping_amount_for_discount,
            base_shipping_amount: test.input.base_shipping_amount,
            base_shipping_discount_amount: test.input.base_shipping_discount_amount,
            rewardpoints_base_discount_for_shipping: test.input.rewardpoints_base_discount_for_shipping,
            base_shipping_tax_amount: test.input.base_shipping_tax_amount,
          }],
        }, null, true)).toEqual(test.expect);
        service.getQuoteBaseTotalWithoutShippingFee = getQuoteBaseTotalWithoutShippingFee;
        RewardPointHelper.allowSpendForShippingFee = allowSpendForShippingFee;
      });
    });
  });

  describe('getMaximumOfRedeemableForQuote', () => {
    let data = [
      {
        testCaseId: 'RPS-13',
        title: "All customer points",
        input: {
          quoteTotal: 10,
          point_balance: 3,
        },
        expect: 3
      },
      {
        testCaseId: 'RPS-14',
        title: "All quote points",
        input: {
          quoteTotal: 10,
          point_balance: 30,
        },
        expect: 10
      },
      {
        testCaseId: 'RPS-15',
        title: "Rate by percent",
        input: {
          quoteTotal: 10,
          point_balance: 30,
          max_price_spended_type: 'by_percent',
          max_price_spended_value: 30,
        },
        expect: 3
      },
      {
        testCaseId: 'RPS-16',
        title: "Rate fixed limit",
        input: {
          quoteTotal: 10,
          point_balance: 30,
          max_price_spended_type: 'by_price',
          max_price_spended_value: 5,
        },
        expect: 5
      },
    ];

    let getActiveSpendingRate = service.getActiveSpendingRate;
    let getSpendMaxPointPerOrder = RewardPointHelper.getSpendMaxPointPerOrder;
    let getQuoteBaseTotal = service.getQuoteBaseTotal;

    data.forEach((test) => {
      it(`[${test.testCaseId}] ${test.title}`, () => {
        service.getActiveSpendingRate = jest.fn(() => {
          return {
            max_price_spended_type: test.input.max_price_spended_type,
            max_price_spended_value: test.input.max_price_spended_value,
            money: 1,
            points: 1,
          };
        });
        RewardPointHelper.getSpendMaxPointPerOrder = jest.fn(() => {
          return test.input.point_balance < 10 ? 0 : 1000;
        });
        service.getQuoteBaseTotal = jest.fn(() => test.input.quoteTotal);

        expect(service.getMaximumOfRedeemableForQuote({
          customer: {extension_attributes: {point_balance: test.input.point_balance}}
        })).toEqual(test.expect);

        service.getActiveSpendingRate = getActiveSpendingRate;
        RewardPointHelper.getSpendMaxPointPerOrder = getSpendMaxPointPerOrder;
        service.getQuoteBaseTotal = getQuoteBaseTotal;
      });
    });
  });

  it('filterDataHoldOrder', () => {
    let order = {
      extension_attributes: {
        rewardpoints_spent: 100,
      },
      addresses: [{}],
      items: [{product: {}}, {}],
    };
    RewardPointService.filterDataHoldOrder(order);
    expect(order.extension_attributes.rewardpoints_spent).toBeUndefined();
  });

  describe('customerCanSpendPoint', () => {
    let data = [
      {
        testCaseId: 'RPS-17',
        title: "Have min redeemable",
        input: {
          minimumRedeemablePoint: 10,
          pointBalance: 1,
        },
        expect: false
      },
      {
        testCaseId: 'RPS-18',
        title: "No min redeemable",
        input: {
          minimumRedeemablePoint: 0,
          pointBalance: 1,
        },
        expect: true
      },
    ];

    let getMinimumRedeemablePoint = RewardPointHelper.getMinimumRedeemablePoint;

    data.forEach((test) => {
      it(`[${test.testCaseId}] ${test.title}`, () => {
        RewardPointHelper.getMinimumRedeemablePoint = jest.fn(() => test.input.minimumRedeemablePoint);

        expect(service.customerCanSpendPoint({
          extension_attributes: {
            point_balance: test.input.pointBalance,
          },
        })).toEqual(test.expect);

        RewardPointHelper.getMinimumRedeemablePoint = getMinimumRedeemablePoint;
      });
    });
  });

  describe('getShippingEarningPoints', () => {
    let data = [
      {
        testCaseId: 'RPS-19',
        title: "Collect shipping earning points",
        input: {
          order_rewardpoints_earn: 2,
          item_rewardpoints_earn: 1,
        },
        expect: 1
      },
    ];

    data.forEach((test) => {
      it(`[${test.testCaseId}] ${test.title}`, () => {
        expect(RewardPointService.getShippingEarningPoints({
          extension_attributes: {
            rewardpoints_earn: test.input.order_rewardpoints_earn,
          },
          items: [
            {
              extension_attributes: {
                rewardpoints_earn: test.input.item_rewardpoints_earn,
              },
            },
            {
              parent_item_id: 1,
            },
          ],
        })).toEqual(test.expect);
      });
    });
  });

  it('get/set used point', () => {
    service.setUsedPoint(10);
    expect(service.getUsedPoint()).toEqual(10);
    service.removeUsedPoint();
    expect(service.getUsedPoint()).toEqual(0);
  });
});
