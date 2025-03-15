import './styles/App.css';
import './styles/index.css';
import { Route, Routes } from 'react-router-dom';
import { AllArtist } from './pages/artist/AllArtist.jsx';
import {ArtistById} from "./pages/artist/OneArtist.jsx";
import {AllEvents} from "./pages/event/AllEvents.jsx";
import {EventById} from "./pages/event/OneEvent.jsx";
import {NotFound} from "./pages/extra/NotFound.jsx";
import {Index} from "./pages/index.jsx";

function App() {
    return (
        <Routes>
            <Route path="/" element={<Index />} />
            <Route path="/artist" element={<AllArtist />} />
            <Route path="/artist/:id" element={<ArtistById />} />
            <Route path="/event" element={<AllEvents />} />
            <Route path="/event/:id" element={<EventById />} />
            <Route path="*" element={<NotFound />} />
        </Routes>
    );
}

export default App;
