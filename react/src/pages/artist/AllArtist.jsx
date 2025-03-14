import {getAllArtist} from "../../services/api.js";
import {useEffect, useState} from "react";
import { Link } from "react-router-dom";
export function AllArtist() {
    const [artists, setArtists] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        getAllArtist().then(setArtists).catch(console.error);
    }, [])

    return (
        <div className="min-h-screen bg-gray-900 text-white flex flex-col items-center p-6">
            <div className="bg-red-500 p-4 text-white">Test Tailwind</div>

            <h1 className="text-3xl font-bold mb-6">Liste des Artistes</h1>
            <div className="w-full max-w-md space-y-4">
                {artists.map((artist) => (
                    <div key={artist.id}
                        className="bg-gray-800 p-4 rounded-lg shadow-lg hover:bg-gray-700 transition">
                        <Link to={`/artist/${artist.id}`} className="text-yellow-400 text-xl font-semibold">
                            {artist.name}
                        </Link>
                        <p className="text-gray-400 text-sm mt-2">{artist.description}</p>
                    </div>
                ))}
            </div>
        </div>
    );
}