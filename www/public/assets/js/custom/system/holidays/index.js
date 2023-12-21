$(document).ready(async function () {
  const container = $(".crud-holidays").eq(0);
  let gridOptions = {
    columnDefs: [{
      orderable: false,
      targets: [0]
    },
    { className: "select-checkbox", targets: [0] },
    { className: "pa1 f8", targets: [1, 2, 3, 4, 5] },
    { className: "text-center", targets: [1] },
    ],
    initComplete: function (settings, json) {
      $(".btn-export-crud-search").on("click", function () {
        exportCrudItems("filters-crud", "dt-itens", 'holidays', 'Exportar lista de Módulos')
      });
    },
  };
  const grid = $("#dt-itens");
  const datatable = grid.DataTable(gridOptions)

  setHolidaysActions(grid);
  setHolidaysMenu(grid, datatable);
  setHolidaysFilters(container, grid, gridOptions);
});

function setHolidaysActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `holidays/edit/${idElement}`,
      title: "Cadastro | Feriado/Ponto Facultativo",
      size: "sm",
      done: function (modal) {
        initHolidaysForm(modal);
        setHolidaysItemMenu(modal);
        setHolidaysModalButtons(modal, grid);
      }
    });

  });
}

function setHolidaysMenu(grid, datatable) {
  $(".btn-add-item").off().on("click", function () {
    App._loadPageModal({
      url: `holidays/create`,
      title: "Cadastro | Feriado/Ponto Facultativo",
      size: "sm",
      done: function (modal) {
        initHolidaysForm(modal);
        setHolidaysItemMenu(modal);
        setHolidaysModalButtons(modal, grid);
      }
    });
  });

  datatable.on("draw", function () {
    $(".btn-remove-item").attr('disabled', true);
  });
  datatable.on('select', function (e, dt, type, indexes) {
    $(".btn-remove-item").attr('disabled', false);
  });
  datatable.on('deselect', function (e, dt, type, indexes) {
    if (datatable.rows({ selected: true }).count() == 0) {
      $(".btn-remove-item").attr('disabled', true);
    }
  });

  $(".btn-remove-item").off().on("click", async function () {
    let ids = grid.find(".selected").map(function (index, row) {
      return $(row).attr("id");
    })

    App._confirmDelete(async function () {
      if (await destroyHolidays(ids.toArray())) {
        grid.DataTable().draw();
      };
    })
  });
}

async function setHolidaysFilters(container, grid, gridOptions) {
  await setFiltrosDinamicosPesquisa({
    "id_elemento": "filters-crud",
    "filtros": JSON.parse($(".filters-template").val()),
    "autoload": true,
    "values": JSON.parse(container.find(".filters-default-values").val()),
    "grid": grid,
    'onSubmit': function (filtros) {
      gridOptions.serverSide = true;
      gridOptions.processing = true;
      gridOptions.rowId = "id";
      gridOptions.ajax = {
        url: `${grid.data('url')}`,
        type: 'GET',
        data: function (values) {
          return {
            ...filtros, ...{
              order: values.order[0],
              start: values.start,
              length: values.length,
              draw: values.draw,
              cookie_path: $("#cookie-path").val(),
              path: "System/Holiday",
              _datatable: true,
              api_token: $(`meta[name="user-token"]`).attr("content"),
            }
          }
        },
        dataSrc: function (response) {
          App._loading(false);
          response.data = response.data.map(function (item) {
            let values = [];
            let itemDate = new Date(`${item.date}T00:00:00`);
            values["id"] = item.id;
            values[0] = '';
            values[1] = item.id;
            values[2] = item.description.toUpperCase();
            values[3] = item.annual.toUpperCase();
            values[4] = item.type.toUpperCase();
            values[5] = itemDate.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
            return values;
          });
          return response.data;
        },
        beforeSend: function () {
          App._loading(true);
        },
        error: function () {
          App._loading(false);
        }
      }
      grid.DataTable().clear().destroy();
      grid.DataTable(gridOptions);
    }
  });
}

function setHolidaysItemMenu(modal) {

}

function setHolidaysModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-holiday");
    await (form.find(`input[name="id"]`).val() == '' ? storeHolidaysItem(form) : updateHolidaysItem(form));
    grid.DataTable().draw();
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-holiday");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeHolidaysItem(form) : updateHolidaysItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storeHolidaysItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.post(`holidays/store`, getFormData(form, 'values'))
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      if (data.status == 'success') {
        form.find(`input[name="id"]`).val(data.id);
        form.find(`input[name="show_item_id"]`).val(data.id);
      }
      App._loading(false);
      return data.status == 'success';
    })
    .catch(function (error) {
      App._loading(false);
      if (error.response.data.errors != undefined) {
        App._showAppMessage('form_error', 'error');
        setFormValidationErrors(form, error.response.data.errors);
      }
      return false;
    });
}

async function updateHolidaysItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.put(`holidays/update`, getFormData(form, 'values'))
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      App._loading(false);
      return data.status == 'success';
    })
    .catch(function (error) {
      App._loading(false);
      if (error.response.data.errors != undefined) {
        App._showAppMessage('form_error', 'error');
        setFormValidationErrors(form, error.response.data.errors);
      }
      return false;
    });
}

async function destroyHolidays(ids) {
  App._loading(true);
  return await axios.delete(`holidays/destroy`, { data: { ids: ids } })
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      App._loading(false);
      return data.status != "error";
    })
    .catch(function (error) {
      App._loading(false);
      App._showMessage(error.response.data.message, 'error');
      return false;
    });
}

function initHolidaysForm(modal) {
  const form = modal.find("#form-holiday")
  const inputAnnual = form.find(`input[name="annual"]`);
  const inputDate = form.find(`input[name="date"]`);

  inputAnnual.on("change", function () {
    inputDate.attr("min", $(this).is(':checked') ? `0001-01-01` : '');
    inputDate.attr("max", $(this).is(':checked') ? `0001-12-31` : '');
  })
}