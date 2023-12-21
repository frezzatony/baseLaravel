$(document).ready(async function () {

  $(".btn-screen").on("click", function (e) {
    const _t = $(this);
    if (_t.data("target") != undefined) {
      const previousScreen = _t.closest(".screen");
      const nextScreen = $(`#screen-${_t.data("target")}`);

      nextScreen.find(".title-description").text("Emissão de senha")
      nextScreen.find(".title").text(`${(!previousScreen.hasClass("first") ? previousScreen.find(".title").text() + " > " : '')} ${_t.text()}`)

      totemChangeScreen(previousScreen, nextScreen, "left");

      nextScreen.find(".btn-previous").off().on("click", function () {
        totemChangeScreen(nextScreen, previousScreen, "right");
      });
    }
  });

});

function totemChangeScreen(previousScreen, nextScreen, direction) {

  previousScreen.find("button").prop("disabled", true);
  nextScreen.find("button").prop("disabled", false);

  nextScreen.removeClass("d-none").css('left', direction == "left" ? '100%' : '-100%').animate({ left: '0' }, 500);
  previousScreen.animate({ left: direction == "left" ? '-100%' : '100%' }, 500, function () {
    $(this).addClass("d-none");
  });

}