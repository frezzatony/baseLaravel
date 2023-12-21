async function setCrudContacts(objParent) {
  const table = objParent.find("table#tbl-crud-contacts");
  const contactsTypes = await App._fetchItems({
    service: "System/Contact/ContactType",
    label: 'tipos de contatos',
  });
  const optionsContactsTypes = contactsTypes.map(function (option) {
    return {
      value: option.id,
      label: option.description.toUpperCase(),
    }
  });

  const initData = objParent.find(".stored-contacts").val().trim() != ""
    ? JSON.parse(objParent.find(".stored-contacts").val().trim()).map(function (contact) {
      return {
        contact: contact
      }
    })
    : null;

  const appendGridContacts = await new AppendGrid({
    element: table.attr("id"),
    uiFramework: "bootstrap5",
    iconParams: {
      icons: {
        append: "ph-plus",
      }
    },
    initRows: 0,
    initData: initData,
    hideRowNumColumn: true,
    i18n: {
      append: "Adicionar novo contato",
      remove: "Remover",
      rowEmpty: "Não há contatos vinculados.",
    },
    sectionClasses: {
      table: "table-sm table-striped border fs-sm",
      thead: "p-0",
      control: "form-control px-1 py-0 fs-sm",
      buttonGroup: "btn-group-sm",
    },
    hideButtons: {
      remove: true,
      removeLast: true,
      moveUp: true,
      moveDown: true,
      insert: true,
      append: true,
    },
    nameFormatter: function (idPrefix, name, uniqueIndex) {
      return `contact[${uniqueIndex}][${name}]`;
    },
    columns: [
      {
        name: "id",
        type: "hidden",
      },
      {
        name: "contact",
        type: "custom",
        cellClass: "p-1",
        displayCss: {
          "display": "none",
        },
        customBuilder: function (parent, idPrefix, name, uniqueIndex) {
          const contactTemplate = objParent.find(".crud-contacts-contact-template").clone();

          contactTemplate.find(".uuid")
            .attr("id", `contact[${uniqueIndex}][uuid]`)
            .attr("name", `contact[${uniqueIndex}][uuid]`)
            .val(uuidv4());

          contactTemplate.find(".contact_type").find("label")
            .attr("for", `contact[${uniqueIndex}][contact_type]`);
          contactTemplate.find(".contact_type").find("select")
            .attr("id", `contact[${uniqueIndex}][contact_type]`)
            .attr("name", `contact[${uniqueIndex}][contact_type]`);
          fillDropdown({
            select: contactTemplate.find("select"),
            options: optionsContactsTypes,
          });

          contactTemplate.find(".preferred").find("label")
            .attr("for", `contact[${uniqueIndex}][preferred]`);
          contactTemplate.find(".preferred").find("input")
            .attr("id", `contact[${uniqueIndex}][preferred]`)
            .attr("name", `contact[${uniqueIndex}][preferred]`);

          contactTemplate.find(".invalid").find("label")
            .attr("for", `contact[${uniqueIndex}][invalid]`);
          contactTemplate.find(".invalid").find("input")
            .attr("id", `contact[${uniqueIndex}][invalid]`)
            .attr("name", `contact[${uniqueIndex}][invalid]`);

          contactTemplate.find(".contact").find("label")
            .attr("for", `contact[${uniqueIndex}][contact]`);
          contactTemplate.find(".contact").find("input")
            .attr("id", `contact[${uniqueIndex}][contact]`)
            .attr("name", `contact[${uniqueIndex}][contact]`);

          contactTemplate.find(".comments").find("label")
            .attr("for", `contact[${uniqueIndex}][comments]`);
          contactTemplate.find(".comments").find("input")
            .attr("id", `contact[${uniqueIndex}][comments]`)
            .attr("name", `contact[${uniqueIndex}][comments]`);

          $(parent).append(contactTemplate.html());
        },
        customGetter: function () {
          return null;
        },
        customSetter: function (idPrefix, name, uniqueIndex, value) {
          if (value != undefined) {
            $(`#contact\\[${uniqueIndex}\\]\\[uuid\\]`).val(value.uuid);
            $(`#contact\\[${uniqueIndex}\\]\\[contact_type\\]`).val(value.type_id);
            $(`#contact\\[${uniqueIndex}\\]\\[preferred\\]`).prop("checked", value.preferred);
            $(`#contact\\[${uniqueIndex}\\]\\[preferred\\]`).prop("checked", value.invalid);
            $(`#contact\\[${uniqueIndex}\\]\\[contact\\]`).val(value.contact);
            $(`#contact\\[${uniqueIndex}\\]\\[comments\\]`).val(value.comments);
          }
        }
      },
    ],
    afterRowAppended: function (table, parentRowIndex, addedRowIndex) {
      addedRowIndex.forEach(function (rowIndex) {
        $('[data-toggle="tooltip"],[data-bs-popup="tooltip"]').tooltip();
        const btnRemove = $(table).find(`tr[data-unique-index="${rowIndex}"]`).find(".btn-crud-contacts-contact-remove");
        btnRemove.off().on("click", function () {
          $('[data-toggle="tooltip"],[data-bs-popup="tooltip"]').tooltip("hide");
          App._confirmDelete(function () {
            appendGridContacts.removeRow($(table).find(`tr[data-unique-index="${rowIndex}"]`).index());
          });
        })
      });
    },
  });

  table.find("thead").remove();
  objParent.find("button.btn-crud-contacts-add-contact").off().on("click", async function () {
    await appendGridContacts.appendRow([{
      uuid: uuidv4(),
    }]);
  });

  objParent.on("reload", function () {
    table.find(`tr[id^="${table.attr("id")}"]`).each(function () {
      appendGridContacts.removeRow($(this).index());
    });
    if (objParent.find(".stored-contacts").val().trim()) {
      appendGridContacts.appendRow(JSON.parse(objParent.find(".stored-contacts").val().trim()).map(function (contact) {
        return {
          contact: contact
        };
      })
      )
    }
  });

}