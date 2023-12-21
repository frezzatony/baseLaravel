
async function setQueuesCallOrders(modal) {
  const form = modal.find("#form-queue");
  const idTblAppendGridQueueCallOrders = "tbl-appendgrid-queues-call-orders";
  const tblAppendGridQueueCallOrders = form.find(`#${idTblAppendGridQueueCallOrders}`);

  const appendGridQueueInitData = form.find("#stored_priorities").val().trim() != ""
    ? JSON.parse(form.find("#stored_priorities").val().trim()).map(function (priority) {
      priority.uuid = priority.uuid == undefined ? uuidv4() : priority.uuid;
      return priority;
    })
    : null;

  const appendGridQueue = await new AppendGrid({
    element: idTblAppendGridQueueCallOrders,
    uiFramework: "bootstrap5",
    iconParams: {
      icons: {
        append: "ph-plus",
      }
    },
    initRows: 0,
    initData: appendGridQueueInitData,
    hideRowNumColumn: true,
    i18n: {
      append: "Adicionar nova ordem de chamada",
      remove: "Remover",
      rowEmpty: "Não há ordens de chamada vinculadas.",
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
      return `${tblAppendGridQueueCallOrders.data("name")}[${uniqueIndex}][${name}]`;
    },
    columns: [
      {
        name: "uuid",
        type: "hidden",
      },
      {
        name: "description",
        display: "Descrição",
        type: "text",
        cellClass: "w-70 p-1",
        ctrlClass: "",
        displayCss: {
          "width": "70%",
          "padding": "1px",
        },
      },
      {
        name: "weight",
        display: "Peso",
        type: "number",
        cellClass: "w-10 p-1",
        displayCss: {
          "width": "10%",
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
          const appendGridQueue = document.getElementById(idTblAppendGridQueueCallOrders).appendGrid;
          const botaoExcluir = $('<button/>', {
            type: "button",
            class: "btn btn-danger p-1",
            html: `<i class="ph-trash fs-sm"></i>`,
            "data-toggle": "tooltip",
            "data-placement": "top",
            "data-original-title": "Excluir"
          });
          botaoExcluir.on("click", function () {
            const inputId = $(parent).closest("tr").find(`input[name="${tblAppendGridQueueCallOrders.data("name")}\\[${uniqueIndex}\\]\\[id\\]"]`);
            if (inputId.val() == '') {
              appendGridQueue.removeRow($(parent).closest("tr").index());
              return;
            }
            App._confirmDelete(function () {
              const appendGridQueue = document.getElementById(idTblAppendGridQueueCallOrders).appendGrid;
              appendGridQueue.removeRow($(parent).closest("tr").index());
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

  document.getElementById(idTblAppendGridQueueCallOrders).appendGrid = appendGridQueue;
  tblAppendGridQueueCallOrders.find("tbody").css("max-height", `${tblAppendGridQueueCallOrders.closest(".vh-50").height() * 0.68}px`);
  form.find(".btn-add-priority").on("click", async function () {
    await appendGridQueue.appendRow([{
      uuid: uuidv4(),
    }]);
  });
}
