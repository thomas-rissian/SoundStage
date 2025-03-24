const API_PORT = 8000;
const API_ADRESS = "http://localhost";
export const SYMFONY_URL = API_ADRESS + ":"+API_PORT
export const API_URL = SYMFONY_URL+"/api";

export async function getAllEvents() {
    const response = await fetch(`${API_URL}/event`);
    return response.json();
}
export async function getEventById(id) {
    const response = await fetch(`${API_URL}/event/${id}`);
    return response.json();
}

export async function getAllArtist() {
    const response = await fetch(`${API_URL}/artist`);
    return response.json();
}
export async function getArtistById(id) {
    const response = await fetch(`${API_URL}/artist/${id}`);
    return response.json();
}