// eslint-disable-next-line
import {Subject, Subscription} from "rxjs";

let events = {};

/**
 * Get a subject singleton
 *
 * @param {String} name
 * @returns {Subject}
 */
function getSubject(name) {
    if (!events.hasOwnProperty(name)) {
        events[name] = new Subject();
    }
    return events[name];
}

/**
 * Subscribe to an event with name
 * If tag is specified, only last observer with this tag is valid
 *
 * @param {String} name
 * @param {Function} observer
 * @param {String} tag
 * @returns {Subscription}
 */
export function subscribe(name, observer, tag = '') {
    let subject = getSubject(name);
    let listener = subject.subscribe(observer);
    if ('' !== tag) {
        if (!subject.tags) {
            subject.tags = {};
        }
        if (undefined !== subject.tags[tag]) {
            subject.tags[tag].unsubscribe();
        }
        subject.tags[tag] = listener;
    }
    return listener;
}

/**
 * Dispatch event with event data
 *
 * Data will be passed to observer functions
 *
 * @param {String} name
 * @param {any} data
 */
export function dispatch(name, data) {
    getSubject(name).next(data);
}

// Export Aliases
export {
    subscribe as listen,
    dispatch as fire,
};
