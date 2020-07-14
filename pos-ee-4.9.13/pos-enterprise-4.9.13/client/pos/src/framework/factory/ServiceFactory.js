import AbstractFactory from "./AbstractFactory";
import ObjectManager from "../ObjectManager";

export class ServiceFactory extends AbstractFactory{

    /**
     * get target service to use
     *
     * @param object
     * @return {*}
     */
    get(object) {
        return ObjectManager.get(this.getObject(`Service`, object));
    }

}

let serviceFactory = new ServiceFactory();

export default serviceFactory;
