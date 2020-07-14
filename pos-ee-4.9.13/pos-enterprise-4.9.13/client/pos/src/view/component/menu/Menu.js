import '../../style/css/Menu.css'
import React, {Fragment} from 'react'
import CoreComponent from "../../../framework/component/CoreComponent";
import Logout from '../logout'
import ExportData from '../export-data/ExportData'
import PosService from "../../../service/PosService";
import LocationService from "../../../service/LocationService";
import UserService from "../../../service/user/UserService";
import SmoothScrollbar from "smooth-scrollbar";
import {Payment} from "../checkout/Payment";
import CheckoutAction from "../../action/CheckoutAction";
import CoreContainer from "../../../framework/container/CoreContainer";
import ComponentFactory from "../../../framework/factory/ComponentFactory";
import {bindActionCreators} from "redux";
import MenuAction from "../../action/MenuAction";
import ContainerFactory from "../../../framework/factory/ContainerFactory";
import permission from "../../../helper/Permission";
import PaymentHelper from "../../../helper/PaymentHelper";
import SessionHelper from "../../../helper/SessionHelper";
import SessionService from "../../../service/session/SessionService";
import GiftcardForm from "../checkout/cart/totals/GiftcardForm";
import SessionAction from "../../action/SessionAction";
import ConfigHelper from "../../../helper/ConfigHelper";
import layout from "../../../framework/Layout";

export class MenuComponent extends CoreComponent {
    static className = 'MenuComponent';

    /**
     *   initial state
     *   get staff, location, pos information
     */
    constructor() {
        super();
        this.state = {
            staffName: UserService.getStaffName(),
            locationName: LocationService.getCurrentLocationName(),
            PosName: PosService.getCurrentPosName().replace(/ *\([^)]*\) */g, ""),
        };
    }

    componentWillUnmount() {
        if (this.scrollbar) {
            SmoothScrollbar.destroy(this.scrollbar);
        }
    }

    /**
     *  close menu, call action whenever user click some menu item
     *
     * @param {Object} item
     * @return void
     */
    changeRoute(item) {
        if (this.props.history.location.pathname.indexOf(item.path) !== -1) {
            return this;
        }

        this.toggle();

        if (SessionService.needDirectSession() && item.id !== 'settings') {
            this.props.actions.redirectToManageSession();
            this.props.history.replace('/session');

        } else {
            if (item.path) {
                this.props.history.replace(item.path);
            }
        }
    }

    /**
     *  toggle action menu button
     *  @return void
     */
    toggle() {
        this.props.actions.toggle()
    }

    /**
     *  export data button DOM expression
     *  @return string
     *
     * */
    exportDataButton() {
        // return (
        //     <li className="item-export" key="item-export">
        //         <a onClick={() =>
        //             this.props.actions.clickExportItem()}> {this.props.t('Export Unsynced Orders')} </a>
        //     </li>
        // )
        return '';
    }

    /**
     *  logout button DOM expression
     *  @return string
     *
     * */
    logoutButton() {
        return (
            <li className="item-logout" key="item-logout">
                <a onClick={() =>
                    this.onClickLogout()} className="logout"> {this.props.t('Logout')} </a>
            </li>
        )
    }

    /**
     * onclick logout
     */
    onClickLogout() {
        this.props.actions.clickLogoutItem();
    }

    /**
     *  toggle button DOM expression
     *  @return string
     *
     * */
    toggleMenuButton() {
        return (
            <a
                onClick={() => this.toggle()}
                className={this.props.isOpen ? "toggle-menu active" : "toggle-menu"}
            >
                <span>menu</span>
            </a>
        )
    }

    /**
     *  menu item DOM expression
     *  validate permission
     *
     *  @return string
     *
     * */
    getMenuItems() {
        let menuItems = this.props.items.map(item => {
            if (item.component.acl && !permission.isAllowed(item.component.acl)) {
                return '';
            }

            if (item.id === 'session') {
                if (!SessionHelper.isEnableSession()) {
                    if(!item.className.split(' ').includes('hidden')) {
                        item.className += ' hidden';
                    }
                }
            }

            return (
                <li onClick={() => this.changeRoute(item)}
                    key={item.id}
                    className={
                        item.className + ' ' +
                        (this.props.history.location.pathname.indexOf(item.path) !== -1 ? 'active' : '')
                    }>
                    <a onClick={() => this.changeRoute(item)}
                    >{this.props.t(item.title)}</a>
                </li>
            )
        });
        menuItems.push(this.exportDataButton(), this.logoutButton());
        return menuItems;
    }

    clickBackButton() {
        return () => {
            const { actions, currentPage } = this.props;
            if (currentPage === Payment.className) {
                return actions.checkoutToCatalog();
            }
            if (currentPage === GiftcardForm.className) {
                return actions.switchPage(Payment.className);
            }
        }
    }

    /**
     * Init smooth scrollbar for content modal
     */
    initScrollbar(content) {
        if (!this.scrollbar && content) {
            this.scrollbar = content;
        }
    }

    getPosVersion() {
      return ConfigHelper.getConfig('about/product/line')
          + ' ' + ConfigHelper.getConfig('about/product/version')
    }

    canBackToCatalog() {
        const {currentPage, hasPaidOrWaitingGatewayPayment} = this.props;
        return (
            [Payment.className, GiftcardForm.className].indexOf(currentPage) !== -1
            && !hasPaidOrWaitingGatewayPayment
        );
    }

    canUseMenuToggle() {
        const {currentPage} = this.props;
        return [Payment.className, GiftcardForm.className].indexOf(currentPage) === -1;
    }

    /**
     *  component render DOM expression
     *  @return string
     *
     * */
    template() {
        const {isOpen} = this.props;

        if (this.scrollbar) {
            isOpen ? SmoothScrollbar.init(this.scrollbar): SmoothScrollbar.destroy(this.scrollbar);
        }

        return (
            <Fragment>
                <div
                    className={isOpen ? "wrapper-menu active" : "wrapper-menu"}>
                    <div className="menu-title old">
                        <div className="title">{this.state.staffName}</div>
                        <div className="subtitle"> {this.state.PosName} / {this.state.locationName} </div>
                    </div>
                    {layout('menu')('menu_layout')('menu_title')()(this,this.state)}
                    <ul className="item-menu" ref={this.initScrollbar.bind(this)}>
                        {this.getMenuItems()}
                    </ul>
                    <div className="menu-footer">
                      <div className="pos-version">{this.getPosVersion()}</div>
                    </div>
                </div>

                <div className="fixed-wrapper-header">
                    <div
                        className="header-left"
                        style={
                            {display: this.canUseMenuToggle() ? 'block' : 'none'}
                        }
                    >
                        {this.toggleMenuButton()}
                    </div>

                    <span
                        onClick={this.clickBackButton()}
                        className="back-payment"
                        style={{display: this.canBackToCatalog() ? 'block' : 'none'}}/>
                </div>
                <ExportData/>
                <Logout/>
            </Fragment>
        );
    }
}

/**
 *
 * @type {MenuComponent}
 */
const component = ComponentFactory.get(MenuComponent);

export class MenuContainer extends CoreContainer {
    static className = 'MenuContainer';

    /**
     *  map state to props of component
     *
     * @param {Object} state
     * @return {{active, items}}
     */
    static mapState(state) {
        const {items, isOpen} = state.core.menu;
        const {currentPage} = state.core.checkout.index;
        const {quote} = state.core.checkout;
        let hasPaidOrWaitingGatewayPayment = PaymentHelper.hasPaidOrWaitingGatewayPayment(quote.payments);

        return {
            isOpen,
            items,
            currentPage,
            hasPaidOrWaitingGatewayPayment
        }
    }

    /**
     *  map dispatch to props of component
     *
     * @param dispatch
     * @return {{actions: {CLICK_MENU_ITEM: string, CLICK_LOGOUT_ITEM: string, clickMenuItem: function(*), clickLogoutItem: function()}|ActionCreator<any>|ActionCreatorsMapObject}}
     */
    static mapDispatch(dispatch) {
        let props = {
            actions: bindActionCreators({...MenuAction, ...CheckoutAction}, dispatch)
        };
        props.actions.redirectToManageSession = () => dispatch(SessionAction.redirectToManageSession());
        return props;
    }
}

/**
 *
 * @type {MenuContainer}
 */
const container = ContainerFactory.get(MenuContainer);

export default container.withRouter(component)
