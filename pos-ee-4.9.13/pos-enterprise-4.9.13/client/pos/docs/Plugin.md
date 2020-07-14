# Plugin Mechanism

A module can modify a method of a class from `core` or `other module`.

## Config Plugin
In the module, we register a plugin in file `etc/config.js`.

For example: We plugin for MenuComponent
```js
import ModuleConfigAbstract from "../../ModuleConfigAbstract";

class HelloWorldConfig extends ModuleConfigAbstract{
    module = ['helloworld'];
    plugin = {
        component: {
            MenuComponent: {
                // method: plugin
                changeRoute: {
                    // name: plugin config
                    giftcard: {
                        sortOrder: 20,
                        disabled: false,
                        before: (item) => {
                            console.log('20 Before' + item.path);
                            return item;
                        },
                        around: (proceed, item) => {
                            console.log('20 Before Around' + item.path);
                            let result = proceed(item);
                            console.log('20 After Around' + item.path);
                            return result;
                        },
                        after: (result, item) => {
                            console.log('20 After' + item.path);
                            return result;
                        },
                    },
                    rewardpoint: {
                        sortOrder: 10,
                        disabled: true,
                        before: (item) => {
                            console.log('10 Before' + item.path);
                            return item;
                        },
                        around: (proceed, item) => {
                            console.log('10 Before Around' + item.path);
                            let result = proceed(item);
                            console.log('10 After Around' + item.path);
                            return result;
                        },
                        after: (result, item) => {
                            console.log('10 After' + item.path);
                            return result;
                        },
                    }
                },
            }
        }
    };
}

export default (new HelloWorldConfig());
```

We have 7 type of class can be plugged in:

1. service
2. resource_model
3. repository
4. epic
5. container
6. component
7. data_resource

To determine which type of class, we can find `Factory` type in source code. Example:
```js
/**
 *
 * @type {MenuComponent}
 */
const component = ComponentFactory.get(MenuComponent);
```

## Plugin Type and Running Order
Please read in [Magento Plugin](https://devdocs.magento.com/guides/v2.2/extension-dev-guide/plugins.html)

Some important notes:

1. We use `this` to reference to original object of plugin class. So, we don't need `$subject` like Magento
2. Each plugin (after, before, around) is a function
