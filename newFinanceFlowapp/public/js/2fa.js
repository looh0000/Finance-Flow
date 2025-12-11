/* ---------------- TIMER ----------------- */
let timeLeft = 60;
const countdown = document.getElementById("countdown");

function startTimer() {
  const interval = setInterval(() => {
    if (timeLeft <= 0) {
      clearInterval(interval);
      countdown.textContent = "Expirado";
      countdown.style.color = "red";
    } else {
      const min = Math.floor(timeLeft / 60);
      const sec = timeLeft % 60;
      countdown.textContent = `${String(min).padStart(2, "0")}:${String(sec).padStart(2, "0")}`;
      timeLeft--;
    }
  }, 1000);
}

/* ---------------- DARK MODE ----------------- */
function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
    localStorage.setItem("darkMode2FA", document.body.classList.contains("dark-mode"));
}

window.onload = function () {
    if (localStorage.getItem("darkMode2FA") === "true") {
        document.body.classList.add("dark-mode");
    }
};

/* ---------------- ACESSIBILIDADE ----------------- */
let fontSize = 16;

function changeFontSize(change) {
  fontSize += change;
  document.body.style.fontSize = fontSize + "px";
}

document.addEventListener("DOMContentLoaded", startTimer);
