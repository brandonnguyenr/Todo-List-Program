async function userIsLoggedIn() {

    let request = await fetch("php/user.php");
    let response = await request.json();

    if (response.error) {
        return [false, response.error];
    }

    return [true, response.user_id];
}

(async () => {
    const [isLoggedIn, user_or_error] = await userIsLoggedIn();

    if (!isLoggedIn) {
        document.body.innerHTML = `<h5>${user_or_error} Please Log In.</h5>`;
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 1000);
    } else {
        // This is proabably how we would retain the userID if we were not using php $_SESSION
        sessionStorage.setItem('userID', user_or_error);
        localStorage.setItem('userID', user_or_error);
    }
})();