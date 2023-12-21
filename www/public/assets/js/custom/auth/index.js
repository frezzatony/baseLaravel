$(document).ready(async function () {
  if ($("#username").length) {
    IMask(document.querySelector('#username'), {
      mask: "000.000.000-00"
    });
  }

  $("form").on("submit", function (e) {
    $.LoadingOverlay("show", {
      image: "",
      fontawesome: "ph-spinner ph-3x spinner"
    });
  })
});