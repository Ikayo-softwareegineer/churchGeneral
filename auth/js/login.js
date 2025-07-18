// ==================== DOM HELPERS ====================
const $ = (sel) => document.querySelector(sel);
const show = (el) => el.classList.remove("d-none");
const hide = (el) => el.classList.add("d-none");

// ==================== SWITCH FORMS ====================
$("#showRegisterLink").addEventListener("click", (e) => {
  e.preventDefault();
  hide($("#loginCard"));
  show($("#registerCard"));
});
$("#showLoginLink").addEventListener("click", (e) => {
  e.preventDefault();
  hide($("#registerCard"));
  show($("#loginCard"));
});

// ==================== TOGGLE PASSWORD VISIBILITY ====================
document.querySelectorAll(".toggle-pw").forEach((eye) => {
  eye.addEventListener("click", () => {
    const target = eye.getAttribute("data-target");
    const input = document.getElementById(target);
    input.type = input.type === "password" ? "text" : "password";
  });
});

// ==================== REGISTRATION SUBMIT ====================
$("#registerForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  // simple client‑side validation
  if (!e.target.checkValidity()) {
    e.target.reportValidity();
    return;
  }

  const body = new FormData(e.target);
  try {
    const res = await fetch("/CMS/home/register_new_member.php", {
      method: "POST",
      body,
    });
    const data = await res.json();
    if (data.success) {
      alert("Registration successful! Please log in using your first name and contact.");
      $("#registerForm").reset();
      hide($("#registerCard"));
      show($("#loginCard"));
      $("#loginUsername").value = body.get("regFirstName");
      $("#loginPassword").value = body.get("regContact");
    } else {
      alert(data.message || "Registration failed.");
    }
  } catch (err) {
    console.error(err);
    alert("Server error. Try again later.");
  }
});

// ==================== LOGIN SUBMIT ====================
$("#loginForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  const body = new FormData(e.target);

  try {
    const res = await fetch("/CMS/auth/php/login.php", {
      method: "POST",
      body,
    });
    const data = await res.json();
    if (data.success) {
      window.location.href = data.redirect; // should be appointments.html eventually
    } else {
      alert(data.message || "Invalid credentials.");
    }
  } catch (err) {
    console.error(err);
    alert("Server error. Try again later.");
  }
});
