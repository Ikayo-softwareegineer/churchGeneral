// Simulated Data
const appointments = [
  { name: "John Okello", date: "2025-07-08", reason: "Marriage Counseling" },
  { name: "Sarah Namutebi", date: "2025-07-10", reason: "Prayer Session" }
];

const members = ["John Okello", "Sarah Namutebi", "Grace Aluma", "Denis Mugabi"];
const leaders = ["Brendah Akello", "Ikayo David"];

// Render Appointments
function renderAppointments() {
  const container = document.getElementById("appointmentsList");
  container.innerHTML = "";
  appointments.forEach((appt, index) => {
    container.innerHTML += `
      <tr>
        <td>${appt.name}</td>
        <td>${appt.date}</td>
        <td>${appt.reason}</td>
        <td>
          <button class="btn btn-success btn-sm" onclick="approve(${index})">Approve</button>
          <button class="btn btn-danger btn-sm" onclick="reject(${index})">Reject</button>
        </td>
      </tr>`;
  });
}

// Render Members
function renderMembers() {
  const container = document.getElementById("memberList");
  container.innerHTML = "";
  members.forEach(member => {
    container.innerHTML += `<li>${member}</li>`;
  });
}

// Render Leaders
function renderLeaders() {
  const container = document.getElementById("leaderList");
  container.innerHTML = "";
  leaders.forEach((leader, index) => {
    container.innerHTML += `
      <li>${leader}
        <button class="btn btn-success btn-sm" onclick="approveLeader(${index})">Approve</button>
        <button class="btn btn-danger btn-sm" onclick="rejectLeader(${index})">Reject</button>
      </li>`;
  });
}

// Actions
function approve(index) {
  alert(`Approved: ${appointments[index].name}`);
  appointments.splice(index, 1);
  renderAppointments();
}

function reject(index) {
  alert(`Rejected: ${appointments[index].name}`);
  appointments.splice(index, 1);
  renderAppointments();
}

function approveLeader(index) {
  alert(`Leader Approved: ${leaders[index]}`);
  leaders.splice(index, 1);
  renderLeaders();
}

function rejectLeader(index) {
  alert(`Leader Rejected: ${leaders[index]}`);
  leaders.splice(index, 1);
  renderLeaders();
}

// Section Switching
function showSection(id) {
  document.querySelectorAll('.section').forEach(div => div.classList.add('d-none'));
  document.getElementById(id).classList.remove('d-none');
}

// Initial Render
renderAppointments();
renderMembers();
renderLeaders();
