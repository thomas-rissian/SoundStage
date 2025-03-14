import {getArtistById} from "../../services/api.js";
import {useEffect, useState} from "react";
import {Link, useParams} from "react-router-dom";
export function ArtistById() {
    const { id } = useParams();
    const [artist, setArtist] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        getArtistById(id).then(setArtist).catch(console.error);
    }, [])

    return (
        <div>
            <p>{artist.name}</p>
        </div>
    );
}