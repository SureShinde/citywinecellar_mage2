import CreateCreditmemoConstant from "../../../../../view/constant/order/creditmemo/CreateCreditmemoConstant";

export default function(eventData) {
    let {key} = eventData;

    let extension_attributes = [
        CreateCreditmemoConstant.ADJUSTMENT_EARNED_KEY,
        CreateCreditmemoConstant.RETURN_SPENT_KEY,
        CreateCreditmemoConstant.REWARDPOINTS_EARN
    ];
    // check key data
    eventData.isExtensionAttributes = extension_attributes.includes(key);
}
