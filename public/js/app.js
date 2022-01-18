(self["webpackChunk"] = self["webpackChunk"] || []).push([["/js/app"],{

/***/ "./resources/js/UI/active-tables.js":
/*!******************************************!*\
  !*** ./resources/js/UI/active-tables.js ***!
  \******************************************/
/***/ (() => {

function init() {
  var selection = null;
  var buttons = $('.active-button'); // tables

  function select(tr) {
    var id = tr.data('row-id');

    if (isNaN(id)) {
      return;
    }

    if (selection) {
      selection.el.removeClass('active-row');
    }

    selection = {
      id: id,
      el: tr
    };
    tr.addClass('active-row');
    refreshButtons();
    var disabled_actions = tr.data('actions-disabled');

    if (typeof disabled_actions !== 'undefined') {
      disabled_actions = disabled_actions.split(',');
      buttons.each(function () {
        var btn = $(this);
        var action = btn.data('action-name');
        btn.prop('disabled', action && disabled_actions.indexOf(action) !== -1);
      });
    }
  }

  $('table.active-table tr').each(function () {
    var tr = $(this);
    tr.on('click', function () {
      select(tr);
    });
  }); // buttons

  function refreshButtons() {
    buttons.each(function () {
      var btn = $(this);
      var action = btn.data('action');
      var require_selection = action.indexOf(':id') !== -1;
      $(this).prop('disabled', require_selection && !selection);
    });
  }

  refreshButtons();

  function redirect(action, method) {
    //console.log(action, method, selection)
    if (action.indexOf(':id') !== -1) {
      if (!selection) {
        return;
      }

      action = action.replace(':id', selection.id);
    }

    var form = $('<form>');
    form.attr('action', action);
    method = method || 'GET';
    form.attr('method', method);
    var hidden = $('<input type="hidden" name="refer_page"/>');
    hidden.val(location.href);
    form.append(hidden);

    if (method == 'POST') {
      var hidden = $('<input type="hidden" name="_token"/>');
      hidden.val($('meta[name="csrf-token"]').attr('content'));
      form.append(hidden);
    }

    $(document.body).append(form);
    form.submit();
  }

  buttons.each(function () {
    var btn = $(this);
    btn.on('click', function () {
      redirect(btn.data('action'), btn.data('method'));
    });
  });
}

$(document).ready(init);

/***/ }),

/***/ "./resources/js/UI/overlay.js":
/*!************************************!*\
  !*** ./resources/js/UI/overlay.js ***!
  \************************************/
/***/ (() => {

window.overlay = {
  el: false,
  render: function render() {
    if (this.el) {
      return;
    }

    this.el = $('<div class="screen-overlay"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>');
    $(document.body).append(this.el);
  },
  show: function show() {
    this.render();
    this.el.show();
  },
  hide: function hide() {
    this.el.hide();
  }
};

/***/ }),

/***/ "./resources/js/UI/region-selector.js":
/*!********************************************!*\
  !*** ./resources/js/UI/region-selector.js ***!
  \********************************************/
/***/ (() => {

// reqire window.regions and window.countries arrays
window.RegionSelector = function (form) {
  var region_sel = form.find('select[name=region_id]').first();
  var country_sel = form.find('select[name=country_id]').first();
  var main_country_id = window.regions.find(function (r) {
    return r.country_id !== null;
  }).id;

  function getRegion(id) {
    return window.regions.find(function (r) {
      return r.id == id;
    });
  }

  function onRegionChange(initial) {
    var region_id = region_sel.val();
    var region = getRegion(region_id);
    var is_main_country = region && region.country_id !== null;

    if (!initial && region) {
      country_sel.val(is_main_country ? region.country_id : '');
    }

    country_sel.find('option[value="' + main_country_id + '"]').prop('disabled', !is_main_country);
    country_sel.attr('readonly', is_main_country);
    country_sel.closest('.form-group').toggle(region && !is_main_country);
  }

  region_sel.on('change', function () {
    onRegionChange();
  });
  onRegionChange(true);
  return {
    reset: function reset() {
      if (!window.regions.length) {
        return;
      }

      var r = window.regions[0];
      region_sel.val(r.id);
      onRegionChange();
    }
  };
};

/***/ }),

/***/ "./resources/js/UI/schools-manager.js":
/*!********************************************!*\
  !*** ./resources/js/UI/schools-manager.js ***!
  \********************************************/
/***/ (() => {

function formatSchoolName(school) {
  var res = school.name + ', ' + school.zip + ' ' + school.city + ', ';

  if (school.region.country_id !== null) {
    res += school.region.name + ', ';
  }

  res += school.country.name;
  return res;
}

function UserSchools() {
  var selection = null;

  if (!('user_schools' in window)) {
    window.user_schools = [];
  }

  function render() {
    var el = $('#schools-my');
    el.empty();

    for (var i = 0; i < window.user_schools.length; i++) {
      el.append('<div class="list-item" data-school-id="' + window.user_schools[i].id + '">' + formatSchoolName(window.user_schools[i]) + '</div>');
    }

    el.find('div').on('click', function () {
      el.find('div.list-item-selected').removeClass('list-item-selected');
      $(this).addClass('list-item-selected');
      var id = $(this).data('school-id');
      select(id);
    });
  }

  function select(id) {
    selection = id;
    $('#btn-school-use').prop('disabled', selection === null);
    $('#btn-school-delete').prop('disabled', selection === null);
  }

  function _refreshParentSelect() {
    var el = $('#edit-form select[name=school_id]');
    el.empty();
    var option;

    for (var i = 0; i < window.user_schools.length; i++) {
      option = $('<option/>').attr('value', window.user_schools[i].id).text(formatSchoolName(window.user_schools[i]));
      el.append(option);
    }

    el.val(selection);
  }

  function doRequest(id, action) {
    select(null);
    overlay.show();
    $.ajax({
      dataType: 'json',
      url: '/user_schools/' + action,
      method: 'POST',
      data: {
        id: id
      },
      success: function success(data) {
        overlay.hide();
        window.user_schools = data;

        _refreshParentSelect();

        render();
      }
    });
  }

  return {
    refresh: function refresh() {
      render();
      select(null);
    },
    setSchools: function setSchools(schools) {
      window.user_schools = schools;
      render();
      select(null);
    },
    refreshParentSelect: function refreshParentSelect() {
      if (selection !== null) {
        _refreshParentSelect();
      }
    },
    addSchool: function addSchool(id) {
      doRequest(id, 'add');
    },
    removeSeleted: function removeSeleted() {
      if (selection !== null) {
        doRequest(selection, 'remove');
      }
    }
  };
}

function SearchSchool() {
  var selection = null;
  var list = [];
  $('#inp-school-search-q').val('');

  function select(id) {
    selection = id;
    $('#btn-school-add').prop('disabled', selection === null);
  }

  select(null);

  function render() {
    var el = $('#schools-search');
    el.empty();

    if (!list.length) {
      el.text('Your search did not match any schools.');
      return;
    }

    for (var i = 0; i < list.length; i++) {
      el.append('<div class="list-item" data-school-id="' + list[i].id + '">' + formatSchoolName(list[i]) + '</div>');
    }

    el.find('div').on('click', function () {
      el.find('div.list-item-selected').removeClass('list-item-selected');
      $(this).addClass('list-item-selected');
      var id = $(this).data('school-id');
      select(id);
    });
  }

  function load(q) {
    select(null);
    overlay.show();
    $.ajax({
      dataType: 'json',
      url: '/user_schools/search',
      data: {
        q: q
      },
      success: function success(data) {
        overlay.hide();
        list = data;
        render();
      }
    });
  }

  return {
    search: function search(q) {
      $('#schools-search').empty();
      load(q);
    },
    getSelection: function getSelection() {
      return selection;
    }
  };
}

function FormSchool(options) {
  var groups = $('#section-schools-editor .form-group');

  function showGroupError(form_group, message) {
    form_group.find('.form-control').addClass('is-invalid');
    form_group.find('.invalid-feedback').remove();
    form_group.append('<div class="invalid-feedback">' + message + '</div>');
  }

  function hideGroupError(form_group) {
    form_group.find('.invalid-feedback').remove();
    form_group.find('.form-control').removeClass('is-invalid');
  }

  function displayErrors(errors) {
    groups.each(function () {
      var group = $(this);
      var control = group.find('.form-control');
      var name = control.attr('name');

      if (name in errors) {
        showGroupError(group, errors[name][0]);
      } else {
        hideGroupError(group);
      }
    });
  }

  function resetErrors() {
    groups.each(function () {
      var group = $(this);
      group.find('.invalid-feedback').remove();
      group.find('.form-control').removeClass('is-invalid');
    });
  }

  function resetValues() {
    groups.each(function () {
      var group = $(this);
      var control = group.find('.form-control');
      control.val('');
    });
  }

  function _reset() {
    resetErrors();
    resetValues();
    region_selector.reset();
  }

  function collectFormData() {
    var res = {};
    groups.each(function () {
      var group = $(this);
      var control = group.find('.form-control');
      var name = control.attr('name');
      res[name] = control.val();
    });
    return res;
  }

  function sendRequest(data) {
    overlay.show();
    $.ajax({
      dataType: 'json',
      url: '/user_schools/create',
      data: data,
      method: 'POST',
      success: function success(data) {
        overlay.hide();

        if (data.success) {
          _reset();

          options.onCreate(data.schools);
        }
      },
      error: function error(xhr, status, _error) {
        var res = xhr.responseJSON;

        if (res.errors) {
          overlay.hide();
          displayErrors(res.errors);
        }
      }
    });
  }

  var region_selector = RegionSelector($('#section-schools-editor'));
  /*
      var region_sel = $('#section-schools-editor select[name=region_id]').first();
      var country_sel = $('#section-schools-editor select[name=country_id]').first();
      function refreshRegions() {
          region_sel.empty();
          var country_id = country_sel.val();
          var first_region_id = null;
          var option;
          for(var i=0; i<regions.length; i++) {
              if(regions[i].country_id != country_id) {
                  continue;
              }
              if(first_region_id === null) {
                  first_region_id = regions[i].id;
              }
              option = $('<option/>').attr('value', regions[i].id).text(regions[i].name);
              region_sel.append(option);
          }
          region_sel.val(first_region_id);
      }
      country_sel.on('change', refreshRegions);
      refreshRegions();
  */

  return {
    reset: function reset() {
      _reset();
    },
    submit: function submit() {
      var data = collectFormData();
      sendRequest(data);
    }
  };
}

window.SchoolsManager = function () {
  var modal_el = $('#schools-manager-modal');
  var user_schools = UserSchools();
  var search_school = SearchSchool();
  var form_school = FormSchool({
    onCreate: function onCreate(schools) {
      $('#section-schools-editor').hide();
      user_schools.setSchools(schools);
      $('#section-schools-manager').show();
    }
  }); // schools manager section

  $('#btn-school-use').on('click', function () {
    user_schools.refreshParentSelect();
    modal_el.modal('hide');
  });
  $('#btn-school-search').on('click', function () {
    var q = $('#inp-school-search-q').val().trim();

    if (q.length > 0) {
      search_school.search(q);
    }
  });
  $('#btn-school-add').on('click', function () {
    var id = search_school.getSelection();
    user_schools.addSchool(id);
  });
  $('#btn-school-delete').on('click', function () {
    user_schools.removeSeleted();
  }); // create school editor section

  $('#btn-school-show-create').on('click', function () {
    form_school.reset();
    $('#section-schools-manager').hide();
    $('#section-schools-editor').show();
  });
  $('#btn-school-create-cancel').on('click', function () {
    $('#section-schools-manager').show();
    $('#section-schools-editor').hide();
  });
  $('#btn-school-create').on('click', function () {
    form_school.submit();
  });
  return {
    show: function show() {
      modal_el.modal('show');
      user_schools.refresh();
    },
    hide: function hide() {
      modal_el.modal('hide');
    }
  };
};

/***/ }),

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

window.$ = window.jQuery = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
window.Popper = (__webpack_require__(/*! popper.js */ "./node_modules/popper.js/dist/esm/popper.js")["default"]);

__webpack_require__(/*! bootstrap */ "./node_modules/bootstrap/dist/js/bootstrap.js");

__webpack_require__(/*! ./UI/overlay.js */ "./resources/js/UI/overlay.js");

__webpack_require__(/*! ./UI/active-tables.js */ "./resources/js/UI/active-tables.js");

__webpack_require__(/*! ./UI/schools-manager.js */ "./resources/js/UI/schools-manager.js");

__webpack_require__(/*! ./UI/region-selector.js */ "./resources/js/UI/region-selector.js");

/***/ }),

/***/ "./resources/css/app.scss":
/*!********************************!*\
  !*** ./resources/css/app.scss ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["css/app","js/vendor"], () => (__webpack_exec__("./resources/js/app.js"), __webpack_exec__("./resources/css/app.scss")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);