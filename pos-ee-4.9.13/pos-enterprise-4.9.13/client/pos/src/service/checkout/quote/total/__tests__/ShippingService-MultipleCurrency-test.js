import QuoteTotalShippingService from '../ShippingService';
import Config from "../../../../../config/Config";

describe('Integration test apply custom price', () => {
    let mocksBackup = {};
    beforeAll(() => {
        // Mock config for test env
        mocksBackup.config = Config.config;
        mocksBackup.location_address = Config.location_address;
        mocksBackup.shipping_methods = Config.shipping_methods;

        Config.config = {
            guest_customer: {},
            settings: [
                {path: "tax/calculation/algorithm", value: "TOTAL_BASE_CALCULATION"},
                {path: "customer/create_account/default_group", value: "1"},
            ],
            customer_groups: [
                {id: 0, code: "NOT LOGGED IN", tax_class_id: 3},
                {id: 1, code: "General", tax_class_id: 3},
            ],
            currencies: [
                {
                    code: "EUR",
                    currency_name: "Euro",
                    currency_rate: 1,
                    currency_symbol: "€",
                    is_default: 0
                },
                {
                    code: "USD",
                    currency_name: "US Dollar",
                    currency_rate: 1,
                    currency_symbol: "$",
                    is_default: 1,
                },
            ],
            current_currency_code: "EUR",
        };

        Config.location_address = {};
        Config.config.shipping = {shipping_methods: "flatrate"};
        Config.shipping_methods = [{
            code: "flatrate_flatrate",
            condition_name: "",
            description: "",
            error_message: "This shipping method is not available. To use this shipping method, please contact us.",
            free_shipping_subtotal: 0,
            handling_action: "O",
            handling_fee: 0,
            handling_type: "F",
            include_virtual_price: 0,
            is_default: 0,
            max_package_weight: 0,
            price: 5,
            rates: [],
            shipment_request_type: 0,
            specific_countries_allow: 0,
            specific_country: "",
            title: "Flat Rate - Fixed",
            type: "I"
        }];
    });

    afterAll(() => {
        Config.config = mocksBackup.config;
        Config.location_address = mocksBackup.location_address;
        Config.shipping_methods = mocksBackup.shipping_methods;
    });

    let data = [
        {
            testCaseId: 'ASM-05',
            title: 'Check shipping fee with currency convert rate is 0.0009',
            currency_rate: "0.0009",
            input: {
                quote: {
                    items: [
                        {
                            is_virtual: 0,
                            parent_item_id: null,
                            qty: 1,
                            weight: null,
                            product: {
                                is_virtual: false,
                                weight: null,
                                weight_type: null
                            }
                        }
                    ],
                    items_qty: 1
                },
                address: {
                    address_type: "shipping",
                    shipping_method: "flatrate_flatrate",
                    city: "Calder",
                    company: "N/A",
                    country_id: "US",
                    region_id: 33,
                    postcode: "49628-7978",
                    base_subtotal: 7,
                    base_subtotal_with_discount: 7,
                    base_virtual_amount: 0,
                    base_subtotal_total_incl_tax: 7.8,
                    street: [
                        "6146 Honey Bluff Parkway"
                    ],
                    current_shipping_method: {
                    }
                }
            },
            expect: {
                shipping_amount: 0.00,
                base_shipping_amount: 5.00
            }
        },
        {
            testCaseId: 'ASM-06',
            title: 'Check shipping fee with currency convert rate is 2.9277',
            currency_rate: "2.9277",
            input: {
                quote: {
                    items: [
                        {
                            is_virtual: 0,
                            parent_item_id: null,
                            qty: 1,
                            weight: null,
                            product: {
                                is_virtual: false,
                                weight: null,
                                weight_type: null
                            }
                        }
                    ],
                    items_qty: 1
                },
                address: {
                    address_type: "shipping",
                    shipping_method: "flatrate_flatrate",
                    city: "Calder",
                    company: "N/A",
                    country_id: "US",
                    region_id: 33,
                    postcode: "49628-7978",
                    base_subtotal: 7,
                    base_subtotal_with_discount: 7,
                    base_virtual_amount: 0,
                    base_subtotal_total_incl_tax: 7.8,
                    street: [
                        "6146 Honey Bluff Parkway"
                    ],
                    current_shipping_method: {
                    }
                }
            },
            expect: {
                shipping_amount: 14.64,
                base_shipping_amount: 5.00
            }
        }
    ];

    for (let i = 0; i < data.length; i++) {
        it(`[${data[i].testCaseId}] ${data[i].title}`, () => {
            Config.config.currencies[0].currency_rate = data[i].currency_rate;

            let quote = data[i].input.quote;
            let address = data[i].input.address;
            let total = {};

            QuoteTotalShippingService.collect(quote, address, total);

            expect(total.base_shipping_amount).toBe(data[i].expect.base_shipping_amount);
            expect(total.shipping_amount).toBe(data[i].expect.shipping_amount);
        })
    }
});
