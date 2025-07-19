document.getElementById("signupForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const form = e.target;

  const memberData = {
    fullName: form.fullName.value.trim(),
    username: form.username.value.trim(),
    email: form.email.value.trim(),
    phone: form.phone.value.trim(),
    address: form.address.value.trim(),
    gender: form.gender.value,
    password: form.password.value
  };

  // Save to localStorage using username as the key
  localStorage.setItem(memberData.username, JSON.stringify(memberData));

  // Show success message
  document.getElementById("successMsg").textContent = "Signup successful! Redirecting to login...";

  // Redirect to login after 2 seconds
  setTimeout(() => {
    window.location.href = "login.html";
  }, 2000);
});
