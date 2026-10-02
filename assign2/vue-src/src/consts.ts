// copied from part2.js

// TODO move these consts to DB when introduced
export const PRODUCT_NAMES: Record<string, string> = {
    "pokemon-usb": "Pokémon USB Drive",
    "extreme-usb": "Extreme Encrypted USB Drive",
    "classic-usb-a": "Classic USB-A Drive"
};

export const PRODUCT_PRICES: Record<string, number> = {
    "pokemon-usb": 29.95,
    "extreme-usb": 89.95,
    "classic-usb-a": 14.95
};

export const STORAGE_NAMES: Record<string, string> = {
    "32gb": "32 GB",
    "64gb": "64 GB",
    "128gb": "128 GB",
    "256gb": "256 GB"
};
export const STORAGE_PRICES: Record<string, number> = {
    "32gb": 0,
    "64gb": 10,
    "128gb": 20,
    "256gb": 35
};

// optional extras, charged once per order
export const EXTRA_NAMES: Record<string, string> = {
    "express-shipping": "Express shipping",
    "gift-wrap": "Gift wrapping",
    "extended-warranty": "Extended 3-year warranty"
};
export const EXTRA_PRICES: Record<string, number> = {
    "express-shipping": 9.95,
    "gift-wrap": 4.00,
    "extended-warranty": 12.00
};

// valid first digits for state postcodes
export const STATE_VALID_DIGITS: Record<string, string[]> = {
    ACT: ["0"],
    NSW: ["1", "2"],
    NT: ["0"],
    QLD: ["4", "9"],
    SA: ["5"],
    TAS: ["7"],
    VIC: ["3", "8"],
    WA: ["6"]
};
