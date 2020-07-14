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
};
