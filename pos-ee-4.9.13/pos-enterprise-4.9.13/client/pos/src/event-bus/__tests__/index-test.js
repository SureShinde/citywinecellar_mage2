import {dispatch, subscribe, fire, listen} from '..'

describe('Event Testing', () => {
    it('Subscribe and dispatch events', () => {
        let observer = jest.fn();

        // Before subscribe
        dispatch('event');
        expect(observer.mock.calls.length).toBe(0);

        // Subscribe to event
        let listener = subscribe('event', observer);
        dispatch('event', 'data');
        expect(observer.mock.calls.length).toBe(1);
        expect(observer.mock.calls[0][0]).toBe('data');

        // Dispatch other event
        dispatch('other');
        expect(observer.mock.calls.length).toBe(1);

        // Redispatch event
        dispatch('event', 'data1');
        expect(observer.mock.calls.length).toBe(2);
        expect(observer.mock.calls[1][0]).toBe('data1');

        // Unsubscribe event
        listener.unsubscribe();
        dispatch('event', 'data');
        expect(observer.mock.calls.length).toBe(2);
    });

    it ('Listen without tag', () => {
        let observer1 = jest.fn();
        let observer2 = jest.fn();
        listen('event', observer1);
        listen('event', observer2);
        fire('event', 'data');
        expect(observer1.mock.calls.length).toBe(1);
        expect(observer2.mock.calls.length).toBe(1);
    });

    it ('Listen with tag, ignore first observer', () => {
        let observer1 = jest.fn();
        let observer2 = jest.fn();
        listen('event', observer1, 'tag1');
        listen('event', observer2, 'tag1');
        fire('event', 'data');
        expect(observer1.mock.calls.length).toBe(0);
        expect(observer2.mock.calls.length).toBe(1);
    });

    it ('Fire and listen', () => {
        let observer = jest.fn();
        listen('event', observer);
        fire('event', 'data');
        expect(observer.mock.calls.length).toBe(1);
        expect(observer.mock.calls[0][0]).toBe('data');
    });
});
