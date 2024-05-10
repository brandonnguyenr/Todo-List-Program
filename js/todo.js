import { userIsLoggedIn } from "./helpers/check_user";


document.addEventListener('load', () => {
    const [isLoggedIn, user_id] = userIsLoggedIn();

    if (!isLoggedIn) {
        document.body.innerHTML = "<h5>Please log in...</h5>";
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 1000);
    }
});