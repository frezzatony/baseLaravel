$(document).ready(async function () {

  const notificationsDropdownResumeButton = $(".notifications-dropdown-resume-button");

  App._loading(true, "notifications");
  $(".notifications-show-all")
    .off()
    .address()
    .on("click", function () {
      notificationsDropdownResumeButton.dropdown("toggle");
    });

  await setNotificationsBadgeUnreadNotifications();
  App._loading(false, "notifications");

  App.websocket.listen(async function (data) {
    if (data[`notifications.user.${$(`meta[name="user-token"]`).attr("content")}`] != undefined) {
      setNotificationsBadgeUnreadNotifications();
    }
  });

  notificationsDropdownResumeButton.on("show.bs.dropdown", async function () {
    const _t = $(this);
    if (!_t.hasClass("loaded")) {
      _t.addClass("loaded");
      await fillNotificationsResumeRows();
    }
  });

  notificationsDropdownResumeButton.on("hide.bs.dropdown", async function () {
    $(".notifications-read-area").text('')
  });
  const notificationsDropdownResume = $(".notifications-dropdown-resume");
  $(".notifications-mark-all-as-read").on("click", async function () {
    App._loadingElements(true, [notificationsDropdownResume]);
    await fetchNotificationsMarkAllAsRead();
    await setNotificationsBadgeUnreadNotifications();
    App._loadingElements(false, [notificationsDropdownResume]);
  });

});

async function setNotificationsBadgeUnreadNotifications(realoadGrid) {
  const badgeUnreadNotifications = $(".notifications-unread");
  const notificationsIcon = $(".notifications-icon");
  const previewNotifications = await fetchNotificationsResumeUnread(false);
  const lastCounter = badgeUnreadNotifications.data("last-count") != undefined ? badgeUnreadNotifications.data("last-count") : previewNotifications.count;
  const notificationsDropdownResumeButton = $(".notifications-dropdown-resume-button");

  badgeUnreadNotifications
    .text(previewNotifications.count > 0 ? (previewNotifications.count > 99 ? `+99` : previewNotifications.count) : "")
    .data("last-count", previewNotifications.count)
    .toggleClass("loaded", lastCounter != badgeUnreadNotifications.data("last-count"));

  if (previewNotifications.count > 0) {
    notificationsIcon.toggleClass("ph-bell-ringing ph-bell").addClass("animated swing");
    notificationsIcon.off().on("animationend", function () {
      $(this).removeClass("animated swing").toggleClass("ph-bell-ringing ph-bell");
    });
  }
  if (lastCounter != badgeUnreadNotifications.data("last-count")) {
    notificationsDropdownResumeButton.removeClass("loaded");
  }

  if (notificationsDropdownResumeButton.hasClass("show") && lastCounter != badgeUnreadNotifications.data("last-count") && badgeUnreadNotifications.data("reaload-grid") != false) {
    await fillNotificationsResumeRows();
  }
}

async function fillNotificationsResumeRows() {

  const notificationsDropdownResume = $(".notifications-dropdown-resume");
  const previewNotifications = await fetchNotificationsResumeUnread(true);

  App._loadingElements(true, [notificationsDropdownResume]);
  $(".notifications-read-area").text('').toggleClass("d-none", previewNotifications.count <= 0);
  $(".notifications-mark-all-as-read").toggleClass("disabled", previewNotifications.count <= 0);

  if (previewNotifications.count <= 0) {
    await setNotificationsGrid();

  }

  if (previewNotifications.count > 0) {
    let gridData = [];
    Object.entries(previewNotifications.notifications).forEach(function (notification) {
      const notificationTime = new Date(notification[1].time);
      gridData.push({
        0: notification[1].author,
        1: notification[1].title,
        2: `
          ${notificationTime.toLocaleString("pt-BR", { day: "2-digit", month: "2-digit", year: "numeric" })} às 
          ${notificationTime.toLocaleString("pt-BR", { hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false, })}h
        `,
        id: notification[1].id,
      })
    });
    await setNotificationsGrid(gridData);

  }
  App._loadingElements(false, [notificationsDropdownResume]);
}


function getNotificationsNotificationResumeRow(notification) {
  const rowTemplate = $(".notifications-notification-resume-template").clone();
  const notificationTime = new Date(notification.time);

  rowTemplate.toggleClass("notifications-notification-resume-template resume-row");
  rowTemplate.find(".author").text(notification.author);
  rowTemplate.find(".time").text(`
    ${notificationTime.toLocaleString("pt-BR", { day: "2-digit", month: "2-digit", year: "numeric" })} às 
    ${notificationTime.toLocaleString("pt-BR", { hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false, })}h
  `);
  rowTemplate.find(".title").text(notification.title);
  return rowTemplate;
}

function setNotificationsGrid(gridData) {
  const grid = $("#dt-app-notifications");

  if (grid.data("datatable") != undefined) {
    grid.data("datatable").clear().destroy();
    grid.off();
  }

  let gridOptions = {
    dom: "t",
    select: {
      style: "single",
    },
    oLanguage: {
      sEmptyTable: "Nenhuma notificação encontrada"
    },
    columnDefs: [
      { className: "pa1 f8", targets: [0, 1, 2] },
    ],
    order: [[2, "desc"]],
    pageLength: 50,
  };

  if (gridData != undefined) {
    gridOptions = {
      ...gridOptions,
      ...{
        autoWidth: false,
        "scrollY": "150px",
        "scrollCollapse": true,
        data: gridData,
      }
    }
  }

  const datatable = grid.DataTable(gridOptions);

  datatable.on("select", async function (e, dt, type, indexes) {
    const notificationsReadArea = $(".notifications-read-area");
    const badgeUnreadNotifications = $(".notifications-unread");
    App._loadingElements(true, [notificationsReadArea]);
    var data = datatable.rows({ selected: true }).data().pop();
    const notification = await fetchNotificationsNotification(data.id);
    if (notification != false) {
      badgeUnreadNotifications.data("reaload-grid", false);
      notificationsReadArea.html(notification.text);
      await setNotificationsBadgeUnreadNotifications(false);
    }
    App._loadingElements(false, [notificationsReadArea]);
  });
  grid.data("datatable", datatable);
}

async function fetchNotificationsResumeUnread(showMessages) {
  return await axios.post(`/api/notifications/resume-unread`, {
    api_token: $(`meta[name="user-token"]`).attr("content"),
    show_notifications: showMessages != undefined ? showMessages : false,
    headers: {
      "X-Requested-With": "xmlhttprequest"
    }
  })
    .then(function ({ data }) {
      return data;
    })
    .catch(function (error) {
      if (error.response !== undefined && error.response.data.message !== undefined && error.response.data.message != "") {
        App._showMessage(error.response.data.message, "info");
        return [];
      }
      if (error !== "Error: Request aborted") {
        App._showMessage(error.response);
        return [];
      }
    });
}

async function fetchNotificationsNotification(idNotification) {
  return await axios.post(`/api/notifications/notification`, {
    api_token: $(`meta[name="user-token"]`).attr("content"),
    id: idNotification,
    headers: {
      "X-Requested-With": "xmlhttprequest"
    }
  })
    .then(function ({ data }) {
      return data.notification;
    })
    .catch(function (error) {
      if (error.response !== undefined && error.response.data.message !== undefined && error.response.data.message != "") {
        App._showMessage(error.response.data.message, "info");
        return false;
      }
      if (error !== "Error: Request aborted") {
        App._showMessage(error.response);
        return false;
      }
    });
}

async function fetchNotificationsMarkAllAsRead(showMessages) {
  return await axios.post(`/api/notifications/mark-all-as-read`, {
    api_token: $(`meta[name="user-token"]`).attr("content"),
    headers: {
      "X-Requested-With": "xmlhttprequest"
    }
  })
    .then(function ({ data }) {
      return true;
    })
    .catch(function (error) {
      if (error.response !== undefined && error.response.data.message !== undefined && error.response.data.message != "") {
        App._showMessage(error.response.data.message, "info");
        return [];
      }
      if (error !== "Error: Request aborted") {
        App._showMessage(error.response);
        return [];
      }
    });
}

async function fetchNotificationsMarkNotificationAsRead(showMessages) {
  return await axios.post(`/api/notifications/mark-notification-as-read`, {
    api_token: $(`meta[name="user-token"]`).attr("content"),
    headers: {
      "X-Requested-With": "xmlhttprequest"
    }
  })
    .then(function ({ data }) {
      return true;
    })
    .catch(function (error) {
      if (error.response !== undefined && error.response.data.message !== undefined && error.response.data.message != "") {
        App._showMessage(error.response.data.message, "info");
        return [];
      }
      if (error !== "Error: Request aborted") {
        App._showMessage(error.response);
        return [];
      }
    });
}