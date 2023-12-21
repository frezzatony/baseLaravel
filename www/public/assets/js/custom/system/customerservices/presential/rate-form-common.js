$(document).ready(async function () {

});

async function editRateCustomerServiceAssisting(customerService) {
  App._loadPageModal({
    url: `/system/customerservices/presential/rate/edit/${customerService.queue_id}/${customerService.customer_service_id}`,
    title: "Classificação de Atendimento Presencial",
    size: "lg",
    backdrop_static: true,
    done: function (modal) {
      initRateCustomerServicePresentialAssisting(modal);
    }
  });
}

async function initRateCustomerServicePresentialAssisting(modal) {
  const formRate = modal.find("#form-rate-customer-service-presential");
  formRate.find("#tags").tokenfield({
    delimiter: [',', '.'],
  });

  if (window.webkitSpeechRecognition != undefined) {
    formRate.find("#problem_description,#resolution_description").each(function () {
      let _t = $(this);
      var recognition = new webkitSpeechRecognition();
      recognition.lang = 'pt-BR';
      recognition.onresult = function (event) {
        var transcript = event.results[0][0].transcript;
        _t.val(`${_t.val()} ${transcript}`);
        _t.closest(".position-relative").find(".btn-voice").removeClass("text-primary");
        _t.removeClass("bg-primary bg-opacity-10");
      };

      _t.closest(".position-relative").find(".btn-voice").on("click", function () {
        if ($(this).hasClass("text-primary")) {
          recognition.stop();
          _t.closest(".position-relative").find(".btn-voice").removeClass("text-primary");
          _t.removeClass("bg-primary bg-opacity-10");
          return false;
        }

        _t.addClass("bg-primary bg-opacity-10");
        $(this).addClass("text-primary");
        recognition.start();
      })
    });
  }
  if (window.webkitSpeechRecognition == undefined) {
    formRate.find(".btn-voice").remove();
  }

  modal.find(".btn-save-crud").off().on("click", async function () {
    const form = modal.find("#form-rate-customer-service-presential");
    const saveResponse = await (updadeCustomerServicePresentialRate(form));
  });

  modal.find(".btn-save-close-crud").off().on("click", async function () {
    const form = modal.find("#form-rate-customer-service-presential");
    const saveResponse = await (updadeCustomerServicePresentialRate(form));
    if (saveResponse.status == 'success') {
      modal.modal("hide");
    }
  });
}

async function updadeCustomerServicePresentialRate(form) {
  App._loading(true);
  setFormValidationErrors(form);
  const formData = getFormData(form, 'formdata');
  formData.append('_method', 'put');

  return await axios.post(`/system/customerservices/presential/update-rate`, formData)
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
