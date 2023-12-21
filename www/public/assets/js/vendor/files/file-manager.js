
function fileManager(container, options) {
  const plugin = this;
  plugin.dataTransfer = new DataTransfer();

  plugin.options = {
    image_types: [
      "gif", "avif",
      "apng", "png", "svg",
      "jpg", 'jpeg', "jfif", "pjpeg", "pjp",
      "bmp", "webp", "ico", "cur", "tif", "tiff",
    ],
    office_types: [
      "doc", "docx", "ppt", "pptx",
      "odt", "odp", "ods",
      "pdf", "csv",
    ],
    plain_types: [
      "txt",
    ],
    ...options
  };
  plugin.options.preview_types = plugin.options.image_types.concat(plugin.options.office_types).concat(plugin.options.plain_types);

  this.changeMethod = function (container, options) {
    return plugin[options.method](options);
  }

  this.init = async function () {
    const _t = this;
    _t.setTemplate();
    _t.setDropzone();
    await _t.setDataTableFilesList();
    await _t.setValues();
    _t.setSelectAll();
    _t.setButtonUpload();
    _t.setButtonRemove();
    _t.setButtonEdit();
    _t.setButtoDownload();
    _t.setButtonPreview();
    _t.setModalProperties();
    _t.setFormParentOnSubmit();
  }

  this.addFile = async function (file, upload) {
    const idAttachment = file.id != undefined ? file.id : this.random(16);
    const fileName = this.getFileName(file.name);
    const fileTime = file.updated_at ? new Date(file.updated_at) : '';
    let iconPreview = `/assets/images/filemanager/${file.extension != undefined ? file.extension : this.getFileExtension(file.name).toLowerCase()}.svg`;

    container.find(".dz-hidden-input").val('');
    if (
      (plugin.options.accept != undefined && !plugin.options.accept.includes(`.${fileName.split(".").pop().toLowerCase()}`)) && upload == true
    ) {
      App._showMessage(`O arquivo '${file.name}' não possui uma extensão válida para envio.`, "error");
      return;
    }

    if (
      (plugin.options.max_size != undefined && plugin.options.max_size < file.size) && upload == true
    ) {
      App._showMessage(`O tamanho do arquivo '${file.name}' ultrapassa ${this.getFileSize(plugin.options.max_size)} permitidos.`, "error");
      return;
    }

    attachment = {
      ...file,
      id: idAttachment,
      name: file.type != undefined ? fileName.substring(0, fileName.lastIndexOf(".")) : file.name,
      extension: file.extension != undefined ? file.extension : fileName.split(".").pop(),
      action: file.updated_at != undefined ? "update" : "create",
    };

    plugin.values.push(attachment);
    params = {
      action: "thumbnail",
      id: file.id,
      crud_id: plugin.options.crud_id,
    };

    if (plugin.options.url_params != undefined) {
      Object.entries(plugin.options.url_params).forEach(function (param) {
        params[param[0]] = param[1];
      });
    }
    if (file.updated_at != undefined && this.getFileIsImage(attachment)) {
      iconPreview = `${plugin.options.url}?action=thumbnail&${$.param(params)}`;
    }
    if (file.updated_at == undefined && this.getFileIsImage(attachment)) {
      iconPreview = ((window.URL || window.webkitURL).createObjectURL(file));
    }

    await plugin.dtFilesList.row
      .add([
        `
          <input type="checkbox" row-id="${idAttachment}" class="filemanager-attachment-select">
          <div class="progress d-none m-0 mt-1" style="height: 3px; position: absolute; left:0; bottom: 2px; z-index:1000;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 190px;"></div>
          </div>
        `,
        `   
          <div class="p-0 m-0 d-inline" title="${attachment.name}.${attachment.extension}">        
            <div class="d-inline-block">
                  <img class="filemanager-file-icon" src="${iconPreview}">
                </div>
            <div class="filemanager-file-name d-inline-block align-middle" style="text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden; width: 80%">${attachment.name}.${attachment.extension}</div>
          </div>

        `,
        `
          <span class="p-0 m-0 file-time">
            ${fileTime != '' ? `${fileTime.toLocaleDateString('pt-Br')} às ${fileTime.toLocaleTimeString('pt-Br', { timeStyle: 'short' }) + 'h'}` : ''}
          </span>
        `,
        `
        <span class="p-0 m-0 file-size">
          ${plugin.getFileSize(file.size)}
        </span>
        <div class="file-upload-running" style="display: none; position: absolute; top: 30%; right: 6px;">                                            
          <div class="spinner-border text-muted" style="width: 15px; height: 15px;"></div>
        </div>
        <div class="text-right text-success file-upload-success pt-1 m-0" style="display: none; position: absolute; right: 6px;">
          <i class="ph-check"></i>
        </div>
        <div class="text-right text-danger file-upload-error pt-1 m-0" style="display: none; position: absolute; right: 6px;">
          <i class="ph-x"></i>
        </div>
       `
      ])
      .draw();

    plugin.tableFilesList.find(".progress").each(function () {
      $(this).css({
        "width": $(this).closest("tr").width(),
      })
    });

    $(".filemanager-file-icon").off().on("error", function () {
      $(this).attr("src", `/assets/images/filemanager/blank.svg`);
    });

    if (file.updated_at == undefined) {
      let dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      const newInputId = `filemanager-attachment-${idAttachment}`;
      let newInput = $(`<input>`, {
        id: newInputId,
        name: `filemanager-attachment-${idAttachment}`,
        type: "file",
        class: "d-none",
      });
      newInput.data("change", true);
      container.append(newInput);
      document.getElementById(newInputId).files = dataTransfer.files;
    }

    const rowAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${idAttachment}"]`).closest("tr");
    rowAttachment.on("dblclick", function () {
      plugin.editFile($(this).find(`input[type="checkbox"]`).first().attr("row-id"));
    });

    if (plugin.options.auto_upload == true && (upload == undefined || upload != false)) {
      this.upload(idAttachment);
    }
  }

  this.editFile = function (idAttachment) {

    const values = plugin.values.filter(function (attachment) {
      return attachment.id == idAttachment;
    });

    if (values.length == 0) {
      return false;
    }

    plugin.options.form_attributes.trigger("reset");
    plugin.options.form_attributes.data("attachment-id", idAttachment);
    plugin.options.form_attributes.find(".attachment-file").empty();
    plugin.modalProperties.find(".btn-update-attachment").prop("disabled", false);
    Object.entries(values[0]).map(function (value) {
      plugin.options.form_attributes.find(`[name="${value[0]}"]`).val(value[1] && value[1].value != undefined ? value[1].value : value[1]).trigger("change");
      plugin.options.form_attributes.find(`.${value[0]}`).text(value[1] && value[1].value != undefined ? value[1].value : value[1]).trigger("change");
    });

    plugin.options.form_attributes.find(".attachment-file").append($("<label>", {
      for: `#filemanager-attachment-edit-${values[0].id}`,
      text: "Anexo",
      class: "form-label fw-semibold fs-sm m-0",
    }));

    let fileInput = $(`<input type="file" class="form-control px-1 pt1 pb1 fs-sm" />`);
    if (values[0].action == "create") {
      fileInput = container.find(`#filemanager-attachment-${values[0].id}`).clone();
    }

    fileInput.attr("id", `filemanager-attachment-edit-${values[0].id}`);
    fileInput.attr("accept", `.${values[0].extension}`);
    fileInput.removeClass("d-none").addClass("form-control");
    plugin.options.form_attributes.find(".attachment-file").append(fileInput);

    fileInput.on("change", function () {
      if ($(this).val().split('.').pop().toLowerCase() != values[0].extension.toLowerCase()) {
        $(this).val('');
        plugin.modalProperties.find(".btn-update-attachment").prop("disabled", true);
        $(this)
          .css({
            "border-color": "#f1556c",
          })
          .data("change", false);
        return;
      }

      plugin.modalProperties.find(".btn-update-attachment").prop("disabled", false);
      $(this)
        .css({
          "border-color": "#0acf97",
        })
        .data("change", true);
    });
    plugin.modalProperties.modal("show");
  }

  this.updateFile = function (idAttachment, attributes, newFileInput) {

    let storedAttachment = null;
    plugin.values = plugin.values.map(function (attachment) {
      if (attachment.id == idAttachment) {
        storedAttachment = attachment;
        if (plugin.options.auto_upload == true) {
          storedAttachment.action = "update";
        }
        Object.entries(attributes).map(function (attribute) {
          attachment[attribute[0]] = attribute[1];
        });
      }
      return attachment;
    });

    const rowAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${idAttachment}"]`).closest("tr");
    rowAttachment.find(".filemanager-file-name").text(`${attributes.name}.${storedAttachment.extension}`);

    if (newFileInput.data("change") == true) {
      container.find(`#filemanager-attachment-${idAttachment}`).remove();
      newFileInput.attr("id", `filemanager-attachment-${idAttachment}`);
      newFileInput.attr("name", `filemanager-attachment-${idAttachment}`);
      newFileInput.addClass("d-none");
      container.append(newFileInput);
      rowAttachment.find(".file-size").text(this.getFileSize(newFileInput[0].files[0].size));
    }

    if (plugin.options.auto_upload == true) {
      this.upload(idAttachment);
    }

    return true;
  }

  this.removeFile = function (idAttachment, showMessage) {
    plugin.values.map(function (attachment, index) {
      if (attachment.id == idAttachment) {
        let rowAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${idAttachment}"]`).closest("tr");
        plugin.values.splice(index, 1);
        plugin.dtFilesList.row(rowAttachment).remove().draw();
        $(`#filemanager-attachment-${idAttachment}`).remove();
        if (showMessage == undefined || showMessage == true) {
          App._showMessage(`Anexo '${attachment.name}.${attachment.extension}' removido.`, "success");
        }
      }
    });
  };

  this.upload = async function (idAttachment) {
    const formData = new FormData();
    formData.append("_token", options.form_parent.find(`input[name="_token"]`).val());
    formData.append("action", "upload");
    formData.append("crud_id", plugin.options.crud_id != undefined ? plugin.options.crud_id : '');
    const fileInput = container.find(`input[id^="filemanager-attachment-${idAttachment}"]`);

    const attributes = plugin.values.filter(function (attachment) {
      return attachment.id == idAttachment
    });

    Object.entries(attributes[0]).map(function (attribute) {
      formData.append(attribute[0], (attribute[1] && attribute[1].value != undefined) ? attribute[1].value : attribute[1]);
    });

    if (fileInput.data("change") == true) {
      formData.append(fileInput.attr("name"), fileInput[0].files[0]);
      formData.append('update_attachment', true);
    }

    if (plugin.options.url_params != undefined) {
      Object.entries(plugin.options.url_params).forEach(function (param) {
        formData.append(param[0], param[1]);
      });
    }

    const progress = function (event) {
      if (fileInput.data("change") == true) {
        const rowAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${idAttachment}"]`).closest("tr");
        const percent = (event.loaded / event.total) * 100;
        const progress = Math.round(percent);
        rowAttachment.find(".progress").removeClass("d-none");
        rowAttachment.find(".progress-bar").css("width", `${progress}%`);
      }
    }
    const load = function (event) {
      const rowAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${idAttachment}"]`).closest("tr");
      rowAttachment.find(".progress").addClass("d-none");
    }

    await $.ajax({
      url: plugin.options.url,
      type: 'POST',
      processData: false,
      contentType: false,
      data: formData,
      beforeSend: function () {
        const rowAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${idAttachment}"]`).closest("tr");
        rowAttachment.find(".file-upload-running").fadeIn();
      },
      success: function (data) {
        const rowAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${idAttachment}"]`).closest("tr");
        const fileTime = data.updated_at ? new Date(data.updated_at) : '';

        rowAttachment.find(".file-upload-running").hide();
        rowAttachment.find(".file-upload-success").show();
        rowAttachment.find(".file-time").text(`${fileTime.toLocaleDateString('pt-Br')} às ${fileTime.toLocaleTimeString('pt-Br', { timeStyle: 'short' }) + ' h'}`);
        rowAttachment.find(".file-editor").text(data.editor_name);
        setTimeout(function () {
          rowAttachment.find(".file-upload-success").fadeOut("slow");
        }, 500);
        fileInput.remove();

        plugin.values = plugin.values.map(function (attachment) {
          if (attachment.id == idAttachment) {
            attachment = { ...attachment, ...data };
            attachment.action = "update";
          }

          return attachment;
        });
        plugin.tableFilesList.find(`input[type="checkbox"][row-id="${idAttachment}"]`).attr("row-id", data.id);
        idAttachment = data.id
      },
      error: function ($xhr) {
        const dadosResposta = JSON.parse($xhr.responseText);
        if (dadosResposta.mensagem != undefined) {
          App._showMessage(dadosResposta.mensagem, "error");
        }
        const rowAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${idAttachment}"]`).closest("tr");
        rowAttachment.find(".file-upload-running").hide();
        rowAttachment.find(".file-upload-error").show();
        fileInput.remove();
      },
      xhr: function () {
        var xhr = new window.XMLHttpRequest();
        xhr.upload.addEventListener("progress", progress, false);
        xhr.addEventListener("load", load, false);
        return xhr;
      }
    });
  }

  this.getFileIsImage = function (file) {
    if (file.type != undefined) {
      return file && file.type.split('/')[0] === 'image';
    }

    return plugin.options.image_types.includes(file.extension.toLowerCase());
  }

  this.getFileName = function (fileName) {

    let duplicateName = null;
    do {
      duplicateName = plugin.values.filter(function (attachment) {
        return `${attachment.name}.${attachment.extension}`.toLowerCase() == fileName.toLowerCase();
      });

      if (duplicateName.length) {
        fileName = fileName.split(".");
        fileName[fileName.length - 2] += '(1)';
        fileName = fileName.join(".");
      }
    } while (duplicateName.length)

    return fileName;
  }

  this.getFileSize = function (size, type) {
    var i;
    i = Math.floor(Math.log(size) / Math.log(1024));
    if ((size === 0) || (parseInt(size) === 0)) {
      return "0 kB";
    } else if (isNaN(i) || (!isFinite(size)) || (size === Number.POSITIVE_INFINITY) || (size === Number.NEGATIVE_INFINITY) || (size == null) || (size < 0)) {
      return '';
    } else {
      if (type == undefined || type == 'abrev') {
        return (size / Math.pow(1024, i)).toFixed(2) * 1 + " " + ["B", "kB", "MB", "GB", "TB", "PB"][i];
      } else {
        return (size / Math.pow(1024, i)).toFixed(2) * 1 + " " + ["bytes", "kilobytes", "megabytes", "gigabytes", "terabytes", "petabytes"][i];
      }
    }
  }

  this.getFileExtension = function (fileName) {
    return fileName.split(".").pop();
  }

  this.setButtonUpload = function () {
    plugin.options.buttons.upload.on("click", function () {
      $('[data-toggle="tooltip"]').tooltip("hide");
      plugin.dropzone.trigger("click");
    });
  }

  this.runUpload = async function () {
    for (const attachment of plugin.values) {
      if (attachment.action == 'create') {
        await plugin.upload(attachment.id);
      }
    }
    let flagAllUploaded = true;
    plugin.values.forEach(function (attachment) {
      if (attachment.action == 'create') {
        flagAllUploaded = false;
      }
    });
    return flagAllUploaded;
  }

  this.setButtonRemove = function () {
    plugin.options.buttons.remove.on("click", function () {
      $('[data-toggle="tooltip"]').tooltip("hide");
      let flagConfirmRemove = false;
      let files = [];
      plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).each(function () {
        let _t = $(this);
        plugin.values.forEach(function (attachment) {
          if (attachment.action == 'create') {
            plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${_t.attr("row-id")}"]`).closest("tr").find(".file-upload-running").hide();
            plugin.removeFile(_t.attr("row-id"), false);
            return;
          }

          if (attachment.id == _t.attr("row-id")) {
            files.push(_t.attr("row-id"));
            flagConfirmRemove = true;
          }
        })
      });

      const removeFiles = async function (files) {
        files.map(function (file) {
          plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${file}"]`).closest("tr").find(".file-upload-running").fadeIn();
        });

        params = {
          action: "delete",
          id: files,
          crud_id: plugin.options.crud_id,
        };

        if (plugin.options.url_params != undefined) {
          Object.entries(plugin.options.url_params).forEach(function (param) {
            params[param[0]] = param[1];
          });
        }

        await axios.get(`${plugin.options.url}?${$.param(params)}`)
          .then(function (response) {
            if (response.data.error_attachments.length > 0) {
              response.data.error_attachments.map(function (attachment) {
                var indexAttachment = files.indexOf(attachment.id);
                if (indexAttachment !== -1) {
                  files.splice(indexAttachment, 1);
                  App._showMessage(`O anexo '${attachment.name}' não pôde ser removido.`, "error");
                }
              });
            }
            files.map(function (file) {
              plugin.tableFilesList.find(`tbody tr input[type="checkbox"][row-id="${file}"]`).closest("tr").find(".file-upload-running").hide();
              plugin.removeFile(file);
            });
          })
          .catch(function (error) {
            if (error.response != undefined && error.response.data.mensagem != undefined) {
              App._showMessage(error.response.data.mensagem, "error")
              return;
            }
            App._showMessage("Não foi possível excluir o anexo.", "error");
          });
      }

      if (flagConfirmRemove == false && files.length) {
        removeFiles(files);
      }

      plugin.options.buttons.edit.prop("disabled", true);
      plugin.options.buttons.preview.prop("disabled", true);
      plugin.options.buttons.remove.prop("disabled", true);
      plugin.options.buttons.download.prop("disabled", true);
      container.find("input.filemanager-select-all").prop("checked", false);

      if (flagConfirmRemove == true) {
        App._confirmDelete(function () {
          removeFiles(files)
        });
      }
    });
  }

  this.setButtonEdit = function () {
    plugin.options.buttons.edit.on("click", function () {
      $('[data-toggle="tooltip"]').tooltip("hide");
      const idAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).first().attr("row-id");
      plugin.editFile(idAttachment);
    });
  }

  this.setDropzone = function () {
    Dropzone.autoDiscover = false;
    plugin.dropzone = container.dropzone({
      url: "/",
      autoProcessQueue: false,
      uploadMultiple: true,
      acceptedFiles: plugin.options.accept != undefined ? plugin.options.accept.join(",") : null,
      addedfile: function (file) {
        $('[data-toggle="tooltip"]').tooltip("hide");
        plugin.addFile(file, true);
      },
    });

    plugin.dropzone.on("dragenter", function (file) {
      const _t = $(this);
      _t.removeClass("border");
      container.css({
        'border': "5px dashed #1C6EA4",
      });
    });

    plugin.dropzone.on("drop", function (file) {
      const _t = $(this);
      container.css({
        'border': "none",
      });
      _t.addClass("border");
    });
  }

  this.setButtoDownload = function () {
    plugin.options.buttons.download.on("click", function () {
      $('[data-toggle="tooltip"]').tooltip("hide");
      const files = [];
      plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).each(function () {
        files.push($(this).attr("row-id"));
      });

      const params = {
        "action": "download",
        "crud_id": plugin.options.crud_id,
        "id": files,
      };

      if (plugin.options.url_params != undefined) {
        Object.entries(plugin.options.url_params).forEach(function (param) {
          params[param[0]] = param[1];
        });
      }

      $.redirect(plugin.options.url, params, "GET");
    });
  }

  this.setButtonPreview = function () {
    plugin.options.buttons.preview.on("click", function () {
      $('[data-toggle="tooltip"]').tooltip("hide");
      const idAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).first().attr("row-id");
      const values = plugin.values.filter(function (attachment) {
        return attachment.id == idAttachment;
      });
      if (values.length == 0 || !plugin.options.preview_types.includes(values[0].extension.toLowerCase())) {
        return false;
      }

      let attachment = values[0];
      let modalTitleExtension = attachment.name.slice(attachment.extension.length * -1) == attachment.extension ? '' : `.${attachment.extension}`;
      plugin.modalPreview.find(".modal-header").find(".modal-title").html(`${attachment.name}${modalTitleExtension}`);
      plugin.modalPreview.find(".modal-body").html('');

      params = {
        action: "preview",
        id: attachment.id,
        crud_id: plugin.options.crud_id,
      };

      if (plugin.options.url_params != undefined) {
        Object.entries(plugin.options.url_params).forEach(function (param) {
          params[param[0]] = param[1];
        });
      }

      const previewFile = {
        image: function (attachment) {
          plugin.modalPreview.find(".modal-body").html(`
            <img class="align-middle img-fluid" src="${plugin.options.url}?${$.param(params)}">
          `);
          plugin.modalPreview.modal('show');
        },
        office: function (attachment) {
          let pdfContainer = $("<div>", {
            style: `height: 100%; border: 3px solid rgba(0,0,0,.1);`,
          });

          pdfContainer.append($(`<iframe>`, {
            style: `width:100%; height: ${plugin.modalPreview.find(".modal-body").height()}px;`,
            src: `${plugin.options.url}?${$.param(params)}`
          }));
          plugin.modalPreview.find(".modal-body").append(pdfContainer);
          plugin.modalPreview.modal('show');


        },
        plain: async function (attachment) {

          let plainContainer = $("<div>", {
            class: "text-start p-0",
            style: `width: 100%; height: 100%; border: 3px solid rgba(0,0,0,.1); overflow-y: auto; overflow-x: auto;`,
          });
          const fileContent = await $.get(`${plugin.options.url}?${$.param(params)}`, function (data) {
            plainContainer.html(`<pre class="text-start f8 p-1" style="width: 100%; white-space:pre-wrap"></pre>`);
            plainContainer.find("pre").text(data);
            plugin.modalPreview.find(".modal-body").append(plainContainer);
            plugin.modalPreview.modal('show');
          }, 'text');


        }
      }
      if (plugin.options.image_types.includes(attachment.extension.toLowerCase())) {
        previewFile.image(attachment);
      }
      if (plugin.options.office_types.includes(attachment.extension.toLowerCase())) {
        previewFile.office(attachment);
      }
      if (plugin.options.plain_types.includes(attachment.extension.toLowerCase())) {
        previewFile.plain(attachment);
      }
    });
  }

  this.setDataTableFilesList = async function () {

    plugin.tableFilesList = $("<table>", {
      class: "table table-borded table-striped table-condensed table-hover table-sm col-md-24 p-0",
      style: "width: 100%;",
    });

    container.find(".filemanager-files-list").append(plugin.tableFilesList);
    plugin.dtFilesList = await container.find(".filemanager-files-list table").DataTable({
      dom: "<'row'<'col-md-24 p-0'tr>>",
      autoWidth: false,
      "columns": [
        {
          title: `<input type="checkbox" class="filemanager-select-all">`,
          className: "col-sm-1 pa1 f8 text-center ",
        },
        {
          title: "Nome",
          className: "col-sm-11 pa1 f8 text-left align-middle",
        },
        {
          title: `Modificação`,
          className: "col-sm-8 pa1 f8 text-center align-middle",
        },
        {
          title: "Tamanho",
          className: "col-sm-4 pa1 f8 text-center align-middle",
        },
      ],
      columnDefs: [
        { orderable: false, targets: [0] },
      ],
      order: [[1, "asc"]],
      language: {
        "emptyTable": `<div class="pa1 f8">Não há arquivos relacionados...</div>`
      },
      createdRow: function (tr) {
        plugin.setSelectRow(tr);
        container.find("input.filemanager-select-all").prop("checked", false);
      },

      responsive: true,
      searching: false,
      paging: false,
      info: false,
      scrollY: '300px',
    });
    container.find(".dataTables_scrollHeadInner").width("100%");
    container.find(".dataTables_scroll table").width("100%");
  }

  this.setFormParentOnSubmit = async function () {
    if (plugin.options.auto_upload != true) {
      plugin.options.form_parent.on("submit", async function (e) {
        await plugin.values.map(function (attachment, index) {
          Object.entries(attachment).map(function (input) {
            plugin.options.form_parent.append($("<textarea />", {
              class: "d-none",
              name: `${plugin.options.prefix_input_name}[${attachment.id}][${input[0]}]`,
              value: input[1].value != undefined ? input[1].value : input[1],
            }));
          })
        });
      });
    }
  }

  this.setModalProperties = function () {
    plugin.modalProperties.find(".btn-update-attachment").on("click", function () {
      const formAttributes = plugin.modalProperties.find(".filemanager-form-attachment-properties");
      const formData = getFormData(formAttributes);
      if (plugin.updateFile(
        formAttributes.data("attachment-id"),
        formData,
        formAttributes.find(`input#filemanager-attachment-edit-${formAttributes.data("attachment-id")}`)
      ) == true) {
        plugin.modalProperties.modal("hide");
      }
    });
  }

  this.setOnSelect = function () {
    const countSelected = plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).length;

    plugin.options.buttons.remove.prop("disabled", true);
    plugin.options.buttons.download.prop("disabled", true);
    plugin.options.buttons.edit.prop("disabled", true);
    plugin.options.buttons.preview.prop("disabled", true);

    if (countSelected > 0) {
      plugin.options.buttons.remove.prop("disabled", false);
      plugin.options.buttons.download.prop("disabled", false);
    }

    if (countSelected == 1) {
      const idAttachment = plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).first().attr("row-id");
      const values = plugin.values.filter(function (attachment) {
        return attachment.id == idAttachment;
      });
      if (values.length == 0) {
        return false;
      }
      attachment = values[0];
      plugin.options.buttons.edit.prop("disabled", false);
      plugin.options.buttons.preview.prop("disabled", !plugin.options.preview_types.includes(attachment.extension.toLowerCase()) || attachment.action != 'update');
      plugin.options.buttons.download.prop("disabled", attachment.action != 'update');
    }
  }

  this.setOptions = function (options) {
    plugin.options = {
      ...plugin.options,
      ...options
    };
  }

  this.setSelectRow = function (tr) {
    $(tr).find(`input[type="checkbox"]`).on("click", function (e) {
      e.stopPropagation();
    });
    $(tr).find(`input[type="checkbox"]`).on("change", function (e) {
      $(tr).toggleClass("table-info", $(this).is(':checked'));
      if ($(this).is(':checked') == true &&
        plugin.tableFilesList.find("tbody tr").length == plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).length
      ) {
        plugin.tableFilesList.find(`.filemanager-select-all`).prop("checked", true);
        plugin.allSelected = true;
      }
      if ($(this).is(':checked') == false) {
        plugin.tableFilesList.find(`.filemanager-select-all`).prop("checked", false);
        plugin.allSelected = false;
      }
      plugin.setOnSelect();

    });
    $(tr).on("click", function (e) {
      if (e.ctrlKey && plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).length > 0) {
        $(this).find(`input[type="checkbox"]`).prop("checked", !$(this).find(`input[type="checkbox"]`).prop("checked")).trigger("change");
        return;
      }
      if (e.ctrlKey == false && plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).length > 1) {
        plugin.tableFilesList.find(`.filemanager-select-all`).prop("checked", false).trigger("change");
        $(this).find(`input[type="checkbox"]`).prop("checked", true).trigger("change");
        return;
      }
      if (e.ctrlKey == false && plugin.tableFilesList.find(`tbody tr input[type="checkbox"]:checked`).length <= 1) {
        const inputChecked = $(this).find(`input[type="checkbox"]`).prop("checked");
        plugin.tableFilesList.find(`.filemanager-select-all`).prop("checked", false).trigger("change");
        $(this).find(`input[type="checkbox"]`).prop("checked", !inputChecked).trigger("change");
      }
    });
  }

  this.setSelectAll = function () {
    container.find("input.filemanager-select-all").on("change", function () {
      if ($(this).is(`input[type="checkbox"]`)) {
        if (plugin.tableFilesList.find("tbody tr").length == undefined || plugin.tableFilesList.find("tbody tr").length < 1) {
          $(this).prop("checked", false);
          return;
        }
        plugin.allSelected = $(this).is(':checked');
        plugin.tableFilesList.find("tbody tr").find(`input[type="checkbox"]`).prop("checked", plugin.allSelected).trigger("change");
      }
    });
  }

  this.setTemplate = function () {
    $(`.filemanager-modal-properties[data-filemaneger-id="${container.attr("id")}"]`).remove();
    $(`.filemanager-modal-preview[data-filemaneger-id="${container.attr("id")}"]`).remove();
    container.addClass("filemanager");
    container.append(`
      <div class="col-md-24 bg-light filemanager-buttons py-1 mb-2 border-bottom">
        <button type="button" title="Enviar Arquivos" class="btn btn-secondary fs-sm px-1 py-0 btn-upload ${plugin.options.readonly == true ? 'd-none' : ''}" data-toggle="tooltip" data-placement="top" data-original-title="Enviar"><i class="ph-file-arrow-up"></i></button>
        ${plugin.options.readonly != true ? '<button type="button" title="Editar Atributos" class="btn btn-light fs-sm px-2 py-0 btn-edit" data-toggle="tooltip" data-placement="top" data-original-title="Editar Atributos" disabled="disabled"><i class="ph-note-pencil"></i></button>' : ''}
        <button type="button" class="btn btn-light fs-sm px-1 py-0 btn-preview" title="Visualizar" data-toggle="tooltip" data-placement="top" data-original-title="Visualizar" disabled="disabled"><i class="ph-eye"></i></button>
        ${plugin.options.readonly != true ? '<button type="button" class="btn btn-light fs-sm px-1 py-0 btn-remove" title="Excluir" data-toggle="tooltip" data-placement="top" data-original-title="Excluir" disabled="disabled"><i class="ph-trash"></i></button>' : ''}
        <button type="button" class="btn btn-light fs-sm px-1 py-0 btn-download" title="Download" data-toggle="tooltip" data-placement="top" data-original-title="Download" disabled="disabled"><i class="ph-download"></i></button>
        </div>
        <div class="col-md-24 filemanager-files-list pa-1">
        </div>
      </div>
    `);

    $("body").append(`
        <div class="modal fade filemanager-modal-properties" data-filemaneger-id="${container.attr("id")}" tabindex="-1">
          <div class="modal-dialog modal-sm">
            <div class="modal-content">
              <div class="modal-header p-1 bg-light">
                  <h5 class="modal-title fs-sm mb-0 px-1">Editar anexo</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
              </div>
              <div class="modal-footer p-1 bg-light">
                <div class="col-md-24 p-0">
                  <div class="col-md-12 p-0 m-0">
                      <button type="button" class="btn btn-secondary fs-sm px-2 py-1 me-2" data-bs-dismiss="modal">
                          <i class="ph-x me-1 fs-sm"></i>Fechar
                      </button>
                  </div>
                  <div class="d-flex justify-content-end m-0">
                      <button type="button" class="btn btn-outline-secondary fs-sm px-2 py-1 btn-update-attachment">
                          <i class="ph-file-arrow-up me-1 fs-sm"></i>Atualizar
                      </button>
                  </div>
            </div>
              </div>
            </div>
          </div>
      </div>
      `);
    plugin.modalProperties = $(`.filemanager-modal-properties[data-filemaneger-id="${container.attr("id")}"]`);

    const formAttributes = $("<form>", {
      class: "filemanager-form-attachment-properties row",
      autocomplete: "off",
    });
    formAttributes.on("submit", function (e) {
      e.preventDefault();
      return false;
    });

    formAttributes.append(`
      <div class="col-md-24 py-0 px-1">
          <label for="" class="form-label fw-semibold fs-sm m-0">Nome</label>
          <div class="input-group">
              <input type="text" name="name" class="form-control px-1 pt1 pb1 fs-sm">
              <span class="input-group-text extension px-1 pt1 pb1 fs-sm "></span>
          </div>
      </div>
    `);

    formAttributes.append($("<div>", {
      class: "col-md-24 py-0 mt-1 px-1 attachment-file",
    }));

    plugin.modalProperties.find(".modal-body").append(formAttributes);
    plugin.options.form_attributes = plugin.modalProperties.find(".filemanager-form-attachment-properties").eq(0);

    if (plugin.options.modal_attributes_callback != undefined) {
      plugin.options.modal_attributes_callback(plugin.modalProperties, formAttributes);
    }

    if (plugin.options.form_attributes != undefined) {
    }

    $("body").append(`
        <div class="modal fade filemanager-modal-preview" data-filemaneger-id="${container.attr("id")}" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header p-1 bg-light">
                  <h5 class="modal-title fs-sm mb-0 px-1"></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body text-center">
              </div>
              <div class="modal-footer bg-light p-1">
                <div class="d-flex justify-content-end m-0">
                  <button type="button" class="btn btn-secondary fs-sm px-2 py-1 me-2" data-bs-dismiss="modal">
                      <i class="ph-x me-1 fs-sm"></i>Fechar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      `);
    plugin.modalPreview = $(`.filemanager-modal-preview[data-filemaneger-id="${container.attr("id")}"]`);

    plugin.modalPreview.find("div.modal-body").css({
      height: ($(window).height() - 200) + "px",
      overflow: "hidden scroll",
    })

    plugin.options.buttons = {
      upload: container.find(".btn-upload"),
      download: container.find(".btn-download"),
      edit: container.find(".btn-edit"),
      preview: container.find(".btn-preview"),
      remove: container.find(".btn-remove"),
    }
  }

  this.setValues = async function (options) {
    if (plugin.values == undefined) {
      plugin.values = [];
    }

    if ((options == undefined || options.values == undefined) && plugin.values.length == 0 && plugin.options.crud_id != undefined) {
      const formData = new FormData();
      formData.append("_token", plugin.options.form_parent.find(`input[name="_token"]`).val());
      formData.append("action", "fetch");
      formData.append("crud_id", plugin.options.crud_id);

      if (plugin.options.url_params != undefined) {
        Object.entries(plugin.options.url_params).forEach(function (param) {
          formData.append(param[0], param[1]);
        });
      }

      await axios.post(`${plugin.options.url}`, formData)
        .then(async function ({ data }) {
          data.catalog.map(async function (attachment) {
            await plugin.addFile(attachment, false);
          });
        })
        .catch(function (error) {
          if (error.response != undefined && error.response.data.mensagem != undefined) {
            toastr.info(error.response.data.mensagem);
          }
        });
    }

    if (options != undefined && options.values != undefined) {
      options.values.map(async function (attachment) {
        await plugin.addFile(attachment, false);
      });
    }
  }

  this.random = function (length) {
    var result = '';
    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
  }

  this.init();
}

$.fn.fileManager = function (options) {
  pluginName = 'fileManager';
  var response = '';

  $(this).each(function () {
    if (!$.data(this, 'plugin_' + pluginName + '_')) {
      $.data(this, 'plugin_' + pluginName + '_', true);

      $.data(this, 'plugin_' + pluginName,
        new fileManager($(this), options));
    }
    else if ($.data(this, 'plugin_' + pluginName)) {
      response = $.data(this, 'plugin_' + pluginName).changeMethod($(this), options);
    }
  });
  return response;
};



/**REDIRECT */
!function (t) { "use strict"; var r = { url: null, values: null, method: "POST", target: null, traditional: !1, redirectTop: !1 }; t.redirect = function (e, a, n, o, i, l) { var u = e; "object" != typeof e && (u = { url: e, values: a, method: n, target: o, traditional: i, redirectTop: l }); var p = t.extend({}, r, u), d = t.redirect.getForm(p.url, p.values, p.method, p.target, p.traditional); t("body", p.redirectTop ? window.top.document : void 0).append(d.form), d.submit(), d.form.remove() }, t.redirect.getForm = function (r, e, o, i, l) { o = o && -1 !== ["GET", "POST", "PUT", "DELETE"].indexOf(o.toUpperCase()) ? o.toUpperCase() : "POST"; var u = (r = r.split("#"))[1] ? "#" + r[1] : ""; if (r = r[0], !e) { var p = t.parseUrl(r); r = p.url, e = p.params } e = n(e); var d = t("<form>").attr("method", o).attr("action", r + u); i && d.attr("target", i); var f = d[0].submit; return a(e, [], d, null, l), { form: d, submit: function () { f.call(d[0]) } } }, t.parseUrl = function (t) { if (-1 === t.indexOf("?")) return { url: t, params: {} }; var r, e, a = t.split("?"), n = a[1].split("&"); t = a[0]; var o = {}; for (r = 0; r < n.length; r += 1)o[(e = n[r].split("="))[0]] = e[1]; return { url: t, params: o } }; var e = function (r, e, a, n, o) { var i, l; if (a.length > 0) { for (l = 1, i = a[0]; l < a.length; l += 1)i += "[" + a[l] + "]"; r = n && o ? i : i + "[" + r + "]" } return t("<input>").attr("type", "hidden").attr("name", r).attr("value", e) }, a = function (t, r, n, o, i) { var l = []; Object.keys(t).forEach(function (u) { "object" == typeof t[u] ? ((l = r.slice()).push(u), a(t[u], l, n, Array.isArray(t[u]), i)) : n.append(e(u, t[u], r, o, i)) }) }, n = function (t) { for (var r = Object.getOwnPropertyNames(t), e = 0; e < r.length; e++) { var a = r[e]; null === t[a] || void 0 === t[a] ? delete t[a] : "object" == typeof t[a] ? t[a] = n(t[a]) : t[a].length < 1 && delete t[a] } return t } }(window.jQuery || window.Zepto || window.jqlite);