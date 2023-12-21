$(document).ready(async function () {

});


async function editCustomerServiceAssisting(book) {
  App._loadPageModal({
    url: `/system/customerservices/presential/edit/${book.queue_id}/${book.customer_service_id}`,
    title: "Atendimento Presencial",
    size: "lg",
    backdrop_static: true,
    done: function (modal) {
      initCustomerServicePresential(modal, book);
    }
  });
}

async function initCustomerServicePresential(modal) {

  const form = modal.find("#form-customer-service-presential");

  setCustomerServicePresentialCustomerHandlingTime(modal);
  setCustomerServicePresentialAttachments(modal);
  setCustomerServicePresentialModalButtons(modal);
  App._address(form.find(".address"));
  App._contacts(form.find(".contacts"));
  form.find("#person_cpf").mask("000.000.000-00").on("blur", function () {
    if ($(this).val().length == 14 && $(this).val() != $(this).data("last-value")) {
      $(this).data("last-value", $(this).val());
      fillPerson(form);
    }
  });
}

function setCustomerServicePresentialCustomerHandlingTime(modal) {
  const form = modal.find("#form-customer-service-presential");
  const inputStatus = form.find("#status");
  const inputCreatedAt = form.find("#created_at");
  const inputUpdatedAt = form.find("#updated_at");
  const inputShowHandlingTime = form.find("#show_handling_time");

  if (inputStatus.val() == 'completed') {
    inputShowHandlingTime.data("time", inputCreatedAt.val()).val(inputUpdatedAt.val());
    inputShowHandlingTime.timeElapsed({
      currentTime: new Date(inputUpdatedAt.val()),
      full: true
    });
  }

  if (inputStatus.val() == 'assisting') {
    clearInterval(inputShowHandlingTime.data("wating-time"));
    inputShowHandlingTime.data("time", inputCreatedAt.val());
    inputShowHandlingTime.data("wating-time", setInterval(function () {
      inputShowHandlingTime.timeElapsed({
        currentTime: new Date,
        full: true
      });
    }, 1000));
  }

}

function setCustomerServicePresentialAttachments(modal) {
  const form = modal.find("#form-customer-service-presential");
  let customerServiceId = form.find(`input[name="id"]`).val();
  form.find("#attachments").fileManager({
    readonly: false,
    crud_id: customerServiceId,
    url: `/system/customerservices/presential/attachments`,
    url_params: {
      queue_id: form.find(`input[name="queue_id"]`).val(),
    },
    auto_upload: (customerServiceId != ''),
    form_parent: form,
    prefix_input_name: `attachments`,
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

function setCustomerServicePresentialModalButtons(modal) {
  const form = modal.find("#form-customer-service-presential");

  modal.find(".btn-save-crud").off().on("click", async function () {
    const saveResponse = await (updadeCustomerServicePresentialItem(form));
    if (saveResponse.timeline_activity_html != undefined) {
      const timeline = form.find(".timeline-container");
      timeline.prepend(saveResponse.timeline_activity_html)
    }
  });

  modal.find(".btn-save-conclude-crud").off().on("click", async function () {
    Swal.fire({
      buttonsStyling: false,
      customClass: {
        confirmButton: 'btn btn-success fs-sm px-1 py-0 ',
        cancelButton: 'btn btn-light fs-sm px-1 py-0',
      },
      title: 'Confirmar encerramento',
      html: 'O atendimento será encerrado e poderá ser classificado. <br><strong>Você confirma esta ação?</strong>',
      icon: 'question',
      showCancelButton: true,
      cancelButtonText: 'Retornar para edição do atendimento',
      confirmButtonText: '<i class="ph-check-square-offset fs-sm align-middle"></i> Confirmar e Encerrar',
      stopKeydownPropagation: true,
      keydownListenerCapture: true,
      focusCancel: true,
      allowEnterKey: true,
      allowEscapeKey: true,
    }).then(async function (result) {
      if (result.isConfirmed) {
        form.find("#conclude").val(1);
        if (await (updadeCustomerServicePresentialItem(form))) {
          const customerService = {
            queue_id: form.find("#queue_id").val(),
            customer_service_id: form.find("#id").val()
          };
          await modal.modal("hide");
          editRateCustomerServiceAssisting(customerService);
        }
      }
    });
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-customer-service-presential");
    const saveResponse = await (updadeCustomerServicePresentialRate(form));
    if (saveResponse.status == 'success') {
      modal.modal("hide");
    }
  });
}

async function updadeCustomerServicePresentialItem(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const formData = getFormData(form, 'formdata');
  formData.append('_method', 'put');

  return await axios.post(`/system/customerservices/presential/update`, formData)
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      if (form.find("#person_id").length) {
        form.find("#person_id").val(data.person_id);
      }
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

async function fillPerson(form) {
  const inputsPerson = {
    id: form.find("#person_id"),
    cpf: form.find("#person_cpf"),
    name: form.find("#person_name"),
    social_name: form.find("#person_social_name"),
  };
  inputsPerson.id.val('');
  App._loadingElements(true, inputsPerson);
  const persons = await App._fetchItems({
    service: "System/Person/Person",
    label: 'pessoas',
    params: {
      format: 0,
      filter: {
        cpf_cnpj: inputsPerson.cpf.val().replace(/\D/g, ''),
      }
    }
  });

  if (persons.length <= 0) {
    App._loadingElements(false, inputsPerson);
    App._showMessage("O CPF informado não consta na base de dados. Os dados da pessoa interessada serão incluídos ao salvar. ;)", "info");
    return false;
  }

  const person = persons.pop();
  inputsPerson.id.val(person.id);
  inputsPerson.name.val(person.name ? person.name.toUpperCase() : '');
  inputsPerson.social_name.val(person.social_name ? person.social_name.toUpperCase() : '');
  form.find(`[name^="person_address_"]`).each(function () {
    const _t = $(this);
    _t.val(person.address[_t.attr("id").replace("address_", '')]).trigger("input");
  });
  form.find(".stored-contacts").val(JSON.stringify(person.contacts));
  form.find(".contacts").trigger("reload");
  App._loadingElements(false, inputsPerson);
}