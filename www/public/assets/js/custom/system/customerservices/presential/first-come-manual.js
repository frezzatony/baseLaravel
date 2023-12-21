function setCustomerServiceFirstComeManual(ticketWithdrawal) {

  const formBooks = $("#form-books");
  $(".btn-provide-ticket").off().prop("disabled", true);

  if (ticketWithdrawal == "attendant_dispenser") {
    setCustomerServiceFirstComeManualProvideTicket(formBooks);
  }

  if (ticketWithdrawal == "dispenser") {
    setCustomerServiceFirstComeManualDispenserCallTicket(formBooks);
  }
}

function setCustomerServiceFirstComeManualProvideTicket(formBooks) {
  $(".btn-provide-ticket").prop("disabled", false);
  $(".btn-provide-ticket").on("click", function () {
    App._loadPageModal({
      url: `/system/customerservices/presential/fetchProvideTicketScreen`,
      title: "Atendimento Presencial - Retirada de Senha",
      size: "sm",
      data: {
        queue: formBooks.find("#queue").val(),
      },
      backdrop_static: false,
      done: function (modal) {
        setCstomerServiceFirstComeManualBookTicketByMatter(modal);
        modal.find(".btn-book-ticket").off().on("click", async function () {
          const _t = $(this);
          App._loading(true);
          if (await customerServiceFirstComeManualBookTicket(formBooks, _t.data("uuid-priority"), _t.data("uuid-matter"), _t.data("ticket"))) {
            modal.modal('hide');
          }
          App._loading(false);
        });
      }
    });
  });
}

async function customerServiceFirstComeManualBookTicket(formBooks, uuidPriority, uuidMatter, ticket) {
  const formData = getFormData(formBooks, 'values');
  formData.uuid_priority = uuidPriority;
  formData.uuid_matter = uuidMatter ?? '';
  formData.ticket = ticket;
  return await axios.get(`/system/customerservices/presential/fetchBookTicket?${$.param(formData)}`)
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      return true;
    })
    .catch(function (error) {
      App._showMessage(error.response.data.message, error.response.data.status);
      return false;
    });
}

function setCustomerServiceFirstComeManualDispenserCallTicket(formBooks) {
  const divsBook = getCustomerServiceDivsBooks();
  if (divsBook.next_book.data("calling") != true && divsBook.assisting_book.data("assisting") != true) {
    App._loadingElements(true, divsBook, 10);
    App._loadAjax({
      showLoading: false,
      url: `/system/customerservices/presential/fetchProvideTicketScreen`,
      dataType: 'html',
      data: {
        queue: formBooks.find("#queue").val(),
      },
      onDone: async function (response) {
        await divsBook.queue_manual_call_ticket.find(".call-content").html(response);
        divsBook.queue_manual_call_ticket.addClass("loaded");
        divsBook.queue_manual_call_ticket.find(".btn-call-ticket").each(function () {
          const _t = $(this);
          setCustomerServiceFirstComeManualDispenserCallTicketButton(formBooks, _t);
        });
        setCstomerServiceFirstComeManualBookTicketByMatter(divsBook.queue_manual_call_ticket.find(".call-content"));
        switchShowDivsBook("queue_manual_call_ticket");
        App._loadingElements(false, divsBook, 10);
      },
      onFail: function () {
        App._loadingElements(false, divsBook, 10);
      }
    });
  }
}

function setCustomerServiceFirstComeManualDispenserCallTicketButton(formBooks, btnCallTicket) {
  btnCallTicket.off().on("click", async function () {
    const formData = getFormData(formBooks, 'values');
    formData.uuid_priority = btnCallTicket.data("uuid-priority");
    formData.uuid_matter = btnCallTicket.data("uuid-matter") != undefined ? btnCallTicket.data("uuid-matter") : '';
    formData.ticket = btnCallTicket.closest(".list-group-item").find("input").first().val();

    return await axios.get(`/system/customerservices/presential/fetchBookAndCallDispenserTicket?${$.param(formData)}`)
      .then(async function ({ data }) {
        const divsBook = getCustomerServiceDivsBooks();
        divsBook.queue_manual_call_ticket.removeClass("loaded");
        setFormValidationErrors($("#form-books"));
        return true;
      })
      .catch(function (error) {
        if (error.response.data.errors != undefined) {
          App._showAppMessage('form_error', 'error');
          setFormValidationErrors($("#form-books"), error.response.data.errors);
        }
        return false;
      });
  });
}

function setCstomerServiceFirstComeManualBookTicketByMatter(parent) {
  parent.find(".btn-prioroty-ticket").off().on("click", function () {
    const _t = $(this);
    setCstomerServiceFirstComeManualBookTicketByChangeScreen(_t.closest('div'), parent.find(`div[data-uuid-priority="${_t.data("uuid-priority")}"]`), "left")
  });

  parent.find(".btn-back").off().on("click", function () {
    const _t = $(this);
    setCstomerServiceFirstComeManualBookTicketByChangeScreen(_t.closest('div.matter'), parent.find(".priorities"), "right")
  });
}

function setCstomerServiceFirstComeManualBookTicketByChangeScreen(previousScreen, nextScreen, direction) {
  previousScreen.find("button").prop("disabled", true);
  nextScreen.find("button").prop("disabled", false);
  nextScreen.removeClass("d-none").css('left', direction == "left" ? '100%' : '-100%').animate({ left: '0' }, 500);
  previousScreen.animate({ left: direction == "left" ? '-100%' : '100%' }, 500, function () {
    $(this).addClass("d-none");
  });
}
