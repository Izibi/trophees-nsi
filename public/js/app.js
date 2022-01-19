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

  function callAction(action, method) {
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

  function redirect() {
    var url = selection.el.data('redirect-url');

    if (url) {
      location.href = url;
    }
  }

  buttons.each(function () {
    var btn = $(this);
    btn.on('click', function () {
      var confirmation = btn.data('confirmation');

      if (!confirmation || confirm(confirmation)) {
        var method = btn.data('method');

        if (method == 'REDIRECT') {
          redirect();
        } else {
          callAction(btn.data('action'), btn.data('method'));
        }
      }
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
window.tinymce = __webpack_require__(/*! tinymce */ "./node_modules/tinymce/tinymce.js");

__webpack_require__(/*! tinymce/themes/silver */ "./node_modules/tinymce/themes/silver/index.js");

__webpack_require__(/*! tinymce/icons/default */ "./node_modules/tinymce/icons/default/index.js");

__webpack_require__(/*! tinymce/skins/ui/oxide/skin.css */ "./node_modules/tinymce/skins/ui/oxide/skin.css"); //require('tinymce/plugins/advlist');


__webpack_require__(/*! tinymce/plugins/code */ "./node_modules/tinymce/plugins/code/index.js");

__webpack_require__(/*! tinymce/plugins/emoticons */ "./node_modules/tinymce/plugins/emoticons/index.js");

__webpack_require__(/*! tinymce/plugins/emoticons/js/emojis */ "./node_modules/tinymce/plugins/emoticons/js/emojis.js");

__webpack_require__(/*! tinymce/plugins/link */ "./node_modules/tinymce/plugins/link/index.js");

__webpack_require__(/*! tinymce/plugins/lists */ "./node_modules/tinymce/plugins/lists/index.js");

__webpack_require__(/*! tinymce/plugins/table */ "./node_modules/tinymce/plugins/table/index.js");

__webpack_require__(/*! bootstrap */ "./node_modules/bootstrap/dist/js/bootstrap.js");

__webpack_require__(/*! ./UI/overlay.js */ "./resources/js/UI/overlay.js");

__webpack_require__(/*! ./UI/active-tables.js */ "./resources/js/UI/active-tables.js");

__webpack_require__(/*! ./UI/schools-manager.js */ "./resources/js/UI/schools-manager.js");

__webpack_require__(/*! ./UI/region-selector.js */ "./resources/js/UI/region-selector.js");

/***/ }),

/***/ "./node_modules/css-loader/dist/runtime/api.js":
/*!*****************************************************!*\
  !*** ./node_modules/css-loader/dist/runtime/api.js ***!
  \*****************************************************/
/***/ ((module) => {

"use strict";


/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
// eslint-disable-next-line func-names
module.exports = function (cssWithMappingToString) {
  var list = []; // return the list of modules as css string

  list.toString = function toString() {
    return this.map(function (item) {
      var content = cssWithMappingToString(item);

      if (item[2]) {
        return "@media ".concat(item[2], " {").concat(content, "}");
      }

      return content;
    }).join("");
  }; // import a list of modules into the list
  // eslint-disable-next-line func-names


  list.i = function (modules, mediaQuery, dedupe) {
    if (typeof modules === "string") {
      // eslint-disable-next-line no-param-reassign
      modules = [[null, modules, ""]];
    }

    var alreadyImportedModules = {};

    if (dedupe) {
      for (var i = 0; i < this.length; i++) {
        // eslint-disable-next-line prefer-destructuring
        var id = this[i][0];

        if (id != null) {
          alreadyImportedModules[id] = true;
        }
      }
    }

    for (var _i = 0; _i < modules.length; _i++) {
      var item = [].concat(modules[_i]);

      if (dedupe && alreadyImportedModules[item[0]]) {
        // eslint-disable-next-line no-continue
        continue;
      }

      if (mediaQuery) {
        if (!item[2]) {
          item[2] = mediaQuery;
        } else {
          item[2] = "".concat(mediaQuery, " and ").concat(item[2]);
        }
      }

      list.push(item);
    }
  };

  return list;
};

/***/ }),

/***/ "./resources/css/app.scss":
/*!********************************!*\
  !*** ./resources/css/app.scss ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js":
/*!****************************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var isOldIE = function isOldIE() {
  var memo;
  return function memorize() {
    if (typeof memo === 'undefined') {
      // Test for IE <= 9 as proposed by Browserhacks
      // @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
      // Tests for existence of standard globals is to allow style-loader
      // to operate correctly into non-standard environments
      // @see https://github.com/webpack-contrib/style-loader/issues/177
      memo = Boolean(window && document && document.all && !window.atob);
    }

    return memo;
  };
}();

var getTarget = function getTarget() {
  var memo = {};
  return function memorize(target) {
    if (typeof memo[target] === 'undefined') {
      var styleTarget = document.querySelector(target); // Special case to return head of iframe instead of iframe itself

      if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
        try {
          // This will throw an exception if access to iframe is blocked
          // due to cross-origin restrictions
          styleTarget = styleTarget.contentDocument.head;
        } catch (e) {
          // istanbul ignore next
          styleTarget = null;
        }
      }

      memo[target] = styleTarget;
    }

    return memo[target];
  };
}();

var stylesInDom = [];

function getIndexByIdentifier(identifier) {
  var result = -1;

  for (var i = 0; i < stylesInDom.length; i++) {
    if (stylesInDom[i].identifier === identifier) {
      result = i;
      break;
    }
  }

  return result;
}

function modulesToDom(list, options) {
  var idCountMap = {};
  var identifiers = [];

  for (var i = 0; i < list.length; i++) {
    var item = list[i];
    var id = options.base ? item[0] + options.base : item[0];
    var count = idCountMap[id] || 0;
    var identifier = "".concat(id, " ").concat(count);
    idCountMap[id] = count + 1;
    var index = getIndexByIdentifier(identifier);
    var obj = {
      css: item[1],
      media: item[2],
      sourceMap: item[3]
    };

    if (index !== -1) {
      stylesInDom[index].references++;
      stylesInDom[index].updater(obj);
    } else {
      stylesInDom.push({
        identifier: identifier,
        updater: addStyle(obj, options),
        references: 1
      });
    }

    identifiers.push(identifier);
  }

  return identifiers;
}

function insertStyleElement(options) {
  var style = document.createElement('style');
  var attributes = options.attributes || {};

  if (typeof attributes.nonce === 'undefined') {
    var nonce =  true ? __webpack_require__.nc : 0;

    if (nonce) {
      attributes.nonce = nonce;
    }
  }

  Object.keys(attributes).forEach(function (key) {
    style.setAttribute(key, attributes[key]);
  });

  if (typeof options.insert === 'function') {
    options.insert(style);
  } else {
    var target = getTarget(options.insert || 'head');

    if (!target) {
      throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
    }

    target.appendChild(style);
  }

  return style;
}

function removeStyleElement(style) {
  // istanbul ignore if
  if (style.parentNode === null) {
    return false;
  }

  style.parentNode.removeChild(style);
}
/* istanbul ignore next  */


var replaceText = function replaceText() {
  var textStore = [];
  return function replace(index, replacement) {
    textStore[index] = replacement;
    return textStore.filter(Boolean).join('\n');
  };
}();

function applyToSingletonTag(style, index, remove, obj) {
  var css = remove ? '' : obj.media ? "@media ".concat(obj.media, " {").concat(obj.css, "}") : obj.css; // For old IE

  /* istanbul ignore if  */

  if (style.styleSheet) {
    style.styleSheet.cssText = replaceText(index, css);
  } else {
    var cssNode = document.createTextNode(css);
    var childNodes = style.childNodes;

    if (childNodes[index]) {
      style.removeChild(childNodes[index]);
    }

    if (childNodes.length) {
      style.insertBefore(cssNode, childNodes[index]);
    } else {
      style.appendChild(cssNode);
    }
  }
}

function applyToTag(style, options, obj) {
  var css = obj.css;
  var media = obj.media;
  var sourceMap = obj.sourceMap;

  if (media) {
    style.setAttribute('media', media);
  } else {
    style.removeAttribute('media');
  }

  if (sourceMap && typeof btoa !== 'undefined') {
    css += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))), " */");
  } // For old IE

  /* istanbul ignore if  */


  if (style.styleSheet) {
    style.styleSheet.cssText = css;
  } else {
    while (style.firstChild) {
      style.removeChild(style.firstChild);
    }

    style.appendChild(document.createTextNode(css));
  }
}

var singleton = null;
var singletonCounter = 0;

function addStyle(obj, options) {
  var style;
  var update;
  var remove;

  if (options.singleton) {
    var styleIndex = singletonCounter++;
    style = singleton || (singleton = insertStyleElement(options));
    update = applyToSingletonTag.bind(null, style, styleIndex, false);
    remove = applyToSingletonTag.bind(null, style, styleIndex, true);
  } else {
    style = insertStyleElement(options);
    update = applyToTag.bind(null, style, options);

    remove = function remove() {
      removeStyleElement(style);
    };
  }

  update(obj);
  return function updateStyle(newObj) {
    if (newObj) {
      if (newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap) {
        return;
      }

      update(obj = newObj);
    } else {
      remove();
    }
  };
}

module.exports = function (list, options) {
  options = options || {}; // Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
  // tags it will allow on a page

  if (!options.singleton && typeof options.singleton !== 'boolean') {
    options.singleton = isOldIE();
  }

  list = list || [];
  var lastIdentifiers = modulesToDom(list, options);
  return function update(newList) {
    newList = newList || [];

    if (Object.prototype.toString.call(newList) !== '[object Array]') {
      return;
    }

    for (var i = 0; i < lastIdentifiers.length; i++) {
      var identifier = lastIdentifiers[i];
      var index = getIndexByIdentifier(identifier);
      stylesInDom[index].references--;
    }

    var newLastIdentifiers = modulesToDom(newList, options);

    for (var _i = 0; _i < lastIdentifiers.length; _i++) {
      var _identifier = lastIdentifiers[_i];

      var _index = getIndexByIdentifier(_identifier);

      if (stylesInDom[_index].references === 0) {
        stylesInDom[_index].updater();

        stylesInDom.splice(_index, 1);
      }
    }

    lastIdentifiers = newLastIdentifiers;
  };
};

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["js/vendor","css/app"], () => (__webpack_exec__("./resources/js/app.js"), __webpack_exec__("./resources/css/app.scss")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);