import { createRoot } from 'react-dom/client'
import './styles/index.css'
import App from './App.jsx'
import {BrowserRouter} from "react-router-dom";
import {Header} from "./pages/extra/header.jsx";
import {Footer} from "./pages/extra/footer.jsx";

createRoot(document.getElementById('root')).render(
  <BrowserRouter>
      <Header />
      <App />
      <Footer />
  </BrowserRouter>,
)
