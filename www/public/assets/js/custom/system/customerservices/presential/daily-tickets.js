$(document).ready(async function () {
  const inputsBook = getCustomerServiceInputsBook();
  const inputsTickets = getCustomerServiceInputsTickets();

  inputsBook.attendance_unit.on("change", function () {
    const _t = $(this);
    if (!_t.val() || !inputsBook.queue.find("option:selected").val()) {
      return false;
    }
    fillCustomerServiceTicketsInputMatters(inputsBook, inputsTickets);
    fillCustomerServiceTicketsInputPriorities(inputsBook, inputsTickets);
  });

  inputsBook.queue.on("change", function () {
    const _t = $(this);
    if (!_t.val() || !inputsBook.attendance_unit.find("option:selected").val()) {
      return false;
    }
    fillCustomerServiceTicketsInputMatters(inputsBook, inputsTickets);
    fillCustomerServiceTicketsInputPriorities(inputsBook, inputsTickets);
  });

  inputsTickets.date.on("blur", function () {
    fetchCustomerServiceDailyTickets();
  });

  inputsTickets.matter.on("change", function () {
    fetchCustomerServiceDailyTickets();
  });

  inputsTickets.priority.on("change", function () {
    fetchCustomerServiceDailyTickets();
  });

  inputsTickets.status.on("change", function () {
    fetchCustomerServiceDailyTickets();
  });

});

async function fillCustomerServiceTicketsInputMatters(inputsBook, inputsTickets) {
  const matters = await App._fetchItems({
    service: "System/Queue/QueueMatter",
    label: 'assuntos de tickets',
    params: {
      format: 0,
      filter: {
        queues_id: inputsBook.queue.find("option:selected").val(),
      },
      order_by: 'matter_description'
    }
  });
  let options = matters.map(function (item) {
    return {
      value: item.matter_id,
      text: item.matter_description.toUpperCase(),
    }
  });

  options.unshift({
    value: '',
    text: 'TODOS OS ASSUNTOS DA FILA',
  });
  options.unshift({
    value: 'my_matters',
    text: 'MEUS ASSUNTOS',
  });

  await fillDropdown({
    first_empty: false,
    select: inputsTickets.matter,
    options: options,
  });
  inputsTickets.matter.trigger("change");
}

async function fillCustomerServiceTicketsInputPriorities(inputsBook, inputsTickets) {
  const priorities = await App._fetchItems({
    service: "System/Queue/QueueCallOrder",
    label: 'prioridades de atendimento de tickets',
    params: {
      format: 0,
      filter: {
        queues_id: inputsBook.queue.find("option:selected").val(),
      },
      order_by: 'description'
    }
  });
  let options = priorities.map(function (item) {
    return {
      value: item.id,
      text: item.description.toUpperCase(),
    }
  });

  options.unshift({
    value: '',
    text: 'TODOS AS PRIORIDADES DA FILA',
  });

  await fillDropdown({
    first_empty: false,
    select: inputsTickets.priority,
    options: options,
  });
  inputsTickets.priority.trigger("change");
}

async function fetchCustomerServiceDailyTickets() {
  const grid = $("#dt-daily-tickets");
  const divsBook = getCustomerServiceDivsBooks();
  const inputsBook = getCustomerServiceInputsBook();
  const inputsTickets = getCustomerServiceInputsTickets();

  if (grid.data("working") == true) {
    return false;
  }

  if (inputsBook.attendance_unit.find("option:selected").val() == '' || inputsBook.queue.find("option:selected").val() == '' ||
    inputsTickets.matter.find("option").length == 0 || inputsTickets.priority.find("option").length == 0) {
    $(".daily-tickets-empty").removeClass("d-none");
    $(".daily-tickes-list").addClass("d-none");
    return false;
  }

  $(".daily-tickets-empty").addClass("d-none");
  $(".daily-tickes-list").removeClass("d-none");

  if (grid.hasClass("dataTable")) {
    grid.DataTable().clear().destroy();
  }

  grid.data("working", true);
  grid.DataTable({
    serverSide: true,
    processing: true,
    columnDefs: [{
      orderable: false,
      targets: [5]
    },

    { className: "pa1 f8", targets: [0, 1, 2, 3, 4, 5] },
    { className: "text-center", targets: [5] },
    ],
    ajax: {
      url: `/api/system/fetch/items/QueueBook`,
      type: 'GET',
      data: function (values) {
        return {
          order: values.order[0],
          start: values.start,
          length: values.length,
          draw: values.draw,
          cookie_path: $("#cookie-path").val(),
          path: "System/Queue",
          method: 'findAllBooksByFilters',
          format: 0,
          _datatable: true,
          api_token: $(`meta[name="user-token"]`).attr("content"),
          filter: {
            date: $("#date").val(),
            queue_id: inputsBook.queue.val(),
            matter: inputsTickets.matter.val(),
            priority: inputsTickets.priority.val(),
            status: inputsTickets.status.val()
          }
        }
      },
      dataSrc: function (response) {
        App._loading(false);
        response.data = response.data.map(function (item) {
          let createdAt = new Date(`${item.created_at}`);
          let values = [
            item.ticket.toUpperCase(),
            `
              ${createdAt.toLocaleString("pt-BR", { day: "2-digit", month: "2-digit", year: "numeric" })} às 
              ${createdAt.toLocaleString("pt-BR", { hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false, })}h
            `,
            item.matter_description.toUpperCase(),
            item.priority_description.toUpperCase(),
            item.status_description.toUpperCase(),
            getCustomerServiceDailyTicketsActionsMenu(item),
          ];
          return values;
        });
        return response.data;
      },
      beforeSend: function () {
        App._loading(true);
      },
      error: function () {
        App._loading(false);
      },
    },
    drawCallback: function () {
      grid.find(".btn-call-ticket").on("click", async function () {
        const _t = $(this);
        const callResponse = await callCustomerServiceBook({
          id: _t.data("book-id")
        });
        if (callResponse) {
          divsBook.next_book.data("calling", false);
          await fillCustomerServiceBook();
        }
      });
      grid.data("working", false);
    }
  });
}

function getCustomerServiceDailyTicketsActionsMenu(item) {
  const divsBook = getCustomerServiceDivsBooks();
  const templateMenu = $(".template-tickets-actions-menu").clone();
  templateMenu.removeClass('template-tickets-actions-menu d-none');

  const actionsButtons = {
    edit_customer_service: templateMenu.find(".btn-edit-customerservice"),
    call_ticket: templateMenu.find(".btn-call-ticket"),
    begin_customer_service: templateMenu.find(".btn-begin-customerservice"),
    cancel_ticket: templateMenu.find(".btn-cancel-ticket"),
  }


  if (
    ['waiting_in_line', 'canceled'].includes(item.status) &&
    divsBook.next_book.data("calling") != true) {
    actionsButtons.call_ticket.addClass("active");
  }

  Object.entries(actionsButtons).forEach(function (button) {
    if (!button[1].hasClass("active")) {
      button[1].remove();
      return;
    }
    button[1].attr("data-book-id", item.book_id);
  });

  return templateMenu.find("button").length ? templateMenu.html() : '';
}

function getCustomerServiceInputsTickets() {
  const formTickets = $("#form-tickets");
  return {
    date: $("#date"),
    matter: formTickets.find("#tickets_matter"),
    priority: formTickets.find("#tickets_priority"),
    status: formTickets.find("#tickets_status"),
  }
}