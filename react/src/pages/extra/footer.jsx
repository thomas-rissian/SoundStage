export function Footer() {
    const date = new Date();
    return(
        <footer>
            <p>&copy; {date.getFullYear()} SoundStage</p>
        </footer>
    );
}