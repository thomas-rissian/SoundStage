import { getAllEvents } from "../../services/api.js";
import React, { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import {Loading} from "../extra/loading.jsx";

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

        if (searchTerm) {
            filtered = filtered.filter(event =>
                event.name.toLowerCase().includes(searchTerm.toLowerCase())
            );
        }

        if (selectedArtist) {
            filtered = filtered.filter(event =>
                event.artist.name.toLowerCase().includes(selectedArtist.toLowerCase())
            );
        }

        if (selectedDate) {
            filtered = filtered.filter(event =>
                new Date(event.date).toLocaleDateString() === new Date(selectedDate).toLocaleDateString()
            );
        }

        setFilteredEvents(filtered);
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <div className="event-page">
            <h1>Évènements</h1>

            <div className="event-filter-section">
                <input
                    type="text"
                    placeholder="Rechercher un événement..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="event-search-input"
                />

                <input
                    type="text"
                    placeholder="Filtrer par artiste..."
                    value={selectedArtist}
                    onChange={(e) => setSelectedArtist(e.target.value)}
                    className="artist-search-input"
                />

                <input
                    type="date"
                    value={selectedDate}
                    onChange={(e) => setSelectedDate(e.target.value)}
                    className="event-date-input"
                />
            </div>

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
