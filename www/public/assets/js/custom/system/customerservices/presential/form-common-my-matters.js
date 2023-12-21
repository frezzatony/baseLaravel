
async function editCustomerServiceMyMatters() {
  App._loadPageModal({
    url: `/system/customerservices/mymatters/edit/${$("#queue").val()}`,
    title: "Editar Assuntos de Atendimento",
    size: "sm",
    backdrop_static: true,
    done: function (modal) {
      initCustomerServiceMyMatters(modal);
      setCustomerServiceMyMattersModalButtons(modal)
    }
  });
}

async function initCustomerServiceMyMatters(modal) {
  const form = modal.find("#form-my-matters");
  const queueUserMatters = form.find("#queue_user_matters").val().trim() != "" ? JSON.parse(form.find("#queue_user_matters").val().trim()) : null;
  const userMatters = form.find("#user_matters").val().trim() != "" ? JSON.parse(form.find("#user_matters").val().trim()) : null;

  if (!queueUserMatters) {
    return false;
  }

  let allMatters = $("<li></li>", {
    class: "folder fs-sm expanded",
    text: "TODOS"
  });
  let mattersNodes = $("<ul></ul>");

  await queueUserMatters.forEach(function (matter) {
    let matterNode = $("<li></li>", {
      class: "fs-sm",
      text: `${matter.matter_description.toUpperCase()}`,
      "data-value": matter.matter_id,
    });

    if (!userMatters || userMatters.includes(matter.matter_id.toString())) {
      matterNode.addClass("selected");
    }
    mattersNodes.append(matterNode);
  });

  allMatters.append(mattersNodes);
  const mattersDiv = form.find(".matters");
  mattersDiv.find(".tree-checkbox-hierarchical").attr("name", `matters`);
  mattersDiv.find(".tree-checkbox-hierarchical").find("ul").first().append(allMatters);
  mattersDiv.find(".tree-checkbox-hierarchical").fancytree({
    checkbox: true,
    selectMode: 3
  });
  mattersDiv.removeClass("d-none");
}


function setCustomerServiceMyMattersModalButtons(modal) {
  const form = modal.find("#form-my-matters");

  modal.find(".btn-save-crud").off().on("click", async function () {
    const saveResponse = await (updateCustomerServiceMyMatters(form));
    if (saveResponse.status == 'success') {
      fillCustomerServiceBook();
    }
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const saveResponse = await (updateCustomerServiceMyMatters(form));
    if (saveResponse.status == 'success') {
      modal.modal("hide");
      fillCustomerServiceBook();
      fetchCustomerServiceDailyTickets();
    }
  });
}

async function updateCustomerServiceMyMatters(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const formData = getFormData(form, 'formdata');
  formData.append('_method', 'put');

  return await axios.post(`/system/customerservices/mymatters/update`, formData)
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      App._loading(false);
      return data;
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