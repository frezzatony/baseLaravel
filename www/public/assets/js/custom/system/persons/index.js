$(document).ready(async function () {
  const container = $(".crud-persons").eq(0);
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
        exportCrudItems("filters-crud", "dt-itens", 'persons', 'Exportar lista de Pessoas')
      });
    },
  };
  const grid = $("#dt-itens");
  const datatable = grid.DataTable(gridOptions)

  setPersonsActions(grid, datatable);
  setPersonsMenu(grid, datatable);
  setPersonsFilters(container, grid, gridOptions);
});

function setPersonsActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `persons/${grid.dataTable().api().row(this).data()['type'].toLowerCase()}/edit/${idElement}`,
      title: "Cadastro | Pessoa",
      size: "lg",
      done: function (modal) {
        initPersonsForm(modal);
        setPersonsItemMenu(modal);
        setPersonsModalButtons(modal, grid);
      }
    });

  });
}

function setPersonsMenu(grid, datatable) {
  $(".btn-add-natural-person").off().on("click", function () {
    App._loadPageModal({
      url: `/system/persons/natural/create`,
      title: "Cadastro | Pessoa",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        initPersonsForm(modal);
        setPersonsItemMenu(modal);
        setPersonsModalButtons(modal, grid);
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
      if (await destroyPersons(ids.toArray())) {
        grid.DataTable().draw();
      };
    })
  });
}

async function setPersonsFilters(container, grid, gridOptions) {
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
              path: "System/Person",
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
            values["type"] = item.type;
            values[0] = '';
            values[1] = item.id;
            values[2] = item.name_show;
            values[3] = item.type_show;
            values[4] = item.cpf_cnpj;
            values[5] = item.is_active;
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

function setPersonsItemMenu(modal) {

}

function setPersonsModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-person");
    await (form.find(`input[name="id"]`).val() == '' ? storePersonsItem(form) : updatePersonsItem(form));
    grid.DataTable().draw();
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-person");
    if (await (form.find(`input[name="id"]`).val() == '' ? storePersonsItem(form) : updatePersonsItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storePersonsItem(form) {
  App._loading(true);
  setFormValidationErrors(form);
  return await axios.post(`persons/natural/store`, getFormData(form, 'formdata'))
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      if (data.status == 'success') {
        form.find(`input[name="id"]`).val(data.id);
        form.find(`input[name="show_item_id"]`).val(data.id);
        form.find("#attachments").fileManager({
          method: "setOptions",
          auto_upload: true,
          crud_id: data.id,
        });
        await form.find("#attachments").fileManager({ method: "runUpload", });
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

async function updatePersonsItem(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const formData = getFormData(form, 'formdata');
  formData.append('_method', 'put');
  return await axios.post(`persons/${form.find("#type").val().toLowerCase()}/update`, formData)
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

async function destroyPersons(ids) {
  App._loading(true);
  return await axios.delete(`persons/destroy`, { data: { ids: ids } })
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