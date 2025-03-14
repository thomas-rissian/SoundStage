import './styles/App.css';
import { Route, Routes } from 'react-router-dom';
import { AllArtist } from './pages/artist/AllArtist.jsx';
import {ArtistById} from "./pages/artist/OneArtist.jsx";
import {AllEvents} from "./pages/event/AllEvents.jsx";
import {EventById} from "./pages/event/OneEvent.jsx";
import {NotFound} from "./pages/extra/NotFound.jsx";

function App() {
    return (
        <div className="bg-red-500 text-white p-4">
            Si Tailwind fonctionne, ce texte doit être sur fond rouge.
        </div>
    );
}

export default App;
