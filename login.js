document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const username = this.username.value.trim();
  const password = this.password.value;

  const storedData = localStorage.getItem(username);

  if (!storedData) {
    document.getElementById("errorMsg").textContent = "User not found. Please sign up.";
    return;
  }

  const user = JSON.parse(storedData);

  if (user.password === password) {
    alert("Login successful! Welcome, " + user.fullName);
    // Redirect to your main dashboard or home page
    window.location.href = "dashboard.html"; 
  } else {
    document.getElementById("errorMsg").textContent = "Incorrect password.";
  }
});
