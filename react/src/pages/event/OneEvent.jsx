import { getEventById } from "../../services/api.js";
import { useEffect, useState } from "react";
import {Link, useParams} from "react-router-dom";
import {Loading} from "../extra/loading.jsx";

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
        return <Loading />;
    }
    if (event === null) {
        return <div>Evènement inconnue</div>
    }
    return (
        <div className="detail-page">
            <h2>{event.name}</h2>
            <div style={{ display: "flex", flexDirection: "row" }}>
                <div className="information-page">
                    <h3><strong>Artiste :</strong> <Link to= {`/artist/${event.artist.id}`}>{event.artist.name}</Link></h3>
                    <h3>Date : {event.date}</h3>
                </div>

                <div className="list-element-right">
                    {event.userEvents && event.userEvents.length > 0 && (
                        <div>
                            <h3>Utilisateurs inscrits : {event.userEvents.length}</h3>
                            <div className="list-element">
                                {event.userEvents.map((userEvent) => (
                                    <ul>
                                        <li key={userEvent.userRef.id}>{userEvent.userRef.pseudo}</li>
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
