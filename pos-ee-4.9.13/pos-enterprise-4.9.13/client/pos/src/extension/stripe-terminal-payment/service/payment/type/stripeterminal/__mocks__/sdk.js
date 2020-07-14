window.StripeTerminal = {
    create: () => ({}),
    processPayment: () => Promise.resolve({}),
    getConnectionStatus: () => 'connected',
    collectPaymentMethod: () => Promise.resolve({}),
    cancelCollectPaymentMethod: () => Promise.resolve({}),
    setReaderDisplay: () => Promise.resolve({}),
    connectReader: () => Promise.resolve({}),
    disconnectReader: () => Promise.resolve({}),
    discoverReaders: () => Promise.resolve({}),
    setReaderDisplay: () => Promise.resolve({}),
    fetchPaymentIntent: () => {
        return {
            internalPromise: Promise.resolve({}),
        }
    },
};
