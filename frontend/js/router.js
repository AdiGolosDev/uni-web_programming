const routes = {
  "/": "views/home.html",
  "/pokedex": "views/pokedex.html",
  "/teams": "views/teams.html",
  "/login": "views/login.html",
  "/register": "views/register.html",
};

async function loadView() {
  const path = location.hash.slice(1) || "/";
  const file = routes[path] || "views/404.html";
  const app = document.getElementById("app");

  try {
    const response = await fetch(file);
    if (!response.ok) throw new Error(response.status);
    app.innerHTML = await response.text();
  } catch (err) {
    app.innerHTML = '<p class="text-danger">Could not load this page.</p>';
  }
}

window.addEventListener("hashchange", loadView);
window.addEventListener("DOMContentLoaded", loadView);
