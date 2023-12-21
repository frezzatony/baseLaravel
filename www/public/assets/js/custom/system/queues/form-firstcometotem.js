$(document).ready(async function () {

});

async function initQueuesFormFirstComeTotem(modal, grid) {
  const idTblAppendGridQueueFirstComeTotemPriorities = "tbl-appendgrid-queues-call-orders";
  let timeout = setInterval(async function () {
    if (document.getElementById(idTblAppendGridQueueFirstComeTotemPriorities) != null) {
      await setQueueFormWeekdays(modal);
      await setQueuesCallOrders(modal);
      await setQueuesAttendants(modal);
      await setQueuesManagers(modal);
      await setQueuesMatters(modal);
      await setQueuesCalendar(modal);
      await setQueuesFirstComeTotemModalButtons(modal, grid);
      clearInterval(timeout);
    }
  }, 200);
}

function setQueuesFirstComeTotemModalButtons(modal, grid) {
  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-queue");
    await (form.find(`input[name="id"]`).val() == '' ? storeQueuesFirstComeTotemItem(form) : updateQueuesFirstComeTotemItem(form));
    grid.DataTable().draw();
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-queue");
    if (await (form.find(`input[name="id"]`).val() == '' ? storeQueuesFirstComeTotemItem(form) : updateQueuesFirstComeTotemItem(form))) {
      grid.DataTable().draw();
      modal.modal("hide");
    }
  });
}

async function storeQueuesFirstComeTotemItem(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const getValues = function (values) {
    let formValues = new FormData();
    Object.entries(values).forEach(function (item) {
      if (typeof item[1] == "object") {
        Object.values(item[1]).map(function (value) {
          formValues.append(item[0], value)
        });
      }
      if (typeof item[1] != "object") {
        formValues.append(item[0], item[1]);
      }
    });
    return formValues;
  }
  return await axios.post(`queues/{queueType.toLowerCase()/store`, getValues(getFormData(form, 'values')))
    .then(async function ({ data }) {
      console.log(data)
      App._showMessage(data.message, data.status);
      if (data.status == 'success') {
        form.find(`input[name="id"]`).val(data.id);
        form.find(`input#show_item_id`).val(data.id);
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

async function updateQueuesFirstComeTotemItem(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const getValues = function (values) {
    let formValues = new FormData();
    Object.entries(values).forEach(function (item) {
      if (typeof item[1] == "object") {
        Object.values(item[1]).map(function (value) {
          formValues.append(item[0], value)
        });
      }
      if (typeof item[1] != "object") {
        formValues.append(item[0], item[1]);
      }
    });
    formValues.append('_method', 'put');
    return formValues;
  }
  return await axios.post(`queues/firstcometotem/update`, getValues(getFormData(form, 'values')))
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