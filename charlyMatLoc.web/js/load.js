export var allEvents = null;
export var allCategories = null;
export async function load() {
    try {
        const response = await fetch('http://docketu.iutnc.univ-lorraine.fr:13000/api/evenements', { method: 'GET' });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        allEvents = Array.isArray(data.evenement) ? data.evenement : [];
    } catch (error) {
        console.error('Unable to fetch events:', error);
        return [];
    }
    try {
        const response = await fetch('http://docketu.iutnc.univ-lorraine.fr:13000/api/categories');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        allCategories = data.categories;
    } catch (error) {
        console.error('Unable to fetch categories:', error);
        return [];
    }
}