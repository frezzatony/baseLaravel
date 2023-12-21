$(document).ready(async function () {
  const container = $(".crud-notifications").eq(0);
  let gridOptions = {
    columnDefs: [{
      orderable: false,
      targets: [0]
    },
    { className: "select-checkbox", targets: [0] },
    { className: "pa1 f8", targets: [1, 2, 3, 4, 5] },
    ],
    order: [[4, "desc"]],
    initComplete: function (settings, json) {
      $(".btn-export-crud-search").on("click", function () {
        exportCrudItems("filters-crud", "dt-itens", 'notifications', 'Exportar lista de Módulos')
      });
    },
  };
  const grid = $("#dt-itens");
  const datatable = grid.DataTable(gridOptions)

  setNotificationsActions(grid);
  setNotificationsMenu(grid, datatable);
  setNotificationsFilters(container, grid, gridOptions);
});

function setNotificationsActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `notifications/view/${idElement}`,
      title: "Notificações | Visualizar Notificação",
      size: "md",
      done: function (modal) {
      }
    });

  });
}

function setNotificationsMenu(grid, datatable) {

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
      if (await destroyNotifications(ids.toArray())) {
        grid.DataTable().draw();
      };
    })
  });
}

async function setNotificationsFilters(container, grid, gridOptions) {
  await setFiltrosDinamicosPesquisa({
    "id_elemento": "filters-crud",
    "filtros": JSON.parse($(".filters-template").val()),
    "autoload": true,
    "values": JSON.parse(container.find(".filters-default-values").val()),
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
              path: "System/Notification",
              _datatable: true,
              api_token: $(`meta[name="user-token"]`).attr("content"),
            }
          }
        },
        dataSrc: function (response) {
          App._loading(false);
          response.data = response.data.map(function (item) {
            const itemCreatedAt = new Date(item.created_at);
            const itemReadAt = item.read_at != null ? new Date(item.read_at) : null;
            let values = [
              '',
              item.author.toUpperCase(),
              item.title.toUpperCase(),
              item.resume ? item.resume.toUpperCase() : '',
              `
                ${itemCreatedAt.toLocaleString("pt-BR", { day: "2-digit", month: "2-digit", year: "numeric" })} às 
                ${itemCreatedAt.toLocaleString("pt-BR", { hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false, })}h
              `,
              (
                itemReadAt == null
                  ? ''
                  : `
                    ${itemReadAt.toLocaleString("pt-BR", { day: "2-digit", month: "2-digit", year: "numeric" })} às 
                    ${itemReadAt.toLocaleString("pt-BR", { hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false, })}h
                  `
              )
            ]
            values["id"] = item.id;
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

async function destroyNotifications(ids) {
  App._loading(true);
  return await axios.delete(`notifications/destroy`, { data: { ids: ids } })
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
