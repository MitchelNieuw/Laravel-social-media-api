(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[1],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/UserMessages.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/UserMessages.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'UserMessages',
  data: function data() {
    return {
      messages: [],
      response: '',
      loading: false
    };
  },
  methods: {
    removeFromMessages: function removeFromMessages(index) {
      this.messages.splice(index, 1);
    },
    getMessages: function () {
      var _getMessages = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        var _this = this;

        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                this.$root.isLoading = true;
                _context.next = 3;
                return axios.get('https://localhost/api/v1/user/messages', {
                  headers: {
                    Authorization: 'Bearer ' + JWTToken,
                    Accept: 'application/json'
                  }
                }).then(function (response) {
                  _this.messages = response.data.data;
                })["catch"](function (error) {
                  console.log(error);
                });

              case 3:
                this.$root.isLoading = false;

              case 4:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function getMessages() {
        return _getMessages.apply(this, arguments);
      }

      return getMessages;
    }(),
    deleteMessage: function deleteMessage(message, index) {
      var _this2 = this;

      axios["delete"]('https://localhost/api/v1/message/' + message.id + '/delete', {
        headers: {
          Authorization: 'Bearer ' + JWTToken,
          Accept: 'application/json',
          'Content-type': 'application/x-www-form-urlencoded'
        },
        _method: 'delete'
      }).then(function () {
        _this2.removeFromMessages(index);
      })["catch"](function () {});
    }
  },
  created: function created() {
    this.getMessages();
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/UserMessages.vue?vue&type=template&id=1de252bc&":
/*!***************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/UserMessages.vue?vue&type=template&id=1de252bc& ***!
  \***************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    _vm._l(_vm.messages, function(message, index) {
      return _vm.messages
        ? _c("div", { key: index, staticClass: "row" }, [
            _c("div", { staticClass: "col-md-6 mx-auto m-2" }, [
              _c("div", { staticClass: "card" }, [
                _c("div", { staticClass: "card-body" }, [
                  _c("p", {
                    staticClass: "mb-0 d-inline-block",
                    domProps: { textContent: _vm._s(message.content) }
                  }),
                  _vm._v(" "),
                  _c(
                    "form",
                    {
                      staticClass: "d-inline-block float-right",
                      on: {
                        submit: function($event) {
                          $event.preventDefault()
                          return _vm.deleteMessage(message, index)
                        }
                      }
                    },
                    [_vm._m(0, true)]
                  )
                ])
              ])
            ])
          ])
        : _vm._e()
    }),
    0
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c(
      "button",
      { staticClass: "close text-danger pt-0", attrs: { type: "submit" } },
      [_c("span", [_vm._v("×")])]
    )
  }
]
render._withStripped = true



/***/ }),

/***/ "./resources/js/components/UserMessages.vue":
/*!**************************************************!*\
  !*** ./resources/js/components/UserMessages.vue ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _UserMessages_vue_vue_type_template_id_1de252bc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./UserMessages.vue?vue&type=template&id=1de252bc& */ "./resources/js/components/UserMessages.vue?vue&type=template&id=1de252bc&");
/* harmony import */ var _UserMessages_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./UserMessages.vue?vue&type=script&lang=js& */ "./resources/js/components/UserMessages.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _UserMessages_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _UserMessages_vue_vue_type_template_id_1de252bc___WEBPACK_IMPORTED_MODULE_0__["render"],
  _UserMessages_vue_vue_type_template_id_1de252bc___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/UserMessages.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/UserMessages.vue?vue&type=script&lang=js&":
/*!***************************************************************************!*\
  !*** ./resources/js/components/UserMessages.vue?vue&type=script&lang=js& ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserMessages_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./UserMessages.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/UserMessages.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserMessages_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/UserMessages.vue?vue&type=template&id=1de252bc&":
/*!*********************************************************************************!*\
  !*** ./resources/js/components/UserMessages.vue?vue&type=template&id=1de252bc& ***!
  \*********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_UserMessages_vue_vue_type_template_id_1de252bc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./UserMessages.vue?vue&type=template&id=1de252bc& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/UserMessages.vue?vue&type=template&id=1de252bc&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_UserMessages_vue_vue_type_template_id_1de252bc___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_UserMessages_vue_vue_type_template_id_1de252bc___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);