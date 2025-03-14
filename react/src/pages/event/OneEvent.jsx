import { getEventById } from "../../services/api.js";
import { useEffect, useState } from "react";
import {Link, useParams} from "react-router-dom";
import '/src/styles/App.css';

export function EventById() {
    const { id } = useParams();
    const [event, setEvent] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        getEventById(id)
            .then((data) => {
                setEvent(data);
                setLoading(false);
            })
            .catch((error) => {
                console.error(error);
                setLoading(false);
            });
    }, [id]);

    if (loading) {
        return <p>Loading...</p>;
    }

    return (
        <div className="event-detail-page">
            <div className="event-info-container">
                <div className="event-description-container">
                    {/* Titre de l'événement */}
                    <h1 className="event-name">{event.name}</h1>

                    {/* Artiste associé */}
                    {event.artist && (
                        <div className="event-artist">
                            <p><strong>Artist:</strong> <Link to= {`/artist/${event.artist.id}`}>{event.artist.name}</Link></p>
                        </div>
                    )}

                    {/* Date de l'événement */}
                    <div className="event-date">
                        <p><strong>Date:</strong> {new Date(event.date).toLocaleDateString()}</p>
                    </div>

                </div>

                {/* Liste des utilisateurs inscrits */}
                <div className="event-users-container">
                    {event.userEvents && event.userEvents.length > 0 && (
                        <div className="event-users">
                            <h2>Users Registered</h2>
                            <div className="users-list">
                                {event.userEvents.map((userEvent, index) => (
                                    <div key={index} className="event-user-item">
                                        <p><strong>User:</strong> {userEvent.userRef.pseudo}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
