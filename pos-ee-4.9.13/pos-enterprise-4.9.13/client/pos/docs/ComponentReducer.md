# Component and Reducer

## Reducer

Register a reducer, we must register via `etc/config.js` file:
```js
import HelloWorld from "../view/reducer";
import ModuleConfigAbstract from "../../ModuleConfigAbstract";

class HelloWorldConfig extends ModuleConfigAbstract{
    module = ['helloworld'];
    reducer = { HelloWorld };
}

export default (new HelloWorldConfig());
```

## Menu
Register a POS menu

```js
import {HelloWorldContainerConnection} from '../view'
import ModuleConfigAbstract from "../../ModuleConfigAbstract";

class HelloWorldConfig extends ModuleConfigAbstract{
    module = ['helloworld'];
    menu = {
        test: {
            "id": "test",
            "title": "Test",
            "path": "/test",
            "component": HelloWorldContainerConnection,
            "className": "item-checkout",
            "sortOrder": 20
        }
    };
}

export default (new HelloWorldConfig());
```

## Component

`HelloWorldContainerConnection` is registered inside a menu. Other way, we can register component via `layout`, `rewrite`, `plugin`, `event` mechanism.

## Reference
1. [POS Sample Extension](https://github.com/Magestore/pwapos-client-omc-2.0/tree/feature/extension_example/src/extension)
2. [Hello World Config](https://github.com/Magestore/pwapos-client-omc-2.0/blob/feature/extension_example/src/extension/helloworld/etc/config.js)
