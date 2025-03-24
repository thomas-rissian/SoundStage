import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { getAllArtist } from "../../services/api.js";
import { Loading } from "../extra/loading.jsx";

export const AllArtist = () => {
    const [artists, setArtists] = useState([]);
    const [filteredArtists, setFilteredArtists] = useState([]);
    const [sortOrder, setSortOrder] = useState('asc');
    const [searchTerm, setSearchTerm] = useState('');
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        getAllArtist()
            .then((data) => {
                setArtists(data);
                setLoading(false);
            })
            .catch(console.error);
    }, []);

    useEffect(() => {
        filterAndSortArtists();
    }, [searchTerm, sortOrder, artists]);

    const filterAndSortArtists = () => {
        let filtered = artists.filter(artist =>
            artist.name.toLowerCase().includes(searchTerm.toLowerCase())
        );

        filtered.sort((a, b) =>
            sortOrder === 'asc' ? a.name.localeCompare(b.name) : b.name.localeCompare(a.name)
        );

        setFilteredArtists(filtered);
    };

    const toggleSortOrder = () => {
        setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc');
    };

    const truncateDescription = (description) => {
        const maxLength = 50;
        return description.length > maxLength ? description.slice(0, maxLength) + '...' : description;
    };

    if (loading) {
        return <Loading />;
    }

    return (
        <div className="artist-page">
            <h1>Artistes</h1>

            <div className="artist-filter-section">
                <input
                    type="text"
                    placeholder="Rechercher par nom..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="artist-search-input"
                />
                <button onClick={toggleSortOrder} className="artist-sort-button">
                    Trier par nom {sortOrder === 'asc' ? '▲' : '▼'}
                </button>
            </div>

            <div className="artist-list">
                {filteredArtists.map((artist) => (
                    <div key={artist.id} className="artist-item">
                        <Link to={`/artist/${artist.id}`} className="artist-link">
                            {artist.name}
                        </Link>
                        <p><strong>Évènements:</strong> {artist.events.length}</p>
                        <p><strong>Description : </strong>
                            <span className="artist-description">
                                {truncateDescription(artist.description)}
                            </span>
                        </p>
                    </div>
                ))}
            </div>
        </div>
    );
};
