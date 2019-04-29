function checkPassword() {
    var firstPassword = document.getElementById('password');
    var secondPassword = document.getElementById('passwordRetype');

    var message = document.getElementById("confirmMessage");

    var correctColour = "#66cc66";
    var wrongColour = "#ff6666";

    if (firstPassword.value == secondPassword.value) {
        secondPassword.backgroundColour = correctColour;
        message.style.color = correctColour;
        message.innerHTML = "The passwords match";
    } else {
        secondPassword.backgroundColour = wrongColour;
        message.style.color = wrongColour;
        message.innerHTML = "The passwords don't match";
    }
}

function validate() {
    //validate username
    var username = document.getElementById('username').value;
    if (username.length > 10) {
        alert("The username mustn't be longer than 10 characters!");
        return false;
    }

    //now validate the passwords
    if (!validatePassword()) {
        alert("Please make sure the passwords match before signing up.");
        return false;
    }
    return true;
}

function validatePassword() {
    var firstPassword = document.getElementById('password');
    var secondPassword = document.getElementById('passwordRetype');

    if (firstPassword.value == secondPassword.value) {
        return true;
    } else {
        return false;
    }
}
