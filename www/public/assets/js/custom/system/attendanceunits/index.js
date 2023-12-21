$(document).ready(async function () {
  const container = $(".crud-attendanceunits").eq(0);
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
        exportCrudItems("filters-crud", "dt-itens", 'attendanceunits', 'Exportar lista de Usuários')
      });
    },
  };
  const grid = $("#dt-itens");
  const datatable = grid.DataTable(gridOptions)

  setAttendanceUnitActions(grid);
  setAttendanceUnitMenu(grid, datatable);
  setAttendanceUnitFilters(container, grid, gridOptions);
});

function setAttendanceUnitActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `/system/attendanceunits/edit/${idElement}`,
      title: "Cadastro | Unidade de Atendimento",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        setAttendanceUnitItemMenu(modal);
        setAttendanceUnitItemModalButtons(modal, grid);
        intAttendanceUnitForm(modal);
      }
    });
  });
}

function setAttendanceUnitMenu(grid, datatable) {
  $(".btn-add-item").off().on("click", function () {
    App._loadPageModal({
      url: `/system/attendanceunits/create`,
      title: "Cadastro | Unidade de Atendimento",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        setAttendanceUnitItemMenu(modal);
        setAttendanceUnitItemModalButtons(modal, grid);
        intAttendanceUnitForm(modal);
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
      if (await destroyAttendanceUnits(ids.toArray())) {
        grid.DataTable().draw();
      }
    })

  });
}

async function setAttendanceUnitFilters(container, grid, gridOptions) {
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

function setAttendanceUnitItemMenu(modal) {

}

function setAttendanceUnitItemModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-attendanceunit");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeAttendanceUnitItem(form) : updateAttendanceUnitItem(form))) {
      grid.DataTable().draw();
    }
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-attendanceunit");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeAttendanceUnitItem(form) : updateAttendanceUnitItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storeAttendanceUnitItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.post(`attendanceunits/store`, getFormData(form, "values"))
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

async function updateAttendanceUnitItem(form) {
  App._loading(true);
  setFormValidationErrors(form);

  return await axios.put(`attendanceunits/update`, getFormData(form, "values"))
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

async function destroyAttendanceUnits(ids) {
  App._loading(true);
  return await axios.delete(`attendanceunits/destroy`, { data: { ids: ids } })
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

async function intAttendanceUnitForm(modal) {
  const form = modal.find("#form-attendanceunit");
  await App._address(form.find(".endereco"));
  await setAttendanceUnitManagersUsers(modal, false);
  await setAttendanceUnitPageEditor(modal);
  await setAttendanceUnitAttachments(modal);

}

async function setAttendanceUnitManagersUsers(modal, readonly) {
  const form = modal.find("#form-attendanceunit");
  const idTblAppendGridAttendanceUnitManagersUsers = "tbl-appendgrid-attendanceunits-managers-users";
  const tblAppendGridAttendanceUnitManagersUsers = $(`#${idTblAppendGridAttendanceUnitManagersUsers}`);

  if (tblAppendGridAttendanceUnitManagersUsers.trigger("appendGrid").data("appendGrid") != undefined) {
    tblAppendGridAttendanceUnitManagersUsers.find("tbody tr").each(function (item) {
      tblAppendGridAttendanceUnitManagersUsers.trigger("appendGrid").data("appendGrid").removeRow($(item).data("unique-index"));
    });
    return false;
  }

  let appendGridRoutineActionsData = null
  if (form.find("#stored_managers_users").val().trim()) {
    appendGridRoutineActionsData = JSON.parse(form.find("#stored_managers_users").val().trim()).map(function (item, index) {
      item.user_cpf = item.user_cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4")
      return item;
    });
  }

  const appendGridRoutineActions = await new AppendGrid({
    element: idTblAppendGridAttendanceUnitManagersUsers,
    uiFramework: "bootstrap5",
    iconParams: {
      icons: {
        append: "ph-plus",
      }
    },
    initRows: 0,
    initData: appendGridRoutineActionsData,
    hideRowNumColumn: true,
    i18n: {
      append: "Adicionar novo usuário",
      remove: "Remover",
      rowEmpty: "Não há usuários vinculados.",
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
      return `${tblAppendGridAttendanceUnitManagersUsers.attr("name")}[${uniqueIndex}][${name}]`;
    },
    columns: [
      {
        name: "user_id",
        type: "hidden",
      },
      {
        name: "user_cpf",
        display: "CPF",
        type: "readonly",
        cellClass: "w-30 p-1",
        ctrlClass: (readonly == true ? "text-center" : ""),
        displayCss: {
          "width": "30%",
          "padding": "1px",
        },
      },
      {
        name: "user_name_show",
        display: "Nome",
        type: readonly == true ? "readonly" : "custom",
        cellClass: "w-60 p-1",
        displayCss: {
          "width": "60%",
          "padding": "1px",
        },
        customBuilder: function (parent, idPrefix, name, uniqueIndex) {
          $(parent).append(`
            <input type="text" id="${tblAppendGridAttendanceUnitManagersUsers.attr("name")}[${uniqueIndex}][${name}]" name="${tblAppendGridAttendanceUnitManagersUsers.attr("name")}[${uniqueIndex}][${name}]" class="form-control fs-sm px-1 py-0">
          `);
        },
        customSetter: function (idPrefix, name, uniqueIndex, value) {
          $(`input[name="${tblAppendGridAttendanceUnitManagersUsers.attr("name")}[${uniqueIndex}][${name}]"`).val(value);
          $(`input[name="${tblAppendGridAttendanceUnitManagersUsers.attr("name")}[${uniqueIndex}][${name}]"`).closest("tr").find(".inputpicker-input").first().val(value);
        }
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
          const appendGridRoutineActions = document.getElementById(idTblAppendGridAttendanceUnitManagersUsers).appendGrid;
          const botaoExcluir = $('<button/>', {
            type: "button",
            class: "btn btn-danger p-1",
            html: `<i class="ph-trash fs-sm"></i>`,
            "data-toggle": "tooltip",
            "data-placement": "top",
            "data-original-title": "Excluir"
          });
          botaoExcluir.on("click", function () {
            const inputId = $(parent).closest("tr").find(`input[name="${tblAppendGridAttendanceUnitManagersUsers.attr("name")}\\[${uniqueIndex}\\]\\[id\\]"]`);
            if (inputId.val() == '') {
              appendGridRoutineActions.removeRow($(parent).closest("tr").index());
              return;
            }
            App._confirmDelete(function () {
              const appendGridRoutineActions = document.getElementById(idTblAppendGridAttendanceUnitManagersUsers).appendGrid;
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
      setAttendanceUnitManagersUsersSearch(addedRowIndex, tblAppendGridAttendanceUnitManagersUsers);
    },
  });

  document.getElementById(idTblAppendGridAttendanceUnitManagersUsers).appendGrid = appendGridRoutineActions;

  form.find(".btn-add-action").on("click", async function () {
    await appendGridRoutineActions.appendRow([{
      id: App.randomstring(),
    }]);
  });
}

function setAttendanceUnitManagersUsersSearch(indexRow, tblAppendGridAttendanceUnitManagersUsers) {
  const inputUser = tblAppendGridAttendanceUnitManagersUsers.find(`input[name="${tblAppendGridAttendanceUnitManagersUsers.attr("name")}\\[${indexRow}\\]\\[user_name_show\\]"]`);
  inputUser.inputpicker({
    autoload: false,
    url: `/api/system/fetch/items/User`,
    urlDelay: 0.4,
    headShow: true,
    urlParam: {
      path: "System",
      _datatable: true,
      api_token: $(`meta[name="user-token"]`).attr("content"),
      length: 5,
      order: {
        column: 1,
        dir: "ASC",
      },
      filter: {
        name: [
          {
            operator: "contains",
            value: "{q}"
          }
        ],
      },
    },
    fields: [
      {
        name: 'login', text: 'CPF', width: "20%", format: function (text) {
          return text.replace(/\D/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
        }
      },
      { name: 'name_show', text: 'Nome/Nome Social', format: function (text) { return text.toUpperCase() } },
    ],
    fieldText: 'name_show',
    fieldValue: 'name_show',
  });

  inputUser.change(async function () {
    const _t = $(this);
    App._loading(true);
    await fillAttendanceUnitManagerUser(indexRow, tblAppendGridAttendanceUnitManagersUsers, _t.data('response'));
    App._loading(false);
  });

}

async function fillAttendanceUnitManagerUser(indexRow, tblAppendGridAttendanceUnitManagersUsers, data) {
  tblAppendGridAttendanceUnitManagersUsers.find(`input[name="${tblAppendGridAttendanceUnitManagersUsers.attr("name")}\\[${indexRow}\\]\\[user_id\\]"]`).val(data.id);
  tblAppendGridAttendanceUnitManagersUsers.find(`input[name="${tblAppendGridAttendanceUnitManagersUsers.attr("name")}\\[${indexRow}\\]\\[user_cpf\\]"]`).val(data.login.replace(/\D/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4"));
}

function setAttendanceUnitPageEditor(modal) {
  const form = modal.find("#form-attendanceunit");
  form.find("#web_page").summernote({
    toolbar: [
      ['style', ['bold', 'italic', 'underline', 'clear']],
      ['font', ['strikethrough',]],
      ['color', ['color']],
      ['para', ['ul', 'ol',]],
    ],
    disableResizeEditor: true,
    lang: 'pt-BR',
    height: 300,
  });
}

function setAttendanceUnitAttachments(modal) {
  const form = modal.find("#form-attendanceunit");
  let idAttendanceUnit = form.find(`input[name="id"]`).val();
  form.find("#attachments").fileManager({
    readonly: false,
    crud_id: idAttendanceUnit,
    url: `/system/attendanceunits/attachments`,
    auto_upload: (idAttendanceUnit != ''),
    form_parent: form,
    prefix_input_name: `attachments`,
    // form_attributes: $("#atributos-anexo"),
    max_size: 16777216, //16mb
    accept: [
      ".xlsx", ".xls", ".doc", ".docx", ".ppt", ".pptx",
      ".png", ".jpg", ".jpeg",
      ".zip", ".7zip", ".rar",
      ".txt", ".csv", ".xml", ".pdf",
      ".odt", ".ods", ".odp", ".odg",
      ".zip",
    ],
  });
}