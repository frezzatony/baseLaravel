$(document).ready(async function () {
  setCustomerServicePresentialMenu();
  const divsBook = getCustomerServiceDivsBooks();
  const inputs = getCustomerServiceInputsBook();


  inputs.attendance_unit.on("change", async function () {
    const _t = $(this);
    App._loadingElements(true, inputs, 10);
    $(".btn-provide-ticket").prop("disabled", true);
    await fillCustomerServiceQueues(inputs);
    if (inputs.attendance_unit.val()) {
      await fillCustomerServiceQueuePoints(inputs);
    }
    $("#next-book").val('');
    toggleCustomerServiceBtnAddItem();
    $(".btn-my-matters").toggleClass("d-none", true);
    $(".btn-my-priorities").toggleClass("d-none", true);
    divsBook.next_book.data("calling", false);
    await fillCustomerServiceBook();
    App._loadingElements(false, inputs);
    await fetchCustomerServiceDailyTickets();
  });

  inputs.queue.on("change", async function () {
    const _t = $(this);
    App._loadingElements(true, inputs, 10);
    await fillCustomerServiceQueuePoints(inputs);
    $("#next-book").val('');
    divsBook.next_book.data("calling", false);
    await fillCustomerServiceBook();

    toggleCustomerServiceBtnAddItem();
    $(".btn-my-matters").toggleClass("d-none", _t.val() == '' || _t.find("option:selected").attr("ticket-sequence") == "priority");
    $(".btn-my-priorities").toggleClass("d-none", _t.val() == '' || _t.find("option:selected").attr("ticket-sequence") == "matter");

    if (_t.find("option:selected").attr("type") == "first_come_manual") {
      await setCustomerServiceFirstComeManual(_t.find("option:selected").attr("ticket-withdrawal"));
    }
    App._loadingElements(false, inputs);
    await fetchCustomerServiceDailyTickets();
  });

  App.websocket.listen(async function (data) {
    if (data['queues.book'] != undefined) {
      data['queues.book'] = JSON.parse(data['queues.book']);
      if (inputs.queue.find("option:selected").val() != '' && data['queues.book'].queue == inputs.queue.find("option:selected").val()) {
        await fillCustomerServiceBook();
        await fetchCustomerServiceDailyTickets();
      }
    }
  });

  App._loadingElements(true, inputs, 10);
  toggleCustomerServiceBtnAddItem();

  await fetchCustomerServiceAssistingBook(inputs);
  await fillCustomerServiceAttendanceUnits(inputs);
  App._loadingElements(false, inputs);
});

function setCustomerServicePresentialMenu() {
  const divsBook = getCustomerServiceDivsBooks();
  const formBooks = $("#form-books");
  $(".btn-add-item").off().on("click", function () {
    Swal.fire({
      buttonsStyling: false,
      customClass: {
        confirmButton: 'btn btn-success fs-sm px-1 py-0 ',
        cancelButton: 'btn btn-danger fs-sm px-1 py-0',
      },
      title: 'Confirmar Novo Atendimento',
      html: `
        Um novo atendimento será iniciado sem agendamento para: 
        <br><br>Unidade de Atendimento <strong>${formBooks.find("#attendance_unit").find("option:selected").text()}</strong>
        <br>Fila de Atendimento <strong>${formBooks.find("#queue").find("option:selected").text()}</strong>
        <br><br><strong>Você confirma esta ação?</strong>
      `,
      icon: 'question',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      confirmButtonText: '<i class="ph-check-square-offset fs-sm align-middle"></i> Confirmar',
      stopKeydownPropagation: true,
      keydownListenerCapture: true,
      focusCancel: true,
      allowEnterKey: true,
      allowEscapeKey: true,
    }).then(async function (result) {
      if (result.isConfirmed) {
        setFormValidationErrors(formBooks);
        let formData = getFormData(formBooks, 'values');
        App._loadPageModal({
          url: `/system/customerservices/presential/create?${$.param(formData)}`,
          title: "Atendimento Presencial",
          size: "lg",
          backdrop_static: true,
          done: function (modal) {
            Object.entries(divsBook).forEach(function (divBook) {
              $(divBook[1]).removeClass("loaded");
            })
            initCustomerServicePresential(modal);
          }
        });
      }
    });
  });

  $(".btn-my-matters").off().on("click", function () {
    editCustomerServiceMyMatters();
  });

}

async function fillCustomerServiceAttendanceUnits(inputs) {
  const attendanceUnits = await App._fetchItems({
    service: "System/Queue/QueueUserAttendant",
    method: "findAllAttendanceUnitsByUserAttendantIdAndFilters",
    label: 'unidades de atendimento',
    params: {
      format: 0,
    }
  });

  let options = attendanceUnits.map(function (item) {
    return {
      value: item.id,
      text: item.name.toUpperCase(),
    }
  });

  await fillDropdown({
    first_empty: true,
    select: inputs.attendance_unit,
    options: options,
  });

  if (options.length == 1) {
    inputs.attendance_unit.val(options[0].value).trigger("change");
  }
}

async function fillCustomerServiceQueues(inputs) {
  const attendanceUnits = await App._fetchItems({
    service: "System/Queue/QueueUserAttendant",
    method: "findAllQueuesByAttendanceUnitIdAndUserAttendantIdAndFilters",
    label: 'filas de atendimento',
    params: {
      format: 0,
      filter: {
        attendance_unit_id: inputs.attendance_unit.find("option:selected").val(),
      }
    }
  });

  let options = attendanceUnits.map(function (item) {
    return {
      value: item.id,
      text: item.description.toUpperCase(),
      type: item.type,
      "ticket-withdrawal": item.ticket_withdrawal,
      "ticket-sequence": item.ticket_sequence ?? "matter",
    }
  });

  await fillDropdown({
    first_empty: true,
    select: inputs.queue,
    options: options,
  });

  if (options.length == 1) {
    inputs.queue.val(options[0].value).trigger("change");
  }
}

async function fillCustomerServiceQueuePoints(inputs) {
  const attendanceUnits = await App._fetchItems({
    service: "System/Queue/Queue",
    method: "findAllAttendancePointsByQueueIdAndFilters",
    label: 'pontos de atendimento',
    params: {
      format: 0,
      filter: {
        queue_id: inputs.queue.find("option:selected").val(),
      }
    }
  });

  let options = attendanceUnits.map(function (item) {
    return {
      value: item.id,
      text: item.description.toUpperCase(),
    }
  });

  await fillDropdown({
    first_empty: true,
    select: inputs.service_point,
    options: options,
  });
}

async function fillCustomerServiceBook() {
  const inputs = {
    attendance_unit: $("#attendance_unit"),
    queue: $("#queue"),
    service_point: $("#service_point"),
  }

  const divsBook = getCustomerServiceDivsBooks();
  if (await fetchCustomerServiceAssistingBook(inputs)) {
    return false;
  }

  divsBook.assisting_book.data("assisting", false);
  if (inputs.attendance_unit.val() == "" || inputs.queue.val() == "") {
    switchShowDivsBook("empty_queue");
  }

  if (inputs.attendance_unit.val() && inputs.queue.val()) {
    App._loadingElements(true, divsBook, 10);
    const books = await fetchCustomerServiceNextBook(inputs);

    if (books.length <= 0) {
      if (inputs.queue.find("option:selected").attr("ticket-withdrawal") == "dispenser") {
        setCustomerServiceFirstComeManual("dispenser");
      }
      else {
        switchShowDivsBook("no_book");
      }
    }

    if (books.length > 0 && ["waiting_in_line", "calling"].includes(books[0].status) && divsBook.next_book.data("calling") != true) {
      switchShowDivsBook("next_book");
      if (books[0].status == "calling") {
        toggleCustomerServiceBtnAddItem();
        divsBook.next_book.data("calling", true);
      }
      fillCustomerServiceNextBook(books[0]);
    }

    if (books.length > 0 && books[0].status == "assisting") {
      switchShowDivsBook("assisting_book");
    }
    App._loadingElements(false, divsBook);
  }
}

async function fillCustomerServiceNextBook(book) {
  const inputNextBook = $("#next-book");
  const divsBook = getCustomerServiceDivsBooks();
  if (inputNextBook.val() == book.id) {
    return false;
  }

  if (book.status == "calling") {
    divsBook.next_book.data("calling", true);
  }

  inputNextBook.val(book.id);
  const createdAt = new Date(book.created_at);
  divsBook.next_book.find(".created-at").text(`
    ${createdAt.toLocaleString("pt-BR", { day: "2-digit", month: "2-digit", year: "numeric" })} às 
    ${createdAt.toLocaleString("pt-BR", { hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false, })}h`
  );

  divsBook.next_book.find(".waiting-time").text('');
  clearInterval(divsBook.next_book.find(".waiting-time").data("wating-time"));
  divsBook.next_book.find(".waiting-time").data("time", book.created_at);
  divsBook.next_book.find(".waiting-time").data("wating-time", setInterval(function () {
    divsBook.next_book.find(".waiting-time").timeElapsed({
      currentTime: new Date,
      full: true
    });
  }, 1000));

  divsBook.next_book.find(".matter").text(book.matter_description != undefined ? book.matter_description.toUpperCase() : '');
  divsBook.next_book.find(".call-order").text(book.call_order_description.toUpperCase());
  divsBook.next_book.find(".ticket").text(`${String(book.ticket_prefix != undefined ? book.ticket_prefix : '').toUpperCase()}${String(book.ticket).toUpperCase()}`);
  divsBook.next_book.find(".call-count").text(book.calls_count);
  if (book.calls_count > 0) {
    divsBook.next_book.find(".title").html(`<i class="ph-clock-clockwise"></i> Aguardando pessoa interessada para iniciar atendimento`);
  }
  else {
    divsBook.next_book.find(".ticket").closest("ul").addClass("animated pulse");
    divsBook.next_book.find(".title").html(`<i class="ph-calendar-check"></i> Próximo atendimento que você pode assumir`);
  }

  divsBook.next_book.find(".ticket").closest("ul").on("animationend", function () {
    $(this).removeClass("animated pulse");
  });

  clearInterval(divsBook.next_book.find(".last-call-time").data("wating-time"));
  if (book.calls_count == 0) {
    divsBook.next_book.find(".last-call-time").text("Não houve chamadas");
  }
  if (book.calls_count > 0) {
    divsBook.next_book.find(".last-call-time").text('');
    divsBook.next_book.find(".last-call-time").data("time", book.updated_at);
    divsBook.next_book.find(".last-call-time").data("wating-time", setInterval(function () {
      divsBook.next_book.find(".last-call-time").timeElapsed({
        currentTime: new Date,
        full: true
      });
    }, 1000));
  }

  divsBook.next_book.find(".btn-cancel-customerservice").off().on("click", async function () {
    await Swal.fire({
      buttonsStyling: false,
      customClass: {
        confirmButton: 'btn btn-danger fs-sm px-1 py-0 ',
        cancelButton: 'btn btn-light fs-sm px-1 py-0',
      },
      title: 'Confirmar cancelamento',
      html: 'Para cancelar o atendimento deve ser informada uma justificativa.<br> <strong>Você confirma esta ação?</strong>',
      icon: 'question',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      confirmButtonText: '<i class="ph-trash fs-sm align-middle"></i> Confirmar e Cancelar',
      stopKeydownPropagation: true,
      keydownListenerCapture: true,
      focusCancel: true,
      allowEnterKey: true,
      allowEscapeKey: true,
      input: 'textarea',
      inputLabel: ' ',
      inputPlaceholder: 'Digite a justificativa...',
      inputValidator: (value) => {
        if (!value) {
          return 'A justificativa deve ser informada.'
        }
      }
    }).then(async function (result) {
      if (result.isConfirmed) {
        App._loadingElements(true, divsBook, 10);
        divsBook.next_book.data("calling", false);
        const cancelBookResponse = await cancelCustomerServiceBook(book, result.value);
        App._loadingElements(false, divsBook);

        if (cancelBookResponse) {
          divsBook.next_book.data("calling", false);
          App._showMessage("Atendimento cancelado.", "success");
        }
      }
    });
  });

  divsBook.next_book.find(".btn-begin-customerservice").off().on("click", async function () {
    clearInterval(divsBook.next_book.find(".last-call-time").data("wating-time"));
    clearInterval(divsBook.next_book.find(".btn-call-customerservice").data("wating-time"));
    beginCustomerServiceAssisting(book);
  });

  divsBook.next_book.find(".btn-call-customerservice").prop("disabled", false).find(".time-counter").text(``);
  clearInterval(divsBook.next_book.find(".btn-call-customerservice").data("wating-time"));

  divsBook.next_book.find(".btn-call-customerservice").off().on("click", async function () {
    await callCustomerServiceBook(book);
  });
}

async function fillCustomerServiceAssistingBook(book) {
  const divsBook = getCustomerServiceDivsBooks();
  App._loadingElements(true, divsBook, 10);
  switchShowDivsBook("assisting_book");

  divsBook.assisting_book.data("assisting", true);

  const createdAt = new Date(book.created_at);
  divsBook.assisting_book.find(".created-at").text(`
    ${createdAt.toLocaleString("pt-BR", { day: "2-digit", month: "2-digit", year: "numeric" })} às 
    ${createdAt.toLocaleString("pt-BR", { hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false, })}h`
  );

  divsBook.assisting_book.find(".waiting-time").text('');
  clearInterval(divsBook.assisting_book.find(".waiting-time").data("wating-time"));
  divsBook.assisting_book.find(".waiting-time").data("time", book.created_at);
  divsBook.assisting_book.find(".waiting-time").data("wating-time", setInterval(function () {
    divsBook.assisting_book.find(".waiting-time").timeElapsed({
      currentTime: new Date,
      full: true
    });
  }, 1000));

  divsBook.assisting_book.find(".attendance-unit").text(book.attendance_unit_name.toUpperCase());
  divsBook.assisting_book.find(".queue").text(book.queue_description.toUpperCase());
  divsBook.assisting_book.find(".ticket").text(book.book_ticket != undefined ? `${String(book.ticket_prefix != undefined ? book.ticket_prefix : '').toUpperCase()}${book.book_ticket.toUpperCase()}` : '');
  divsBook.assisting_book.find(".matter").text(book.matter_description != undefined ? book.matter_description.toUpperCase() : '');
  App._loadingElements(false, divsBook);

  divsBook.assisting_book.find(".btn-edit-customerservice").off().on("click", async function () {
    editCustomerServiceAssisting(book);
  });
}

async function fetchCustomerServiceNextBook(inputs) {
  return await App._fetchItems({
    label: 'próximo atendimento',
    service: "System/CustomerService/CustomerService",
    method: "findNextAttendantBookByAttendanceUserIdAndQueueIdAndFilters",
    params: {
      format: 0,
      filter: {
        queue_id: inputs.queue.val(),
      }
    }
  });
}

async function callCustomerServiceBook(book) {
  const divsBook = getCustomerServiceDivsBooks();

  const _t = $(this);
  divsBook.next_book.find(".btn-call-customerservice").prop("disabled", true);
  divsBook.next_book.data("calling", true);
  App._loadingElements(true, divsBook, 10);

  const formBooks = $("#form-books");
  setFormValidationErrors(formBooks);
  let formData = getFormData(formBooks, 'values');
  formData['book'] = book.id;
  formData['api_token'] = $(`meta[name="user-token"]`).attr("content");

  const callsCount = await axios.post(`/api/system/queue/callbook`, formData, {
    headers: {
      'X-Requested-With': 'xmlhttprequest'
    },
  })
    .then(async function ({ data }) {
      return data.book.calls_count;
    })
    .catch(function (error) {
      if (error.response.data.errors != undefined) {
        App._showAppMessage('form_error', 'error');
        setFormValidationErrors($("#form-books"), error.response.data.errors);
      }
      return false;
    });

  App._loadingElements(false, divsBook);
  if (callsCount == false) {
    divsBook.next_book.find(".btn-call-customerservice").prop("disabled", false);
    return false;
  }

  divsBook.next_book.find(".call-count").text(callsCount);

  clearInterval(divsBook.next_book.find(".last-call-time").data("wating-time"));
  divsBook.next_book.find(".last-call-time").data("time", new Date());
  divsBook.next_book.find(".last-call-time").text('');
  divsBook.next_book.find(".last-call-time").data("wating-time", setInterval(function () {
    divsBook.next_book.find(".last-call-time").timeElapsed({
      currentTime: new Date,
      full: true
    });
  }, 1000));

  divsBook.next_book.find(".title").html(`<i class="ph-clock-clockwise"></i> Aguardando pessoa interessada para iniciar atendimento`);

  App._showMessage("Chamando atendimento.", "success")
  let counterDisableButtonCall = 4;
  divsBook.next_book.find(".btn-call-customerservice").data("wating-time", setInterval(function () {
    divsBook.next_book.find(".btn-call-customerservice").find(".time-counter").text(` (${counterDisableButtonCall})`);
    counterDisableButtonCall--;
    if (counterDisableButtonCall < 0) {
      divsBook.next_book.find(".btn-call-customerservice").find(".time-counter").text(``);
      divsBook.next_book.find(".btn-call-customerservice").prop("disabled", false);
      clearInterval(divsBook.next_book.find(".btn-call-customerservice").data("wating-time"));
    }
  }, 1000));
  return true;
}

async function cancelCustomerServiceBook(book, justification) {
  const formBooks = $("#form-books");
  setFormValidationErrors(formBooks);

  let formData = getFormData(formBooks, 'values');
  formData['book'] = book.id;
  formData['justification'] = justification;
  formData['api_token'] = $(`meta[name="user-token"]`).attr("content");

  return await axios.post(`/api/system/queue/cancelbook`, formData, {
    headers: {
      'X-Requested-With': 'xmlhttprequest'
    },
  })
    .then(async function ({ data }) {
      return true;
    })
    .catch(function (error) {
      if (error.response.data.errors != undefined) {
        App._showAppMessage('form_error', 'error');
        setFormValidationErrors($("#form-books"), error.response.data.errors);
      }
      return false;
    });
}

async function fetchCustomerServiceAssistingBook() {

  const assistingBook = await App._fetchItems({
    label: 'atendimento em curso',
    service: "System/CustomerService/CustomerService",
    method: "findAssistingByAttendanceByUserId",
    params: {
      format: 0,
    }
  });
  toggleCustomerServiceBtnAddItem();
  if (assistingBook.length) {
    await fillCustomerServiceAssistingBook(assistingBook[0])
    return true;
  }

  return false;
}

async function beginCustomerServiceAssisting(book) {
  const formBooks = $("#form-books");
  const divsBook = getCustomerServiceDivsBooks();
  divsBook.next_book.data("calling", false);
  setFormValidationErrors(formBooks);

  let formData = getFormData(formBooks, 'values');
  formData['book'] = book.id;
  App._loadPageModal({
    url: `/system/customerservices/presential/create?${$.param(formData)}`,
    title: "Atendimento Presencial",
    size: "lg",
    backdrop_static: true,
    done: function (modal) {
      fetchCustomerServiceAssistingBook();
      initCustomerServicePresential(modal, book);
    }
  });
}

function getCustomerServiceDivsBooks() {
  return {
    empty_queue: $("div.queue-empty"),
    no_book: $("div.queue-no-book"),
    next_book: $("div.queue-next-book"),
    assisting_book: $("div.queue-assisting-book"),
    queue_manual_call_ticket: $("div.queue-manual-dispenser-call-ticket"),
  }
}

function getCustomerServiceInputsBook() {
  const formBooks = $("#form-books");
  return {
    attendance_unit: formBooks.find("#attendance_unit"),
    queue: formBooks.find("#queue"),
    service_point: formBooks.find("#service_point"),
  }
}

function switchShowDivsBook(showOnly) {
  const divsBook = getCustomerServiceDivsBooks();
  Object.entries(divsBook).forEach(function (element) {
    if (typeof showOnly == "object" ? showOnly.includes(element[0]) : element[0] == showOnly) {
      element[1].removeClass("d-none");
    }

    if (typeof showOnly == "object" ? !showOnly.includes(element[0]) : element[0] != showOnly) {
      element[1].addClass("d-none");
    }
  });
}

function toggleCustomerServiceBtnAddItem() {
  const divsBook = getCustomerServiceDivsBooks();
  const inputsBook = getCustomerServiceInputsBook();

  if (inputsBook.queue.val() == '' || inputsBook.attendance_unit.val() == '' || divsBook.next_book.data("calling") == true) {
    $(".btn-add-item").prop("disabled", true);
    return;
  }

  $(".btn-add-item").prop("disabled", false);
}