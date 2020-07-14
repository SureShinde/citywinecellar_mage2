# Event Observer

An event has two parts. In the core, we `dispatch` (or `fire`) an event to create customize point. In the plugin, we `subscribe` (or `listen`) to execute our custom code.

## Fire an event
```js
import {fire} from "../src/event-bus";

...
// Fire an event to allow modify logic
fire('[name_of_event]', [event_data]);
```

## Listen an event
```js
import {listen} from "../src/event-bus";

...
// Listen an event
listen('[name_of_event]', (eventData) => {
    // Custom logic here
}, [listener_tag]);
```

1. **name_of_event**: must be same as `fire` name_of_event
2. **observer**: is a function that receive `eventData` parameter from `fire` event
3. **listener_tag**: to identify listener. Listeners that registed with same tag is overrided. Only last observer with same tag (and same name_of_event) is active.

## Event naming
To manage events easier, we should naming events following this format
`[object_type]_[object_name]_[method/action]_[event_position]`

For example, an event name `model_customer_save_after` means
    1. object_type: `model`
    2. object_name: `customer`
    3. method/action: `save`
    4. event_position: `after`

## Recommended Reading
[Magento's event best practice](https://devdocs.magento.com/guides/v2.3/ext-best-practices/extension-coding/observers-bp.html)
