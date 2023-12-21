function getFormData(a, b) {
  const c = {};

  a.find("input, textarea").each(function () {
    const d = $(this).attr("name");
    if (d !== undefined) {
      const e = $(this).attr("type");
      const f = e === "checkbox" ? ($(this).is(":checked") ? $(this).val() : "") : $(this).val();
      c[d] = f;
    }
  });

  a.find("select").each(function () {
    const d = $(this).attr("name");
    if (d !== undefined) {
      const g = $(this).prop("multiple");
      const h = $(this).find("option:selected");

      if (!g) {
        c[d] = {
          text: h.text(),
          value: h.val(),
        };
      } else {
        c[d] = [];
        h.each(function () {
          c[d].push({
            text: $(this).text(),
            value: $(this).val(),
          });
        });
      }
    }
  });

  a.find(`[class^="tree-checkbox-"]`).each(function () {
    const tree = $(this);
    if (!tree.attr("name")) {
      return true;
    }
    const selectedNodes = tree.fancytree("getTree").getSelectedNodes();
    c[tree.attr("name")] = c[tree.attr("name")] != undefined ? c[tree.attr("name")] : [];
    $.map(selectedNodes, function (n) {
      if (n.toDict().data == undefined || n.toDict().data.value == undefined) {
        return true;
      }
      c[tree.attr("name")].push(n.toDict().data.value)
    });
  })


  if (b == 'values') {
    const getValues = function (values) {
      let formValues = {};
      Object.entries(values).forEach(function (item) {
        let value = item[1].value != undefined ? item[1].value : item[1];
        if (typeof value == 'object') {
          value = getValues(value);
        }
        if (item[0].startsWith("tbl-appendgrid")) {
          let appendGridId = item[0].split('_')[0];
          formValues[document.getElementById(appendGridId).getAttribute('name')] = document.getElementById(appendGridId).appendGrid.getAllValue();
          return;
        }
        formValues[item[0]] = value;
      });
      return formValues;
    }
    return getValues(c)
  }

  if (b == 'formdata') {
    const i = new FormData();
    for (const [d, f] of Object.entries(c)) {
      const j = typeof f === "object" && f.value !== undefined;
      i.append(d, j ? f.value : f);
    }
    return i;
  }

  return c;
}

function setFormValidationErrors(form, errors) {
  form.find(".invalid-feedback").remove();
  form.find(".border-danger").removeClass("border-danger");
  if (errors == undefined) {
    return;
  }

  Object.entries(errors).forEach(function (item) {
    if (item[0].split('.').length > 1) {
      let inputName = item[0].split(".");
      if ($(`table[name="${inputName[0]}"]`).length || $(`table[data-name="${inputName[0]}"]`).length) {
        let appendGridTable = $(`table[name="${inputName[0]}"]`).length ? $(`table[name="${inputName[0]}"]`) : $(`table[data-name="${inputName[0]}"]`);
        let appendGridInput = appendGridTable.find("tbody tr").find(`[name$="\\[${inputName[inputName.length - 2]}\\]\\[${inputName[inputName.length - 1]}\\]"]`);
        if (appendGridInput.length > 0) {
          appendGridInput.after(`<div class="invalid-feedback col-md-24 p-0 m-0" style="font-size: 8pt;">${item[1]}</div>`);
        }
      }
      else if ($(`[name="${inputName[0]}${inputName.slice(1).map(valor => `[${valor}]`).join('')}"]`).length > 0) {
        $(`[name="${inputName[0]}${inputName.slice(1).map(valor => `[${valor}]`).join('')}"]`).after(`<div class="invalid-feedback col-md-24 p-0 m-0 d-block" style="font-size: 8pt;">${item[1]}</div>`);
      }
      return;
    }

    if (form.find(`[name="${item[0]}"]`).length > 0) {
      form.find(`[name="${item[0]}"]`).closest("div").append(`<div class="invalid-feedback col-md-24 p-0 m-0" style="font-size: 8pt;">${item[1]}</div>`);
    }
    else if (form.find(`table[data-name="${item[0]}"]`).length > 0) {
      form.find(`table[data-name="${item[0]}"]`).before(`<div class="invalid-feedback col-md-24 p-0 m-0" style="font-size: 8pt;">${item[1]}</div>`);
    }
  });
  form.find(".invalid-feedback").show();
}

async function fillDropdown(options) {
  options.select.data("filling", true);
  options.select.empty();

  let setDropdownOptions = function (selectOptions) {
    if (options.first_empty != undefined && options.first_empty == true) {
      let newOption = $("<option>", {
        value: '',
        html: '&nbsp;',
      });
      options.select.append(newOption);
    }

    selectOptions.map(function (selectOption) {
      let newOption = $("<option>", selectOption);
      options.select.append(newOption);
    })
  }
  await setDropdownOptions(options.options);

  if (options.select.data("value") != undefined && options.select.data("value")) {
    options.select.val(options.select.data("value")).trigger("change");
    options.select.data("value", null);
  }
  options.select.data("filling", false);
  options.select.trigger("fillcomplete");
}