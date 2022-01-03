(self["webpackChunk"] = self["webpackChunk"] || []).push([["/js/app"],{

/***/ "./resources/js/UI/active-tables.js":
/*!******************************************!*\
  !*** ./resources/js/UI/active-tables.js ***!
  \******************************************/
/***/ (() => {

function init() {
  var selection = null; // tables

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
  }

  $('table.active-table>tbody>tr').each(function () {
    var tr = $(this);
    tr.on('click', function () {
      select(tr);
    });
  }); // buttons

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

  $('button.active-button').each(function () {
    var btn = $(this);
    btn.on('click', function () {
      redirect(btn.attr('action'), btn.attr('method'));
    });
  });
}

$(document).ready(init);

/***/ }),

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

window.$ = window.jQuery = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
window.Popper = (__webpack_require__(/*! popper.js */ "./node_modules/popper.js/dist/esm/popper.js")["default"]);

__webpack_require__(/*! bootstrap */ "./node_modules/bootstrap/dist/js/bootstrap.js");

__webpack_require__(/*! ./UI/active-tables.js */ "./resources/js/UI/active-tables.js");

__webpack_require__(/*! ./document-ready.js */ "./resources/js/document-ready.js");

/***/ }),

/***/ "./resources/js/document-ready.js":
/*!****************************************!*\
  !*** ./resources/js/document-ready.js ***!
  \****************************************/
/***/ (() => {

$(document).ready(function () {
  console.log('doc.ready');
});

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
/******/ __webpack_require__.O(0, ["js/vendor","css/app"], () => (__webpack_exec__("./resources/js/app.js"), __webpack_exec__("./resources/css/app.scss")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);