
async function setQueuesCalendar(modal) {
  const form = modal.find("#form-queue");
  const divCalendarDates = form.find(".listgroup-calendar-dates");
  const divCalendarDatesDatails = form.find(".calendar-dates-details");
  const storedCalendar = form.find("#stored_calendar").val().trim() ? JSON.parse(form.find("#stored_calendar").val().trim()) : [];

  const addDate = async function (date) {
    form.find(".empty-calendar-dates,.list-group-calendar-dates-empty").addClass("d-none");

    const labelDateTemplate = divCalendarDates.find(".label-calendar-date-template").clone();
    labelDateTemplate.removeClass("label-calendar-date-template d-none");
    labelDateTemplate.addClass(`label-calendar-date label-calendar-date-${date.uuid}`);
    labelDateTemplate.find(`input[type="hidden"]`).attr("name", `calendar[${date.uuid}][uuid]`).val(date.uuid);
    labelDateTemplate.find(`input[type="radio"]`).data("date", date);
    labelDateTemplate.find(`input[type="date"]`).attr("name", `calendar[${date.uuid}][date]`);
    divCalendarDates.prepend(labelDateTemplate);

    const calendarDateDetailTemplate = divCalendarDatesDatails.find(".list-group-calendar-dates-datails-template").clone();
    calendarDateDetailTemplate.removeClass("list-group-calendar-dates-datails-template").addClass(`list-group-calendar-dates-datails list-group-calendar-dates-datails-${date.uuid}`);

    calendarDateDetailTemplate.find("input,select").each(function () {
      let _t = $(this);
      _t.closest("div").find("label").attr("for", `calendar[${date.uuid}][${_t.data("id")}]`);
      _t.attr("id", `calendar[${date.uuid}][${_t.data("id")}]`);
      _t.attr("name", `calendar[${date.uuid}][${_t.data("id")}]`);
    });

    calendarDateDetailTemplate.find(`#calendar\\[${date.uuid}\\]\\[full_day\\]`).on("change", function () {
      calendarDateDetailTemplate.find(".calendar-date-details-hours").toggleClass("d-none", $(this).val() == "t");
    });

    divCalendarDatesDatails.append(calendarDateDetailTemplate);

    let tblAppendGridQueueCalendarHours = calendarDateDetailTemplate.find("table").eq(0);
    tblAppendGridQueueCalendarHours.attr("data-name", `calendar_hours[${date.uuid}]`);
    tblAppendGridQueueCalendarHours.attr("id", `tbl-appendgrid-queues-calendar-hours-${date.uuid}`);
    let idTblAppendGridQueueCalendarHours = tblAppendGridQueueCalendarHours.attr("id");

    let appendGridQueueCalendarHours = await new AppendGrid({
      element: idTblAppendGridQueueCalendarHours,
      uiFramework: "bootstrap5",
      iconParams: {
        icons: {
          append: "ph-plus",
        }
      },
      initRows: 0,
      initData: null,
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
        return `calendar[${date.uuid}][hours][${uniqueIndex}][${name}]`;
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
            const appendGridQueueCalendarHours = document.getElementById(idTblAppendGridQueueCalendarHours).appendGrid;
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
                appendGridQueueCalendarHours.removeRow($(parent).closest("tr").index());
                return;
              }
              App._confirmDelete(function () {
                const appendGridQueueCalendarHours = document.getElementById(idTblAppendGridQueueCalendarHours).appendGrid;
                appendGridQueueCalendarHours.removeRow($(parent).closest("tr").index());
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

    document.getElementById(idTblAppendGridQueueCalendarHours).appendGrid = appendGridQueueCalendarHours;
    tblAppendGridQueueCalendarHours.closest("fieldset").css("height", `${tblAppendGridQueueCalendarHours.closest(".vh-50").height() * 0.7}px`);
    tblAppendGridQueueCalendarHours.find("tbody").css({
      "max-height": `${tblAppendGridQueueCalendarHours.closest(".vh-50").height() * 0.56}px`,
      "margin-right": "10px",
    });

    divCalendarDatesDatails.find(".btn-add-calendar-hour").on("click", async function () {
      await appendGridQueueCalendarHours.appendRow([{
        uuid: uuidv4(),
      }]);
    })

    labelDateTemplate.find(`input[type="radio"]`).on("change", function () {
      const _t = $(this);
      divCalendarDates.find(`input[type="radio"]`).prop("checked", false);
      _t.prop("checked", true);
      divCalendarDatesDatails.find(".list-group-calendar-dates-datails").addClass("d-none");
      divCalendarDatesDatails.find(`.list-group-calendar-dates-datails-${date.uuid}`).removeClass("d-none");
    });

    labelDateTemplate.find(`input[type="date"`).on("focus addDate", function () {
      labelDateTemplate.find(`input[type="radio"]`).trigger("change");
    });

    labelDateTemplate.find(".btn-remove-date").on("click", function () {
      App._confirmDelete(function () {
        divCalendarDates.find(`.label-calendar-date-${date.uuid}`).remove();
        divCalendarDatesDatails.find(`.list-group-calendar-dates-datails-${date.uuid}`).remove();

        if (divCalendarDates.find(".label-calendar-date").length) {
          divCalendarDates.find(".label-calendar-date").first().find(`input[type="radio"]`).trigger("change");
        }

        if (divCalendarDates.find(".label-calendar-date").length <= 0 && divCalendarDatesDatails.find(".list-group-calendar-dates-datails").length <= 0) {
          form.find(".empty-calendar-dates,.list-group-calendar-dates-empty").removeClass("d-none");
        }
      });
    });

    labelDateTemplate.find(`input[type="date"]`).val(date.date);
    calendarDateDetailTemplate.find(`select[data-id="availability"]`).val(date.availability == true ? 't' : 'f');
    calendarDateDetailTemplate.find(`select[data-id="full_day"]`).val(date.full_day == true ? 't' : 'f').trigger("change");
    calendarDateDetailTemplate.find(`input[data-id="reason"]`).val(date.reason);
    if (date.hours) {
      appendGridQueueCalendarHours.appendRow(date.hours);
    }

    labelDateTemplate.find(`input[type="date"]`).trigger("addDate");
  };

  storedCalendar.forEach(async function (date) {
    await addDate(date)
  });

  form.find(".btn-add-calendar-date").on("click", async function () {
    await addDate({
      uuid: uuidv4(),
      date: null,
      availability: true,
      full_day: true,
      reason: '',
      hours: [],
    });
  });
}

