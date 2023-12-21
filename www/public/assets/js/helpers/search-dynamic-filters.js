async function setFiltrosDinamicosPesquisa(options) {

  const filtroDinamico = $(`#${options.id_elemento}`);
  const form = filtroDinamico.closest("form");
  filtroDinamico.addClass("filtro-dinamico");

  await filtroDinamico
    .on("afterInit.queryBuilder", function (e) {
      $(this).removeClass("form-inline");
    })
    .on("afterAddRule.queryBuilder", function (e, rule) {

      $("button.btn-remove-rule").off().on("mouseenter mouseleave", function (e) {
        $(this).toggleClass("btn-danger");
        $(this).closest(".rule-container").toggleClass("bg-light");
      });
    })
    .on("afterUpdateRuleFilter.queryBuilder", function (e, rule, options) {
      if (rule.$el.find(".rule-value-container").find(".form-group").length > 1) {
        rule.$el.find(".rule-value-container").find(".form-group").removeClass("col-md-24").addClass("col-md-10");
      }
    })
    .on("afterUpdateRuleOperator.queryBuilder", function (e, rule, options) {
      if (rule.$el.find(".rule-value-container").find(".form-group").length > 1) {
        rule.$el.find(".rule-value-container").find(".form-group").removeClass("col-md-24").addClass("col-md-10");
      }
    })
    .on("validationError.queryBuilder", function (e, rule, error, value) {
      e.preventDefault();
      return true;
    })
    .queryBuilder({
      rules: getValuesQueryBuilder(form, options.values, options.filtros),
      allow_groups: false,
      no_add_rule: true,
      conditions: ["AND"],
      display_empty_filter: false,
      inputs_separator: `<div class="col-md-2 p-0 m-0 text-center">e</div>`,
      filters: getFiltrosQueryBuilder(options.filtros),
      templates: getTemplatesQueryBuilder(),
      operators: $.fn.queryBuilder.constructor.DEFAULTS.operators.concat([
        { type: "after", nb_inputs: 1, multiple: false, apply_to: ["string"] },
        { type: "after_or_equal", nb_inputs: 1, multiple: false, apply_to: ["string"] },
        { type: "before", nb_inputs: 1, multiple: false, apply_to: ["string"] },
        { type: "before_or_equal", nb_inputs: 1, multiple: false, apply_to: ["string"] },
        { type: "equal_date", nb_inputs: 1, multiple: false, apply_to: ["string"] },
      ]),
      lang: {
        operators: {
          before: "Antes de",
          before_or_equal: "Antes ou igual a",
          after: "Depois de",
          after_or_equal: "Depois ou igual a",
          equal_date: "Igual a",
        }
      },
    });

  cleanFiltrosDinamicos(form, filtroDinamico, options);

  $("button.btn-add-filter-search-crud").on("click", function (e) {
    filtroDinamico.queryBuilder("addRule", filtroDinamico.data("queryBuilder").model.root);
  });

  $("button.btn-reset-search-crud").on("click", function (e) {
    $.removeCookie("filtros_pesquisa", { path: window.location.pathname });
    $.removeCookie("filtros_dinamicos_pesquisa", { path: window.location.pathname });

    form.trigger("reset");
    filtroDinamico.queryBuilder('reset');

    if (options.onReset != undefined && options.onReset instanceof Function) {
      return options.onReset();
    }
    location.href = form.attr('action');
  });

  form.on("submit", function (e) {
    e.preventDefault();
    let values = getFiltros($(this));
    const filtrosDinamicos = getFiltrosDinamicos(filtroDinamico);
    values = { ...values, ...filtrosDinamicos };

    if (options.onSubmit != undefined && options.onSubmit instanceof Function) {
      return options.onSubmit(values);
    }

    $.redirect(form.attr("action"), values, form.attr("method"));
    return false;
  });

  if (options.autoload == true) {
    var interval = setInterval(function () {
      if ($.fn.DataTable.isDataTable(options.grid)) {
        form.trigger('submit');
        clearInterval(interval);
      }
    }, 300);
  }
};

function getFiltrosQueryBuilder(inputs) {
  let filtros = [];
  inputs.map(function (input) {
    if (input.filtros != undefined) {
      input.id = input.label;
      input.filters = input.filtros.map(function (childInput) {
        childInput.optgroup = input.label;
        filtros.push({ ...childInput, ...getInputQueryBuilder(childInput) });
      });
    }
    if (input.filtros == undefined) {
      filtros.push({ ...input, ...getInputQueryBuilder(input) });
    }
  });
  return filtros;
}

function getValuesQueryBuilder(form, values, filtros) {
  const valuesQueryBuilder = [];

  if ((values == undefined || values.length < 1) && form.find(`[name^="filter\\["]`).length == 0) {
    const inputQueryBuilderValor = getInputQueryBuilder(filtros[0]);
    valuesQueryBuilder.push({
      id: filtros[0].id,
      operator: inputQueryBuilderValor.operators[0],
      value: "",
    })
  }

  if (values != undefined && typeof values === 'object') {
    Object.keys(values).forEach(function (keyFiltro) {
      Object.entries(values[keyFiltro]).forEach(function (filtro) {
        valuesQueryBuilder.push({
          id: keyFiltro,
          operator: filtro[1].operator,
          value: filtro[1].value
        })
      });
    });
  }

  if (form.find(`[name^="filtro["]`).length == 0) {
    valuesQueryBuilder[0].flags = {
      no_delete: true
    };
  }

  return valuesQueryBuilder.length ? valuesQueryBuilder : null;
}

function getInputQueryBuilder(input) {

  const inputs = {
    textbox: {
      input: function (rule, inputName) {
        return `
          <div class="form-group col-md-24 p-0 m-0">
              <input type="textbox" name="${inputName}"" class="form-control py-0 px-1 fs-sm">
          </div>
      `
      },
      operators: ["contains", "not_contains", "begins_with", "ends_with", "equal", "not_equal", "not_begins_with", "not_ends_with", "is_null", "is_not_null"],
    },
    select: {
      input: "select",
      values: input.values != undefined
        ? Object.fromEntries(
          Object.entries(input.values).map(([key, value]) => [key, value.toString().toUpperCase()])
        )
        : [],
      operators: ["equal", "not_equal", "is_null", "is_not_null"],
    },
    date: {
      input: function (rule, inputName) {
        return `
          <div class="form-group col-md-24 p-0 m-0 d-inline-block">
              <input type="date" name="${inputName}"" class="form-control py-0 px-1 fs-sm">                    
          </div>
      `
      },
      operators: ["equal_date", "not_equal", "before", "before_or_equal", "after", "after_or_equal", "between", "not_between", "is_null", "is_not_null"]
    },
    integer: {
      input: function (rule, inputName) {
        return `
          <div class="form-group col-md-24 p-0 m-0 d-inline-block">
              <input type="number" name="${inputName}"" class="form-control py-0 px-1 fs-sm" step="1">                    
          </div>
      `
      },
      operators: ["equal", "not_equal", "less", "less_or_equal", "greater", "greater_or_equal", "between", "not_between", "is_null", "is_not_null"]
    },
    bool: {
      input: "select",
      values: [
        { 'true': 'SIM' },
        { 'false': 'NÃO' },
      ],
      operators: ["equal", "not_equal",],
    },
  }
  return inputs[input.input_type];
}


function getFiltrosDinamicos(objQueryBuilder) {
  let values = {};
  const filtrosDinamicos = objQueryBuilder.queryBuilder("getRules", { allow_invalid: true });
  Object.entries(filtrosDinamicos.rules).forEach(function (filtroDinamico) {
    let nomeFiltro = `dynamic_filter[${filtroDinamico[1].id}]`;
    if (Object.keys(values).indexOf(nomeFiltro) == -1) {
      values[nomeFiltro] = [];
    }
    values[nomeFiltro].push({
      "operator": `${filtroDinamico[1].operator}`,
      "value": filtroDinamico[1].value != undefined ? `${filtroDinamico[1].value}` : "",
    })
  });
  return values;
}

function cleanFiltrosDinamicos(form, objFiltroDinamico, options) {
  if ((options.values == undefined || options.values.length == 0) && $.cookie("filtros_pesquisa")) {
    objFiltroDinamico.queryBuilder('deleteRule', objFiltroDinamico[0].queryBuilder.model.root.rules[0]);
  }
}

function getTemplatesQueryBuilder() {
  return {
    group: `
<div id="{{= it.group_id }}" class="rules-group-container"> 
<div class="rules-group-header"> 
<div class="btn-group pull-right group-actions"> 
<button type="button" class="btn btn-xs btn-success" data-add="rule"> 
<i class="{{= it.icons.add_rule }}"></i> {{= it.translate("add_rule") }} 
</button> 
{{? it.settings.allow_groups===-1 || it.settings.allow_groups>=it.level }} 
<button type="button" class="btn btn-xs btn-success" data-add="group"> 
<i class="{{= it.icons.add_group }}"></i> {{= it.translate("add_group") }} 
</button> 
{{?}} 
{{? it.level>1 }} 
<button type="button" class="btn btn-xs btn-danger" data-delete="group"> 
<i class="{{= it.icons.remove_group }}"></i> {{= it.translate("delete_group") }} 
</button> 
{{?}} 
</div> 
<div class="btn-group group-conditions"> 
{{~ it.conditions: condition }} 
<label class="btn btn-xs btn-primary"> 
<input type="radio" name="{{= it.group_id }}_cond" value="{{= condition }}"> {{= it.translate("conditions", condition) }} 
</label> 
{{~}} 
</div> 
{{? it.settings.display_errors }} 
<div class="error-container"><i class="{{= it.icons.error }}"></i></div> 
{{?}} 
</div> 
<div class="rules-group-body row pl-3"> 
<div class="rules-list col d-inline-block" style="min-width: 85%;"></div> 
<div class="d-inline-block col">
  <div class="btn-group pt-1">
      <button type="submit" class="btn btn-outline-secondary fs-sm px-1 py-0 py-0 btn-search-crud">
          <i class="ph-magnifying-glass fs-sm me-1"></i>Pesquisar
      </button>
      <div class="btn-group">
          <button type="button" class="btn btn-outline-secondary fs-sm px-1 py-0 py-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"></button>
          <div class="dropdown-menu" style="">
              <button type="button" class="dropdown-item fs-sm px-1 py-0 py-1 btn-add-filter-search-crud">
                  <i class="ph-plus fs-sm me-1"></i>Adicionar filtro
              </button>
              <button type="button" class="dropdown-item fs-sm px-1 py-0 py-1 btn-reset-search-crud">
                  <i class="ph-arrows-counter-clockwise fs-sm me-1"></i>Reiniciar pesquisa
              </button>
          </div>
      </div>
  </div>
</div>
</div> 
</div>`,
    rule: `
<div id="{{= it.rule_id }}" class="rule-container pt-1 pb-1 mb-1">
<div class="rule-filter-container d-inline-block p-0 m-0" style="min-width: 30%;"></div>
<div class="rule-operator-container d-inline-block ml-3" style="min-width: 20%;"></div>
<div class="rule-value-container d-inline-block p-0 ml-3" style="min-width: 41%;"></div>
<div class="p-0 ml-1 d-inline-block">
<div class="btn-group rule-actions">
<button type="button" class="btn btn-flat-pink pa1 btn-remove-rule" data-delete="rule">
<i class="ph-minus fs-sm"></i>
</button>
</div>
</div>
</div>`,
    operatorSelect: `
{{? it.operators.length === 1 }}
<div class="col-md-24 text-center"> 
{{= it.translate("operators", it.operators[0].type) }} 
</div> 
{{?}} 
{{ var optgroup = null; }}
<div class="form-group col-md-24 p-0 m-0  {{? it.operators.length === 1 }} d-none{{?}}">
<select class="form-select form-control-sm fs-sm me-1 px-1 py-0 w-100 {{? it.operators.length === 1 }} d-none{{?}}" name="{{= it.rule.id }}_operator"> 
{{~ it.operators: operator }} 
{{? optgroup !== operator.optgroup }} 
{{? optgroup !== null }}</optgroup>{{?}} 
{{? (optgroup = operator.optgroup) !== null }} 
<optgroup label="{{= it.translate(it.settings.optgroups[optgroup]) }}"> 
{{?}} 
{{?}} 
<option value="{{= operator.type }}" {{? operator.icon}}data-icon="{{= operator.icon}}"{{?}}>{{= it.translate("operators", operator.type) }}</option> 
{{~}} 
{{? optgroup !== null }}</optgroup>{{?}} 
</select>
</div>`,
    filterSelect: `
{{ var optgroup = null; }}                     
<div class="form-group col-md-24 p-0 m-0 pe-1">
<select class="form-select form-control-sm fs-sm px-1 py-0 w-100" name="{{= it.rule.id }}_filter"> 
{{? it.settings.display_empty_filter }} 
<option value="-1">{{= it.settings.select_placeholder }}</option> 
{{?}} 
{{~ it.filters: filter }} 
{{? optgroup !== filter.optgroup }} 
{{? optgroup !== null }}</optgroup>{{?}} 
{{? (optgroup = filter.optgroup) !== null }} 
<optgroup label="{{= it.translate(it.settings.optgroups[optgroup]) }}"> 
{{?}} 
{{?}} 
<option value="{{= filter.id }}" {{? filter.icon}}data-icon="{{= filter.icon}}"{{?}}>{{= it.translate(filter.label) }}</option> 
{{~}} 
{{? optgroup !== null }}</optgroup>{{?}} 
</select>
</div>`,
    ruleValueSelect: `
{{ var optgroup = null; }} 
<select class="form-select form-control-sm fs-sm px-1 py-0 w-100" name="{{= it.name }}" {{? it.rule.filter.multiple }}multiple{{?}}> 
{{? it.rule.filter.placeholder }} 
<option value="{{= it.rule.filter.placeholder_value }}" disabled selected>{{= it.rule.filter.placeholder }}</option> 
{{?}} 
{{~ it.rule.filter.values: entry }} 
{{? optgroup !== entry.optgroup }} 
{{? optgroup !== null }}</optgroup>{{?}} 
{{? (optgroup = entry.optgroup) !== null }} 
<optgroup label="{{= it.translate(it.settings.optgroups[optgroup]) }}"> 
{{?}} 
{{?}} 
<option value="{{= entry.value }}">{{= entry.label }}</option> 
{{~}} 
{{? optgroup !== null }}</optgroup>{{?}} 
</select>`
  }
}