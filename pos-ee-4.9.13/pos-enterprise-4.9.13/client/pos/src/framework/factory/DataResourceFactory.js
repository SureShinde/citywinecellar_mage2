import AbstractFactory from "./AbstractFactory";

export class DataResourceFactory extends AbstractFactory{

    /**
     * get target resource model to use
     *
     * @param object
     * @return {*}
     */
    get(object) {
        return this.getObject(`data_resource`, object);
    }

}

let dataResourceFactory = new DataResourceFactory();

export default dataResourceFactory;
