async function setQueuesManagers(modal) {
  const form = modal.find("#form-queue");
  const idTblAppendGridQueueManagers = "tbl-appendgrid-queues-managers";
  const tblAppendGridQueueManagers = form.find(`#${idTblAppendGridQueueManagers}`);
  const storedValues = form.find("#stored_managers").val().trim() ? JSON.parse(form.find("#stored_managers").val().trim()) : null;

  const appendGridQueueManagers = await new AppendGrid({
    element: idTblAppendGridQueueManagers,
    uiFramework: "bootstrap5",
    iconParams: {
      icons: {
        append: "ph-plus",
      }
    },
    initRows: 0,
    initData: form.find("#stored_managers").val().trim() ? JSON.parse(form.find("#stored_managers").val().trim()) : null,
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
      return `${tblAppendGridQueueManagers.data("name")}[${uniqueIndex}][${name}]`;
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
              <input type="text" id="${tblAppendGridQueueManagers.data("name")}[${uniqueIndex}][${name}]" name="${tblAppendGridQueueManagers.data("name")}[${uniqueIndex}][${name}]" class="form-control px-1 py-0 fs-sm" readonly>
            `);
        },
        customSetter: function (idPrefix, name, uniqueIndex, value) {
          $(`#${tblAppendGridQueueManagers.data("name")}\\[${uniqueIndex}\\]\\[${name}\\]`).val(value != undefined ? value.replace(/\D/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4") : '');
        },
        customGetter: function (idPrefix, name, uniqueIndex, value) {
          return $(`#${tblAppendGridQueueManagers.data("name")}\\[${uniqueIndex}\\]\\[${name}\\]`).val();
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
              <input type="text" id="${tblAppendGridQueueManagers.data("name")}[${uniqueIndex}][${name}]" name="${tblAppendGridQueueManagers.data("name")}[${uniqueIndex}][${name}]" class="form-control px-1 py-0 fs-sm">
            `);
        },
        customSetter: function (idPrefix, name, uniqueIndex, value) {
          $(`#${tblAppendGridQueueManagers.data("name")}\\[${uniqueIndex}\\]\\[${name}\\]`).val(value != undefined ? value.toUpperCase() : '');
          setQueuesManagersSearch(modal, uniqueIndex, tblAppendGridQueueManagers, storedValues);
        },
        customGetter: function (idPrefix, name, uniqueIndex, value) {
          return $(`#${tblAppendGridQueueManagers.data("name")}\\[${uniqueIndex}\\]\\[${name}\\]`).val();
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
          const appendGridQueueManagers = document.getElementById(idTblAppendGridQueueManagers).appendGrid;
          const botaoExcluir = $('<button/>', {
            type: "button",
            class: "btn btn-danger p-1",
            html: `<i class="ph-trash fs-sm"></i>`,
            "data-toggle": "tooltip",
            "data-placement": "top",
            "data-original-title": "Excluir"
          });
          botaoExcluir.on("click", function () {
            const inputId = $(parent).closest("tr").find(`input[name="${tblAppendGridQueueManagers.data("name")}\\[${uniqueIndex}\\]\\[user_id\\]"]`);
            if (inputId.val() == '') {
              appendGridQueueManagers.removeRow($(parent).closest("tr").index());
              return;
            }
            App._confirmDelete(function () {
              const appendGridQueueManagers = document.getElementById(idTblAppendGridQueueManagers).appendGrid;
              appendGridQueueManagers.removeRow($(parent).closest("tr").index());
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
    },
  });

  document.getElementById(idTblAppendGridQueueManagers).appendGrid = appendGridQueueManagers;
  form.find(".btn-add-manager").on("click", async function () {
    await appendGridQueueManagers.appendRow([{}]);
  });
}

function setQueuesManagersSearch(modal, indexRow, tblAppendGridQueueManagers, storedValues) {
  const inputUser = tblAppendGridQueueManagers.find(`input[name="${tblAppendGridQueueManagers.data("name")}\\[${indexRow}\\]\\[user_name_show\\]"]`);
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
    await fillQueuesManager(indexRow, tblAppendGridQueueManagers, _t.data('response'));
    App._loading(false);
  });
}

async function fillQueuesManager(indexRow, tblAppendGridQueueManagers, data) {
  tblAppendGridQueueManagers.find(`input[name="${tblAppendGridQueueManagers.data("name")}\\[${indexRow}\\]\\[user_id\\]"]`).val(data.id);
  tblAppendGridQueueManagers.find(`input[name="${tblAppendGridQueueManagers.data("name")}\\[${indexRow}\\]\\[user_cpf\\]"]`).val(data.login.replace(/\D/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4"));
}