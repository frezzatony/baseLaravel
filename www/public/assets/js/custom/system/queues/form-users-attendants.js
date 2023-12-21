
async function setQueuesAttendants(modal) {
  const form = modal.find("#form-queue");
  const idTblAppendGridQueueAttendants = "tbl-appendgrid-queues-attendants";
  const tblAppendGridQueueAttendants = form.find(`#${idTblAppendGridQueueAttendants}`);
  const storedValues = form.find("#stored_attendants").val().trim() ? JSON.parse(form.find("#stored_attendants").val().trim()) : null;

  const appendGridQueueAttendants = await new AppendGrid({
    element: idTblAppendGridQueueAttendants,
    uiFramework: "bootstrap5",
    iconParams: {
      icons: {
        append: "ph-plus",
      }
    },
    initRows: 0,
    initData: storedValues,
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
      return `${tblAppendGridQueueAttendants.data("name")}[${uniqueIndex}][${name}]`;
    },
    columns: [
      {
        name: "user_id",
        type: "hidden",
      },
      {
        name: "user_cpf",
        display: "CPF",
        type: "custom",
        cellClass: "w-30 p-1",
        ctrlClass: "",
        displayCss: {
          "width": "30%",
          "padding": "1px",
        },
        customBuilder: function (parent, idPrefix, name, uniqueIndex) {
          $(parent).append(`
              <input type="text" id="${tblAppendGridQueueAttendants.data("name")}[${uniqueIndex}][${name}]" name="${tblAppendGridQueueAttendants.data("name")}[${uniqueIndex}][${name}]" class="form-control px-1 py-0 fs-sm" readonly>
            `);
        },
        customSetter: function (idPrefix, name, uniqueIndex, value) {
          $(`#${tblAppendGridQueueAttendants.data("name")}\\[${uniqueIndex}\\]\\[${name}\\]`).val(value != undefined ? value.replace(/\D/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4") : '');
        },
        customGetter: function (idPrefix, name, uniqueIndex, value) {
          return $(`#${tblAppendGridQueueAttendants.data("name")}\\[${uniqueIndex}\\]\\[${name}\\]`).val();
        },
      },
      {
        name: "user_name_show",
        display: "Nome",
        type: "custom",
        cellClass: "w-60 p-1",
        displayCss: {
          "width": "60%",
          "padding": "1px",
        },
        customBuilder: function (parent, idPrefix, name, uniqueIndex) {
          $(parent).append(`
              <input type="text" id="${tblAppendGridQueueAttendants.data("name")}[${uniqueIndex}][${name}]" name="${tblAppendGridQueueAttendants.data("name")}[${uniqueIndex}][${name}]" class="form-control px-1 py-0 fs-sm">
            `);
        },
        customSetter: function (idPrefix, name, uniqueIndex, value) {
          $(`#${tblAppendGridQueueAttendants.data("name")}\\[${uniqueIndex}\\]\\[${name}\\]`).val(value != undefined ? value.toUpperCase() : '');
          setQueuesAttendantsSearch(modal, uniqueIndex, tblAppendGridQueueAttendants, storedValues);
        },
        customGetter: function (idPrefix, name, uniqueIndex, value) {
          return $(`#${tblAppendGridQueueAttendants.data("name")}\\[${uniqueIndex}\\]\\[${name}\\]`).val();
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
          const appendGridQueueAttendants = document.getElementById(idTblAppendGridQueueAttendants).appendGrid;
          const botaoExcluir = $('<button/>', {
            type: "button",
            class: "btn btn-danger p-1",
            html: `<i class="ph-trash fs-sm"></i>`,
            "data-toggle": "tooltip",
            "data-placement": "top",
            "data-original-title": "Excluir"
          });
          botaoExcluir.on("click", function () {
            const inputId = $(parent).closest("tr").find(`input[name="${tblAppendGridQueueAttendants.data("name")}\\[${uniqueIndex}\\]\\[user_id\\]"]`);
            if (inputId.val() == '') {
              appendGridQueueAttendants.removeRow($(parent).closest("tr").index());
              return;
            }
            App._confirmDelete(function () {
              const appendGridQueueAttendants = document.getElementById(idTblAppendGridQueueAttendants).appendGrid;
              appendGridQueueAttendants.removeRow($(parent).closest("tr").index());
            });
          });
          $(parent).append(botaoExcluir);
        },
        customGetter: function () {
          return null;
        }
      },
    ],
    afterRowAppended: function (table, parentRowIndex, addedRowIndex) {
      if (form.find(".list-group-item-attendant").not(".label-attendant-template").length) {
        form.find(".list-group-item-attendant").data("redraw", true);
        form.find(".list-group-item-attendant").find(`input[type="radio"]:checked`).trigger("change");
      }
    },
    afterRowRemoved: function () {
      if (form.find(".list-group-item-attendant").not(".label-attendant-template").length) {
        form.find(".list-group-item-attendant").data("redraw", true);
        form.find(".list-group-item-attendant").find(`input[type="radio"]:checked`).trigger("change");
      }
    },
  });

  document.getElementById(idTblAppendGridQueueAttendants).appendGrid = appendGridQueueAttendants;
  form.find(".btn-add-attendant").on("click", async function () {
    await appendGridQueueAttendants.appendRow([{}]);
  });
}

function setQueuesAttendantsSearch(modal, indexRow, tblAppendGridQueueAttendants, storedValues) {
  const form = modal.find("#form-queue");
  const inputUser = tblAppendGridQueueAttendants.find(`input[name="${tblAppendGridQueueAttendants.data("name")}\\[${indexRow}\\]\\[user_name_show\\]"]`);

  inputUser.off().inputpicker({
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
        name_login: [
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
    await fillQueuesAttendant(indexRow, tblAppendGridQueueAttendants, _t.data('response'));
    if (form.find(".list-group-item-attendant").not(".label-attendant-template").length) {
      form.find(".list-group-item-attendant").data("redraw", true);
      form.find(".list-group-item-attendant").find(`input[type="radio"]:checked`).trigger("change");
    }
    App._loading(false);
  });

}

async function fillQueuesAttendant(indexRow, tblAppendGridQueueAttendants, data) {
  tblAppendGridQueueAttendants.find(`input[name="${tblAppendGridQueueAttendants.data("name")}\\[${indexRow}\\]\\[user_id\\]"]`).val(data.id);
  tblAppendGridQueueAttendants.find(`input[name="${tblAppendGridQueueAttendants.data("name")}\\[${indexRow}\\]\\[user_cpf\\]"]`).val(data.login.replace(/\D/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4"));
}