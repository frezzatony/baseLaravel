$(document).ready(async function () {
  const container = $(".crud-users").eq(0);
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
        exportCrudItems("filters-crud", "dt-itens", 'users', 'Exportar lista de Usuários')
      });
    },
  };
  const grid = $("#dt-itens");
  const datatable = grid.DataTable(gridOptions)

  setUsersActions(grid);
  setUsersMenu(grid, datatable);
  setUsersFilters(container, grid, gridOptions);
});

function setUsersActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `users/edit/${idElement}`,
      title: "Cadastro | Usuário",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        setUsersItemMenu(modal);
        setUsersModalButtons(modal, grid);
        intUsersForm(modal);
      }
    });

  });
}

function setUsersMenu(grid, datatable) {
  $(".btn-add-item").off().on("click", function () {
    App._loadPageModal({
      url: `users/create`,
      title: "Cadastro | Usuário",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        setUsersItemMenu(modal);
        setUsersModalButtons(modal, grid);
        intUsersForm(modal);
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
    });
    App._confirmDelete(async function () {
      if (await destroyUsers(ids.toArray())) {
        grid.DataTable().draw();
      }
    })

  });
}

async function setUsersFilters(container, grid, gridOptions) {
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
            values[2] = item.name_show;
            values[3] = item.login;
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

function setUsersItemMenu(modal) {

}

function setUsersModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-user");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeUsersItem(form) : updateUsersItem(form))) {
      grid.DataTable().draw();
    }
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-user");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeUsersItem(form) : updateUsersItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storeUsersItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.post(`users/store`, getFormData(form, "values"))
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

async function updateUsersItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.put(`users/update`, getFormData(form, "values"))
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

async function destroyUsers(ids) {
  App._loading(true);
  return await axios.delete(`users/destroy`, { data: { ids: ids } })
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      App._loading(false);
      return true;
    })
    .catch(function (error) {
      App._loading(false);
      App._showMessage(error.response.data.message, 'error');
      return false;
    });
}

async function intUsersForm(modal) {
  const form = modal.find("#form-user")
  form.find("#login").mask('000.000.000-00');

  await fillUserProfiles(modal)
}

async function fillUserProfiles(modal) {

  const form = modal.find("#form-user")
  const inputProfiles = form.find("#profiles");

  App._loading(true, 'user_profiles');
  const profiles = await App._fetchItems({
    service: 'System/Profile',
    label: 'Perfis de Usuários',
    params: {
      order: {
        column: 2,
        dir: 'ASC'
      },
      length: "all"
    }
  });

  let options = profiles.map(function (item) {
    return {
      value: item.id,
      text: item.name.toUpperCase(),
    }
  });

  await fillDropdown({
    select: inputProfiles,
    options: options,
  });

  if (form.find("#stored_profiles").val().trim() != '') {
    const storedProfiles = JSON.parse(form.find("#stored_profiles").val().trim());
    storedProfiles.forEach(function (item) {
      inputProfiles.find(`option[value="${item.profile_id}"]`).attr('selected', true);
    });
  }

  await new DualListbox(inputProfiles[0], {
    availableTitle: "Perfis de Usuários",
    selectedTitle: "Perfis atribuídos",
    addButtonText: "<i class='ph-caret-right'></i>",
    removeButtonText: "<i class='ph-caret-left'></i>",
    addAllButtonText: "<i class='ph-caret-double-right'></i>",
    removeAllButtonText: "<i class='ph-caret-double-left'></i>"
  });
  const dualListBoxProfiles = $(".dual-listbox.profiles");
  dualListBoxProfiles.find("input").addClass("px-1 pt1 pb1 fs-sm").attr("placeholder", "Localizar...");
  dualListBoxProfiles.find(".dual-listbox__title").addClass("fs-sm mb-0 px-1");
  dualListBoxProfiles.find(".dual-listbox__item").addClass("f8 mb-0 pa1");
  dualListBoxProfiles.find(".dual-listbox__button").addClass("p-1");

  App._loading(false, 'user_profiles');
}