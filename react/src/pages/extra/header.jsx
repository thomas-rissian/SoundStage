import { Link } from "react-router-dom";
export function Header() {
    return(
        <header>
            <nav>
                <ul>

                    <li><Link to={"/"}>Accueil</Link></li>
                    <li><Link to={"/artist"}>Artistes</Link></li>
                    <li><Link to={"/event"}>Evènement</Link></li>
                </ul>
            </nav>
        </header>
    );
}