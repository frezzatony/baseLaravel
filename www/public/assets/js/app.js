var addressModelMenuConfigured = false;
var loadingControlQueue = [];
var loadedScripts = [];

const App = function () {

  const transitionsDisabled = function () {
    document.body.classList.add('no-transitions');
  };

  const transitionsEnabled = function () {
    document.body.classList.remove('no-transitions');
  };

  const loadingOnCallLinkPage = function () {
    $('a[href][href!="#"]:not([data-load]):not([data-load=""]):not(.nav-link):not(.CLASSESCSS)').on("click", function (e) {
      $.LoadingOverlay("show", {
        image: `/assets/images/logo_loading.png`,
        imageAnimation: "2000ms rotate_right",
        imageResizeFactor: 0.5,
      });
    })
  };

  const detectOS = function () {
    const platform = window.navigator.platform,
      windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
      customScrollbarsClass = 'custom-scrollbars';

    windowsPlatforms.indexOf(platform) != -1 && document.documentElement.classList.add(customScrollbarsClass);
  };

  const sidebarMainResize = function () {
    const sidebarMainElement = document.querySelector('.sidebar-main'),
      sidebarMainToggler = document.querySelectorAll('.sidebar-main-resize'),
      resizeClass = 'sidebar-main-resized',
      unfoldClass = 'sidebar-main-unfold';

    if (sidebarMainElement) {
      const unfoldDelay = 150;
      let timerStart,
        timerFinish;

      sidebarMainToggler.forEach(function (toggler) {
        $(toggler).on('click', function (e) {
          e.preventDefault();
          sidebarMainElement.classList.toggle(unfoldClass);
          if (sidebarMainElement.classList.contains(unfoldClass)) {
            $(".sidebar-logo-icon").addClass("d-none");
          }
          if (!sidebarMainElement.classList.contains(unfoldClass)) {
            $(".sidebar-logo-icon").removeClass("d-none");
          }
        });
      });

      $(sidebarMainElement).find("a.nav-link").on("click", function (e) {
        if (!$(sidebarMainElement).hasClass(unfoldClass)) {
          e.preventDefault();
          sidebarMainElement.classList.toggle(unfoldClass);
          if (sidebarMainElement.classList.contains(unfoldClass)) {
            $(".sidebar-logo-icon").addClass("d-none");
          }
          if (!sidebarMainElement.classList.contains(unfoldClass)) {
            $(".sidebar-logo-icon").removeClass("d-none");
          }
        }
      });

      // sidebarMainElement.addEventListener('mouseenter', function () {
      //   clearTimeout(timerFinish);
      //   timerStart = setTimeout(function () {
      //     sidebarMainElement.classList.contains(resizeClass) && sidebarMainElement.classList.add(unfoldClass);
      //   }, unfoldDelay);
      // });

      sidebarMainElement.addEventListener('mouseleave', function () {
        clearTimeout(timerStart);
        timerFinish = setTimeout(function () {
          sidebarMainElement.classList.remove(unfoldClass);
          if (sidebarMainElement.classList.contains(unfoldClass)) {
            $(".sidebar-logo-icon").addClass("d-none");
          }
          if (!sidebarMainElement.classList.contains(unfoldClass)) {
            $(".sidebar-logo-icon").removeClass("d-none");
          }
        }, unfoldDelay);
      });
    }
  };

  const sidebarMainToggle = function () {

    const sidebarMainElement = document.querySelector('.sidebar-main'),
      sidebarMainRestElements = document.querySelectorAll('.sidebar:not(.sidebar-main):not(.sidebar-component)'),
      sidebarMainDesktopToggler = document.querySelectorAll('.sidebar-main-toggle'),
      sidebarMainMobileToggler = document.querySelectorAll('.sidebar-mobile-main-toggle'),
      sidebarCollapsedClass = 'sidebar-collapsed',
      sidebarMobileExpandedClass = 'sidebar-mobile-expanded';

    sidebarMainDesktopToggler.forEach(function (toggler) {
      toggler.addEventListener('click', function (e) {
        e.preventDefault();
        sidebarMainElement.classList.toggle(sidebarCollapsedClass);
      });
    });

    sidebarMainMobileToggler.forEach(function (toggler) {
      toggler.addEventListener('click', function (e) {
        e.preventDefault();
        sidebarMainElement.classList.toggle(sidebarMobileExpandedClass);

        sidebarMainRestElements.forEach(function (sidebars) {
          sidebars.classList.remove(sidebarMobileExpandedClass);
        });
      });
    });
  };

  const sidebarSecondaryToggle = function () {

    const sidebarSecondaryElement = document.querySelector('.sidebar-secondary'),
      sidebarSecondaryRestElements = document.querySelectorAll('.sidebar:not(.sidebar-secondary):not(.sidebar-component)'),
      sidebarSecondaryDesktopToggler = document.querySelectorAll('.sidebar-secondary-toggle'),
      sidebarSecondaryMobileToggler = document.querySelectorAll('.sidebar-mobile-secondary-toggle'),
      sidebarCollapsedClass = 'sidebar-collapsed',
      sidebarMobileExpandedClass = 'sidebar-mobile-expanded';

    sidebarSecondaryDesktopToggler.forEach(function (toggler) {
      toggler.addEventListener('click', function (e) {
        e.preventDefault();
        sidebarSecondaryElement.classList.toggle(sidebarCollapsedClass);
      });
    });

    sidebarSecondaryMobileToggler.forEach(function (toggler) {
      toggler.addEventListener('click', function (e) {
        e.preventDefault();
        sidebarSecondaryElement.classList.toggle(sidebarMobileExpandedClass);

        sidebarSecondaryRestElements.forEach(function (sidebars) {
          sidebars.classList.remove(sidebarMobileExpandedClass);
        });
      });
    });
  };

  const sidebarRightToggle = function () {

    const sidebarRightElement = document.querySelector('.sidebar-end'),
      sidebarRightRestElements = document.querySelectorAll('.sidebar:not(.sidebar-end):not(.sidebar-component)'),
      sidebarRightDesktopToggler = document.querySelectorAll('.sidebar-end-toggle'),
      sidebarRightMobileToggler = document.querySelectorAll('.sidebar-mobile-end-toggle'),
      sidebarCollapsedClass = 'sidebar-collapsed',
      sidebarMobileExpandedClass = 'sidebar-mobile-expanded';

    sidebarRightDesktopToggler.forEach(function (toggler) {
      toggler.addEventListener('click', function (e) {
        e.preventDefault();
        sidebarRightElement.classList.toggle(sidebarCollapsedClass);
      });
    });

    sidebarRightMobileToggler.forEach(function (toggler) {
      toggler.addEventListener('click', function (e) {
        e.preventDefault();
        sidebarRightElement.classList.toggle(sidebarMobileExpandedClass);

        sidebarRightRestElements.forEach(function (sidebars) {
          sidebars.classList.remove(sidebarMobileExpandedClass);
        });
      });
    });
  };

  const sidebarComponentToggle = function () {

    const sidebarComponentElement = document.querySelector('.sidebar-component'),
      sidebarComponentMobileToggler = document.querySelectorAll('.sidebar-mobile-component-toggle'),
      sidebarMobileExpandedClass = 'sidebar-mobile-expanded';

    sidebarComponentMobileToggler.forEach(function (toggler) {
      toggler.addEventListener('click', function (e) {
        e.preventDefault();
        sidebarComponentElement.classList.toggle(sidebarMobileExpandedClass);
      });
    });
  };

  const navigationSidebar = function () {

    const navContainerClass = 'nav-sidebar',
      navItemOpenClass = 'nav-item-open',
      navLinkClass = 'nav-link',
      navLinkDisabledClass = 'disabled',
      navSubmenuContainerClass = 'nav-item-submenu',
      navSubmenuClass = 'nav-group-sub',
      navScrollSpyClass = 'nav-scrollspy',
      sidebarNavElement = document.querySelectorAll(`.${navContainerClass}:not(.${navScrollSpyClass})`);

    sidebarNavElement.forEach(function (nav) {
      nav.querySelectorAll(`.${navSubmenuContainerClass} > .${navLinkClass}:not(.${navLinkDisabledClass})`).forEach(function (link) {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          const submenuContainer = link.closest(`.${navSubmenuContainerClass}`);
          const submenu = link.closest(`.${navSubmenuContainerClass}`).querySelector(`:scope > .${navSubmenuClass}`);

          if (submenuContainer.classList.contains(navItemOpenClass)) {
            new bootstrap.Collapse(submenu).hide();
            submenuContainer.classList.remove(navItemOpenClass);
          } else {
            new bootstrap.Collapse(submenu).show();
            submenuContainer.classList.add(navItemOpenClass);
          }

          if (link.closest(`.${navContainerClass}`).getAttribute('data-nav-type') == 'accordion') {
            for (let sibling of link.parentNode.parentNode.children) {
              if (sibling != link.parentNode && sibling.classList.contains(navItemOpenClass)) {
                sibling.querySelectorAll(`:scope > .${navSubmenuClass}`).forEach(function (submenu) {
                  new bootstrap.Collapse(submenu).hide();
                  sibling.classList.remove(navItemOpenClass);
                });
              }
            }
          }
        });
      });
    });
  };

  const componentTooltip = function () {
    $('[data-toggle="tooltip"],[data-bs-popup="tooltip"]').tooltip();
  };

  const componentPopover = function () {
    const popoverSelector = document.querySelectorAll('[data-bs-popup="popover"]');

    popoverSelector.forEach(function (popup) {
      new bootstrap.Popover(popup, {
        boundary: '.page-content'
      });
    });
  };

  const componentToTopButton = function () {

    const toTopContainer = document.querySelector('.content-wrapper'),
      toTopElement = document.createElement('button'),
      toTopElementIcon = document.createElement('i'),
      toTopButtonContainer = document.createElement('div'),
      toTopButtonColorClass = 'btn-secondary',
      toTopButtonIconClass = 'ph-arrow-up',
      scrollableContainer = document.querySelector('.content-inner'),
      scrollableDistance = 250,
      footerContainer = document.querySelector('.navbar-footer');

    if (scrollableContainer) {

      toTopContainer.appendChild(toTopButtonContainer);
      toTopButtonContainer.classList.add('btn-to-top');

      toTopElement.classList.add('btn', toTopButtonColorClass, 'btn-icon', 'rounded-pill');
      toTopElement.setAttribute('type', 'button');
      toTopButtonContainer.appendChild(toTopElement);
      toTopElementIcon.classList.add(toTopButtonIconClass);
      toTopElement.appendChild(toTopElementIcon);

      const to_top_button = document.querySelector('.btn-to-top'),
        add_class_on_scroll = () => to_top_button.classList.add('btn-to-top-visible'),
        remove_class_on_scroll = () => to_top_button.classList.remove('btn-to-top-visible');

      scrollableContainer.addEventListener('scroll', function () {
        const scrollpos = scrollableContainer.scrollTop;
        scrollpos >= scrollableDistance ? add_class_on_scroll() : remove_class_on_scroll();
        if (footerContainer) {
          if (this.scrollHeight - this.scrollTop - this.clientHeight <= footerContainer.clientHeight) {
            to_top_button.style.bottom = footerContainer.clientHeight + 20 + 'px';
          } else {
            to_top_button.removeAttribute('style');
          }
        }
      });

      document.querySelector('.btn-to-top .btn').addEventListener('click', function () {
        scrollableContainer.scrollTo(0, 0);
      });
    }
  };

  const cardActionReload = function () {

    const buttonClass = '[data-card-action=reload]',
      containerClass = 'card',
      overlayClass = 'card-overlay',
      spinnerClass = 'ph-circle-notch',
      overlayAnimationClass = 'card-overlay-fadeout';

    document.querySelectorAll(buttonClass).forEach(function (button) {
      button.addEventListener('click', function (e) {
        e.preventDefault();

        const parentContainer = button.closest(`.${containerClass}`),
          overlayElement = document.createElement('div'),
          overlayElementIcon = document.createElement('i');

        overlayElement.classList.add(overlayClass);
        parentContainer.appendChild(overlayElement);
        overlayElementIcon.classList.add(spinnerClass, 'spinner', 'text-body');
        overlayElement.appendChild(overlayElementIcon);

        setTimeout(function () {
          overlayElement.classList.add(overlayAnimationClass);
          ['animationend', 'animationcancel'].forEach(function (e) {
            overlayElement.addEventListener(e, function () {
              overlayElement.remove();
            });
          });
        }, 2500);
      });
    });
  };

  const cardActionCollapse = function () {

    const buttonClass = '[data-card-action=collapse]',
      cardCollapsedClass = 'card-collapsed';

    document.querySelectorAll(buttonClass).forEach(function (button) {
      button.addEventListener('click', function (e) {
        e.preventDefault();

        const parentContainer = button.closest('.card'),
          collapsibleContainer = parentContainer.querySelectorAll(':scope > .collapse');

        if (parentContainer.classList.contains(cardCollapsedClass)) {
          parentContainer.classList.remove(cardCollapsedClass);
          collapsibleContainer.forEach(function (toggle) {
            new bootstrap.Collapse(toggle, {
              show: true
            });
          });
        } else {
          parentContainer.classList.add(cardCollapsedClass);
          collapsibleContainer.forEach(function (toggle) {
            new bootstrap.Collapse(toggle, {
              hide: true
            });
          });
        }
      });
    });
  };

  const cardActionRemove = function () {

    const buttonClass = '[data-card-action=remove]',
      containerClass = 'card'

    document.querySelectorAll(buttonClass).forEach(function (button) {
      button.addEventListener('click', function (e) {
        e.preventDefault();
        button.closest(`.${containerClass}`).remove();
      });
    });
  };

  const cardActionFullscreen = function () {

    const buttonAttribute = '[data-card-action=fullscreen]',
      buttonClass = 'text-body',
      buttonContainerClass = 'd-inline-flex',
      cardFullscreenClass = 'card-fullscreen',
      collapsedClass = 'collapsed-in-fullscreen',
      scrollableContainerClass = 'content-inner',
      fullscreenAttr = 'data-fullscreen';

    document.querySelectorAll(buttonAttribute).forEach(function (button) {
      button.addEventListener('click', function (e) {
        e.preventDefault();

        const cardFullscreen = button.closest('.card');

        cardFullscreen.classList.toggle(cardFullscreenClass);

        if (!cardFullscreen.classList.contains(cardFullscreenClass)) {
          button.removeAttribute(fullscreenAttr);
          cardFullscreen.querySelectorAll(`:scope > .${collapsedClass}`).forEach(function (collapsedElement) {
            collapsedElement.classList.remove('show');
          });
          document.querySelector(`.${scrollableContainerClass}`).classList.remove('overflow-hidden');
          button.closest(`.${buttonContainerClass}`).querySelectorAll(`:scope > .${buttonClass}:not(${buttonAttribute})`).forEach(function (actions) {
            actions.classList.remove('d-none');
          });
        } else {
          button.setAttribute(fullscreenAttr, 'active');
          cardFullscreen.removeAttribute('style');
          cardFullscreen.querySelectorAll(`:scope > .collapse:not(.show)`).forEach(function (collapsedElement) {
            collapsedElement.classList.add('show', `.${collapsedClass}`);
          });
          document.querySelector(`.${scrollableContainerClass}`).classList.add('overflow-hidden');
          button.closest(`.${buttonContainerClass}`).querySelectorAll(`:scope > .${buttonClass}:not(${buttonAttribute})`).forEach(function (actions) {
            actions.classList.add('d-none');
          });
        }
      });
    });
  };

  const dropdownSubmenu = function () {

    const menuClass = 'dropdown-menu',
      submenuClass = 'dropdown-submenu',
      menuToggleClass = 'dropdown-toggle',
      disabledClass = 'disabled',
      showClass = 'show';

    if (submenuClass) {

      document.querySelectorAll(`.${menuClass} .${submenuClass}:not(.${disabledClass}) .${menuToggleClass}`).forEach(function (link) {
        link.addEventListener('click', function (e) {
          e.stopPropagation();
          e.preventDefault();

          link.closest(`.${submenuClass}`).classList.toggle(showClass);
          link.closest(`.${submenuClass}`).querySelectorAll(`:scope > .${menuClass}`).forEach(function (children) {
            children.classList.toggle(showClass);
          });

          for (let sibling of link.parentNode.parentNode.children) {
            if (sibling != link.parentNode) {
              sibling.classList.remove(showClass);
              sibling.querySelectorAll(`.${showClass}`).forEach(function (submenu) {
                submenu.classList.remove(showClass);
              });
            }
          }
        });
      });

      document.querySelectorAll(`.${menuClass}`).forEach(function (link) {
        if (!link.parentElement.classList.contains(submenuClass)) {
          link.parentElement.addEventListener('hidden.bs.dropdown', function (e) {
            link.querySelectorAll(`.${menuClass}.${showClass}`).forEach(function (children) {
              children.classList.remove(showClass);
            });
          });
        }
      });
    }
  };

  const formTabEnter = function () {

    $("form").each(function (index) {
      var _parent = $(this);
      if (_parent.data('tabenter') == true) {
        return true;
      }

      _parent.data('tabenter', true);
      var strSearch = 'input:not([readonly]), select:not([readonly])'
      _children = _parent.find(strSearch);
      var tot_idx = _children.length;
      _children.each(function (indexChild) {
        $(this).keydown(function (e) {
          if (e.keyCode === 13 && $(this).hasClass('btn') != true) {
            var next_idx = indexChild + 1;
            if (e.shiftKey) {
              var next_idx = indexChild - 1;
            }
            e.preventDefault(e);
            if (tot_idx === next_idx)
              _parent.find(strSearch).eq(0).focus();
            else {
              _parent.find(strSearch).eq(next_idx).focus();
            }
          }
        });
      });
    });
  }
  return {

    initBeforeLoad: function () {
      detectOS();
      transitionsDisabled();
    },

    initAfterLoad: function () {
      transitionsEnabled();
      loadingOnCallLinkPage();
    },

    initComponents: function () {
      componentTooltip();
      componentPopover();
      componentToTopButton();
      formTabEnter();

      $("input.uppercase").on("input", function () {
        var start = $(this)[0].selectionStart;
        $(this).val(function (_, val) {
          return val.toUpperCase();
        });
        $(this)[0].selectionStart = $(this)[0].selectionEnd = start;
      })
        .val(function (_, val) {
          return val.toUpperCase();
        });

      $("input.lowercase").on("input", function () {
        var start = $(this)[0].selectionStart;
        $(this).val(function (_, val) {
          return val.toLowerCase();
        });
        $(this)[0].selectionStart = $(this)[0].selectionEnd = start;
      }).val(function (_, val) {
        return val.toLowerCase();
      });
    },

    initSidebars: function () {
      sidebarMainResize();
      sidebarMainToggle();
      sidebarSecondaryToggle();
      sidebarRightToggle();
      sidebarComponentToggle();
    },

    initNavigations: function () {
      navigationSidebar();
    },

    initCardActions: function () {
      cardActionReload();
      cardActionCollapse();
      cardActionRemove();
      cardActionFullscreen();
    },

    initDropdowns: function () {
      dropdownSubmenu();
    },

    initModuleMenu: function () {

      $(".module-menu").find(".menu-link").each(function () {
        if ($(this).attr('_address_loaded') == undefined && $(this).attr('href') != '#') {
          $(this).attr('_address_loaded', true);
          $(this).off().address();
        }
      });

      $.address.state(BASE_URL).change(function (event) {
        if (addressModelMenuConfigured == false) {
          addressModelMenuConfigured = true;
          return;
        }

        App._loadPage({
          "container": $("#contents"),
          "url": `/${event.value.substring(1)}`
        });
      });
    },

    initDatatables: function () {
      $.extend(true, $.fn.dataTable.defaults, {
        'columnDefs': [
          {
            'targets': 0,
            'checkboxes': {
              'selectRow': true
            }
          }
        ],
        'select': {
          'style': 'multi'
        },
        paginationType: "input",
        dom: "<'row'<'col-md-24'tr>>" +
          "<'row mx-1 p-0' <'col-md-24 p-0 m-0 border' <'col-md-12 p-0' <'d-inline-block p-1' p> <'d-inline-block pt-2' i>><'col-md-12 p-1 text-right'l>>>",
        language: {
          url: "/assets/js/vendor/tables/datatables/i18n/pt-BR.json"
        },
        rowCallback: function (row, data) {
          $(row).data("data", data);
        },
      });
    },

    systemMessages: function () {
      if ($("#system_messages").length && $("#system_messages").val().trim()) {
        const systemMessages = JSON.parse($("#system_messages").val().trim());
        systemMessages.forEach(function (message) {
          App._showMessage(message.message, message.status);
        })
      }
    },

    _loading: function (status, idControl, element) {
      if (status == true && idControl == undefined) {
        $.LoadingOverlay("show", {
          image: `/assets/images/logo_loading.png`,
          imageAnimation: "2000ms rotate_right",
          imageResizeFactor: 0.5,
        });
      }

      if (status != true && (idControl == undefined || !idControl) && loadingControlQueue.length == 0) {
        $.LoadingOverlay("hide");
      }

      if (status == true && idControl != undefined) {
        if (!loadingControlQueue.includes(idControl)) {
          loadingControlQueue.push(idControl);
        }
        $.LoadingOverlay("show", {
          image: `/assets/images/logo_loading.png`,
          imageAnimation: "2000ms rotate_right",
          imageResizeFactor: 0.5,
        })
      }

      if (
        status != true &&
        idControl != undefined &&
        loadingControlQueue != undefined &&
        loadingControlQueue.includes(idControl)
      ) {
        loadingControlQueue.splice(loadingControlQueue.indexOf(idControl), 1);
        if (loadingControlQueue.length == 0) {
          $.LoadingOverlay("hide");
        }
      }
    },

    _loadAjax: function (options) {
      if (options.showLoading == undefined || options.showLoading != false) {
        App._loading(true, null, (options.elementLoadingOverlay != undefined) ? options.elementLoadingOverlay : null);
      }
      options.data = options.data != undefined ? options.data : {};
      options.method = options.method != undefined ? options.method : "GET";

      $.when(
        $.ajax({
          url: options.url,
          type: options.method,
          data: options.data,
          dataType: options.dataType != "undefined" ? options.dataType : 'JSON',
        })
      )
        .done(async function (response) {
          await App._loading(false);
          if (response.status == 'error_secutiry') {
            _redirect(response.url);
            return;
          }
          if (
            response.messages != undefined &&
            (options.showResponseMessages == undefined || options.showResponseMessages == true)
          ) {
            $.each(response.messages, function (index, value) {
              showMessage(value.type, value.message)
            });
          }
          if (options.onDone != undefined) {
            options.onDone(response);
            return;
          }

        })
        .fail(function (response) {
          App._loading(false);
          if (options.onFail != undefined) {
            options.onFail(response);
          }
        });
    },

    _loadPage: function (options) {
      this._loadAjax({
        url: options.url,
        onDone: async function (response) {
          options.container.html(response);
          App.initAfterLoad();
          App.initComponents();
        }
      });
    },

    _loadPageModal: async function (options) {
      const modal = $(".modal-template").clone();
      modal.removeClass("modal-template");

      if (options.size != undefined) {
        modal.addClass(`modal-${options.size}`);
      }

      modal.find(".modal-title").html(options.title);
      modal.find(".btn-close").on("click", function () {
        modal.modal("hide");
      });

      modal.find(".btn-close").on("focus", function () {
        $(this).blur();
      });

      modal.on('hidden.bs.modal', function () {
        modal.remove();
      });

      modal.on('shown.bs.modal', function () {
        App.initAfterLoad();
        App.initComponents();
      });
      await this._loadAjax({
        url: options.url,
        type: options.method ?? 'POST',
        data: options.data ?? null,
        onDone: async function (response) {
          modal.find(".modal-body").html(response);

          if (modal.find(".modal-body").find(".modal-footer-template").length) {
            modal.find(".modal-footer").append(modal.find(".modal-body").find(".modal-footer-template").removeClass("d-none"));
          }

          if (options.done != undefined) {
            options.done(modal);
          }
          modal.on("shown.bs.modal", function () {
            if (options.afterShow != undefined) {
              options.afterShow($(this));
            }
          });
          await modal.modal({
            backdrop: (options.backdrop_static != undefined && options.backdrop_static) ? "static" : true,
          }).modal("show");


        },
        onFail: async function (response) {
          if (response.responseJSON.message != undefined) {
            App._showMessage(response.responseJSON.message, response.responseJSON.status);
          }
          if (options.fail != undefined) {
            options.fail(response.responseJSON);
          }
        }
      });
    },

    _loadJsFile: async function (scriptName, callback) {
      $.getScript(scriptName)
        .done(async function (script, textStatus) {
          if (callback != undefined) {
            await callback();
          }
        });
    },

    _loadCssFile: async function (scriptName, callback) {
      $.loader(
        {
          'css': [
            {
              name: scriptName,
              src: scriptName,
              dep: []
            },
          ],
          onfinish: async function (total) {
            if (callback != undefined) {
              await callback();
            }
          },
        });
    },

    _showAppMessage(action, type) {
      App._loadAjax({
        showLoading: false,
        url: `/api/app-message?action=${action}`,
        onDone: function (response) {
          App._showMessage(response.message, type);
        }
      })
    },

    _showMessage: function (message, type) {
      new Noty({
        theme: 'mint',
        layout: 'bottomRight',
        text: message,
        type: type,
        timeout: 2500,
      }).show();
    },

    randomstring: function (L) {
      var s = '';
      var randomchar = function () {
        var n = Math.floor(Math.random() * 62);
        if (n < 10) return n; //1-10
        if (n < 36) return String.fromCharCode(n + 55); //A-Z
        return String.fromCharCode(n + 61); //a-z
      }
      while (s.length < L) s += randomchar();
      return s;
    },

    _confirmDelete: async function (done) {
      await Swal.fire({
        buttonsStyling: false,
        customClass: {
          confirmButton: 'btn btn-danger fs-sm px-1 py-0 ',
          cancelButton: 'btn btn-light fs-sm px-1 py-0',
        },
        title: 'Confirmar exclusão',
        html: 'Excluir itens os deixará indisponíveis para uso futuro. <br> <strong>Você confirma esta ação?</strong>',
        icon: 'question',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: '<i class="ph-trash fs-sm align-middle"></i> Confirmar e Excluir',
        stopKeydownPropagation: true,
        keydownListenerCapture: true,
        focusCancel: true,
        allowEnterKey: true,
        allowEscapeKey: true,
      }).then(async function (result) {
        if (result.isConfirmed) {
          done();
        }
      });

    },

    _fetchItems: async function (options) {
      const fetchId = App.randomstring(16);
      if (options.parent !== undefined) {
        let fetchingControl = options.parent.data("fetching") !== undefined ? options.parent.data("fetching") : [];
        fetchingControl.push(fetchId);
        options.parent.data("fetching", fetchingControl);
        options.parent.trigger("fetching");
      }

      options.params = options.params !== undefined ? options.params : {};
      options.params.path = options.service.split("/");
      options.service = options.params.path.pop();
      options.params.path = options.params.path.length ? options.params.path.join("/") : '';
      options.params.method = options.method !== undefined ? options.method : null;
      options.params['api_token'] = $(`meta[name="user-token"]`).attr("content");
      options.params = $.param(options.params);

      const data = await axios.get(`/api/system/fetch/items/${options.service}?${options.params}`, {
        headers: {
          'X-Requested-With': 'xmlhttprequest'
        }
      })
        .then(function ({ data }) {
          return data;
        })
        .catch(function (error) {
          if (error.response !== undefined && error.response.data.empty_list === true) {
            App._showMessage(`A lista ${options.label} não possui dados, verifique as opções selecionadas`, "info");
            return [];
          }

          if (error.response !== undefined && error.response.data.message !== undefined && error.response.data.message != '') {
            App._showMessage(error.response.data.message, "info");
            return [];
          }
          if (error !== 'Error: Request aborted') {
            App._showMessage(`Não conseguimos retornar os dados para a lista ${options.label}.`, "error");
            return [];
          }

        });

      if (options.parent !== undefined) {
        fetchingControl = options.parent.data("fetching");
        const indexInputFilling = fetchingControl.indexOf(fetchId);
        if (indexInputFilling !== -1) {
          fetchingControl.splice(indexInputFilling, 1);
        }
        options.parent.data("filling", fetchingControl);
        if (fetchingControl.length === 0) {
          options.parent.trigger("fetchcomplete");
        }
      }
      return data;
    },
    _loadingElements: function (flag, elements, zIndex) {
      Object.entries(elements).forEach(function (element) {
        if (flag == true) {
          element[1].LoadingOverlay("show", {
            image: `/assets/images/logo_minimo_civis.png`,
            imageAnimation: "2000ms rotate_right",
            imageResizeFactor: 0.5,
            zIndex: zIndex != undefined ? zIndex : 2147483647,
          });
        }
        if (flag != true) {
          element[1].LoadingOverlay("hide");
        }
      })
    },
    _address: function (objParent) {
      const inputs = {
        "cep": objParent.find(`input[name$="cep"]`),
        "state": objParent.find(`select[name$="state"]`),
        "city": objParent.find(`input[name$="city"]`),
        "neighborhood": objParent.find(`input[name$="neighborhood"]`),
        "street": objParent.find(`input[name$="street"]`),
        "number": objParent.find(`input[name$="number"]`),
      };

      inputs.cep
        .mask('00.000-000')
        .on('keypress', function (e) {
          if (e.which == 13) {
            e.preventDefault();
            $(this).blur();
          }
        })
        .blur(async function () {
          if ($(this).val().length == 10) {
            if ($(this).data("last-value") == $(this).val()) {
              return;
            }
            $(this).data("last-value", $(this).val());

            App._loadingElements(true, inputs);

            const params = {
              'api_token': $(`meta[name="user-token"]`).attr("content")
            };
            const address = await axios.get(`/api/system/address/findbycep/${inputs.cep.val()}?${$.param(params)}`, {
              headers: {
                'X-Requested-With': 'xmlhttprequest'
              }
            })
              .then(function ({ data }) {
                return data.address;
              })
              .catch(function (error) {
                App._showMessage(error.response.data.message, "error");
                return false;
              });

            if (address) {
              inputs.state.val(address.estado.trim()).change();
              inputs.city.val(address.localidade.toUpperCase());
              inputs.neighborhood.val(address.bairro.toUpperCase());
              inputs.street.val(address.logradouro.toUpperCase());
              inputs.number.focus();
            }

            App._loadingElements(false, inputs);
          }
        });
    },
    _contacts: async function (objParent) {
      App._loadJsFile("/assets/js/vendor/jquery-appendgrid/AppendGrid.js", function () {
        App._loadJsFile("/assets/js/helpers/crud-contacts.js", async function () {
          await setCrudContacts(objParent);
        })
      });
    },

    _fileManager: async function (options) {
      App._loadCssFile("/assets/js/vendor/files/file-manager.css", function () {
        App._loadJsFile("/assets/js/vendor/files/file-manager.js", function () {
          App._loadJsFile("/assets/js/vendor/uploaders/dropzone.min.js", function () {
            options.element.fileManager(options);
          });
        });
      });
    },

    _generateSlug: function (str) {
      const prepositions = ["a", "ante", "após", "até", "com", "contra", "da", "de", "do", "desde", "em", "entre", "para", "per", "perante", "por", "sem", "sob", "sobre", "trás"];
      return str.toLowerCase().replace(/ /g, "_").split("_").filter(word => !prepositions.includes(word)).join("_").normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    },

    initCore: function () {
      App.initBeforeLoad();
      App.initSidebars();
      App.initNavigations();
      App.initComponents();
      App.initCardActions();
      App.initDropdowns();
      App.initModuleMenu();
      App.initDatatables();
      App.systemMessages();
    },

    websocket: $.simpleWebSocket({ url: 'ws://127.0.0.1:8889' }).connect(),
  };
}();

document.addEventListener('DOMContentLoaded', function () {
  App.initCore();
});

window.addEventListener('load', function () {
  App.initAfterLoad();
  // App.websocket.send({ "user_token": $(`meta[name="user-token"]`).attr("content") });
  // App.websocket.listen(function (data) {
  //   if (data['logout'] != undefined) {
  //     location.reload();
  //   }
  // });
});


function uuidv4() {
  return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
  );
}