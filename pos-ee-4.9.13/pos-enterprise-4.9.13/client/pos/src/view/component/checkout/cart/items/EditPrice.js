import React from 'react';
import Popover from '../../../lib/react-popover';
import {ContentNumber} from "../../../lib/react-numpad/components/ContentNumber";
import CoreComponent from "../../../../../framework/component/CoreComponent";
import CoreContainer from "../../../../../framework/container/CoreContainer";
import ContainerFactory from "../../../../../framework/factory/ContainerFactory";
import ComponentFactory from "../../../../../framework/factory/ComponentFactory";
import {Content} from "../../../lib/react-numpad/elements/PopoverKeyPad";
import {Tab, Tabs} from "react-bootstrap";
import ItemConstant from "../../../../constant/checkout/cart/ItemConstant";
import {CustomPriceNumPad} from "../../../lib/react-numpad/components/CustomPriceNumPad";
import "../../../../style/css/CustomDiscountPrice.css"

class EditPriceComponent extends CoreComponent {
    static className = 'EditPriceComponent';

    blurTimeout = null;
    setReasonBoxElement = element => this.reason_box = element;

    constructor(props) {
        super(props);
        this.state = {
            customPrice: props.customPrice,
            reason: props.reason,
            showXButton: false,
            numpadActive: true,
            customPriceDiscountType: props.customPriceDiscountType,
            customPriceDiscountValue: props.customPriceDiscountValue,
            customPriceType: props.customPriceType,
        };
        this.keyDown = this.keyDown.bind(this);
        this.handleChangeCustomPrice = this.handleChangeCustomPrice.bind(this);
        this.handleChangeCustomDiscountPrice = this.handleChangeCustomDiscountPrice.bind(this);
        this.handleSelectCustomPriceType = this.handleSelectCustomPriceType.bind(this);
        this.confirm = this.confirm.bind(this);
        this.cancel = this.cancel.bind(this);
    }
    componentDidMount() {
        document.addEventListener('keydown', this.keyDown);
    }

    componentWillUnmount() {
        document.removeEventListener('keydown', this.keyDown);
    }

    keyDown(event) {
        if(this.reason_box === event.target){
            const {key} = event;
            if (key === 'Enter' ) {
                this.confirm();
            } else if (key === 'Escape') {
                this.cancel();
            }
        }
    }
    /**
     * Change input reason box
     *
     * @param event
     */
    changeReason(event) {
        let reason = event.target.value;
        this.setState({
            reason: reason,
            showXButton: !!reason
        });
    }

    /**
     * on focus input
     * @param event
     */
    onReasonFocus(event) {
        this.setState({showXButton: !!event.target.value, numpadActive: false});
    }

    /**
     * Clear input reason box
     */
    clearReasonBox() {
        this.setState({reason: ""});
        this.reason_box.value = "";
        setTimeout(() => {
            return this.reason_box.focus();
        }, 220);
    }

    /**
     * Handle change custom price
     *
     * @param customPrice
     * @param isEmpty
     */
    handleChangeCustomPrice(customPrice, isEmpty) {
        this.setState({showXButton: false, customPrice: (isEmpty) ? null : parseFloat(customPrice)});
    }

    /**
     * Handle change custom price discount
     *
     * @param customPrice
     * @param isEmpty
     * @param customPriceDiscountType
     * @param unitCustomPriceDiscount
     */
    handleChangeCustomDiscountPrice(customPrice, isEmpty, customPriceDiscountType, unitCustomPriceDiscount) {
        this.setState({
            showXButton: false,
            customPrice: (isEmpty) ? null : parseFloat(customPrice),
            customPriceDiscountType: customPriceDiscountType,
            customPriceDiscountValue: (isEmpty) ? null:parseFloat(customPrice),
            unitCustomPriceDiscount: (isEmpty) ? null:parseFloat(unitCustomPriceDiscount)
        });
    }

    /**
     * Confirm custom price discount
     */
    confirm() {
        this.props.confirm();
    }

    /**
     * Cancel custom price discount
     */
    cancel() {
        this.props.cancel();
    }

    /**
     * Show Custom price discount content
     *
     * @param customPriceDiscountDisplay
     * @returns {XML}
     */
    customPriceDiscountTemplate(customPriceDiscountDisplay) {
        return (
            <CustomPriceNumPad sync={true}
                               cancel={this.cancel}
                               finish={this.confirm}
                               onChange={this.handleChangeCustomDiscountPrice}
                               active={this.state.numpadActive}
                               value={customPriceDiscountDisplay}
                               customPriceDiscountType={this.state.customPriceDiscountType}
                               unitCustomPriceDiscount={this.state.unitCustomPriceDiscount}
            />
        );
    }

    /**
     * Show Custom price content
     *
     * @param customPriceDisplay
     * @returns {XML}
     */
    customPriceTemplate(customPriceDisplay) {
        return (
            <ContentNumber sync={true}
                           cancel={this.cancel}
                           finish={this.confirm}
                           onChange={this.handleChangeCustomPrice}
                           active={this.state.numpadActive}
                           value={customPriceDisplay}
            />
        );
    }

    /**
     * Handle select the custom price type
     * @param key
     */
    handleSelectCustomPriceType(key) {
        let type = "";
        switch (key) {
            case ItemConstant.STATE_CUSTOM_PRICE:
                type = ItemConstant.STATE_CUSTOM_PRICE;
                break;
            case ItemConstant.STATE_CUSTOM_DISCOUNT_PRICE:
                type = ItemConstant.STATE_CUSTOM_DISCOUNT_PRICE;
                break;
            default:
                break
        }
        this.setState({customPriceType: type});
    }
    template() {
        const {
            width,
            customPrice,
            customPriceDiscountValue,
            reason
        } = this.props;

        let customPriceDisplay = parseFloat(customPrice)* 100;
        customPriceDisplay = customPriceDisplay.toFixed(2) * 1;

        let customPriceDiscountDisplay = parseFloat(customPriceDiscountValue)* 100;
        customPriceDiscountDisplay = customPriceDiscountDisplay.toFixed(2) * 1;

        let customPriceDiscountTemplate = this.customPriceDiscountTemplate(customPriceDiscountDisplay);
        let customPriceTemplate = this.customPriceTemplate(customPriceDisplay);
        return (
            <Content width={width} className="set-custom-price-popup">
                <Tabs defaultActiveKey={this.state.customPriceType}
                      animation={false}
                      onSelect={(type) => this.handleSelectCustomPriceType(type)}
                      bsStyle="pills"
                      className="custom-price-container"
                      id="custom-price-container">
                    <Tab eventKey={ItemConstant.STATE_CUSTOM_DISCOUNT_PRICE}
                         title={this.props.t('Discount')}>
                        {customPriceDiscountTemplate}
                    </Tab>
                    <Tab eventKey={ItemConstant.STATE_CUSTOM_PRICE}
                         title={this.props.t('Custom Price')}>
                        {customPriceTemplate}
                    </Tab>
                </Tabs>
                <div className="custom-price-reason-wrapper">
                    <input type="text" className="input-reason form-control"
                           defaultValue={reason}
                           ref={this.setReasonBoxElement}
                           placeholder={this.props.t("Reason")}
                           onKeyUp={event => this.changeReason(event)}
                           onFocus={event => this.onReasonFocus(event)}
                           maxLength="127"
                    />
                    {
                        this.state.showXButton ?
                            (
                                <button className="btn-remove" type="button"
                                        onClick={() => this.clearReasonBox()}>
                                    <span>remove</span>
                                </button>
                            ) :
                            ""
                    }
                </div>
                <ul className="list-action">
                    <li className="cancel" onClick={(event) => this.cancel(event) }>
                        <a><span>Cancel</span></a>
                    </li>
                    <li className="confirm" onClick={(event) => this.confirm(event) }>
                        <a><span>Confirm</span></a>
                    </li>
                </ul>
            </Content>
        );
    }
}

const defaultProps = {
    element: ComponentFactory.get(EditPriceComponent),
    arrow:"left"
};

/**
 *
 * @type {CartItemComponent}
 */
const component = Popover(defaultProps);

class EditPriceContainer extends CoreContainer {
    static className = 'EditPriceContainer';

    /**
     *
     * @param state
     * @return {{isDisableEdit: boolean}}
     */
    static mapState(state) {

        return {

        }
    }

    /**
     *
     * @param dispatch
     * @returns {{}}
     */
    static mapDispatch(dispatch) {
        return {

        }
    }
}

/**
 *
 * @type {EditPriceContainer}
 */
const container = ContainerFactory.get(EditPriceContainer);

export default container.getConnect(component);