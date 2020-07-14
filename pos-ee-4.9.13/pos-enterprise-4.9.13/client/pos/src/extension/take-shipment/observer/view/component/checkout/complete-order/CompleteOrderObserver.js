import {listen} from "../../../../../../../event-bus";

export default class CompleteOrderObserver {
    constructor() {
        /**
         *  styling checkout complete order screen if has create shipment toggle button or not
         */
        listen(
            'component_complete_order_template_calculate_block_content_class_name_after',
            ({ component, blockContentClassName }
            ) => {
            const {quote} = component.props;
            if (quote.is_virtual) {
                return;
            }

            blockContentClassName.push('has-create-shipment-button');
        })
    }
}