$(document).ready(async function () {
  const container = $(".crud-modules").eq(0);
  let gridOptions = {
    columnDefs: [{
      orderable: false,
      targets: [0]
    },
    { className: "select-checkbox", targets: [0] },
    { className: "pa1 f8", targets: [1, 2, 3, 4] },
    { className: "text-center", targets: [1] },
    ],
    initComplete: function (settings, json) {
      $(".btn-export-crud-search").on("click", function () {
        exportCrudItems("filters-crud", "dt-itens", 'modules', 'Exportar lista de Módulos')
      });
    },
  };
  const grid = $("#dt-itens");
  const datatable = grid.DataTable(gridOptions)

  setModulesActions(grid);
  setModulesMenu(grid, datatable);
  setModulesFilters(container, grid, gridOptions);
});

function setModulesActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `modules/edit/${idElement}`,
      title: "Cadastro | Módulo",
      size: "lg",
      done: function (modal) {
        initModulesForm(modal);
        setModulesItemMenu(modal);
        setModulesModalButtons(modal, grid);
      }
    });

  });
}

function setModulesMenu(grid, datatable) {
  $(".btn-add-item").off().on("click", function () {
    App._loadPageModal({
      url: `modules/create`,
      title: "Cadastro | Módulo",
      size: "lg",
      done: function (modal) {
        initModulesForm(modal);
        setModulesItemMenu(modal);
        setModulesModalButtons(modal, grid);
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
      if (await destroyModules(ids.toArray())) {
        grid.DataTable().draw();
      };
    })
  });
}

async function setModulesFilters(container, grid, gridOptions) {
  await setFiltrosDinamicosPesquisa({
    "id_elemento": "filters-crud",
    "filtros": JSON.parse($(".filters-template").val()),
    "autoload": true,
    "values": $.cookie("filtros_dinamicos_pesquisa") ? JSON.parse($.cookie("filtros_dinamicos_pesquisa")) : JSON.parse(container.find(".filters-default-values").val()),
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
              path: "System",
              _datatable: true,
              api_token: $(`meta[name="user-token"]`).attr("content"),
            }
          }
        },
        dataSrc: function (response) {
          App._loading(false);
          response.data = response.data.map(function (item) {
            let values = [];
            values["id"] = item.id;
            values[0] = '';
            values[1] = item.id;
            values[2] = item.name;
            values[3] = item.slug;
            values[4] = item.is_active;
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

function setModulesItemMenu(modal) {

}

function setModulesModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-module");
    await (form.find(`input[name="id"]`).val() == '' ? storeModulesItem(form) : updateModulesItem(form));
    grid.DataTable().draw();
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-module");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeModulesItem(form) : updateModulesItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storeModulesItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.post(`modules/store`, getFormData(form, 'values'))
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

async function updateModulesItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.put(`modules/update`, getFormData(form, 'values'))
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

async function destroyModules(ids) {
  App._loading(true);
  return await axios.delete(`modules/destroy`, { data: { ids: ids } })
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

function initModulesForm(modal) {
  const form = modal.find("#form-module")
  const inputName = form.find(`input[name="name"]`);
  const inputSlug = form.find(`input[name="slug"]`);

  inputName.on("input", function () {
    if (inputSlug.data('value-by-input') != true) {
      inputSlug.val(App._generateSlug(inputName.val()));
    }
  });

  inputSlug.data('value-by-input', inputSlug.val() != '');
  inputSlug.on("input", function () {
    $(this).data('value-by-input', $(this).val() != '');
  });

}