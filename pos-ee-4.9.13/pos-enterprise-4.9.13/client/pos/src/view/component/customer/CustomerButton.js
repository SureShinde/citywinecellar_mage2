import React, {Fragment} from "react";
import {Modal} from "react-bootstrap";
import '../../style/css/Customer.css';
import CoreContainer from "../../../framework/container/CoreContainer";
import CoreComponent from "../../../framework/component/CoreComponent";
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import CustomerPopup from "./CustomerPopup";
import CustomerList from "./CustomerList";
import AddCustomerPopupConstant from "../../constant/customer/AddCustomerPopupConstant";
import QuoteAction from "../../action/checkout/QuoteAction";
import ProductList from "../catalog/ProductList";
import CustomerConstant from "../../constant/CustomerConstant";
import cloneDeep from 'lodash/cloneDeep';
import ScanConstant from "../../constant/ScanConstant";
import ScanAction from "../../action/ScanAction";
import layout from "../../../framework/Layout"

export class CustomerButtonComponent extends CoreComponent {
    static className = 'CustomerButtonComponent';

    /**
     * Constructor
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            isOpen: false,
            isOpenCustomerPopup: false,
            currentCustomer: {},
            popupCustomer: {},
        }
    }

    /**
     * Show or hide popups
     *
     * @param {string} type
     */
    showPopup(type) {
        if (type === AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER_LIST) {
            this.props.actions.setScanPage(ScanConstant.SCAN_PAGES.CUSTOMER);
        } else if (type) {
            this.props.actions.setScanPage();
        } else {
            this.props.actions.setScanPage(ScanConstant.SCAN_PAGES.PRODUCT);
        }
        this.setState({
            isOpenList: type === AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER_LIST,
            isOpenCustomerPopup: type === AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER,
            isOpenCustomerAddress: type === AddCustomerPopupConstant.POPUP_TYPE_ADDRESS
        });
    }

    /**
     * Component will receive props
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        let quote = nextProps.quote;
        let popupCustomer = this.state.popupCustomer;
        if (quote && popupCustomer && popupCustomer.id) {
            let customer = quote.customer;
            if (customer && customer.id) {
                if (customer.email === popupCustomer.email && (customer.id !== popupCustomer.id || customer.updated_time !== popupCustomer.updated_time)) {
                    this.setState({popupCustomer: customer});
                }
            }
        }
    }

    /**
     * Show customer popup
     */
    showCustomerPopup() {
        let popupCustomer = (this.props.quote.customer && this.props.quote.customer.id) ?
            this.props.quote.customer : {};
        if (this.isNewCustomer()) {
            popupCustomer = cloneDeep(CustomerConstant.NEW_CUSTOMER_DATA);
            popupCustomer.id = new Date().getTime();
        }
        this.setState({popupCustomer: popupCustomer});
        this.showPopup(AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER);
    }

    /**
     * Click to customer in cart section
     *
     * @return {boolean}
     */
    clickCustomerCart() {
        if (this.props.currentPage !== ProductList.className) {
            return false;
        }
        if (this.props.quote.customer && this.props.quote.customer.id) {
            this.showCustomerPopup();
        } else {
            this.showPopup(AddCustomerPopupConstant.POPUP_TYPE_CUSTOMER_LIST);
        }
    }

    /**
     * Select customer
     *
     * @param customer
     */
    selectCustomer(customer) {
        this.setState({
            popupCustomer: customer || {},
            isOpenList: false
        });
        this.props.actions.setCustomer(customer);
        this.props.actions.setScanPage(ScanConstant.SCAN_PAGES.PRODUCT);
    }

    /**
     * Get current customer name
     *
     * @return {string}
     */
    getCustomerName() {
        let customer = this.props.quote.customer;
        if (customer && customer.id) {
            return customer.firstname + " " + customer.lastname;
        }
        return this.props.t('Guest');
    }

    /**
     * Check current customer is new
     *
     * @return {boolean}
     */
    isNewCustomer() {
        return !(this.props.quote.customer && this.props.quote.customer.id)
    }

    template() {

        return (
            <Fragment>
                <div className="customer-drop dropdown">
                    <a className= {this.props.currentPage === ProductList.className ? "dropdown-toggle" : "dropdown-toggle disabled"}
                       data-toggle="modal" data-target="#popup-drop-customer"
                       onClick={() => this.clickCustomerCart()}>
                        {this.getCustomerName()} &nbsp;

                        {layout('customer')('customer_button_after')()(this)}

                    </a>
                    {
                        ((this.props.quote.customer && this.props.quote.customer.id) &&
                        this.props.currentPage === ProductList.className) ?
                            (
                                <a className="remove-user" onClick={() => this.selectCustomer()}>
                                    <span>remove</span>
                                </a>
                            ) :
                            ""
                    }
                </div>
                <Modal
                    bsSize={"sm"}
                    className={this.state.isOpenList ? "popup-drop-customer" : "popup-drop-customer hidden"}
                    backdropClassName={this.state.isOpenList ? "" : "hidden"}
                    dialogClassName={this.state.isOpenList ? "" : "hidden"}
                    show={true}
                    onHide={() => {
                        this.setState({isOpenList: false});
                        this.props.actions.setScanPage(ScanConstant.SCAN_PAGES.PRODUCT);
                    }}
                >
                    <ul className="dropdown-menu-customer">
                        <li>
                            <a className="toggle-create-customer"
                               onClick={() => this.showCustomerPopup()}
                            >
                                {this.props.t('Create Customer')}
                            </a>
                        </li>
                    </ul>
                    <CustomerList selectCustomer={(customer) => this.selectCustomer(customer)}
                                  isOpen={this.state.isOpenList}/>
                </Modal>
                <CustomerPopup isOpenCustomerPopup={this.state.isOpenCustomerPopup}
                               isOpenCustomerAddress={this.state.isOpenCustomerAddress}
                               showPopup={(type) => this.showPopup(type)}
                               customer={this.state.popupCustomer}
                               isNewCustomer={this.isNewCustomer()}/>
            </Fragment>
        );
    }
}

/**
 *
 * @type {CustomerButtonComponent}
 */
const component = ComponentFactory.get(CustomerButtonComponent);

export class CustomerButtonContainer extends CoreContainer {
    static className = 'CustomerButtonContainer';

    // This maps the state to the property of the component
    static mapState(state) {
        let quote = state.core.checkout.quote;
        let currentPage = state.core.checkout.index.currentPage;
        return {quote: quote, currentPage};
    }

    // This maps the dispatch to the property of the component
    static mapDispatch(dispatch) {
        return {
            actions: {
                setCustomer: customer => dispatch(QuoteAction.setCustomer(customer)),
                setScanPage: scanPage => dispatch(ScanAction.setScanPage(scanPage))
            }
        }
    }
}

/**
 *
 * @type {CustomerButtonContainer}
 */
const container = ContainerFactory.get(CustomerButtonContainer);

export default container.withRouter(component);
