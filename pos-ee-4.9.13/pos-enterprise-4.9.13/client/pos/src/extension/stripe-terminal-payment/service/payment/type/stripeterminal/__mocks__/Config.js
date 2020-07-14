import Config from "../../../../../../../config/Config";

Config.config = {
    settings: [{}],
    base_currency_code: 'USD',
    current_currency_code: 'USD',
    price_formats: [
        {
            "currency_code": "EUR",
            "decimal_symbol": ".",
            "group_symbol": ",",
            "group_length": 3,
            "integer_required": 0,
            "pattern": "€%s",
            "precision": 2,
            "required_precision": 2
        },
        {
            "currency_code": "USD",
            "decimal_symbol": ".",
            "group_symbol": ",",
            "group_length": 3,
            "integer_required": 0,
            "pattern": "$%s",
            "precision": 2,
            "required_precision": 2
        }
    ],
    currencies: [
        {
            "code": "EUR",
            "currency_name": "Euro",
            "currency_symbol": "€",
            "is_default": 0,
            "currency_rate": "0.706700000000"
        },
        {
            "code": "USD",
            "currency_name": "US Dollar",
            "currency_symbol": "$",
            "is_default": 1,
            "currency_rate": 1
        }]
};