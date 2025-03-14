import {getAllEvents} from "../../services/api.js";
import {useEffect, useState} from "react";
import { Link } from "react-router-dom";
export function AllEvents() {
    const [events, setEvents] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        getAllEvents().then(setEvents).catch(console.error);
    }, [])

    return (
        <div>
            {events.map((event) => (

                <div key={event.id}>
                    <Link to={`/event/${event.id}`}>{event.name}</Link>
                </div>
            ))}
        </div>
    );
}