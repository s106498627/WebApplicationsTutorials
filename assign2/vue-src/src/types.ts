export interface Details {
    firstName: string;
    lastName: string;
    email: string;
    street: string;
    suburb: string;
    state: string;
    postcode: string;
    phone: string;
    contact: string;
}

export interface CartItem {
    product: string;
    capacity: string;
    quantity: string;// input mode allows non-numeric chars initially so has to be string
}
