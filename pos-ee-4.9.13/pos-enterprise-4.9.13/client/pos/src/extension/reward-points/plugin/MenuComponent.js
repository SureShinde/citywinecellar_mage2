/**
 * Plugin to navigate menu for Reward Point Page
 */
export default {
  canUseMenuToggle: {
    rewardpoints: {
      sortOrder: 100,
      disabled: false,
      after: function(result) {
        if (result) {
          return this.props.currentPage !== 'SpendRewardPointComponent';
        }
        return result;
      },
    }
  },
  canBackToCatalog: {
    rewardpoints: {
      sortOrder: 100,
      disabled: false,
      after: function(result) {
        if (!result) {
          const {currentPage, hasPaidOrWaitingGatewayPayment} = this.props;
          return currentPage === 'SpendRewardPointComponent'
            && !hasPaidOrWaitingGatewayPayment;
        }
        return result;
      },
    }
  },
  clickBackButton: {
    rewardpoints: {
      sortOrder: 100,
      disabled: false,
      after: function(result) {
        const { actions, currentPage } = this.props;
        return () => {
          let action = result();
          if (undefined === action && currentPage === 'SpendRewardPointComponent') {
            return actions.switchPage('Payment');
          }
          return action;
        }
      },
    }
  },
};
