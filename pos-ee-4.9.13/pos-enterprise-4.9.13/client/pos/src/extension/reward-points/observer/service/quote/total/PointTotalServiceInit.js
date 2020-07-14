export default function(eventData) {
    let PointTotalService = require("../../../../service/quote/total/PointTotalService").default;
    eventData.service.totalCollectors.push({
        name: "rewardpoint",
        class: PointTotalService,
        sort_order: 410,
    });
}
