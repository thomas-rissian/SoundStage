import { getArtistById } from "../../services/api.js";
import React, { useEffect, useState } from "react";
import {useParams, Link} from "react-router-dom";
import {Loading} from "../extra/loading.jsx";
import {SYMFONY_URL} from "../../services/api.js";
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
        return <Loading />;
    }
    if (artist === null) {
        return <div>Artiste inconnue</div>
    }
    return (
        <div className="detail-page">
            <h2>{artist.name}</h2>
            <div style={{ display: "flex", flexDirection: "row" }}>
                <div className="information-page">
                    <h3>Description : </h3>
                    <p>{artist.description}</p>
                    <img src={SYMFONY_URL + "/uploads/images/"+artist.image} alt="test"/>
                </div>

                <div className="list-element-right">
                    {artist.events && artist.events.length > 0 && (
                        <div>
                            <h3>Evènement Participant ({artist.events.length})</h3>
                            <div className="list-element">
                                {artist.events.map((event) => (
                                    <ul>
                                        <li key={event.id}><Link to={`/event/${event.id}`}>{event.name}</Link> ({event.date}) </li>
                                    </ul>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                <div></div>
            </div>
        </div>
    );
}
