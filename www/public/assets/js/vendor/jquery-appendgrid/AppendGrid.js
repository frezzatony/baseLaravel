! function (e, t) {
  "object" == typeof exports && "object" == typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define([], t) : "object" == typeof exports ? exports.AppendGrid = t() : e.AppendGrid = t()
}(self, (function () {
  return (() => {
    "use strict";
    var e = {
      d: (t, n) => {
        for (var o in n) e.o(n, o) && !e.o(t, o) && Object.defineProperty(t, o, {
          enumerable: !0,
          get: n[o]
        })
      },
      o: (e, t) => Object.prototype.hasOwnProperty.call(e, t)
    },
      t = {};

    function n(e, t) {
      if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
    }

    function o(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }
    e.d(t, {
      default: () => lt
    });
    var r = function () {
      function e(t) {
        var o = arguments.length > 1 && void 0 !== arguments[1] && arguments[1];
        n(this, e), this.name = t, this.icons = {
          append: null,
          removeLast: null,
          insert: null,
          remove: null,
          moveUp: null,
          moveDown: null
        }, this.isTextBased = o
      }
      var t, r, i;
      return t = e, (r = [{
        key: "generateIcon",
        value: function (e, t) {
          throw "*generateIcon* is not overrided for *".concat(this.name, "*.")
        }
      }]) && o(t.prototype, r), i && o(t, i), e
    }();
    const i = r;

    function u(e) {
      return u = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, u(e)
    }

    function l(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function c(e, t) {
      return c = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, c(e, t)
    }

    function a(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = f(e);
        if (t) {
          var r = f(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return s(this, n)
      }
    }

    function s(e, t) {
      return !t || "object" !== u(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function f(e) {
      return f = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, f(e)
    }
    const p = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && c(e, t)
      }(i, e);
      var t, n, o, r = a(i);

      function i(e) {
        var t;
        return function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), t = r.call(this, "icon-default", !0), Object.assign(t.icons, {
          append: "＋",
          removeLast: "－",
          insert: "↜",
          remove: "✕",
          moveUp: "▲",
          moveDown: "▼"
        }), e && Object.assign(t.icons, e), t
      }
      return t = i, (n = [{
        key: "generateIcon",
        value: function (e, t) {
          var n = document.createTextNode(this.icons[t] || "");
          return e.appendChild(n), n
        }
      }]) && l(t.prototype, n), o && l(t, o), i
    }(i);

    function d(e) {
      for (var t = arguments.length, n = new Array(t > 1 ? t - 1 : 0), o = 1; o < t; o++) n[o - 1] = arguments[o];
      n && n.length && n.forEach((function (t) {
        if (t) {
          var n = t.split(/\s+/gi);
          n && n.length && n.forEach((function (t) {
            t && e.classList.add(t)
          }))
        }
      }))
    }

    function y(e) {
      return null == e
    }

    function m(e) {
      return !isNaN(parseFloat(e)) && isFinite(e)
    }

    function h(e) {
      return "[object Object]" === Object.prototype.toString.call(e)
    }

    function v(e) {
      var t = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : null,
        n = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : null,
        o = arguments.length > 3 && void 0 !== arguments[3] ? arguments[3] : null,
        r = arguments.length > 4 && void 0 !== arguments[4] ? arguments[4] : null,
        i = document.createElement(e);
      return t && (i.id = t), n && (i.name = n), o && d(i, o), r && (i.type = r), i
    }

    function b(e) {
      return b = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, b(e)
    }

    function w(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function g(e, t) {
      return g = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, g(e, t)
    }

    function O(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = R(e);
        if (t) {
          var r = R(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return C(this, n)
      }
    }

    function C(e, t) {
      return !t || "object" !== b(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function R(e) {
      return R = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, R(e)
    }
    const k = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && g(e, t)
      }(i, e);
      var t, n, o, r = O(i);

      function i(e) {
        var t;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), t = r.call(this, "icon-bootstrapicons");
        var n = {
          baseUrl: "",
          icons: null
        };
        Object.assign(n, e);
        var o = {
          append: "plus",
          removeLast: "dash",
          insert: "arrow-90deg-left",
          remove: "trash",
          moveUp: "chevron-up",
          moveDown: "chevron-down"
        };
        return n.icons && Object.assign(o, n.icons), t.icons = o, t.baseUrl = n.baseUrl, t
      }
      return t = i, (n = [{
        key: "generateIcon",
        value: function (e, t) {
          var n = document.createElement("img");
          return n.src = this.baseUrl + this.icons[t] + ".svg", d(n, this.icons[t]), e.appendChild(n), n
        }
      }]) && w(t.prototype, n), o && w(t, o), i
    }(i);

    function P(e) {
      return P = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, P(e)
    }

    function _(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function j(e, t) {
      return j = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, j(e, t)
    }

    function x(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = E(e);
        if (t) {
          var r = E(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return S(this, n)
      }
    }

    function S(e, t) {
      return !t || "object" !== P(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function E(e) {
      return E = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, E(e)
    }
    const B = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && j(e, t)
      }(i, e);
      var t, n, o, r = x(i);

      function i(e) {
        var t;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), t = r.call(this, "icon-fontawesome6");
        var n = {
          icons: null
        };
        Object.assign(n, e);
        var o = {
          append: "fa-solid fa-plus",
          removeLast: "fa-solid fa-minus",
          insert: "fa-solid fa-reply",
          remove: "fa-solid fa-times",
          moveUp: "fa-solid fa-angle-up",
          moveDown: "fa-solid fa-angle-down"
        };
        return n.icons && Object.assign(o, n.icons), t.icons = o, t
      }
      return t = i, (n = [{
        key: "generateIcon",
        value: function (e, t) {
          var n = document.createElement("i");
          return d(n, this.icons[t]), e.appendChild(n), n
        }
      }]) && _(t.prototype, n), o && _(t, o), i
    }(i);

    function I(e) {
      return I = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, I(e)
    }

    function F(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function L(e, t) {
      return L = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, L(e, t)
    }

    function T(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = G(e);
        if (t) {
          var r = G(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return D(this, n)
      }
    }

    function D(e, t) {
      return !t || "object" !== I(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function G(e) {
      return G = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, G(e)
    }
    const U = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && L(e, t)
      }(i, e);
      var t, n, o, r = T(i);

      function i(e) {
        var t;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), t = r.call(this, "icon-fontawesome5");
        var n = {
          icons: null
        };
        Object.assign(n, e);
        var o = {
          append: "fas fa-plus",
          removeLast: "fas fa-minus",
          insert: "fas fa-reply",
          remove: "fas fa-times",
          moveUp: "fas fa-angle-up",
          moveDown: "fas fa-angle-down"
        };
        return n.icons && Object.assign(o, n.icons), t.icons = o, t
      }
      return t = i, (n = [{
        key: "generateIcon",
        value: function (e, t) {
          var n = document.createElement("i");
          return d(n, this.icons[t]), e.appendChild(n), n
        }
      }]) && F(t.prototype, n), o && F(t, o), i
    }(i);

    function A(e) {
      return A = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, A(e)
    }

    function N(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function q(e, t) {
      return q = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, q(e, t)
    }

    function M(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = $(e);
        if (t) {
          var r = $(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return V(this, n)
      }
    }

    function V(e, t) {
      return !t || "object" !== A(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function $(e) {
      return $ = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, $(e)
    }
    const W = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && q(e, t)
      }(i, e);
      var t, n, o, r = M(i);

      function i(e) {
        var t;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), t = r.call(this, "icon-materialdesignicons3");
        var n = {
          icons: null
        };
        Object.assign(n, e);
        var o = {
          append: "mdi mdi-plus",
          removeLast: "mdi mdi-minus",
          insert: "mdi mdi-reply",
          remove: "mdi mdi-close",
          moveUp: "mdi mdi-chevron-up",
          moveDown: "mdi mdi-chevron-down"
        };
        return n.icons && Object.assign(o, n.icons), t.icons = o, t
      }
      return t = i, (n = [{
        key: "generateIcon",
        value: function (e, t) {
          var n = document.createElement("span");
          return d(n, this.icons[t]), e.appendChild(n), n
        }
      }]) && N(t.prototype, n), o && N(t, o), i
    }(i);

    function z(e) {
      return z = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, z(e)
    }

    function H(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function J(e, t) {
      return J = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, J(e, t)
    }

    function K(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = X(e);
        if (t) {
          var r = X(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return Q(this, n)
      }
    }

    function Q(e, t) {
      return !t || "object" !== z(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function X(e) {
      return X = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, X(e)
    }
    const Y = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && J(e, t)
      }(i, e);
      var t, n, o, r = K(i);

      function i(e) {
        var t;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), t = r.call(this, "icon-ionicon4");
        var n = {
          icons: null
        };
        Object.assign(n, e);
        var o = {
          append: "icon ion-md-add",
          removeLast: "icon ion-md-remove",
          insert: "icon ion-md-undo",
          remove: "icon ion-md-close",
          moveUp: "icon ion-md-arrow-dropup",
          moveDown: "icon ion-md-arrow-dropdown"
        };
        return n.icons && Object.assign(o, n.icons), t.icons = o, t
      }
      return t = i, (n = [{
        key: "generateIcon",
        value: function (e, t) {
          var n = document.createElement("i");
          return d(n, this.icons[t]), e.appendChild(n), n
        }
      }]) && H(t.prototype, n), o && H(t, o), i
    }(i);

    function Z(e) {
      return Z = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, Z(e)
    }

    function ee(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function te(e, t) {
      return te = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, te(e, t)
    }

    function ne(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = re(e);
        if (t) {
          var r = re(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return oe(this, n)
      }
    }

    function oe(e, t) {
      return !t || "object" !== Z(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function re(e) {
      return re = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, re(e)
    }
    const ie = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && te(e, t)
      }(i, e);
      var t, n, o, r = ne(i);

      function i(e) {
        var t;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), t = r.call(this, "icon-typicons2");
        var n = {
          icons: null
        };
        Object.assign(n, e);
        var o = {
          append: "typcn typcn-plus",
          removeLast: "typcn typcn-minus",
          insert: "typcn typcn-arrow-back",
          remove: "typcn typcn-times",
          moveUp: "typcn typcn-arrow-sorted-up",
          moveDown: "typcn typcn-arrow-sorted-down"
        };
        return n.icons && Object.assign(o, n.icons), t.icons = o, t
      }
      return t = i, (n = [{
        key: "generateIcon",
        value: function (e, t) {
          var n = document.createElement("span");
          return d(n, this.icons[t]), e.appendChild(n), n
        }
      }]) && ee(t.prototype, n), o && ee(t, o), i
    }(i);

    function ue(e) {
      return ue = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, ue(e)
    }

    function le(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function ce(e, t) {
      return ce = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, ce(e, t)
    }

    function ae(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = fe(e);
        if (t) {
          var r = fe(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return se(this, n)
      }
    }

    function se(e, t) {
      return !t || "object" !== ue(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function fe(e) {
      return fe = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, fe(e)
    }
    const pe = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && ce(e, t)
      }(i, e);
      var t, n, o, r = ae(i);

      function i(e) {
        var t;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), t = r.call(this, "icon-openiconic");
        var n = {
          icons: null
        };
        Object.assign(n, e);
        var o = {
          append: "plus",
          removeLast: "minus",
          insert: "share",
          remove: "x",
          moveUp: "chevron-top",
          moveDown: "chevron-bottom"
        };
        return n.icons && Object.assign(o, n.icons), t.icons = o, t
      }
      return t = i, (n = [{
        key: "generateIcon",
        value: function (e, t) {
          var n = document.createElement("span");
          return n.className = "oi", n.dataset.glyph = this.icons[t], n.setAttribute("aria-hidden", "true"), e.appendChild(n), n
        }
      }]) && le(t.prototype, n), o && le(t, o), i
    }(i);

    function de(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }
    const ye = function () {
      function e(t, n) {
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, e), this.i18n = t, this.iconFramework = n, this.sectionClasses = {
          table: null,
          thead: null,
          theadRow: null,
          theadCell: null,
          tbody: null,
          tbodyRow: null,
          tbodyCell: null,
          tfoot: null,
          tfootRow: null,
          tfootCell: null,
          first: null,
          last: null,
          control: null,
          button: null,
          buttonGroup: null,
          append: null,
          removeLast: null,
          insert: null,
          remove: null,
          moveUp: null,
          moveDown: null,
          empty: null
        }
      }
      var t, n, o;
      return t = e, (n = [{
        key: "applySectionClasses",
        value: function (e) {
          for (var t in this.sectionClasses) e[t] && (this.sectionClasses[t] ? this.sectionClasses[t] += " " + e[t] : this.sectionClasses[t] = e[t])
        }
      }, {
        key: "getSectionClasses",
        value: function (e) {
          return this.sectionClasses[e]
        }
      }, {
        key: "createButtonGroup",
        value: function () {
          return null
        }
      }, {
        key: "generateButton",
        value: function (e, t, n) {
          var o = v("button", n, null, null, "button");
          return o.title = this.i18n[t], d(o, this.getSectionClasses("button"), this.getSectionClasses(t)), e.appendChild(o), this.iconFramework.generateIcon(o, t), o
        }
      }, {
        key: "generateControl",
        value: function (e, t, n, o) {
          var r = null;
          if ("select" === t.type)
            if (r = v("select", n, o), Array.isArray(t.ctrlOptions)) {
              if (t.ctrlOptions.length > 0)
                if (h(t.ctrlOptions[0]))
                  for (var i = null, u = null, l = 0; l < t.ctrlOptions.length; l++) {
                    y(t.ctrlOptions[l].group) ? u = null : i !== t.ctrlOptions[l].group && (i = t.ctrlOptions[l].group, (u = v("optgroup")).label = i, r.appendChild(u));
                    var c = v("option");
                    c.value = t.ctrlOptions[l].value, c.innerText = t.ctrlOptions[l].label, y(t.ctrlOptions[l].title) || c.setAttribute("title", t.ctrlOptions[l].title), null === u ? c.appendTo(r) : c.appendTo(u)
                  } else
                  for (var a = 0; a < t.ctrlOptions.length; a++) {
                    var s = t.ctrlOptions[a];
                    r.options[r.options.length] = new Option(s, s)
                  }
            } else if (h(t.ctrlOptions))
              for (var f in t.ctrlOptions) r.options[r.options.length] = new Option(t.ctrlOptions[f], f);
            else if ("string" == typeof t.ctrlOptions)
              for (var p = t.ctrlOptions.split(";"), m = 0; m < p.length; m++) {
                var b = p[m].indexOf(":");
                r.options[r.options.length] = -1 === b ? new Option(p[m], p[m]) : new Option(p[m].substring(b + 1, p[m].length), p[m].substring(0, b))
              } else "function" == typeof t.ctrlOptions && t.ctrlOptions(r);
          else if ("checkbox" === t.type) (r = v("input", n, o, null, "checkbox")).value = 1;
          else if ("textarea" === t.type) r = v("textarea", n, o);
          else if (-1 != t.type.search(/^(color|date|datetime|datetime\-local|email|month|number|range|search|tel|time|url|week)$/)) {
            r = v("input", n, o);
            try {
              r.type = t.type
            } catch (e) { }
          } else (r = v("input", n, o)).type = "text";
          return d(r, this.getSectionClasses("control"), t.ctrlClass), e && e.appendChild(r), r
        }
      }]) && de(t.prototype, n), o && de(t, o), e
    }();

    function me(e) {
      return me = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, me(e)
    }

    function he(e, t) {
      return he = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, he(e, t)
    }

    function ve(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = we(e);
        if (t) {
          var r = we(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return be(this, n)
      }
    }

    function be(e, t) {
      return !t || "object" !== me(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function we(e) {
      return we = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, we(e)
    }
    const ge = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && he(e, t)
      }(n, e);
      var t = ve(n);

      function n(e, o, r) {
        var i;
        return function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, n), (i = t.call(this, o, r)).name = "ui-default", i
      }
      return n
    }(ye);

    function Oe(e) {
      return Oe = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, Oe(e)
    }

    function Ce(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function Re(e, t, n) {
      return Re = "undefined" != typeof Reflect && Reflect.get ? Reflect.get : function (e, t, n) {
        var o = function (e, t) {
          for (; !Object.prototype.hasOwnProperty.call(e, t) && null !== (e = je(e)););
          return e
        }(e, t);
        if (o) {
          var r = Object.getOwnPropertyDescriptor(o, t);
          return r.get ? r.get.call(n) : r.value
        }
      }, Re(e, t, n || e)
    }

    function ke(e, t) {
      return ke = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, ke(e, t)
    }

    function Pe(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = je(e);
        if (t) {
          var r = je(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return _e(this, n)
      }
    }

    function _e(e, t) {
      return !t || "object" !== Oe(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function je(e) {
      return je = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, je(e)
    }
    const xe = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && ke(e, t)
      }(i, e);
      var t, n, o, r = Pe(i);

      function i(e, t, n) {
        var o;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), (o = r.call(this, t, n)).name = "ui-bootstrap4";
        var u = {
          useButtonGroup: !0,
          sectionClasses: null,
          sizing: "normal"
        };
        Object.assign(u, e);
        var l = {
          table: "table",
          thead: "thead-light",
          control: "form-control",
          button: "btn",
          buttonGroup: "btn-group",
          append: "btn-outline-secondary",
          removeLast: "btn-outline-secondary",
          insert: "btn-outline-secondary",
          remove: "btn-outline-secondary",
          moveUp: "btn-outline-secondary",
          moveDown: "btn-outline-secondary",
          empty: "text-center"
        };
        return "small" === u.sizing ? (l.table += " table-sm", l.buttonGroup += " btn-group-sm", l.control += " form-control-sm") : "large" === u.sizing && (l.buttonGroup += " btn-group-lg", l.control += " form-control-lg"), u.sectionClasses && Object.assign(l, u.sectionClasses), o.applySectionClasses(l), o.uiParams = u, o
      }
      return t = i, (n = [{
        key: "createButtonGroup",
        value: function () {
          if (this.uiParams.useButtonGroup) {
            var e = document.createElement("div");
            return d(e, this.getSectionClasses("buttonGroup")), e
          }
          return Re(je(i.prototype), "createButtonGroup", this).call(this)
        }
      }, {
        key: "generateControl",
        value: function (e, t, n, o) {
          var r = null;
          if ("checkbox" === t.type) {
            var u = v("div", null, null, "form-check");
            e.appendChild(u), (r = v("input", n, o, "form-check-input position-static")).type = "checkbox", r.value = 1, d(r, t.ctrlClass), u.appendChild(r)
          } else "readonly" === t.type ? (d(r = v("input", n, o, null, "text"), this.getSectionClasses("control"), t.ctrlClass), r.classList.remove("form-control"), r.classList.add("form-control"), r.readOnly = !0, e.appendChild(r)) : r = Re(je(i.prototype), "generateControl", this).call(this, e, t, n, o);
          return r
        }
      }]) && Ce(t.prototype, n), o && Ce(t, o), i
    }(ye);

    function Se(e) {
      return Se = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, Se(e)
    }

    function Ee(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function Be(e, t, n) {
      return Be = "undefined" != typeof Reflect && Reflect.get ? Reflect.get : function (e, t, n) {
        var o = function (e, t) {
          for (; !Object.prototype.hasOwnProperty.call(e, t) && null !== (e = Te(e)););
          return e
        }(e, t);
        if (o) {
          var r = Object.getOwnPropertyDescriptor(o, t);
          return r.get ? r.get.call(n) : r.value
        }
      }, Be(e, t, n || e)
    }

    function Ie(e, t) {
      return Ie = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, Ie(e, t)
    }

    function Fe(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = Te(e);
        if (t) {
          var r = Te(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return Le(this, n)
      }
    }

    function Le(e, t) {
      return !t || "object" !== Se(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function Te(e) {
      return Te = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, Te(e)
    }
    const De = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && Ie(e, t)
      }(i, e);
      var t, n, o, r = Fe(i);

      function i(e, t, n) {
        var o;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), (o = r.call(this, t, n)).name = "ui-bootstrap5";
        var u = {
          useButtonGroup: !0,
          sectionClasses: null,
          sizing: "normal"
        };
        Object.assign(u, e);
        var l = {
          table: "table",
          thead: "table-light",
          control: "form-control",
          button: "btn",
          buttonGroup: "btn-group",
          append: "btn-outline-secondary",
          removeLast: "btn-outline-secondary",
          insert: "btn-outline-secondary",
          remove: "btn-outline-secondary",
          moveUp: "btn-outline-secondary",
          moveDown: "btn-outline-secondary",
          empty: "text-center"
        };
        return "small" === u.sizing ? (l.table += " table-sm", l.buttonGroup += " btn-group-sm", l.control += " form-control-sm") : "large" === u.sizing && (l.buttonGroup += " btn-group-lg", l.control += " form-control-lg"), u.sectionClasses && Object.assign(l, u.sectionClasses), o.applySectionClasses(l), o.uiParams = u, o
      }
      return t = i, (n = [{
        key: "createButtonGroup",
        value: function () {
          if (this.uiParams.useButtonGroup) {
            var e = document.createElement("div");
            return d(e, this.getSectionClasses("buttonGroup")), e
          }
          return Be(Te(i.prototype), "createButtonGroup", this).call(this)
        }
      }, {
        key: "generateControl",
        value: function (e, t, n, o) {
          var r = null;
          return "checkbox" === t.type ? ((r = v("input", n, o, "form-check-input")).type = "checkbox", r.value = 1, d(r, t.ctrlClass), e.appendChild(r)) : "readonly" === t.type ? (d(r = v("input", n, o, null, "text"), this.getSectionClasses("control"), t.ctrlClass), r.classList.remove("form-control"), r.classList.add("form-control"), r.readOnly = !0, e.appendChild(r)) : r = Be(Te(i.prototype), "generateControl", this).call(this, e, t, n, o), r
        }
      }]) && Ee(t.prototype, n), o && Ee(t, o), i
    }(ye);

    function Ge(e) {
      return Ge = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, Ge(e)
    }

    function Ue(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function Ae(e, t, n) {
      return Ae = "undefined" != typeof Reflect && Reflect.get ? Reflect.get : function (e, t, n) {
        var o = function (e, t) {
          for (; !Object.prototype.hasOwnProperty.call(e, t) && null !== (e = Ve(e)););
          return e
        }(e, t);
        if (o) {
          var r = Object.getOwnPropertyDescriptor(o, t);
          return r.get ? r.get.call(n) : r.value
        }
      }, Ae(e, t, n || e)
    }

    function Ne(e, t) {
      return Ne = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, Ne(e, t)
    }

    function qe(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = Ve(e);
        if (t) {
          var r = Ve(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return Me(this, n)
      }
    }

    function Me(e, t) {
      return !t || "object" !== Ge(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function Ve(e) {
      return Ve = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, Ve(e)
    }
    const $e = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && Ne(e, t)
      }(i, e);
      var t, n, o, r = qe(i);

      function i(e, t, n) {
        var o;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), (o = r.call(this, t, n)).name = "ui-bulma";
        var u = {
          useButtonGroup: !0,
          sectionClasses: null,
          sizing: "normal"
        };
        Object.assign(u, e);
        var l = {
          table: "table",
          control: "input",
          button: "button",
          buttonGroup: "field has-addons",
          append: "is-outlined",
          removeLast: "is-outlined",
          insert: "is-outlined",
          remove: "is-outlined",
          moveUp: "is-outlined",
          moveDown: "is-outlined",
          empty: "has-text-centered"
        };
        return "small" === u.sizing ? (l.table += " is-narrow", l.control += " is-small", l.button += " is-small") : "medium" === u.sizing ? (l.control += " is-medium", l.button += " is-medium") : "large" === u.sizing && (l.control += " is-large", l.button += " is-large"), u.sectionClasses && Object.assign(l, u.sectionClasses), o.applySectionClasses(l), o.uiParams = u, o
      }
      return t = i, (n = [{
        key: "generateButton",
        value: function (e, t, n) {
          var o = v("button", n, null, null, "button");
          o.title = this.i18n[t], d(o, this.getSectionClasses("button"), this.getSectionClasses(t));
          var r = null;
          if (this.iconFramework.isTextBased ? r = o : ((r = document.createElement("span")).classList.add("icon"), o.appendChild(r)), this.iconFramework.generateIcon(r, t), this.uiParams.useButtonGroup) {
            var i = document.createElement("p");
            i.classList.add("control"), i.appendChild(o), e.appendChild(i)
          } else e.appendChild(o);
          return o
        }
      }, {
        key: "createButtonGroup",
        value: function () {
          if (this.uiParams.useButtonGroup) {
            var e = document.createElement("div");
            return d(e, this.getSectionClasses("buttonGroup")), e
          }
          return Ae(Ve(i.prototype), "createButtonGroup", this).call(this)
        }
      }, {
        key: "generateControl",
        value: function (e, t, n, o) {
          var r = null;
          if ("select" === t.type) {
            var u = v("div", null, null, "select");
            "small" === this.uiParams.sizing ? u.classList.add("is-small") : "medium" === this.uiParams.sizing ? u.classList.add("is-medium") : "large" === this.uiParams.sizing && u.classList.add("is-large"), e.appendChild(u), d(r = Ae(Ve(i.prototype), "generateControl", this).call(this, null, t, n, o), t.ctrlClass), u.appendChild(r)
          } else if ("checkbox" === t.type) {
            var l = v("label", null, null, "checkbox");
            e.appendChild(l), (r = v("input", n, o, null, "checkbox")).value = 1, d(r, t.ctrlClass), l.appendChild(r)
          } else "readonly" === t.type ? (d(r = v("input", n, o, null, "text"), this.getSectionClasses("control"), t.ctrlClass), r.classList.add("is-static"), r.readOnly = !0, e.appendChild(r)) : r = Ae(Ve(i.prototype), "generateControl", this).call(this, e, t, n, o);
          return r
        }
      }]) && Ue(t.prototype, n), o && Ue(t, o), i
    }(ye);

    function We(e) {
      return We = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
        return typeof e
      } : function (e) {
        return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
      }, We(e)
    }

    function ze(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }

    function He(e, t, n) {
      return He = "undefined" != typeof Reflect && Reflect.get ? Reflect.get : function (e, t, n) {
        var o = function (e, t) {
          for (; !Object.prototype.hasOwnProperty.call(e, t) && null !== (e = Xe(e)););
          return e
        }(e, t);
        if (o) {
          var r = Object.getOwnPropertyDescriptor(o, t);
          return r.get ? r.get.call(n) : r.value
        }
      }, He(e, t, n || e)
    }

    function Je(e, t) {
      return Je = Object.setPrototypeOf || function (e, t) {
        return e.__proto__ = t, e
      }, Je(e, t)
    }

    function Ke(e) {
      var t = function () {
        if ("undefined" == typeof Reflect || !Reflect.construct) return !1;
        if (Reflect.construct.sham) return !1;
        if ("function" == typeof Proxy) return !0;
        try {
          return Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], (function () { }))), !0
        } catch (e) {
          return !1
        }
      }();
      return function () {
        var n, o = Xe(e);
        if (t) {
          var r = Xe(this).constructor;
          n = Reflect.construct(o, arguments, r)
        } else n = o.apply(this, arguments);
        return Qe(this, n)
      }
    }

    function Qe(e, t) {
      return !t || "object" !== We(t) && "function" != typeof t ? function (e) {
        if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        return e
      }(e) : t
    }

    function Xe(e) {
      return Xe = Object.setPrototypeOf ? Object.getPrototypeOf : function (e) {
        return e.__proto__ || Object.getPrototypeOf(e)
      }, Xe(e)
    }
    const Ye = function (e) {
      ! function (e, t) {
        if ("function" != typeof t && null !== t) throw new TypeError("Super expression must either be null or a function");
        e.prototype = Object.create(t && t.prototype, {
          constructor: {
            value: e,
            writable: !0,
            configurable: !0
          }
        }), t && Je(e, t)
      }(i, e);
      var t, n, o, r = Ke(i);

      function i(e, t, n) {
        var o;
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, i), (o = r.call(this, t, n)).name = "ui-foundation6";
        var u = {
          useButtonGroup: !0,
          sectionClasses: null
        };
        Object.assign(u, e);
        var l = {
          button: "button",
          buttonGroup: "button-group"
        };
        return u.sectionClasses && Object.assign(l, u.sectionClasses), o.applySectionClasses(l), o.uiParams = u, o
      }
      return t = i, (n = [{
        key: "createButtonGroup",
        value: function () {
          if (this.uiParams.useButtonGroup) {
            var e = document.createElement("div");
            return d(e, this.getSectionClasses("buttonGroup")), e
          }
          return He(Xe(i.prototype), "createButtonGroup", this).call(this)
        }
      }]) && ze(t.prototype, n), o && ze(t, o), i
    }(ye);

    function Ze(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }
    var et = function () {
      function e(t) {
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, e);
        var n = this;
        Object.assign(n, {
          uniqueIndex: 0,
          rowOrder: [],
          isDataLoaded: !1,
          visibleCount: 0,
          finalColSpan: 0,
          hideLastColumn: !1,
          settings: null,
          tbWhole: null,
          tbBody: null,
          iconFramework: null,
          uiFramework: null
        });
        var o = Object.assign({}, t);
        n.settings = o;
        var r = null;
        if (!(r = "string" == typeof o.element ? document.getElementById(o.element) : o.element) || !r.tagName || "TABLE" !== r.tagName) throw "*element* is not defined or is not a table DOM element.";
        if (n.tbWhole = r, r.innerHTML = "", "bootstrapicons" === o.iconFramework) n.iconFramework = new k(o.iconParams);
        else if ("fontawesome6" === o.iconFramework) n.iconFramework = new B(o.iconParams);
        else if ("fontawesome5" === o.iconFramework) n.iconFramework = new U(o.iconParams);
        else if ("ionicon4" === o.iconFramework) n.iconFramework = new Y(o.iconParams);
        else if ("materialdesignicons3" === o.iconFramework) n.iconFramework = new W(o.iconParams);
        else if ("openiconic" === o.iconFramework) n.iconFramework = new pe(o.iconParams);
        else if ("typicons2" === o.iconFramework) n.iconFramework = new ie(o.iconParams);
        else {
          if (o.iconFramework && "default" !== o.iconFramework) throw "Unknown Icon framework *".concat(o.iconFramework, "*.");
          n.iconFramework = new p(o.iconParams)
        }
        if ("bootstrap4" === o.uiFramework) n.uiFramework = new xe(o.uiParams, o.i18n, n.iconFramework);
        else if ("bootstrap5" === o.uiFramework) n.uiFramework = new De(o.uiParams, o.i18n, n.iconFramework);
        else if ("bulma" === o.uiFramework) n.uiFramework = new $e(o.uiParams, o.i18n, n.iconFramework);
        else if ("foundation6" === o.uiFramework) n.uiFramework = new Ye(o.uiParams, o.i18n, n.iconFramework);
        else {
          if (o.uiFramework && "default" !== o.uiFramework) throw "Unknown UI framework *".concat(o.uiFramework, "*.");
          n.uiFramework = new ge(o.uiParams, o.i18n, n.iconFramework)
        }
        y(o.idPrefix) && (r.id ? o.idPrefix = r.id : o.idPrefix = "ag" + (new Date).getTime()), o.sectionClasses && n.uiFramework.applySectionClasses(o.sectionClasses), d(r, n.uiFramework.getSectionClasses("table"));
        var i = n.createElement("thead");
        r.appendChild(i);
        var u, l = n.createElement("tr", "theadRow");
        i.appendChild(l);
        var c = 0;
        o.hideRowNumColumn || (u = n.createElement("th", "theadCell"), l.appendChild(u), c++);
        for (var a = 0, s = 0; s < o.columns.length; s++)
          if ("hidden" !== o.columns[s].type) {
            if (0 === a) {
              if (u = n.createElement("th", "theadCell"), l.appendChild(u), d(u, o.columns[s].displayClass), !y(o.columns[s].displayCss))
                for (var f in o.columns[s].displayCss) u.style[f] = o.columns[s].displayCss[f];
              o.columns[s].headerSpan > 1 && (u.setAttribute("colSpan", o.columns[s].headerSpan), a = o.columns[s].headerSpan - 1), "function" == typeof o.columns[s].display ? o.columns[s].display(u) : o.columns[s].display && (u.innerText = o.columns[s].display)
            } else a--;
            c++
          } u = n.createElement("th", "theadCell"), o.hideButtons.insert && o.hideButtons.remove && o.hideButtons.moveUp && o.hideButtons.moveDown ? (n.hideLastColumn = !0, u.style.display = "none") : c++, !n.hideLastColumn && o.rowButtonsInFront ? o.hideRowNumColumn ? l.insertBefore(u, l.firstChild) : l.insertBefore(u, l.childNodes[1]) : l.appendChild(u), n.finalColSpan = c;
        var m = n.createElement("tbody");
        r.appendChild(m), n.tbBody = m;
        var h = n.createElement("tfoot");
        r.appendChild(h), l = n.createElement("tr", "tfootRow"), h.appendChild(l), (u = n.createElement("td", "tfootCell")).colSpan = n.finalColSpan, l.appendChild(u);
        var b = o.idPrefix + "_rowOrder",
          w = v("input", b, b, null, "hidden");
        if (u.appendChild(w), o.hideButtons.append && o.hideButtons.removeLast) l.style.display = "none";
        else {
          var g = n.uiFramework.createButtonGroup();
          if (g ? u.appendChild(g) : g = u, !o.hideButtons.append) n.uiFramework.generateButton(g, "append").addEventListener("click", (function (e) {
            n.insertRow(1)
          }));
          if (!o.hideButtons.removeLast) n.uiFramework.generateButton(g, "removeLast").addEventListener("click", (function (e) {
            n.removeRow()
          }))
        }
        this.showEmptyMessage(), n.settings = o
      }
      var t, n, o;
      return t = e, n = [{
        key: "createElement",
        value: function (e, t, n) {
          return v(e, n, null, this.uiFramework.getSectionClasses(t || e))
        }
      }, {
        key: "loadData",
        value: function (e) {
          var t = arguments.length > 1 && void 0 !== arguments[1] && arguments[1];
          if (!Array.isArray(e) || !e.length) throw "*records* should be in array format!";
          var n = this,
            o = n.settings;
          n.tbBody.innerHTML = "", n.rowOrder.length = 0, n.uniqueIndex = 0;
          for (var r = n.insertRow(e.length), i = 0; i < r.addedRows.length; i++) {
            for (var u = 0; u < o.columns.length; u++) n.setCtrlValue(u, n.rowOrder[i], e[i][o.columns[u].name]);
            "function" == typeof o.rowDataLoaded && o.rowDataLoaded(n.tbWhole, e[i], i, n.rowOrder[i])
          }
          n.isDataLoaded = !0, t && (n.settings.initData = null), "function" == typeof o.dataLoaded && o.dataLoaded(n.tbWhole, e)
        }
      }, {
        key: "insertRow",
        value: function (e, t, n) {
          var o, r, i = this,
            u = i.settings,
            l = i.uiFramework,
            c = i.tbBody,
            a = [],
            s = null,
            f = !1,
            p = e,
            h = !1;
          if (Array.isArray(e) && (p = e.length, h = !0), m(n)) {
            for (var b = 0; b < i.rowOrder.length; b++)
              if (i.rowOrder[b] === n) {
                t = b, 0 !== b && (s = b - 1);
                break
              }
          } else m(t) ? t >= i.rowOrder.length ? t = null : s = t - 1 : 0 !== i.rowOrder.length && (t = null, s = i.rowOrder.length - 1);
          0 === i.rowOrder.length && (c.innerHTML = "");
          for (var w = function (n) {
            if (0 < u.maxRowsAllowed && i.rowOrder.length >= u.maxRowsAllowed) return f = !0, "break";
            var s = ++i.uniqueIndex,
              p = [];
            if ((o = i.createElement("tr", "tbodyRow", u.idPrefix + "_$row_" + s)).dataset.uniqueIndex = s, m(t)) {
              var b = t + n;
              i.rowOrder.splice(b, 0, s), c.insertBefore(o, c.childNodes[b])
            } else i.rowOrder.push(s), c.appendChild(o);
            a.push(s), u.hideRowNumColumn || ((r = i.createElement("td", "tbodyCell", u.idPrefix + "_$rowNum_" + s)).innerText = "" + i.rowOrder.length, d(r, l.getSectionClasses("first")), o.appendChild(r));
            for (var w = 0; w < u.columns.length; w++)
              if ("hidden" !== u.columns[w].type) {
                if (r = i.createElement("td", "tbodyCell"), o.appendChild(r), d(r, u.columns[w].cellClass), !y(u.columns[w].cellCss))
                  for (var g in u.columns[w].cellCss) r.style[g] = u.columns[w].cellCss[g];
                var O = u.idPrefix + "_" + u.columns[w].name + "_" + s,
                  C = void 0;
                C = "function" == typeof u.nameFormatter ? u.nameFormatter(u.idPrefix, u.columns[w].name, s) : O;
                var R = null,
                  k = "custom" === u.columns[w].type;
                if (k) "function" == typeof u.columns[w].customBuilder && (R = u.columns[w].customBuilder(r, u.idPrefix, u.columns[w].name, s));
                else {
                  if (R = i.uiFramework.generateControl(r, u.columns[w], O, C), !y(u.columns[w].ctrlAttr))
                    for (var P in u.columns[w].ctrlAttr) R.setAttribute(P, u.columns[w].ctrlAttr[P]);
                  if (!y(u.columns[w].ctrlCss))
                    for (var _ in u.columns[w].ctrlCss) R.style[_] = u.columns[w].ctrlCss[_];
                  if (u.columns[w].events) {
                    R.dataset.columnName = u.columns[w].name, R.dataset.uniqueIndex = s;
                    var j = function (e) {
                      var t = u.columns[w].events[e];
                      R.addEventListener(e, (function (e) {
                        e.columnName = e.currentTarget.dataset.columnName, e.uniqueIndex = parseInt(e.currentTarget.dataset.uniqueIndex), t(e)
                      }))
                    };
                    for (var x in u.columns[w].events) j(x)
                  }
                }
                h ? i.setCtrlValue(w, s, e[n][u.columns[w].name]) : y(u.columns[w].value) || i.setCtrlValue(w, s, u.columns[w].value), k || "function" != typeof u.columns[w].ctrlAdded || u.columns[w].ctrlAdded(R, r, s)
              } else p.push(w);
            if (r = i.createElement("td", "tbodyCell", u.idPrefix + "_$rowButton_" + s), i.hideLastColumn || !u.rowButtonsInFront ? o.appendChild(r) : u.hideRowNumColumn ? o.insertBefore(r, o.firstChild) : o.insertBefore(r, o.childNodes[1]), p.forEach((function (t) {
              var o, l = u.columns[t].name,
                c = u.idPrefix + "_" + l + "_" + s;
              o = "function" == typeof u.nameFormatter ? u.nameFormatter(u.idPrefix, l, s) : c, r.appendChild(v("input", c, o, null, "hidden")), h ? i.setCtrlValue(t, s, e[n][l]) : y(u.columns[t].value) || i.setCtrlValue(t, s, u.columns[t].value)
            })), i.hideLastColumn) r.style.display = "none";
            else if (u.columns.length > i.visibleCount) {
              d(r, l.getSectionClasses("last"));
              var S = l.createButtonGroup();
              S ? r.appendChild(S) : S = r, ["insert", "remove", "moveUp", "moveDown"].forEach((function (e) {
                if (!u.hideButtons[e]) {
                  var t = u.idPrefix + "_$" + e + "_" + s,
                    n = l.generateButton(S, e, t);
                  n.dataset.uniqueIndex = s, n.addEventListener("click", (function (t) {
                    var n = parseInt(t.currentTarget.dataset.uniqueIndex);
                    i.rowButtonActions(e, n)
                  }))
                }
              }))
            }
          }, g = 0; g < p && "break" !== w(g); g++);
          return i.saveSetting(), u.hideRowNumColumn || y(t) || i.sortSequence(t), m(t) ? "function" == typeof u.afterRowInserted && u.afterRowInserted(i.tbWhole, s, a) : "function" == typeof u.afterRowAppended && u.afterRowAppended(i.tbWhole, s, a), f && "function" == typeof u.maxNumRowsReached && u.maxNumRowsReached(i.tbWhole), {
            addedRows: a,
            parentIndex: s,
            rowIndex: t
          }
        }
      }, {
        key: "removeRow",
        value: function (e, t, n) {
          var o = this,
            r = o.settings,
            i = o.tbBody;
          if (m(t))
            for (var u = 0; u < o.rowOrder.length; u++)
              if (o.rowOrder[u] === t) {
                e = u;
                break
              } m(e) ? (n || "function" != typeof r.beforeRowRemove || r.beforeRowRemove(o.tbWhole, e)) && (o.rowOrder.splice(e, 1), i.removeChild(i.childNodes[e]), o.saveSetting(), r.hideRowNumColumn || o.sortSequence(e), "function" == typeof r.afterRowRemoved && r.afterRowRemoved(o.tbWhole, e)) : (n || "function" != typeof r.beforeRowRemove || r.beforeRowRemove(o.tbWhole, o.rowOrder.length - 1)) && (t = o.rowOrder.pop(), i.removeChild(i.lastChild), o.saveSetting(), "function" == typeof r.afterRowRemoved && r.afterRowRemoved(o.tbWhole, null)), 0 === o.rowOrder.length && o.showEmptyMessage()
        }
      }, {
        key: "moveUpRow",
        value: function (e, t) {
          var n = this,
            o = n.settings,
            r = n.tbBody,
            i = null;
          if (m(e) && e > 0 && e < n.rowOrder.length ? (i = e, t = n.rowOrder[e]) : m(t) && (i = n.findRowIndex(t)), !y(i) && i > 0) {
            var u = n.rowOrder[i - 1],
              l = document.getElementById(o.idPrefix + "_$row_" + t),
              c = document.getElementById(o.idPrefix + "_$row_" + u);
            if (r.removeChild(l), r.insertBefore(l, c), n.rowOrder[i] = u, n.rowOrder[i - 1] = t, !o.hideRowNumColumn) {
              var a = document.getElementById(o.idPrefix + "_$rowNum_" + t),
                s = document.getElementById(o.idPrefix + "_$rowNum_" + u),
                f = s.innerHTML;
              s.innerHTML = a.innerHTML, a.innerHTML = f
            }
            n.saveSetting(), document.getElementById(o.idPrefix + "_$moveUp_" + t).blur(), document.getElementById(o.idPrefix + "_$moveUp_" + u).focus(), "function" == typeof o.afterRowSwapped && o.afterRowSwapped(n.tbWhole, i, i - 1)
          }
        }
      }, {
        key: "moveDownRow",
        value: function (e, t) {
          var n = this,
            o = n.settings,
            r = n.tbBody,
            i = null;
          if (m(e) && e >= 0 && e < n.rowOrder.length - 1 ? (i = e, t = n.rowOrder[e]) : m(t) && (i = n.findRowIndex(t)), !y(i) && i !== n.rowOrder.length - 1) {
            var u = n.rowOrder[i + 1],
              l = document.getElementById(o.idPrefix + "_$row_" + t),
              c = document.getElementById(o.idPrefix + "_$row_" + u);
            if (r.removeChild(c), r.insertBefore(c, l), n.rowOrder[i] = u, n.rowOrder[i + 1] = t, !o.hideRowNumColumn) {
              var a = document.getElementById(o.idPrefix + "_$rowNum_" + t),
                s = document.getElementById(o.idPrefix + "_$rowNum_" + u),
                f = s.innerHTML;
              s.innerHTML = a.innerHTML, a.innerHTML = f
            }
            n.saveSetting(), document.getElementById(o.idPrefix + "_$moveDown_" + t).blur(), document.getElementById(o.idPrefix + "_$moveDown_" + u).focus(), "function" == typeof o.afterRowSwapped && o.afterRowSwapped(n.tbWhole, i, i + 1)
          }
        }
      }, {
        key: "setCtrlValue",
        value: function (e, t, n) {
          var o = this.settings,
            r = o.columns[e].type,
            i = o.columns[e].name;
          if ("custom" === r) "function" == typeof o.columns[e].customSetter && o.columns[e].customSetter(o.idPrefix, i, t, n);
          else {
            var u = this.getCellCtrl(o.idPrefix, i, t);
            "checkbox" === r ? "boolean" == typeof n ? u.checked = n : m(n) ? u.checked = 0 !== n : u.checked = !y(n) : u.value = y(n) ? "" : n
          }
        }
      }, {
        key: "getCellCtrl",
        value: function (e, t, n) {
          return document.getElementById(e + "_" + t + "_" + n)
        }
      }, {
        key: "getCtrlValue",
        value: function (e, t) {
          var n = this.settings,
            o = n.columns[e];
          if ("custom" === o.type) {
            if ("function" == typeof o.customGetter) return o.customGetter(n.idPrefix, o.name, t);
            throw "*customGetter* of column *".concat(o.name, "* is not defined.")
          }
          var r = this.getCellCtrl(n.idPrefix, o.name, t);
          return null === r ? null : "checkbox" === o.type ? r.checked ? 1 : 0 : r.value
        }
      }, {
        key: "getRowValue",
        value: function (e, t) {
          var n = this,
            o = {},
            r = y(t) ? "" : "_" + t;
          return n.settings.columns.forEach((function (t, i) {
            var u = t.name + r;
            o[u] = n.getCtrlValue(i, e)
          })), o
        }
      }, {
        key: "getColumnIndex",
        value: function (e) {
          for (var t = this.settings.columns, n = 0; n < t.length; n++)
            if (t[n].name === e) return n;
          return null
        }
      }, {
        key: "isRowEmpty",
        value: function (e) {
          for (var t = this, n = t.settings.columns, o = 0; o < n.length; o++) {
            var r = n[o].emptyCriteria,
              i = t.getCtrlValue(o, e);
            if ("function" == typeof r) {
              if (!r(i)) return !1
            } else {
              var u = null;
              if (y(r)) {
                var l = n[o].type;
                if ("checkbox" === l) u = 0;
                else if ("select" === l) {
                  var c = t.getCellCtrl(t.settings.idPrefix, n[o].name, e).options;
                  u = c.length > 0 ? c[0].value : ""
                } else u = ""
              } else u = r;
              if (i !== u) return !1
            }
          }
          return !0
        }
      }, {
        key: "findRowIndex",
        value: function (e) {
          for (var t = 0; t < this.rowOrder.length; t++)
            if (this.rowOrder[t] === e) return t;
          return null
        }
      }, {
        key: "saveSetting",
        value: function () {
          document.getElementById(this.settings.idPrefix + "_rowOrder").value = this.rowOrder.join()
        }
      }, {
        key: "showEmptyMessage",
        value: function () {
          var e = this;
          e.tbBody.innerHTML = "";
          var t = e.createElement("tr", "tbodyRow");
          e.tbBody.appendChild(t);
          var n = e.createElement("td", "tbodyCell");
          n.setAttribute("colspan", e.finalColSpan), d(n, e.uiFramework.getSectionClasses("empty")), n.innerText = e.settings.i18n.rowEmpty, t.appendChild(n)
        }
      }, {
        key: "sortSequence",
        value: function (e) {
          for (var t = this, n = e || 0; n < t.rowOrder.length; n++) document.getElementById(t.settings.idPrefix + "_$rowNum_" + t.rowOrder[n]).innerText = "" + (n + 1)
        }
      }, {
        key: "rowButtonActions",
        value: function (e, t) {
          var n = this;
          "insert" === e ? n.insertRow(1, null, t) : "remove" === e ? n.removeRow(null, t) : "moveUp" === e ? n.moveUpRow(null, t) : "moveDown" === e && n.moveDownRow(null, t)
        }
      }], n && Ze(t.prototype, n), o && Ze(t, o), e
    }();
    const tt = et;

    function nt(e, t) {
      for (var n = 0; n < t.length; n++) {
        var o = t[n];
        o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o)
      }
    }
    var ot = new WeakMap,
      rt = {
        element: null,
        uiFramework: null,
        uiParams: null,
        iconFramework: null,
        iconParams: null,
        initRows: 3,
        idPrefix: null,
        initData: null,
        columns: [],
        i18n: null,
        hideButtons: null,
        hideRowNumColumn: !1,
        rowButtonsInFront: !1,
        rowCountName: "_RowCount",
        sectionClasses: null,
        maxRowsAllowed: 0
      },
      it = {
        nameFormatter: null,
        dataLoaded: null,
        rowDataLoaded: null,
        afterRowAppended: null,
        afterRowInserted: null,
        afterRowSwapped: null,
        beforeRowRemove: null,
        afterRowRemoved: null,
        maxNumRowsReached: null
      },
      ut = {
        type: "text",
        name: null,
        value: null,
        display: null,
        displayCss: null,
        displayClass: null,
        displayTooltip: null,
        headerSpan: 1,
        cellCss: null,
        cellClass: null,
        ctrlAttr: null,
        ctrlProp: null,
        ctrlCss: null,
        ctrlClass: null,
        ctrlOptions: null,
        invisible: !1,
        emptyCriteria: null,
        customBuilder: null,
        customGetter: null,
        customSetter: null,
        events: null,
        ctrlAdded: null
      };
    const lt = function () {
      function e(t) {
        ! function (e, t) {
          if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
        }(this, e);
        var n = Object.assign({}, rt, it, t),
          o = {
            append: "Append Row",
            removeLast: "Remove Last Row",
            insert: "Insert Row Above",
            remove: "Remove Current Row",
            moveUp: "Move Up",
            moveDown: "Move Down",
            rowEmpty: "This Grid Is Empty"
          };
        n.i18n && Object.assign(o, n.i18n), n.i18n = o;
        var r = {
          append: !1,
          removeLast: !1,
          insert: !1,
          remove: !1,
          moveUp: !1,
          moveDown: !1
        };
        n.hideButtons && Object.assign(r, n.hideButtons), n.hideButtons = r;
        for (var i = 0; i < n.columns.length; i++) {
          var u = Object.assign({}, ut, n.columns[i]);
          n.columns[i] = u
        }
        var l = new tt(n);
        ot.set(this, l), Array.isArray(n.initData) ? l.loadData(n.initData, !0) : n.initRows > 0 && l.insertRow(n.initRows)
      }
      var t, n, o;
      return t = e, (n = [{
        key: "appendRow",
        value: function (e) {
          return ot.get(this).insertRow(e || 1).addedRows
        }
      }, {
        key: "insertRow",
        value: function (e, t) {
          return ot.get(this).insertRow(e, t).addedRows
        }
      }, {
        key: "removeRow",
        value: function (e) {
          ot.get(this).removeRow(e)
        }
      }, {
        key: "moveUpRow",
        value: function (e) {
          ot.get(this).moveUpRow(e)
        }
      }, {
        key: "moveDownRow",
        value: function (e) {
          ot.get(this).moveDownRow(e)
        }
      }, {
        key: "load",
        value: function (e) {
          ot.get(this).loadData(e)
        }
      }, {
        key: "getAllValue",
        value: function (e) {
          var t = ot.get(this),
            n = e ? {} : [];
          return t.rowOrder.forEach((function (o, r) {
            e ? Object.assign(n, t.getRowValue(o, r)) : n.push(t.getRowValue(o))
          })), e && (n[t.settings.rowCountName] = t.rowOrder.length), n
        }
      }, {
        key: "getUniqueIndex",
        value: function (e) {
          var t = ot.get(this).rowOrder;
          return e >= 0 && e < t.length ? t[e] : null
        }
      }, {
        key: "getRowIndex",
        value: function (e) {
          for (var t = ot.get(this).rowOrder, n = 0; n < t.length; n++)
            if (t[n] === e) return n;
          return null
        }
      }, {
        key: "getRowCount",
        value: function () {
          return ot.get(this).rowOrder.length
        }
      }, {
        key: "getRowOrder",
        value: function () {
          return ot.get(this).rowOrder.slice()
        }
      }, {
        key: "getRowValue",
        value: function (e) {
          var t = this.getUniqueIndex(e);
          return null !== t ? ot.get(this).getRowValue(t) : null
        }
      }, {
        key: "getCtrlValue",
        value: function (e, t) {
          var n = ot.get(this).getColumnIndex(e),
            o = this.getUniqueIndex(t);
          return null !== n && null !== o ? ot.get(this).getCtrlValue(n, o) : null
        }
      }, {
        key: "setCtrlValue",
        value: function (e, t, n) {
          var o = ot.get(this).getColumnIndex(e),
            r = this.getUniqueIndex(t);
          if (null !== o && null !== r) return ot.get(this).setCtrlValue(o, r, n)
        }
      }, {
        key: "getColumns",
        value: function () {
          return ot.get(this).settings.columns.slice()
        }
      }, {
        key: "getCellCtrl",
        value: function (e, t) {
          var n = this.getUniqueIndex(t);
          return this.getCellCtrlByUniqueIndex(e, n)
        }
      }, {
        key: "getCellCtrlByUniqueIndex",
        value: function (e, t) {
          var n = ot.get(this);
          return null !== n.getColumnIndex(e) && m(t) ? n.getCellCtrl(n.settings.idPrefix, e, t) : null
        }
      }, {
        key: "isRowEmpty",
        value: function (e) {
          var t = this.getUniqueIndex(e);
          return null === t || ot.get(this).isRowEmpty(t)
        }
      }, {
        key: "removeEmptyRows",
        value: function () {
          for (var e = ot.get(this), t = this.getRowOrder(), n = 0; n < t.length; n++) e.isRowEmpty(t[n]) && e.removeRow(null, t[n], !0)
        }
      }]) && nt(t.prototype, n), o && nt(t, o), e
    }();
    return t = t.default
  })()
}));