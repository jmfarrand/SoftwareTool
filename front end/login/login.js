function validate() {
    //validate username
    var username = document.getElementById('username').value;
    if (username.length > 10) {
        alert("The username mustn't be longer than 10 characters!");
        return false;
    }
    return true;
}
