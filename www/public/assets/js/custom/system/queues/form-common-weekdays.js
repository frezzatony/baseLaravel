
async function setQueueFormWeekdays(modal) {
  const form = modal.find("#form-queue");
  const weekdayHoursListGroup = form.find(".list-group-hours-weekday");

  await weekdayHoursListGroup.find(".list-group-item").each(async function (index, item) {
    let _t = $(this);
    let weekday = _t.find(".list-group-radio-label").eq(0).data("weekday");

    let idTblAppendGridQueueHours = `tbl-appendgrid-queues-hours-weekday-${weekday}`;
    let tblAppendGridQueueHours = form.find(`#${idTblAppendGridQueueHours}`);

    const storedValues = JSON.parse(form.find("#stored_weekdays_hours").val().trim());
    const weekdayHoursValues = storedValues.filter(function (weekdayStored) {
      return weekdayStored.weekday == weekday || weekdayStored.weekday == 'all';
    });

    let appendGridQueueHours = await new AppendGrid({
      element: idTblAppendGridQueueHours,
      uiFramework: "bootstrap5",
      iconParams: {
        icons: {
          append: "ph-plus",
        }
      },
      initRows: 0,
      initData: weekdayHoursValues[0] != undefined ? weekdayHoursValues[0].hours : null,
      hideRowNumColumn: true,
      i18n: {
        append: "Adicionar novo horário",
        remove: "Remover",
        rowEmpty: "Não há horários vinculados.",
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
        return `${tblAppendGridQueueHours.data("name")}[${uniqueIndex}][${name}]`;
      },
      columns: [
        {
          name: "uuid",
          type: "hidden",
        },
        {
          name: "start",
          display: "Início",
          type: "time",
          cellClass: "w-40 p-1",
          ctrlClass: "",
          displayCss: {
            "width": "40%",
            "padding": "1px",
          },
        },
        {
          name: "end",
          display: "Término",
          type: "time",
          cellClass: "w-40 p-1",
          displayCss: {
            "width": "40%",
            "padding": "1px",
          },
        },
        {
          name: "acoes",
          display: "Ações",
          type: "custom",
          cellClass: "w-40 p-1 text-left",
          ctrlClass: "text-left",
          displayCss: {
            "width": "30%",
            "padding": "1px",
          },
          customBuilder: function (parent, idPrefix, name, uniqueIndex) {
            const appendGridQueueHours = document.getElementById(idTblAppendGridQueueHours).appendGrid;
            const botaoExcluir = $('<button/>', {
              type: "button",
              class: "btn btn-danger p-1",
              html: `<i class="ph-trash fs-sm"></i>`,
              "data-toggle": "tooltip",
              "data-placement": "top",
              "data-original-title": "Excluir"
            });
            botaoExcluir.on("click", function () {
              const inputTimeStart = $(parent).closest("tr").find(`input[name$="\\[start\\]"]`);
              const inputTimeEnd = $(parent).closest("tr").find(`input[name$="\\[end\\]"]`);
              if (inputTimeStart.val() == '' && inputTimeEnd.val() == '') {
                appendGridQueueHours.removeRow($(parent).closest("tr").index());
                return;
              }
              App._confirmDelete(function () {
                const appendGridQueueHours = document.getElementById(idTblAppendGridQueueHours).appendGrid;
                appendGridQueueHours.removeRow($(parent).closest("tr").index());
              });
            });
            $(parent).append(botaoExcluir);
          },
          customGetter: function () {
            return null;
          }
        },
      ],
      afterRowAppended: function (table, parentRowIndex, addedRowIndex) {
      },
    });

    document.getElementById(idTblAppendGridQueueHours).appendGrid = appendGridQueueHours;
    tblAppendGridQueueHours.closest("fieldset").css("height", `${tblAppendGridQueueHours.closest(".vh-50").height() * 0.7}px`);
    tblAppendGridQueueHours.find("tbody").css({
      "max-height": `${tblAppendGridQueueHours.closest(".vh-50").height() * 0.56}px`,
      "margin-right": "10px",
    });

    form.find(`.list-group-hours-weekday-${weekday}`).find(".btn-add-hour").on("click", async function () {
      await appendGridQueueHours.appendRow([{
        uuid: uuidv4(),
      }]);
    });

    _t.find(".list-group-radio-label").on("change", function () {
      const inputWeekday = $(this);
      weekdayHoursListGroup.find(`input[type="radio"]`).each(function () {
        $(this).prop("checked", false);
      });
      inputWeekday.prop("checked", true);
      form.find(".list-group-hours-weekday-hours").addClass("d-none");
      form.find(`.list-group-hours-weekday-${inputWeekday.data("weekday")}`).removeClass("d-none");
    });
  });

  weekdayHoursListGroup.find(".list-group-radio-label").eq(0).trigger("change");
}