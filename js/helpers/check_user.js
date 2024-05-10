export const userIsLoggedIn = async () => {

    let request = await fetch("php/user.php", "GET");
    let response = await request.json();

    if (response.error) {
        return [false, ''];
    }

    return [true, response.user_id];
}