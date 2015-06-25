<?php

function randomFirstName() {
    return random("Mario", "Dario", "Lara", "Giulia", "Luisa", "Tania", "Marco", "Andrea", "Diego", "Gianluca", "Michele", "Ernesto", "Davide", "Carlo", "Carla", "Gianni", "Gianna", "Francesco", "Francesca");
}

function randomLastName() {
    return random("Rossi", "Verdi", "Pasquali", "Peresson", "De Francesco", "Lorenzon", "Pinto", "De Luigi", "Bersani", "Speranza", "Girondi", "Fatrane", "Allegri", "Dello", "De Preti", "Mieli", "Paulate", "Verri", "Siniscalco");
}
function randomId() {
    return "EMPL_AW_" . str_pad(mt_rand(500, 1000), 4, '0', STR_PAD_LEFT);
}
function randomStoreCode() {
    return "AW" . str_pad(mt_rand(500, 1000), 4, '0', STR_PAD_LEFT);
}
function randomStreet() {
    return random("Viale Verdi", "Via Roma", "Via Udine", "Viale Montecristo", "Vicolo Corto", "Vicolo stretto");
}
function randomNr() {
    return random("1", "7", "12", "14/c", "19", "52", "44", "7/b", "20", "20/a", "25", "34", "35/b", "17");
}
function randomCity() {
    return random("Pero", "Romano", "Desenzano", "Merano", "Villanova");
}
function randomCountry() {
    return random("IT");
}
function randomRegion() {
    return random("UD", "PN", "TS", "FI", "RM", "TO");
}
function randomPostcode() {
    return mt_rand(12345, 99999);
}
function randomTelephone() {
    return mt_rand(4012345, 4999999);
}
function randomStore() {
    return random("Retail", "Franchising");
}
function random() {
    $a = func_get_args();
    return $a[mt_rand(0, func_num_args() - 1)];
}

function mkMail($n, $l) {
    return "name.surname+" . preg_replace('|\s+|', '_', strtolower($n . "." . $l . '.' . mt_rand(100000000, 999999999) . '_' . mt_rand(100000000, 999999999) . '@example.com'));
}

/*
 * first_name
 * last_name
 * aw_store_code
 * aw_employee_id
 * email
 * street
 * city
 * country
 * region
 * postcode
 * telephone
 * group
 */

# 989999
echo "_address_firstname,_address_lastname,firstname,lastname,email,_address_street,_address_city," . "_address_country_id,_address_region,_address_postcode,_address_telephone,group_id,_website,_store,_address_default_billing_,_address_default_shipping_\n";
for ($i = 0; $i < 10000; $i++) {
    $firstname = randomFirstName();
    $lastname = randomLastName();
    $email = mkMail($firstname, $lastname);
    $street = randomStreet();
    $nr = randomNr();
    $city = randomCity();
    $country = randomCountry();
    $region = randomRegion();
    $postcode = randomPostcode();
    $phone = randomTelephone();
    echo "\"$firstname\",\"$lastname\",\"$firstname\",\"$lastname\",\"$email\",\"$street, $nr\",\"$city\",\"$country\",\"$region\",\"$postcode\",\"$phone\",1,\"base\",\"default\",1,1\n";
}
