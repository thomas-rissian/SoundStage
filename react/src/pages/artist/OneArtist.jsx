import { getArtistById } from "../../services/api.js";
import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import '/src/styles/App.css';

export function ArtistById() {
    const { id } = useParams();
    const [artist, setArtist] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        getArtistById(id)
            .then((data) => {
                setArtist(data);
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
        <div className="artist-detail-page">
            <div className="artist-info-container">
                <div className="artist-description-container">
                    <h1 className="artist-name">{artist.name}</h1>

                    {/* Image de l'artiste */}
                    {artist.image && (
                        <div className="artist-image-container">
                            <img src={artist.image} alt={artist.name} className="artist-image" />
                        </div>
                    )}

                    {/* Description de l'artiste */}
                    <div className="artist-description">
                        <h2>Description</h2>
                        <p>{artist.description}</p>
                    </div>
                </div>

                {/* Événements à droite */}
                <div className="artist-events-container">
                    {artist.events && artist.events.length > 0 && (
                        <div className="artist-events">
                            <h2>Événements ({artist.events.length})</h2>
                            <div className="events-list">
                                {artist.events.map((event) => (
                                    <div key={event.id} className="artist-event-item">
                                        <Link to={`/event/${event.id}`} className="event-link">
                                            <p>{event.name}</p>
                                        </Link>
                                        <p>{new Date(event.date).toLocaleDateString()}</p>
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
