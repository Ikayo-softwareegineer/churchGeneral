function loadPage(page) {
  fetch(`pages/${page}.html`)
    .then(res => {
      if (!res.ok) throw new Error("Page not found");
      return res.text();
    })
    .then(html => {
      document.getElementById("contentArea").innerHTML = html;
    })
    .catch(err => {
      document.getElementById("contentArea").innerHTML = `<p class="text-danger">Error loading page: ${err.message}</p>`;
    });
}
