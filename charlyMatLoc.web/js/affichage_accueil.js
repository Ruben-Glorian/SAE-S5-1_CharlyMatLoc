import Handlebars from 'handlebars';
import {allEvents, load} from './load';

export async function displayAccueil() {
    if (allEvents === null) {
        await load();
    }
    try {
        const accueilTemplate = document.querySelector('#accueilTemplate').innerHTML;
        const template = Handlebars.compile(accueilTemplate);
        document.querySelector('#body').innerHTML = template();
    }catch (error){
        console.error('Error displaying accueil:', error);
        document.querySelector('#body').innerHTML = '<p>Unable to display.</p>';
    }
}
