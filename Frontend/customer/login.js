// Switch forms
const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => container.classList.add("active"));
loginBtn.addEventListener('click', () => container.classList.remove("active"));

// Clear all error messages
function clearErrors() {
    document.querySelectorAll(".error").forEach(el => el.textContent = "");
}

//=======================> Sign-UP <=============================
//=======================> Sign-UP <=============================
document.getElementById("sign-UP").addEventListener("submit", (e) => {
    clearErrors(); // only clear errors

    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const cpassword = document.getElementById('cpassword').value.trim();

    let hasError = false;

    if (!name) {
        document.getElementById("nameError").textContent = "Name is required";
        hasError = true;
    }
    if (!email) {
        document.getElementById("emailError").textContent = "Email is required";
        hasError = true;
    }
    if (!password) {
        document.getElementById("passwordError").textContent = "Password is required";
        hasError = true;
    }
    if (!cpassword) {
        document.getElementById("cpasswordError").textContent = "Please confirm password";
        hasError = true;
    } else if (password !== cpassword) {
        document.getElementById("cpasswordError").textContent = "Passwords do not match";
        hasError = true;
    }

    if (hasError) {
        e.preventDefault(); // prevent submit only if there are errors
    }
    // Otherwise, allow form to submit to register.php
});

//===========================> Sign-IN <===================================
document.getElementById("sign-IN").addEventListener("submit", function (e) {
    e.preventDefault();
    clearErrors();

    const email = document.getElementById('L_email').value.trim();
    const password = document.getElementById('L_password').value.trim();

    let hasError = false;

    if (!email) {
        document.getElementById("loginEmailError").textContent = "Email is required";
        hasError = true;
    }
    if (!password) {
        document.getElementById("loginPasswordError").textContent = "Password is required";
        hasError = true;
    }

    if (hasError) return;

    let users = JSON.parse(localStorage.getItem("users")) || [];
    const foundUser = users.find(user => user.email === email && user.password === password);

    if (foundUser) {
        localStorage.setItem("loggedInUser", JSON.stringify(foundUser));
        window.location.href = "index.html";
    } else {
        document.getElementById("loginPasswordError").textContent = "Invalid email or password";
    }
});
