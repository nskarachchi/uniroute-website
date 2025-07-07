document.getElementById("feedback-form").addEventListener("submit", function(event) {
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let contact = document.getElementById("contact").value.trim();
    let comments = document.getElementById("comments").value.trim();

    if (name === "" || email === "" || comments === "") {
        alert("Please fill out all the fields before submitting.");
        event.preventDefault();
    }
});
