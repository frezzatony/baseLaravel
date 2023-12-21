$(document).ready(async function () {
  const container = $(".crud-queues").eq(0);
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
        exportCrudItems("filters-crud", "dt-itens", 'queues', 'Exportar lista de Fila de Atendimentos')
      });
    },
  };
  const grid = $("#dt-itens");
  const datatable = grid.DataTable(gridOptions)

  setQueuesActions(grid);
  setQueuesMenu(grid, datatable);
  setQueuesFilters(container, grid, gridOptions);
});

function setQueuesActions(grid) {

  grid.find("tbody").on("dblclick", "tr", function (e) {
    let words = $(this).data("data")._prop.type.split('_');
    for (var i = 0; i < words.length; i++) {
      words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
    }
    let queueType = words.join('');
    const idElement = $(this).attr("id");
    App._loadPageModal({
      url: `queues/${queueType.toLowerCase()}/edit/${idElement}`,
      title: "Cadastro | Fila de Atendimento",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        window[`initQueuesForm${queueType}`](modal, grid);
      }
    });
  });
}

function setQueuesMenu(grid, datatable) {
  $(".btn-add-first-come-totem").off().on("click", function () {
    App._loadPageModal({
      url: `/system/queues/firstcometotem/create`,
      title: "Cadastro | Fila de Atendimento",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        initQueuesFormFirstComeTotem(modal, grid);
      },
    });
  });

  $(".btn-add-first-come-manual").off().on("click", function () {
    App._loadPageModal({
      url: `/system/queues/firstcomemanual/create`,
      title: "Cadastro | Fila de Atendimento",
      size: "lg",
      backdrop_static: true,
      done: function (modal) {
        initQueuesFormFirstComeManual(modal, grid);
      },
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
      if (await destroyQueues(ids.toArray())) {
        grid.DataTable().draw();
      };
    })
  });
}

async function setQueuesFilters(container, grid, gridOptions) {
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
            values["_prop"] = item._prop != undefined ? item._prop : null;
            values[0] = '';
            values[1] = item.id;
            values[2] = item.description;
            values[3] = item.type;
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

function setQueuesItemMenu(modal) {

}

async function destroyQueues(ids) {
  App._loading(true);
  return await axios.delete(`queues/destroy`, { data: { ids: ids } })
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