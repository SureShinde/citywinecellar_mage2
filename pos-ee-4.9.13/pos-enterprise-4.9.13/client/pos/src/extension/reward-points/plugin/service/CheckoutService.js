/**
 * Plugin checkout service to convert quote to order
 */
export default {
  convertQuoteDataToOrder: {
    rewardpoints: {
      sortOrder: 100,
      disabled: false,
      after: function(order, quote) {
        let extensionAttributes = {};
        if (typeof order.extension_attributes !== 'undefined' && order.extension_attributes){
          extensionAttributes = order.extension_attributes;
        }
        extensionAttributes.rewardpoints_spent = quote.rewardpoints_spent;
        extensionAttributes.rewardpoints_base_discount = quote.rewardpoints_base_discount;
        extensionAttributes.rewardpoints_discount = quote.rewardpoints_discount;
        extensionAttributes.rewardpoints_earn = quote.rewardpoints_earn;
        extensionAttributes.rewardpoints_base_amount = quote.rewardpoints_base_amount;
        extensionAttributes.rewardpoints_amount = quote.rewardpoints_amount;
        extensionAttributes.rewardpoints_base_discount_for_shipping = quote.rewardpoints_base_discount_for_shipping;
        extensionAttributes.rewardpoints_discount_for_shipping = quote.rewardpoints_discount_for_shipping;
        order.magestore_base_discount_for_shipping = quote.magestore_base_discount_for_shipping;
        order.magestore_discount_for_shipping = quote.magestore_discount_for_shipping;
        order.magestore_base_discount = quote.magestore_base_discount;
        order.magestore_discount = quote.magestore_discount;
        order.extension_attributes = extensionAttributes;
        return order;
      },
    }
  },
  convertQuoteItemToOrderItem: {
    rewardpoints: {
      sortOrder: 100,
      disabled: false,
      after: function(orderItem, item) {
        let extensionAttributes = {
          ...orderItem.extension_attributes,
          rewardpoints_base_discount: item.extension_attributes.rewardpoints_base_discount,
          rewardpoints_discount: item.extension_attributes.rewardpoints_discount,
          rewardpoints_earn: item.extension_attributes.rewardpoints_earn,
          rewardpoints_spent: item.extension_attributes.rewardpoints_spent
        };
        Object.assign(orderItem, {
          magestore_base_discount: item.magestore_base_discount,
          magestore_discount: item.magestore_discount,
          extension_attributes: extensionAttributes
        });
        return orderItem;
      },
    }
  },
};
