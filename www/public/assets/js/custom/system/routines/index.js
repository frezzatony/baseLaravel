$(document).ready(async function () {
  const container = $(".crud-routines").eq(0);
  let gridOptions = {
    columnDefs: [{
      orderable: false,
      targets: [0]
    },
    { className: "select-checkbox", targets: [0] },
    { className: "pa1 f8", targets: [1, 2, 3, 4, 5] },
    { className: "text-center", targets: [1, 5] },
    ],
    initComplete: function (settings, json) {
      $(".btn-export-crud-search").on("click", function () {
        exportCrudItems("filters-crud", "dt-itens", 'routines', 'Exportar lista de Perfis de Usuários')
      });
    },
  };
  const grid = $("#dt-itens");
  const datatable = grid.DataTable(gridOptions)

  setRoutinesActions(grid);
  setRoutinesMenu(grid, datatable);
  setRoutinesFilters(container, grid, gridOptions);
});

function setRoutinesActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `routines/edit/${idElement}`,
      title: "Cadastro | Rotina e Ações",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        setRoutinesItemMenu(modal);
        setRoutinesModalButtons(modal, grid);
        intRoutinesForm(modal);
      }
    });

  });
}

function setRoutinesMenu(grid, datatable) {
  $(".btn-add-item").off().on("click", function () {
    App._loadPageModal({
      url: `routines/create`,
      title: "Cadastro | Rotina e Ações",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        setRoutinesItemMenu(modal);
        setRoutinesModalButtons(modal, grid);
        intRoutinesForm(modal);
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
      if (await destroyRoutines(ids.toArray())) {
        grid.DataTable().draw();
      }
    })

  });
}

async function setRoutinesFilters(container, grid, gridOptions) {
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
            values[2] = item.name.toUpperCase();
            values[3] = item.slug;
            values[4] = item.module_name.toUpperCase();
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

function setRoutinesItemMenu(modal) {

}

function setRoutinesModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-routine");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeRoutinesItem(form) : updateRoutinesItem(form))) {
      grid.DataTable().draw();
    }
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-routine");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeRoutinesItem(form) : updateRoutinesItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storeRoutinesItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.post(`routines/store`, getFormData(form, 'values'))
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      if (data.status == 'success') {
        form.find(`input[name="id"]`).val(data.id);
        form.find(`input[name="show_item_id"]`).val(data.id);
        setRoutinesActionsIds(form, data.actions);
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

async function updateRoutinesItem(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const getValues = function (values) {
    let formValues = {};
    Object.entries(values).forEach(function (item) {
      formValues[item[0]] = item[1].value != undefined ? item[1].value : item[1];
    });
    return formValues;
  }
  return await axios.put(`routines/update`, getValues(getFormData(form, 'values')))
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      if (data.status == 'success') {
        setRoutinesActionsIds(form, data.actions);
      }
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

async function destroyRoutines(ids) {
  App._loading(true);
  return await axios.delete(`routines/destroy`, { data: { ids: ids } })
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

async function intRoutinesForm(modal) {
  const form = modal.find("#form-routine")
  await fillRoutineModules(modal);
  await setRoutineActions(modal, false);
}

async function fillRoutineModules(modal) {

  const form = modal.find("#form-routine")
  const inputModule = form.find("#modules_id");

  App._loading(true, 'routine_modules');
  const profiles = await App._fetchItems({
    service: 'System/Module',
    label: 'Módulos',
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
    select: inputModule,
    options: options,
  });

  App._loading(false, 'routine_modules');
}

async function setRoutineActions(modal, readonly) {
  const form = modal.find("#form-routine");
  const idTblAppendGridRoutineActions = "tbl-appendgrid-routines-actions";
  const tblAppendGridRoutineActions = $(`#${idTblAppendGridRoutineActions}`);

  if (tblAppendGridRoutineActions.trigger("appendGrid").data("appendGrid") != undefined) {
    tblAppendGridRoutineActions.find("tbody tr").each(function (item) {
      tblAppendGridRoutineActions.trigger("appendGrid").data("appendGrid").removeRow($(item).data("unique-index"));
    });
    return false;
  }

  const appendGridRoutineActions = await new AppendGrid({
    element: idTblAppendGridRoutineActions,
    uiFramework: "bootstrap5",
    iconParams: {
      icons: {
        append: "ph-plus",
      }
    },
    initRows: 0,
    initData: form.find("#stored_routines").val().trim() != '' ? JSON.parse(form.find("#stored_routines").val().trim()) : null,
    hideRowNumColumn: true,
    i18n: {
      append: "Adicionar nova ação",
      remove: "Remover",
      rowEmpty: "Não há ações vinculadas.",
    },
    sectionClasses: {
      table: "table-sm table-striped border fs-sm",
      thead: "p-0",
      control: "form-control px-1 py-0 fs-sm",
      buttonGroup: "btn-group-sm",
    },
    hideButtons: {
      remove: true,
      removeLast: true,
      moveUp: true,
      moveDown: true,
      insert: true,
      append: true,
    },
    nameFormatter: function (idPrefix, name, uniqueIndex) {
      return `actions[${uniqueIndex}][${name}]`;
    },
    columns: [
      {
        name: "id",
        type: "hidden",
      },
      {
        name: "slug",
        display: "Slug*",
        type: readonly == true ? "readonly" : "text",
        cellClass: "w-30 p-1",
        ctrlClass: (readonly == true ? "text-center" : ""),
        displayCss: {
          "width": "30%",
          "padding": "1px",
        },
      },
      {
        name: "description",
        display: "Descrição",
        type: readonly == true ? "readonly" : "text",
        cellClass: "w-60 p-1",
        displayCss: {
          "width": "60%",
          "padding": "1px",
        },
      },
      {
        name: "acoes",
        display: "Ações",
        type: "custom",
        cellClass: "w-10 p-1 text-center",
        ctrlClass: "text-center",
        displayCss: {
          "width": "10%",
          "padding": "1px",
        },
        customBuilder: function (parent, idPrefix, name, uniqueIndex) {
          const appendGridRoutineActions = document.getElementById(idTblAppendGridRoutineActions).appendGrid;
          const botaoExcluir = $('<button/>', {
            type: "button",
            class: "btn btn-danger p-1",
            html: `<i class="ph-trash fs-sm"></i>`,
            "data-toggle": "tooltip",
            "data-placement": "top",
            "data-original-title": "Excluir"
          });
          botaoExcluir.on("click", function () {
            $('[data-toggle="tooltip"]').tooltip("hide");
            const inputId = $(parent).closest("tr").find(`input[name="actions\\[${uniqueIndex}\\]\\[id\\]"]`);
            if (inputId.val() == '') {
              appendGridRoutineActions.removeRow($(parent).closest("tr").index());
              return;
            }
            App._confirmDelete(function () {
              const appendGridRoutineActions = document.getElementById(idTblAppendGridRoutineActions).appendGrid;
              appendGridRoutineActions.removeRow($(parent).closest("tr").index());
            });
          });
          if (readonly != true) {
            $(parent).append(botaoExcluir);
          }
        },
        customGetter: function () {
          return null;
        }
      },
    ],
    afterRowAppended: function (table, parentRowIndex, addedRowIndex) {

    },
  });

  document.getElementById(idTblAppendGridRoutineActions).appendGrid = appendGridRoutineActions;

  form.find(".btn-add-action").on("click", async function () {
    await appendGridRoutineActions.appendRow([{
      id: App.randomstring(),
    }]);
  });
}

function setRoutinesActionsIds(form, actions) {
  const tblAppendGridRoutineActions = form.find(`[name="actions"]`);
  const appendGridRoutineActions = document.getElementById(tblAppendGridRoutineActions.attr("id")).appendGrid;
  if (typeof actions == 'string') {
    actions = JSON.parse(actions);
  }

  actions.forEach(function (action, index) {
    appendGridRoutineActions.setCtrlValue("id", index, action);
  });
}