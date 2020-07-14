import LocalStorageHelper from "../helper/LocalStorageHelper";
import CoreService from "./CoreService";
import ServiceFactory from "../framework/factory/ServiceFactory";
import LocationResourceModel from "../resource-model/user/LocationResourceModel";

export class LocationService extends CoreService{
    static className = 'LocationService';
    resourceModel = LocationResourceModel;
    /**
     * get location name local storage
     *
     * @return {string}
     */
    getCurrentLocationName() {
        return LocalStorageHelper.get(LocalStorageHelper.LOCATION_NAME);
    }

    /**
     * get location id from localStorage
     * @returns {*|string}
     */
    getCurrentLocationId(){
        return Number(LocalStorageHelper.get(LocalStorageHelper.LOCATION_ID));
    }

    /**
     * save location id and location name local storage
     *
     * @param locationId
     * @param locationName
     */
    saveCurrentLocation(locationId, locationName, locationAddress, locationTelephone) {
        LocalStorageHelper.set(LocalStorageHelper.LOCATION_ID, locationId);
        LocalStorageHelper.set(LocalStorageHelper.LOCATION_NAME, locationName);
        LocalStorageHelper.set(LocalStorageHelper.LOCATION_ADDRESS, JSON.stringify(locationAddress));
        LocalStorageHelper.set(LocalStorageHelper.LOCATION_TELEPHONE, locationTelephone);
    }

    /**
     * save location receipt in local storage
     *
     * @param receiptHeader
     * @param receiptFooter
     */
    saveReceiptInformation(receiptHeader, receiptFooter) {
        if (!receiptHeader) {
            receiptHeader = '';
        }
        if (!receiptFooter) {
            receiptFooter = '';
        }
        LocalStorageHelper.set(LocalStorageHelper.RECEIPT_HEADER, receiptHeader);
        LocalStorageHelper.set(LocalStorageHelper.RECEIPT_FOOTER, receiptFooter);
    }

    /**
     * remove location id and location name local storage
     *
     * @return void
     */
    removeCurrentLocation() {
        LocalStorageHelper.remove(LocalStorageHelper.LOCATION_ID);
        LocalStorageHelper.remove(LocalStorageHelper.LOCATION_NAME);
    }

    /**
     * get locations from localStorage
     * @returns {*|string}
     */
    getLocationsInLocalStorage(){
        return LocalStorageHelper.get(LocalStorageHelper.LOCATIONS);
    }

    /**
     * user assign pos
     * @param posId
     * @param locationId
     * @param currentStaffId
     * @returns {*|promise|{type, posId, locationId, currentStaffId}}
     */
    assignPos(posId, locationId, currentStaffId) {
        let locationResource = this.getResourceModel();
        return locationResource.assignPos(posId, locationId, currentStaffId);
    }

    /**
     * get current location address
     * @returns {*|string}
     */
    getCurrentLocationAddress(){
        return JSON.parse(LocalStorageHelper.get(LocalStorageHelper.LOCATION_ADDRESS));
    }

    /**
     * get current location telephone
     * @returns {*|string}
     */
    getCurrentLocationTelephone(){
        return LocalStorageHelper.get(LocalStorageHelper.LOCATION_TELEPHONE);
    }

    /**
     * get current location receipt header
     * @returns {*|string}
     */
    getCurrentLocationHeader(){
        return LocalStorageHelper.get(LocalStorageHelper.RECEIPT_HEADER);
    }

    /**
     * get current location receipt footer
     * @returns {*|string}
     */
    getCurrentLocationFooter(){
        return LocalStorageHelper.get(LocalStorageHelper.RECEIPT_FOOTER);
    }

    /**
     * save store name
     *
     * @param storeName
     */
    setCurrentStoreName(storeName) {
        if (!storeName) {
            storeName = '';
        }
        LocalStorageHelper.set(LocalStorageHelper.STORE_NAME, storeName);
    }

    /**
     * get current store name
     * @returns {*|string}
     */
    getCurrentStoreName(){
        return LocalStorageHelper.get(LocalStorageHelper.STORE_NAME);
    }


    /**
     * Call LocationResourceModel request get list location
     *
     * @param queryService
     */
    getNewLocations(queryService) {
        return this.getDataOnline(queryService);
    }

    /**
     * get location by id
     * @returns {string}
     */
    getLocationById(locationId) {
        let locationsString = this.getLocationsInLocalStorage();
        let locations = [];
        if (locationsString && locationsString !== "") {
            locations = JSON.parse(locationsString);
        }
        return locations.find(location => location.location_id === locationId);
    }
    /**
     * get display location
     * @param order
     * @return {*}
     */
    getDisplayLocation(order) {
        let locationId = order.pos_location_id ? order.pos_location_id : order.warehouse_id;
        if (locationId) {
            let location = this.getLocationById(locationId);
            if (location) {
                return location.name;
            }
        }
        return "";
    }
}

/**
 * @type {LocationService}
 */
let locationService = ServiceFactory.get(LocationService);

export default locationService;
