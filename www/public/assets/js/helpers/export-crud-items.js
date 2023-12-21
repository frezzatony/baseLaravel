
async function exportCrudItems(form, tableId, service, title) {
  const createExportModal = (title) => {
    const modalTemplate = `<div id="modal-export-crud-itemns" class="modal fade" tabindex="-1" role="dialog"> <div class="modal-dialog modal-lg" role="dialog"> <div class="modal-content"> <div class="modal-header bg-light"> <h5 class="modal-title">Export ${title}</h5> <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> </div> <div class="modal-body" style="overflow-y: auto; height: 80vh;"></div> <div class="modal-footer"> <button type="button" class="btn btn-light waves-effect" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button> <button type="button" class="btn btn-secondary waves-effect btn-exportar"><i class="fa fa-print"></i> Export</button> </div> </div> </div> </div>`;
    $("body").append(modalTemplate);
  };

  const exportItemListTableColumnsReport = (tableItems) => {
    const $modalExportItemList = $("#modal-export-item-list");
    const $formProperties = $modalExportItemList.find("#form-export-item-list");
    const $columnInputs = $formProperties.find("#print\\[columns\\]");
    const $columnTable = $modalExportItemList.find(".column-table-report");
    let totalWidth = 0;

    tableItems.eq(0).find('th').each(function (index, column) {
      if ($columnInputs.val().split(',').includes(index.toString())) {
        totalWidth += parseFloat($(column).width());
      }
    });

    tableItems.eq(0).find('th').each(function (index, column) {
      if ($columnInputs.val().split(',').includes(index.toString())) {
        let $columnRow = $("<tr>", {
          "key-column": index,
          "data-resizable-column-id": random(16),
        });

        $columnRow.append($(`<td>`, {
          class: "border text-left font-12 p-2",
          text: $(column).text(),
          style: `
              width: 45%;
              text-overflow: ellipsis;
              white-space: nowrap; 
              overflow: hidden;
            `,
        }));

        $columnRow.append($(`<td width="15%">
              <input type="textbox" data-mask="99.99" class="form-control text-right m-0 width-percentage" style="font-size: 12px; min-height: 2.5em; height: 2.5em;" value="${parseFloat(parseFloat(parseFloat($(column).width()) / totalWidth) * 100).toFixed(2)}">
            </td>`,
          {
            class: "p-2",
          }
        ));

        $columnRow.append($(`<td width="20%">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input visible-column" id="export_item_list[column_visible][${index}]" checked="">
                <label class="custom-control-label" for="export_item_list[column_visible][${index}]"></label>
              </div>
            </td>`,
          {
            class: "p-2",
          }
        ));

        $columnRow.append($(`<td width="20%">
              <button type="button" class="btn btn-icon waves-effect btn-light border btn-order"> <i class="fa fa-bars"></i> </button>
            </td>`,
          {
            class: "p-2",
          }
        ));

        $columnTable.find("tbody").append($columnRow);
      }
    });

    $.curCSS = function (element, prop, val) {
      return $(element).css(prop, val);
    };

    App.initCore();

    $modalExportItemList.on('shown.bs.modal', function () {
      $(this).find(".modal-body").scrollTop(0);
      $columnTable.find("tbody").sortable({
        axys: 'y',
        handle: 'button',
        cancel: '',
      });
    }).disableSelection();
  }

  let modalExport = $("#modal-export-crud-items");
  const table = $(tableId);
  if (modalExport.length == 0) {
    await createExportModal(form, service, title);
    modalExport = $("#modal-export-crud-items");
  }
  App._loading("show");
  let formData = {
    path: '',
  };
  formData.path = service.split("/");
  service = formData.path.pop();
  formData.path = formData.path.length ? formData.path.join("/") : '';

  $.ajax({
    url: `/relatorio/exportarlistaitens/fetchForm/${service}`,
    type: 'GET',
    dataType: 'html',
    data: formData,
  }).done(function (response) {
    modalExport.find(".modal-body").html(response);
    exportItemListTableColumnsReport(table);
    modalExport.modal("show");
    App._loading("hide");
  });
}