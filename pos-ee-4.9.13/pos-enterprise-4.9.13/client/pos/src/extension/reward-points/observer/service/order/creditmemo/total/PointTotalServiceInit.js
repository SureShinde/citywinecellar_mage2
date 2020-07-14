export default function(eventData) {
    let CreditmemoPointTotal = require("../../../../../service/order/creditmemo/total/PointTotalService").default;
    eventData.service.totalModels.push({
        name: "rewardpoint",
        class: CreditmemoPointTotal,
        sort_order: 210
    });
}
