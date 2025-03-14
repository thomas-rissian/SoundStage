import { getAllEvents } from "../../services/api.js";
import { useEffect, useState } from "react";
import { Link } from "react-router-dom";

export function AllEvents() {
    const [events, setEvents] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedArtist, setSelectedArtist] = useState('');
    const [selectedDate, setSelectedDate] = useState('');
    const [filteredEvents, setFilteredEvents] = useState([]);

    useEffect(() => {
        setLoading(true);
        getAllEvents()
            .then((data) => {
                setEvents(data);
                setLoading(false);
            })
            .catch((error) => {
                console.error(error);
                setLoading(false);
            });
    }, []);

    useEffect(() => {
        filterAndSortEvents();
    }, [searchTerm, selectedArtist, selectedDate, events]);

    const filterAndSortEvents = () => {
        let filtered = events;

        // Filtrage par nom d'événement
        if (searchTerm) {
            filtered = filtered.filter(event =>
                event.name.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }

        // Filtrage par artiste
        if (selectedArtist) {
            filtered = filtered.filter(event =>
                event.artist.name.toLowerCase().includes(selectedArtist.toLowerCase())
            );
        }

        // Filtrage par date
        if (selectedDate) {
            filtered = filtered.filter(event =>
                new Date(event.date).toLocaleDateString() === new Date(selectedDate).toLocaleDateString()
            );
        }

        setFilteredEvents(filtered);
    };

    if (loading) {
        return <p>Loading...</p>;
    }

    return (
        <div className="event-page">
            <h1>Événements</h1>

            {/* Filtres */}
            <div className="event-filter-section">
                {/* Recherche par nom d'événement */}
                <input
                    type="text"
                    placeholder="Rechercher un événement..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="event-search-input"
                />

                {/* Filtrer par artiste */}
                <input
                    type="text"
                    placeholder="Filtrer par artiste..."
                    value={selectedArtist}
                    onChange={(e) => setSelectedArtist(e.target.value)}
                    className="artist-search-input"
                />

                {/* Filtrer par date */}
                <input
                    type="date"
                    value={selectedDate}
                    onChange={(e) => setSelectedDate(e.target.value)}
                    className="event-date-input"
                />
            </div>

            {/* Liste des événements */}
            <div className="event-list">
                {filteredEvents.map((event) => (
                    <div key={event.id} className="event-item">
                        <h2>
                            <Link to={`/event/${event.id}`} className="event-link">
                                {event.name}
                            </Link>
                        </h2>
                        <p><strong>Date:</strong> {new Date(event.date).toLocaleDateString()}</p>
                        <p><strong>Artiste:</strong>
                            <Link to={`/artist/${event.artist.id}`} className="artist-link">
                                {event.artist.name}
                            </Link>
                        </p>
                        <p><strong>Nombre d'utilisateurs inscrits:</strong> {event.userEvents.length}</p>
                    </div>
                ))}
            </div>
        </div>
    );
}
