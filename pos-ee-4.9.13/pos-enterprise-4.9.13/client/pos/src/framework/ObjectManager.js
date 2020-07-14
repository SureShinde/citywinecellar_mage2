/**
 * Object manager
 */
export class ObjectManager {
    instances = {};

    /**
     * get object
     * @param Class
     * @returns {*}
     */
    get(Class) {
        let className = Class.className;
        if (!className) {
            return new Class();
        }

        if(!this.instances[className]) {
            this.instances[className] = new Class();
        }
        return this.instances[className];
    }
}

/**
 *
 * @type {ObjectManager}
 */
let objectManager = new ObjectManager();

export default objectManager;
