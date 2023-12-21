function initPersonsForm(modal) {
  const form = modal.find("#form-person")

  form.find("#cpf_cnpj").mask("000.000.000-00");
  App._address(form.find(".address"));
  App._contacts(form.find(".contacts"));
  setPersonAttachments(modal);
}

function setPersonAttachments(form) {
  let idCrud = form.find(`input[name="id"]`).val();
  App._fileManager({
    element: form.find("#attachments"),
    readonly: false,
    crud_id: idCrud,
    url: `/system/persons/attachments`,
    auto_upload: (idCrud != ''),
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