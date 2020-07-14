/**
 * Plugin to add processor to quote service
 */
export default {
  collectTotals: {
    rewardpoints: {
      sortOrder: 100,
      disabled: false,
      before: function() {
        let ResetRewardProcessor = require("../../service/quote/processor/ResetRewardProcessor").default;
        if (!this.beforeCollectTotalProcessors.find(item => item.class === ResetRewardProcessor)) {
          this.beforeCollectTotalProcessors.push({
            class: ResetRewardProcessor,
            sort_order: 100,
          });
        }
        let EarningPointProcessor = require("../../service/quote/processor/EarningPointProcessor").default;
        if (!this.afterCollectTotalProcessors.find(item => item.class === EarningPointProcessor)) {
          this.afterCollectTotalProcessors.push({
            class: EarningPointProcessor,
            sort_order: 100,
          });
        }
      },
    }
  },
};
