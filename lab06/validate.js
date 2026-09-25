"use strict";

const Form = class {
    /**
     * Holds the properties of the form
     * 
     * @param {HTMLElement} form
     * @param {HTMLElement} firstName 
     * @param {HTMLElement} lastName
     * @param {HTMLElement} age
     * @param {HTMLElement} partySize
     * @param {HTMLCollectionOf<HTMLInputElement>} species
     * @param {HTMLElement} beard
     */
    constructor(form, firstName, lastName, age, partySize, species, beard) {
        /** @type {HTMLElement} */
        this.form = form;
        /** @type {HTMLElement} */
        this.firstName = firstName;
        /** @type {HTMLElement} */
        this.lastName = lastName;
        /** @type {HTMLElement} */
        this.age = age;
        /** @type {HTMLElement} */
        this.partySize = partySize;
        /** @type {HTMLCollectionOf<HTMLInputElement>} */
        this.species = species;
        /** @type {HTMLElement} */
        this.beard = beard;
    }
}

/**
 * Contains the state of the page.
 * 
 * @param {Form} form 
 */
const AppState = class {
    /**
     * 
     * @param {Form} form 
     */
    constructor(form) {
        /** @type {Form} */
        this.form = form;
    }
}

/**
 * @type {AppState}
 */
var appState;

function setDelegates() {
    appState.form.form.onsubmit = validateDelegate;
}

function registerElements() {
    let formElem = document.getElementById("regform");
    let firstNameElem = document.getElementById('firstname');
    let lastNameElem = document.getElementById('lastname');
    let ageElem = document.getElementById('age');
    let partySizeElem = document.getElementById('partySize');
    let speciesElems = document.getElementById('species').getElementsByTagName('input');
    let beardElem = document.getElementById('beard');

    let form = new Form(formElem, firstNameElem, lastNameElem, ageElem, partySizeElem, speciesElems, beardElem);
    appState = new AppState(form);
}

/** 
 * returns the species name of the selected species radio button.
 * 
 * @returns {string}
 */
function getSpecies() {
    let speciesName = "Unknown";
    let species = appState.form.species;
    for (const s of species) {
        if (s.checked) {
            speciesName = s.value;
            break;
        }
    }
    return speciesName;
}

const Result = class {
    constructor(ok, val) {
        /** @type {boolean} */
        this.ok = ok;
        /** @type {any} */
        this.val = val;
    }

    isValue() {
        return this.ok;
    }

    /**
     * 
     * @returns @type {any} returns the value if ok, otherwise returns the error message.
     */
    unwrap() {
        return this.val;
    }
}

function validateSpeciesBeardLength() {
    let species = getSpecies();
    let beardLength = appState.form.beard.value;
    let age = appState.form.age.value;
    let errMsg = "";
    let result = true;

    if (isNaN(beardLength)) {
        errMsg += "Beard length must be a number.\n";
        result = false;
        return new Result(result, errMsg);
    }



    if (species == "Dwarf" && age > 30 && beardLength < 12) {
        errMsg += "Dwarves must have a beard of at least 12 inches.\n";
        result = false;
    } else if ((species == "Elf" || species == "Hobbit") && beardLength > 0) {
        errMsg += species + " cannot have a beard.\n";
        result = false;
    }

    return new Result(result, errMsg);
}

function validateSpeciesAge() {
    let age = appState.form.age.value;
    let errMsg = "";
    let result = true;

    if (isNaN(age)) {
        errMsg += "Age must be a number.\n";
        result = false;
    } else if (age < 18) {
        errMsg += "You must be 18 years old or older.\n";
        result = false;
    } else if (age >= 10_000) {
        errMsg += "You must be less than 10,000 years old.\n";
        result = false;
    }

    let species = getSpecies();
    switch (species) {
        case "Human":
            if (age > 120) {
                errMsg += "You must be 120 years old or less.\n";
                result = false;
            }
            break;
        case "Dwarf":
        case "Hobbit":
            if (age > 150) {
                errMsg += "You must be 150 years old or less.\n";
                result = false;
            }
            break;
        case "Elf":
            break;
        default:
            errMsg += "We don't allow your kind on our tours.\n";
            result = false;
            break;
    }
    return new Result(result, errMsg);
}

/**
 * Validates the form.
 * 
 * @returns {boolean} result of validation, true if ok.
 */
function validateDelegate() {
    let errMsg = "";
    let result = true;

    let firstName = appState.form.firstName.value;
    if (!firstName.match(/^[a-zA-Z]+$/)) {
        errMsg += "Your first name must only contain alphabetical characters.\n";
        result = false;
    }


    let lastName = appState.form.lastName.value;
    if (!lastName.match(/^[a-zA-Z\-]+$/)) {
        errMsg += "Your last name must only contain alphabetical characters or hyphen.\n";
        result = false;
    }

    let ret = validateSpeciesAge();
    if (result) {
        result = ret.isValue();
    }
    errMsg += ret.unwrap();

    ret = validateSpeciesBeardLength();
    if (result) {
        result = ret.isValue();
    }
    errMsg += ret.unwrap();

    let partySize = appState.form.partySize.value;
    if (partySize < 1 || partySize > 100) {
        errMsg += "Party size must be between 1 and 100.\n";
        result = false;
    }

    if (errMsg != "") {
        alert(errMsg);
    }

    return result;
}

function init() {
    registerElements();
    setDelegates();

}

window.onload = init