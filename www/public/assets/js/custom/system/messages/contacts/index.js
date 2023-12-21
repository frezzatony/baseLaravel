$(document).ready(async function () {
  const container = $(".crud-notifications-contacts").eq(0);
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

  setNotificationsContactsActions(grid, datatable);
  setNotificationsContactsMenu(grid, datatable);
  setNotificationsContactsFilters(container, grid, gridOptions);
});

function setNotificationsContactsActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `/system/notifications/contacts/edit/${idElement}`,
      title: "Cadastro | Contato para Envio de Notificações",
      size: "md",
      done: function (modal) {
        initNotificationsContactsForm(modal);
        setNotificationsContactsItemMenu(modal);
        setNotificationsContactsModalButtons(modal, grid);
      }
    });

  });
}

function setNotificationsContactsMenu(grid, datatable) {
  $(".btn-add-item").off().on("click", function () {
    App._loadPageModal({
      url: `/system/notifications/contacts/create`,
      title: "Cadastro | Contato para Envio de Notificações",
      size: "md",
      done: function (modal) {
        initNotificationsContactsForm(modal);
        setNotificationsContactsItemMenu(modal);
        setNotificationsContactsModalButtons(modal, grid);
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
      if (await destroyNotificationsContacts(ids.toArray())) {
        grid.DataTable().draw();
      };
    })
  });
}

async function setNotificationsContactsFilters(container, grid, gridOptions) {
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
              path: "System/Notifications",
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
            values[2] = item.name;
            values[3] = item.telegram;
            values[4] = item.email;
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

function setNotificationsContactsItemMenu(modal) {

}

function setNotificationsContactsModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-notifications-contact");
    await (form.find(`input[name="id"]`).val() == '' ? storeNotificationsContactsItem(form) : updateNotificationsContactsItem(form));
    grid.DataTable().draw();
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-notifications-contact");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeNotificationsContactsItem(form) : updateNotificationsContactsItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storeNotificationsContactsItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.post(`/system/notifications/contacts/store`, getFormData(form, 'values'))
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      if (data.status == 'success') {
        form.find(`input[name="id"]`).val(data.id);
        form.find(`input[id="show_item_id"]`).val(data.id);
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

async function updateNotificationsContactsItem(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const formData = getFormData(form, 'formdata');
  formData.append('_method', 'put');
  return await axios.post(`/system/notifications/contacts/update`, formData)
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

async function destroyNotificationsContacts(ids) {
  App._loading(true);
  return await axios.delete(`/system/notifications/contacts/destroy`, { data: { ids: ids } })
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