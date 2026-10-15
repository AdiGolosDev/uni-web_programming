$(document).ready(function () {
  const app = $.spapp({
    templateDir: "./views/",
    defaultView: "home",
    pageNotFound: "error_404",
  });

  function onViewChange() {
    const view = location.hash.slice(1) || "home";

    $("#main-nav .nav-link")
      .removeClass("active")
      .filter('[href="#' + view + '"]')
      .addClass("active");

    const title = $('#main-nav .nav-link[href="#' + view + '"]')
      .text()
      .trim();
    document.title = title ? title + " · PokéPlanner" : "PokéPlanner";

    const menu = bootstrap.Collapse.getInstance("#mainNav");
    if (menu) menu.hide();

    window.scrollTo(0, 0);
  }

  window.addEventListener("hashchange", onViewChange);
  onViewChange();

  app.run();
});
