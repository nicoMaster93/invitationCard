const config = {
    "session":false,
    "app-name":"Baby Shower",
    "babyShort": "Christopher P. Hernández García",
    "backend": "http://localhost/proyectsInHouse/baby-shower/back/?endpoint=Guests&action=",
    "location": 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1494.8578366007052!2d-74.0828819025027!3d4.670612546974904!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e3f9b014bae00db%3A0x288b47b53350acdb!2sConjunto%20Residencial%20De%20Vivienda%20El%20Portal%20Del%20J.%20Vargas!5e0!3m2!1sen!2sco!4v1680801901269!5m2!1sen!2sco',
    "max-date":"2023-04-21",
    "date-event": "23 de Abril del 2023 -  01:00 pm"
};
if(!(/localhos/i.test(window.location.host))){
    config["backend"] = "https://web-html.com/baby-shower-meet/back/?endpoint=Guests&action=";
}

export default config