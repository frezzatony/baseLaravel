
function setFiltrosPesquisa(form, valores) {
  if (valores != undefined && valores != '') {
    Object.entries(valores).forEach(function (filtro) {
      form.find(`[name="filter[${filtro[0]}]"]`).attr("data-value", filtro[1]);
      form.find(`[name="filter[${filtro[0]}]"]`).val(filtro[1]).trigger('change');
    });
  }
}

function getFiltros(form) {
  const inputsFiltros = form.find(`[name^="filter\\["]`);
  let values = {};
  inputsFiltros.each(function (keyInput, input) {
    let inputName = $(input).attr("name");
    values[inputName] = $(input).val();
  });
  return values;
}
