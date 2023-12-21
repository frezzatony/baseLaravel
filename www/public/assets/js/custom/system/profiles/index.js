$(document).ready(async function () {
  const container = $(".crud-profiles").eq(0);
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
        exportCrudItems("filters-crud", "dt-itens", 'profiles', 'Exportar lista de Perfis de Usuários')
      });
    },
  };
  const grid = $("#dt-itens");
  const datatable = grid.DataTable(gridOptions)

  setProfilesActions(grid);
  setProfilesMenu(grid, datatable);
  setProfilesFilters(container, grid, gridOptions);
});

function setProfilesActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `profiles/edit/${idElement}`,
      title: "Cadastro | Perfil de Usuário",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        setProfilesItemMenu(modal);
        setProfilesModalButtons(modal, grid);
        intProfilesForm(modal);
      }
    });

  });
}

function setProfilesMenu(grid, datatable) {
  $(".btn-add-item").off().on("click", function () {
    App._loadPageModal({
      url: `profiles/create`,
      title: "Cadastro | Perfil de Usuário",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        setProfilesItemMenu(modal);
        setProfilesModalButtons(modal, grid);
        intProfilesForm(modal);
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
      if (await destroyProfiles(ids.toArray())) {
        grid.DataTable().draw();
      }
    })
  });
}

async function setProfilesFilters(container, grid, gridOptions) {
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

function setProfilesItemMenu(modal) {

}

function setProfilesModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-profile");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeProfilesItem(form) : updateProfilesItem(form))) {
      grid.DataTable().draw();
    };

  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-profile");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeProfilesItem(form) : updateProfilesItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storeProfilesItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  const getValues = function (values) {
    let formValues = {};
    Object.entries(values).forEach(function (item) {
      formValues[item[0]] = item[1].value != undefined ? item[1].value : item[1];
    });
    return formValues;
  }

  return await axios.post(`profiles/store`, getValues(getFormData(form, 'values')))
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

async function updateProfilesItem(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const getValues = function (values) {
    let formValues = {};
    Object.entries(values).forEach(function (item) {
      formValues[item[0]] = item[1].value != undefined ? item[1].value : item[1];
    });
    return formValues;
  }
  return await axios.put(`profiles/update`, getValues(getFormData(form, 'values')))
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      App._loading(false);
      return true;
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

async function destroyProfiles(ids) {
  App._loading(true);
  return await axios.delete(`profiles/destroy`, { data: { ids: ids } })
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

async function intProfilesForm(modal) {
  const form = modal.find("#form-profile");
  await fillProfilePrivileges(form);
}

async function fillProfilePrivileges(form) {
  App._loading(true, 'profile_privileges');
  const modules = await App._fetchItems({
    service: "System/Module",
    params: {
      order: {
        column: 2,
        dir: "ASC"
      },
      length: 'all',
    }
  });

  const listGroupModules = form.find(".listgroup-modules");
  for (const item of modules) {
    let templateLabelModule = form.find(".label-module-template").eq(0).clone();
    templateLabelModule.removeClass("label-module-template d-none");
    templateLabelModule.append(item.name.toUpperCase());
    templateLabelModule.find(".list-group-radio-label").val(item.id).on("change", async function () {
      await fillModuleRoutines(form, item);
    });

    if (form.find(`input[name="id"]`).val() != "") {
      await fillModuleRoutines(form, item);
    }

    listGroupModules.append(templateLabelModule);
  };

  if (form.find(`input[name="id"]`).val() != "") {
    form.find(".list-group-item-action").eq(1).trigger("click");
  }

  App._loading(false, 'profile_privileges');
}

async function fillModuleRoutines(form, module) {
  const divModuleRoutines = form.find(".module-routines");
  const setListGroupModuleRoutines = async function () {
    App._loading(true);
    const routines = await App._fetchItems({
      service: "System/Routine",
      params: {
        order: {
          column: 2,
          dir: "ASC"
        },
        filter: {
          module: [
            {
              value: module.id,
              operator: "equal_integer"
            }
          ]
        }
      }
    });

    const storedActions = form.find("#stored_actions").val().trim() == '' ? [] : JSON.parse(form.find("#stored_actions").val().trim()).map(function (action) {
      return action.routines_actions_id;
    });

    const templateListGroupModuleRoutines = form.find(".list-group-routines-template").clone();
    templateListGroupModuleRoutines.removeClass("list-group-routines-template").addClass(`list-group-routines-module-${module.id}`);

    await routines.forEach(function (routine) {
      if (routine.routine_actions.length == 0) {
        return true;
      }

      let routineNode = $("<li></li>", {
        class: "folder fs-sm expanded text-break",
        text: routine.name.toUpperCase()
      });
      let routineActionsListNodes = $("<ul></ul>");

      routine.routine_actions.forEach(function (action) {
        let routineActionNode = $("<li></li>", {
          class: "fs-sm",
          text: `${action.description} [${action.slug}]`,
          "data-value": action.id,
        });
        if (storedActions.includes(action.id)) {
          routineActionNode.addClass("selected");
        }
        routineActionsListNodes.append(routineActionNode);
      });

      if (routineActionsListNodes.find("li").length == routineActionsListNodes.find(".fancytree-selected")) {
        routineActionsListNodes.addClass("selected");
      }

      routineNode.append(routineActionsListNodes);
      templateListGroupModuleRoutines.find("ul").first().append(routineNode);
    });

    divModuleRoutines.append(templateListGroupModuleRoutines);

    if (templateListGroupModuleRoutines.find("li").length) {
      templateListGroupModuleRoutines.find(".tree-checkbox-hierarchical").attr("name", "actions");
      templateListGroupModuleRoutines.find(".tree-checkbox-hierarchical").fancytree({
        checkbox: true,
        selectMode: 3
      });
    }

    if (templateListGroupModuleRoutines.find("li").length == 0) {
      templateListGroupModuleRoutines.find("div").removeClass("d-none");
    }
    App._loading(false);
  }


  divModuleRoutines.find(".list-group").addClass("d-none");
  let listGroupRoutines = form.find(`.list-group-routines-module-${module.id}`);
  if (listGroupRoutines.length == 0) {
    await setListGroupModuleRoutines();
    listGroupRoutines = form.find(`.list-group-routines-module-${module.id}`);
  }
  listGroupRoutines.addClass("fade").removeClass("d-none").addClass("show");
}