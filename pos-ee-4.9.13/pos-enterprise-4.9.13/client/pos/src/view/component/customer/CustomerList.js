import React, {Fragment} from "react";
import CoreContainer from "../../../framework/container/CoreContainer";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import AbstractList from '../../../framework/component/list/AbstractList'
import SmoothScrollbar from 'smooth-scrollbar';
import CustomerAction from "../../action/CustomerAction";
import QueryService from '../../../service/QueryService';
import Config from "../../../config/Config";
import ScanConstant from "../../constant/ScanConstant";
import ScanAction from "../../action/ScanAction";
import SyncConstant from "../../constant/SyncConstant";

export class CustomerListComponent extends AbstractList {
    static className = 'CustomerListComponent';

    setCustomerListElement = element => this.customer_list = element;
    setSearchBoxElement = element => this.search_box = element;

    searchTimeOut = null;

    /**
     * Constructor
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            items: [],
            search_box_value: ''
        }
    }

    /**
     * Component will mount
     */
    componentWillMount() {
        /* Set default state mode for component from Config */
        if (Config.dataTypeMode && Config.dataTypeMode[SyncConstant.TYPE_CUSTOMER]) {
            this.setState({mode: Config.dataTypeMode[SyncConstant.TYPE_CUSTOMER]});
        }
    }

    /**
     * This function after mapStateToProps then push more items to component or change load customer mode
     *
     * @param nextProps
     */
    componentWillReceiveProps(nextProps) {
        if (!nextProps.globalBarcodeString && nextProps.isOpen && !this.props.isOpen) {
            /* Load customer when open popup customer list */
            this.clearItems();
            this.searchCustomer(this.state.search_box_value);
            this.startLoading();
            return;
        }
        if (!this.isModeChange(nextProps)) {
            if (nextProps.customers && nextProps.search_key === this.state.search_box_value) {
                this.addItems(nextProps.customers);
                this.stopLoading();
                if (nextProps.total_count > nextProps.customers.length) {
                    let self = this;
                    setTimeout(() => {
                        if (
                            self.props.total_count > self.props.customers.length
                            && self.customer_list
                            && self.customer_list.scrollHeight <= self.customer_list.clientHeight
                        ) {
                            self.lazyload();
                        }
                    }, 300);
                }
            }
            if (nextProps.updated_customers && nextProps.updated_customers.length && this.state.items.length) {
                this.updateListAfterSyncCustomer(nextProps.updated_customers);
            }
            if (nextProps.deleted_customer_ids && nextProps.deleted_customer_ids.length && this.state.items.length) {
                this.updateListAfterSyncDeletedCustomers(nextProps.deleted_customer_ids);
            }
        }
        if (nextProps.order_id && nextProps.order_id !== this.props.order_id) {
            this.clearSearchBox();
        }

        if (nextProps.globalBarcodeString && nextProps.currentScanPage === ScanConstant.SCAN_PAGES.CUSTOMER) {
            this.search_box.value = nextProps.globalBarcodeString;
            this.setState({search_box_value: nextProps.globalBarcodeString});
            if (document.activeElement !== this.search_box) {
                this.search_box.focus();
                this.clearItems();
                this.startLoading();
                this.searchCustomer(nextProps.globalBarcodeString);
            }
            this.props.actions.setBarcodeString();
        }
    }

    /**
     * update list customer after sync customer
     * @param customers
     */
    updateListAfterSyncCustomer(customers = []) {
        if (customers && customers.length) {
            let items = this.state.items;

            customers.map(customer => {
                let index = items.findIndex(item => item.id === customer.id);
                if (index >= 0) {
                    items[index] = customer;
                }
                return customer;
            });
            this.addItems(items);
            this.props.actions.resetSyncActionUpdateCustomer();
        }
    }

    /**
     * update list customer after sync deleted customer
     * @param ids
     */
    updateListAfterSyncDeletedCustomers(ids = []) {
        if (ids && ids.length) {
            let items = this.state.items;
            ids.map(id => {
                let index = items.findIndex(item => item.id === id);
                if (index >= 0) {
                    items.splice(index, 1);
                }
                return id;
            });
            this.addItems(items);

            this.props.actions.resetSyncDeletedCustomers();
        }
    }

    /**
     * Check mode is changed and reload product list
     *
     * @param nextProps
     * @return {boolean}
     */
    isModeChange(nextProps) {
        if (
            nextProps.dataTypeMode
            && nextProps.dataTypeMode[SyncConstant.TYPE_CUSTOMER]
            && (nextProps.dataTypeMode[SyncConstant.TYPE_CUSTOMER] !== this.state.mode)
        ) {
            this.setState({mode: nextProps.dataTypeMode[SyncConstant.TYPE_CUSTOMER]});
            this.startLoading();
            this.clearItems();
            this.searchCustomer(this.state.search_box_value);
            return true;
        }
        return false;
    }

    /**
     * Load more customer when you scroll customer list
     *
     */
    lazyload() {
        if (this.canLoad() === true) {
            this.startLoading();
            this.searchCustomer(
                this.state.search_box_value,
                this.props.search_criteria.page_size,
                this.props.search_criteria.current_page + 1
            );
        }
    }

    /**
     * Check can load more customer
     *
     * @return {boolean}
     */
    canLoad() {
        return !this.isLoading() && (this.props.customers.length < this.props.total_count);
    }

    /**
     * Change search box input value
     *
     * @param event
     */
    changeSearchKey(event) {
        let searchKey = event.target.value;
        this.setState({search_box_value: searchKey});
        if (this.searchTimeOut) {
            clearTimeout(this.searchTimeOut);
        }
        this.searchTimeOut = setTimeout(() => {
            this.props.actions.resetCustomerList();
            this.scrollbar.scrollTo(0, 0);
            this.clearItems();
            this.startLoading();
            this.searchCustomer(searchKey);
        }, 600);
    }

    /**
     * Clear search box
     */
    clearSearchBox() {
        this.search_box.value = "";
        this.setState({search_box_value: ""});
        this.clearItems();
        this.startLoading();
        this.searchCustomer();
    }

    /**
     * Get customer name
     *
     * @param {object} customer
     * @return {string}
     */
    getCustomerName(customer) {
        return customer.id ? customer.firstname + " " + customer.lastname : "";
    }

    /**
     * Get customer telephone
     *
     * @param {object} customer
     * @return {string}
     */
    getCustomerTelephone(customer) {
        return customer.id && customer.telephone ? customer.telephone : "";
    }

    /**
     * Search Customer
     *
     * @param searchKey
     * @param pageSize
     * @param currentPage
     */
    searchCustomer(searchKey = '', pageSize = 10, currentPage = 1) {
        let queryService = QueryService.reset();
        queryService.setOrder('full_name').setPageSize(pageSize).setCurrentPage(currentPage);
        queryService.addQueryString(searchKey);
        this.props.actions.searchCustomer(queryService, searchKey);
    }

    template() {
        if (this.customer_list && !this.props.isOpen) {
            SmoothScrollbar.destroy(this.customer_list);
            this.scrollbar = null;
        }

        if (!this.scrollbar && this.customer_list && this.props.isOpen) {
            this.scrollbar = SmoothScrollbar.init(this.customer_list);
            this.scrollbar.addListener(event => {
                if ((event.limit.y <= event.offset.y + 100 && event.offset.y > 0)) {
                    this.lazyload();
                }
                return true;
            });
        }
        return (
            <Fragment>
                <div className="search-customer">
                    <div className="box-search">
                        <button className="btn-search" type="button"><span>search</span></button>
                        <input className="input-search form-control" type="text"
                               ref={this.setSearchBoxElement}
                               onKeyUp={event => this.changeSearchKey(event)}/>
                        {
                            this.state.search_box_value ?
                                (
                                    <button className="btn-remove" type="button"
                                            onClick={() => this.clearSearchBox()}>
                                        <span>remove</span>
                                    </button>
                                ) :
                                ""
                        }
                    </div>
                </div>
                <div className="list-customer" ref={this.setCustomerListElement}>
                    <ul>
                        {
                            this.state.items.map((customer) => {
                                return (
                                    <li key={customer.email + customer.updated_at}
                                        onClick={() => this.props.selectCustomer(customer)}>
                                        <span className="name">{this.getCustomerName(customer)}</span>
                                        <span className="phone">{this.getCustomerTelephone(customer)}</span>
                                    </li>
                                )
                            })
                        }
                    </ul>
                    <div className="text-center list-customer-norecords"
                         style={{display: (this.state.items.length === 0 && !this.isLoading() ? "block" : "none")}}>
                        {this.props.t('Sorry, we couldn\'t find any records.')}
                    </div>
                    <div className="loader-product"
                         style={{display: (this.isLoading() ? 'block' : 'none')}}>
                    </div>
                </div>
            </Fragment>
        )
    }
}

/**
 *
 * @type {CustomerListComponent}
 */
const component = ComponentFactory.get(CustomerListComponent);

class CustomerListContainer extends CoreContainer {
    static className = 'CustomerListContainer';

    // This maps the state to the property of the component
    static mapState(state) {
        let {mode, dataTypeMode} = state.core.sync;
        let {
            customers,
            search_criteria,
            total_count,
            search_key,
            order_id,
            updated_customers,
            deleted_customer_ids
        } = state.core.customer.customerList;
        let {barcodeString, scanPage} = state.core.scan;
        return {
            mode,
            dataTypeMode,
            customers,
            search_criteria,
            total_count,
            search_key,
            order_id,
            updated_customers,
            deleted_customer_ids,
            globalBarcodeString: barcodeString,
            currentScanPage: scanPage
        };
    }

    // This maps the dispatch to the property of the component
    static mapDispatch(dispatch) {
        return {
            actions: {
                searchCustomer: (queryService, searchKey) =>
                    dispatch(CustomerAction.searchCustomer(queryService, searchKey)),
                resetCustomerList: () => dispatch(CustomerAction.resetCustomerList()),
                resetSyncActionUpdateCustomer: () => dispatch(CustomerAction.syncActionUpdateDataFinish()),
                resetSyncDeletedCustomers: () => dispatch(CustomerAction.syncDeletedCustomerFinish()),
                setBarcodeString: barcodeString => dispatch(ScanAction.setBarcodeString(barcodeString)),
                setScanPage: scanPage => dispatch(ScanAction.setScanPage(scanPage))
            }
        }
    }
}

/**
 *
 * @type {CustomerListContainer}
 */
const container = ContainerFactory.get(CustomerListContainer);

export default container.withRouter(component);
