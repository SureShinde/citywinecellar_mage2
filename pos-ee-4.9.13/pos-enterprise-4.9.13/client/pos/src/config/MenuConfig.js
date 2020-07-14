import Checkout from '../view/component/checkout/Checkout';
import Extension from "../framework/Extension";
import OrderHistory from "../view/component/order/OrderHistory";
import OnHoldOrder from "../view/component/on-hold-order/OnHoldOrder";
import Setting from "../view/component/settings/Setting";
import SessionManagement from "../view/component/session/SessionManagement";
import i18n from "./i18n";

/**
 * Menu config
 * @type {{items: Array}} - list menu item
 * @type {{defaultItem(): (T|undefined|{id, title, path, component, className, sortOrder}|*), items: *[]}}
 */
const MenuConfig = {
    /**
     * get default active menu item, after loading complete
     * @return {T | undefined | {id, title, path, component, className, sortOrder} | *}
     */
    defaultItem() {
        return this.items.find(item => {
            return item.id === 'checkout'
        }) || this.items[0]
    },

    /**
     *
     * @return {*|*[]}
     */
    getMenuItem() {
        /**
         * merge extension menu with core menu
         * @type {Array}
         */

        let menuFromExtension = Extension.ExtensionConfig.menu;
        menuFromExtension = menuFromExtension &&
        Object.keys(menuFromExtension).length ? Object.values(menuFromExtension) : [];
        const unsortedMenu = MenuConfig.items.concat(menuFromExtension);

        unsortedMenu.forEach(item => {
            if (item.component.name === '') {
                item.component = item.component();
            }
        });

        /**
         *  sort menu by sortOrder property value
         *  @param object, object
         *  @return Array
         * */
        return unsortedMenu.sort((a, b) => {
            return a.sortOrder - b.sortOrder
        });
    },

    /**
     * menu items
     */
    items: [
        {
            id: "checkout",
            title: i18n.translator.translate("Checkout"),
            path: "/checkout",
            component: Checkout,
            className: "item-checkout",
            sortOrder: 10,

        },
        {
            id: "order",
            title: i18n.translator.translate("Order History"),
            path: "/order",
            component: OrderHistory,
            className: "item-order",
            sortOrder: 20
        },
        {
            id: "hold",
            title: i18n.translator.translate("On-hold Orders"),
            path: "/hold",
            component: OnHoldOrder,
            className: "item-orderhold",
            sortOrder: 30
        },
        {
            id: "session",
            title: i18n.translator.translate("Session Management"),
            path: "/session",
            component: SessionManagement,
            className: "item-session",
            sortOrder: 40
        },
        {
            id: "settings",
            title: i18n.translator.translate("Settings"),
            path: "/settings",
            component: Setting,
            className: "item-settings",
            sortOrder: 50
        }
    ]
};

export default MenuConfig
