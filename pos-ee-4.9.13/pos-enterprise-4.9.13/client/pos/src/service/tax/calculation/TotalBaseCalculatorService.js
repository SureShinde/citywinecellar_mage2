import {AbstractAggregateCalculatorService} from "./AbstractAggregateCalculatorService";
import ServiceFactory from "../../../framework/factory/ServiceFactory";

export class TotalBaseCalculatorService extends AbstractAggregateCalculatorService {
    static className = 'TotalBaseCalculatorService';

    /**
     * {@inheritdoc}
     */
    roundAmount(
        amount,
        rate = null,
        direction = null,
        type = this.KEY_REGULAR_DELTA_ROUNDING,
        round = true,
        item = null
    ) {
        return this.deltaRound(amount, rate, direction, type, round);
    }
}

/** @type TotalBaseCalculatorService */
let totalBaseCalculatorService = ServiceFactory.get(TotalBaseCalculatorService);

export default totalBaseCalculatorService;
