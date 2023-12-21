function setQueuesMatters(modal) {
  const form = modal.find("#form-queue");
  const mattersListGroup = form.find(".listgroup-matters");
  const storedMatters = form.find("#stored_matters").val().trim()
    ? JSON.parse(form.find("#stored_matters").val().trim())
    : [{
      uuid: uuidv4(),
      description: null,
      users: [],
    }];

  const addMatters = function (matters) {
    matters.forEach(async function (matter, index) {
      matter.users = matter.users ? matter.users : [];
      let matterTemplate = mattersListGroup.find(".label-attendant-template").clone();
      matterTemplate.data("matter", matter);
      matterTemplate.removeClass("label-attendant-template d-none");
      matterTemplate.addClass("label-attendant");
      matterTemplate.find(`input[type="hidden"]`).attr("name", `matters[${matter.uuid}][uuid]`).val(matter.uuid);
      matterTemplate.find(`input[type="text"]`).attr("name", `matters[${matter.uuid}][description]`).val(matter.description);
      mattersListGroup.append(matterTemplate);
      await setQueuesMattersInputsActions(modal, matterTemplate);
      App.initComponents();
    });
    mattersListGroup.find(".empty-matters").toggleClass("d-none", mattersListGroup.find(".label-attendant").length > 0);
  }
  addMatters(storedMatters);
  form.find(".btn-add-matter").on("click", async function () {
    await addMatters([{
      uuid: uuidv4(),
      description: null,
      users: []
    }]);
    mattersListGroup.find(`input[type="text"]`).last().focus();
  });
}

function setQueuesMattersInputsActions(modal, label) {
  const form = modal.find("#form-queue");
  const mattersListGroup = label.closest(".listgroup-matters");
  const inputRadio = label.find(`input[type="radio"]`);

  label.find(`input[type="text"]`).on("focus", function () {
    if (inputRadio.is(":checked")) {
      return false;
    }
    inputRadio.trigger("change");
  });

  if (label.find(`input[type="text"][name$="\\[description\\]"]`).val()) {
    setQueuesMattersAttendants(modal, label, true);
  }

  inputRadio.on("change", async function () {
    await setQueuesMattersAttendants(modal, label, form.find(`.list-group-matter-attendants-${label.data("matter").uuid}`).length <= 0 || label.data("redraw") == true);
    mattersListGroup.find(`input[type="radio"]`).prop('checked', false);
    label.closest(".listgroup-matters").find(`input[type="radio"]`).prop('checked', false);
    form.find(".matter-attendants").find(".list-group").addClass("d-none");
    form.find(`.list-group-matter-attendants-${label.data("matter").uuid}`).removeClass("d-none");
    $(this).prop('checked', true);
  });

  label.find(".btn-remove-matter").on("click", function () {
    $('[data-bs-popup=tooltip]').tooltip("hide");
    App._confirmDelete(function () {
      if (inputRadio.is(":checked")) {
        form.find(".matter-attendants").find(".list-group").addClass("d-none");
        form.find(".list-group-matter-attendants-empty").removeClass("d-none");
      }
      label.remove();
    });
    mattersListGroup.find(".empty-matters").toggleClass("d-none", mattersListGroup.find(".label-attendant").length > 0);
  });
}

async function setQueuesMattersAttendants(modal, label, redraw) {
  const form = modal.find("#form-queue");
  const divMatterAttendants = form.find(".matter-attendants");
  const appendGridQueueAttendants = document.getElementById("tbl-appendgrid-queues-attendants").appendGrid;

  const attendantsUsers = appendGridQueueAttendants.getAllValue().sort((a, b) => {
    if (a.user_name_show < b.user_name_show) {
      return -1;
    }
    if (a.user_name_show > b.user_name_show) {
      return 1;
    }
    return 0;
  });;

  if (form.find(`.list-group-matter-attendants-${label.data("matter").uuid}`).length > 0 && redraw != true) {
    return false;
  }

  const attendants = form.find(`.list-group-matter-attendants-${label.data("matter").uuid}`).length > 0
    ? getFormData(form.find(`.list-group-matter-attendants-${label.data("matter").uuid}`).eq(0))[`matters[${label.data("matter").uuid}][users][]`]
    : label.data("matter").users;

  form.find(`.list-group-matter-attendants-${label.data("matter").uuid}`).remove();

  const matterAttendantTemplate = divMatterAttendants.find(".list-group-matter-attendants-template").clone();
  matterAttendantTemplate.removeClass("list-group-matter-attendants-template").addClass(`list-group-matter-attendants-${label.data("matter").uuid}`);

  let allAttendantNode = $("<li></li>", {
    class: "folder fs-sm expanded",
    text: "TODOS"
  });
  let attendantsNodes = $("<ul></ul>");

  await attendantsUsers.forEach(function (user) {
    if ((user.user_name_show == '')) {
      return false;
    }

    let attendantNode = $("<li></li>", {
      class: "fs-sm",
      text: `${user.user_name_show.toUpperCase()} [${user.user_cpf}]`,
      "data-value": user.user_id,
    });

    if (attendants.includes(parseInt(user.user_id))) {
      attendantNode.addClass("selected");
    }
    attendantsNodes.append(attendantNode);
  });

  if (attendantsNodes.find("li").length == attendantsNodes.find(".fancytree-selected")) {
    attendantsNodes.addClass("selected");
  }

  allAttendantNode.append(attendantsNodes);
  matterAttendantTemplate.find("ul").first().append(allAttendantNode);

  matterAttendantTemplate.find(".empty").toggleClass("d-none", attendantsNodes.find("li").length > 0);
  matterAttendantTemplate.find(".users").toggleClass("d-none", attendantsNodes.find("li").length <= 0);
  label.data("redraw", attendantsNodes.find("li").length <= 0);

  divMatterAttendants.append(matterAttendantTemplate);

  if (matterAttendantTemplate.find("li").length) {
    matterAttendantTemplate.find(".tree-checkbox-hierarchical").attr("name", `matters[${label.data("matter").uuid}][users][]`);
    matterAttendantTemplate.find(".tree-checkbox-hierarchical").fancytree({
      checkbox: true,
      selectMode: 3
    });
  }
}