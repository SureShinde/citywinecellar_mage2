import React from "react";
/**
 * Plugin to add cart total for spend reward point page
 */
export default {
  template: {
    rewardpoints: {
      sortOrder: 100,
      disabled: false,
      before: function() {
        if (this.showOnPages.indexOf('SpendRewardPointComponent') === -1) {
          this.showOnPages.push('SpendRewardPointComponent');
        }
      },
    }
  },
  getTemplateTotal: {
    rewardpoints_spent: {
      sortOrder: 100,
      disabled: false,
      after: function(result, item) {
        let PointDiscount = require("../view/component/checkout/cart/totals/PointDiscount").default;
        if(item.code === "spend_point") {
          return <PointDiscount key={item.code}
                                quote={this.props.quote}
                                total={item}
                                showBackDrop={() => this.showBackDrop()}
          />
        }
        let EarnPoint = require("../view/component/checkout/cart/totals/EarnPoint").default;
        if(item.code === "earn_point") {
          return <EarnPoint key={item.code}
                            quote={this.props.quote}
                            total={item}
                            showBackDrop={() => this.showBackDrop()}
          />
        }
        return result;
      },
    }
  },
};
