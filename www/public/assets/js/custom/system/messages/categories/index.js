$(document).ready(async function () {
  const container = $(".crud-notifications-categories").eq(0);
  let gridOptions = {
    columnDefs: [{
      orderable: false,
      targets: [0]
    },
    { className: "select-checkbox", targets: [0] },
    { className: "pa1 f8", targets: [1, 2, 3] },
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

  setNotificationsCategoriesActions(grid, datatable);
  setNotificationsCategoriesMenu(grid, datatable);
  setNotificationsCategoriesFilters(container, grid, gridOptions);
});

function setNotificationsCategoriesActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `/system/notifications/categories/edit/${idElement}`,
      title: "Cadastro | Categoria para Envio de Notificações",
      size: "md",
      done: function (modal) {
        initNotificationsCategoriesForm(modal);
        setNotificationsCategoriesItemMenu(modal);
        setNotificationsCategoriesModalButtons(modal, grid);
      }
    });

  });
}

function setNotificationsCategoriesMenu(grid, datatable) {
  $(".btn-add-item").off().on("click", function () {
    App._loadPageModal({
      url: `/system/notifications/categories/create`,
      title: "Cadastro | Categoria para Envio de Notificações",
      size: "md",
      done: function (modal) {
        initNotificationsCategoriesForm(modal);
        setNotificationsCategoriesItemMenu(modal);
        setNotificationsCategoriesModalButtons(modal, grid);
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
      if (await destroyNotificationsCategories(ids.toArray())) {
        grid.DataTable().draw();
      };
    })
  });
}

async function setNotificationsCategoriesFilters(container, grid, gridOptions) {
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
            values[2] = item.description;
            values[3] = item.is_active;
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

function setNotificationsCategoriesItemMenu(modal) {

}

function setNotificationsCategoriesModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-notifications-category");
    await (form.find(`input[name="id"]`).val() == '' ? storeNotificationsCategoriesItem(form) : updateNotificationsCategoriesItem(form));
    grid.DataTable().draw();
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-notifications-category");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeNotificationsCategoriesItem(form) : updateNotificationsCategoriesItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storeNotificationsCategoriesItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.post(`/system/notifications/categories/store`, getFormData(form, 'values'))
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

async function updateNotificationsCategoriesItem(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const formData = getFormData(form, 'formdata');
  formData.append('_method', 'put');
  return await axios.post(`/system/notifications/categories/update`, formData)
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

async function destroyNotificationsCategories(ids) {
  App._loading(true);
  return await axios.delete(`/system/notifications/categories/destroy`, { data: { ids: ids } })
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