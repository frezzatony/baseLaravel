personalInformationBtn = document.getElementById("personal-information-button")
personalInformationBtn.addEventListener('click', storePersonalInfomation)
configBtn = document.getElementById("personal-config-button")
configBtn.addEventListener('click', storeConfigInformation)


async function storePersonalInfomation() {
  App._loading(true);
  const form = $("#personal-information-form")
  setFormValidationErrors(form);
  return await axios.patch(`/system/users/modify`, getFormData(form, "values"))
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      App._loading(false);
      data.status == 'success'
      return data.status
    })
    .catch(function (error) {
      App._loading(false);
      console.log(error)
      if (error.response.data.errors != undefined) {
        App._showAppMessage('form_error', 'error');
        setFormValidationErrors(form, error.response.data.errors);
      }
      return false;
    });
}


async function storeConfigInformation() {
  App._loading(true);
  const form = $("#update-user-config-form")
  setFormValidationErrors(form);
  return await axios.patch(`/system/users/update-config`, getFormData(form, "values"))
    .then(async function ({ data }) {
      App._showMessage(data.message, data.status);
      App._loading(false);
      data.status == 'success'
      return data.status
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


function updateImage() {
  const form = document.getElementById("#imageForm")
  const dataForm = getFormData(form, values)
}



