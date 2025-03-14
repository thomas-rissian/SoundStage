import {getEventById} from "../../services/api.js";
import {useEffect, useState} from "react";
import {Link, useParams} from "react-router-dom";
export function EventById() {
    const { id } = useParams();
    const [event, setEvent] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        getEventById(id).then(setEvent).catch(console.error);
    }, [])

    return (
        <div>
            <p>{event.id}</p>
        </div>
    );
}