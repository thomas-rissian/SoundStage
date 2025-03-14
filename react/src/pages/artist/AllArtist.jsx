import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import '/src/styles/App.css';
import { getAllArtist } from "../../services/api.js";

export const AllArtist = () => {
    const [artists, setArtists] = useState([]);
    const [filteredArtists, setFilteredArtists] = useState([]);
    const [sortOrder, setSortOrder] = useState('asc');
    const [searchTerm, setSearchTerm] = useState('');

    useEffect(() => {
        // Récupérer les artistes dès le chargement de la page
        getAllArtist()
            .then((data) => setArtists(data))
            .catch(console.error);
    }, []);

    useEffect(() => {
        // Appliquer le filtre et le tri lorsque searchTerm, sortOrder, ou artists changent
        filterAndSortArtists();
    }, [searchTerm, sortOrder, artists]);

    const filterAndSortArtists = () => {
        // Filtrer les artistes par nom
        let filtered = artists.filter(artist =>
            artist.name.toLowerCase().includes(searchTerm.toLowerCase())
        );

        // Appliquer le tri en fonction de l'ordre sélectionné
        if (sortOrder === 'asc') {
            filtered.sort((a, b) => a.name.localeCompare(b.name));
        } else {
            filtered.sort((a, b) => b.name.localeCompare(a.name));
        }

        // Mettre à jour les artistes filtrés
        setFilteredArtists(filtered);
    };

    const truncateDescription = (description) => {
        const maxLength = 50; // Limite de caractères
        if (description.length > maxLength) {
            return description.slice(0, maxLength) + '...';
        }
        return description;
    };

    return (
        <div className="artist-page">
            <h1>Artistes</h1>

            {/* Filtrage par nom */}
            <div className="artist-filter-section">
                <input
                    type="text"
                    placeholder="Rechercher par nom..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="artist-search-input"
                />
                {/* Tri par nom */}
                <select
                    value={sortOrder}
                    onChange={(e) => setSortOrder(e.target.value)}
                    className="artist-sort-select"
                >
                    <option value="asc">Nom croissant</option>
                    <option value="desc">Nom décroissant</option>
                </select>
            </div>

            {/* Liste des artistes */}
            <div className="artist-list">
                {filteredArtists.map((artist) => (
                    <div key={artist.id} className="artist-item">
                        <Link to={`/artist/${artist.id}`} className="artist-link">
                            {artist.name}
                        </Link>
                        <p><strong>Événements:</strong> {artist.events.length}</p>
                        <p><strong>Description:</strong>
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
