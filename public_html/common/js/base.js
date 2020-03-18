window.requestAnimFrame = (function(callback) {
  return window.requestAnimationFrame ||
  window.webkitRequestAnimationFrame ||
  window.mozRequestAnimationFrame ||
  window.oRequestAnimationFrame ||
  window.msRequestAnimationFrame ||
  function(callback){
    return window.setTimeout(callback, 1000/60);
  };
})();

window.cancelAnimFrame = (function(_id) {
  return window.cancelAnimationFrame ||
  window.cancelRequestAnimationFrame ||
  window.webkitCancelAnimationFrame ||
  window.webkitCancelRequestAnimationFrame ||
  window.mozCancelAnimationFrame ||
  window.mozCancelRequestAnimationFrame ||
  window.msCancelAnimationFrame ||
  window.msCancelRequestAnimationFrame ||
  window.oCancelAnimationFrame ||
  window.oCancelRequestAnimationFrame ||
  function(_id) { window.clearTimeout(id); };
})();

function closest(el, selector) {
  // type el -> Object
  // type select -> String
  var matchesFn;
  // find vendor prefix
  ['matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector'].some(function(fn) {
    if (typeof document.body[fn] == 'function') {
      matchesFn = fn;
      return true;
    }
    return false;
  })
  var parent;
  // traverse parents
  while (el) {
    parent = el.parentElement;
    if (parent && parent[matchesFn](selector)) {
      return parent;
    }
    el = parent;
  }
  return null;
}

function getCssProperty(elem, property) {
  return window.getComputedStyle(elem, null).getPropertyValue(property);
}
var easingEquations = {
  easeOutSine: function(pos) {
    return Math.sin(pos * (Math.PI / 2));
  },
  easeInOutSine: function(pos) {
    return (-0.5 * (Math.cos(Math.PI * pos) - 1));
  },
  easeInOutQuint: function(pos) {
    if ((pos /= 0.5) < 1) {
      return 0.5 * Math.pow(pos, 5);
    }
    return 0.5 * (Math.pow((pos - 2), 5) + 2);
  }
};

function isPartiallyVisible(el) {
  var elementBoundary = el.getBoundingClientRect();
  var top = elementBoundary.top;
  var bottom = elementBoundary.bottom;
  var height = elementBoundary.height;
  return ((top + height >= 0) && (height + window.innerHeight >= bottom));
}

function isFullyVisible(el) {
  var elementBoundary = el.getBoundingClientRect();
  var top = elementBoundary.top;
  var bottom = elementBoundary.bottom;
  return ((top >= 0) && (bottom <= window.innerHeight));
}

function CreateElementWithClass(elementName, className) {
  var el = document.createElement(elementName);
  el.className = className;
  return el;
}

function createElementWithId(elementName, idName) {
  var el = document.createElement(elementName);
  el.id = idName;
  return el;
}

function detectIE() {
  var ua = window.navigator.userAgent;
  var msie = ua.indexOf('MSIE ');
  var trident = ua.indexOf('Trident/');
  if (msie > 0 || trident > 0) {
    // IE 10 or older => return version number
    // return 'ie'+parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    return 'ie';
  }
  return false;
}

var PageTop = (function() {
  function PageTop() {
    var pa = this;
    this._target = document.getElementById('gotop');
    this.flag_start = false;
    this.stopEverything = function() {
      pa.flag_start = false;
    }
    this.scrollToY = function(scrollTargetY, speed, easing) {
      var scrollY = window.scrollY || window.pageYOffset,
        scrollTargetY = scrollTargetY || 0,
        speed = speed || 2000,
        easing = easing || 'easeOutSine',
        currentTime = 0;
      var time = Math.max(.1, Math.min(Math.abs(scrollY - scrollTargetY) / speed, .8));

      function tick() {
        if (pa.flag_start) {
          currentTime += 1 / 60;
          var p = currentTime / time;
          var t = easingEquations[easing](p);
          if (p < 1) {
            requestAnimFrame(tick);
            window.scrollTo(0, scrollY + ((scrollTargetY - scrollY) * t));
          } else {
            window.scrollTo(0, scrollTargetY);
          }
        }
      }
      tick();
    }
    this._target.addEventListener('click', function(e) {
      e.preventDefault();
      pa.flag_start = true;
      pa.scrollToY(0, 1000, 'easeOutSine');
    })
    document.querySelector("body").addEventListener('mousewheel', pa.stopEverything, false);
    document.querySelector("body").addEventListener('DOMMouseScroll', pa.stopEverything, false);
  }
  return PageTop;
})();

var Menu = (function() {
  function Menu() {
    var m = this;
    this._target = document.getElementById('icon_nav');
    this._mobile = document.getElementById('nav');
    this._header = document.getElementById('header');
    this._target.addEventListener('click', function() {
      if (this.classList.contains('open')) {
        this.classList.remove('open');
        m._mobile.classList.remove('open');
        m._mobile.style.height = 0;
        document.body.style.overflow = 'inherit';
      } else {
        this.classList.add('open');
        m._mobile.classList.add('open');
        document.body.style.overflow = 'hidden';
        m._mobile.style.height = window.innerHeight + 'px';
        m._mobile.style.top = 0;
      }
    })
    this._reset = function() {
      if (m._target.classList.contains('open')) {
        if (window.innerWidth > 768) {
          m._target.classList.remove('open');
          m._mobile.classList.remove('open');
          document.body.style.overflow = 'auto';
          m._mobile.style.height = 'auto';
          m._mobile.style.height = 'auto';
        } else {
          m._mobile.style.height = window.innerHeight + 'px';
          m._mobile.style.top = 0;
        }
      } else {
        if (window.innerWidth < 769) {
          m._mobile.style.height = 0;
        } else {
          m._mobile.style.height = 'auto';
        }
      }
    }
    this._reset();
    window.addEventListener('resize', m._reset, false);
  }
  return Menu;
})();

var Sticky = (function(){
  function Sticky(){
    var s = this;
    this._target = document.getElementById('header');
    this._mobile = document.getElementById('nav');
    this._for_sp = function(top){
      s._target.style.left = 0;
      document.body.style.paddingTop = s._target.clientHeight+'px';
      if(top > 0) {
        s._target.classList.add('fixed');
        document.body.style.paddingTop = s._target.clientHeight+'px';
      } else {
        s._target.classList.remove('fixed');
        document.body.style.paddingTop = 0;
      }
    }
    this._for_pc = function(top,left){
      if(top > 0) {
        s._target.classList.add('fixed');
        document.body.style.paddingTop = s._target.clientHeight+'px';
        s._target.style.left = -left+'px';
      } else {
        s._target.classList.remove('fixed');
        document.body.style.paddingTop = 0;
      }
    }
    this.handling = function(){
      var _top  = document.documentElement.scrollTop || document.body.scrollTop;
      var _left  = document.documentElement.scrollLeft || document.body.scrollLeft;
      if(window.innerWidth < 769) {
        s._for_sp(_top);
      }  else {
        if(!s._target.classList.contains('top')) {
          s._target.classList.remove('fixed')
        }
        s._for_pc(_top,_left);
      }
    }
    window.addEventListener('scroll',s.handling,false);
    window.addEventListener('resize',s.handling,false);
    window.addEventListener('load',s.handling,false);
  }
  return Sticky;
})();

var Itlink = (function() {
  function Itlink() {
    var i = this;
    this._target = document.getElementById('nav');
    this._link = window.location.href.split('/')[3];
    
    Array.prototype.forEach.call(i._target.querySelectorAll('a'), function(a, b) {
      if ((a.getAttribute('href').split("/")[1] == i._link)) {
        a.parentNode.classList.add('active');
      }
    })
  }
  return Itlink;
})()

var Base = function() {
  !function Base() {
    new PageTop();
    new Sticky();
    new Menu();
    new Itlink();
  }();
  return Base;
};

window.addEventListener('DOMContentLoaded', function() {
  if (window.jQuery) window.Velocity = window.jQuery.fn.velocity;
  if (detectIE()) {
    document.body.classList.add(detectIE());
  }
  new Base();
});

/* images pc <---> sp */
(function () {
  var PicturePolyfill = (function () {
    function PicturePolyfill() {
      var _this = this;
      this.pictures = [];
      this.onResize = function () {
        var width = document.body.clientWidth;
        for (var i = 0; i < _this.pictures.length; i = (i + 1)) {
          _this.pictures[i].update(width);
        };
      };
      if ([].includes) return;
      var picture = Array.prototype.slice.call(document.getElementsByTagName('picture'));
      for (var i = 0; i < picture.length; i = (i + 1)) {
        this.pictures.push(new Picture(picture[i]));
      };
      window.addEventListener("resize", this.onResize, false);
      this.onResize();
    }
    return PicturePolyfill;
  }());
  var Picture = (function () {
    function Picture(node) {
      var _this = this;
      this.update = function (width) {
        width <= _this.breakPoint ? _this.toSP() : _this.toPC();
      };
      this.toSP = function () {
        if (_this.isSP) return;
        _this.isSP = true;
        _this.changeSrc();
      };
      this.toPC = function () {
        if (!_this.isSP) return;
        _this.isSP = false;
        _this.changeSrc();
      };
      this.changeSrc = function () {
        var toSrc = _this.isSP ? _this.srcSP : _this.srcPC;
        _this.img.setAttribute('src', toSrc);
      };
      this.img = node.getElementsByTagName('img')[0];
      this.srcPC = this.img.getAttribute('src');
      var source = node.getElementsByTagName('source')[0];
      this.srcSP = source.getAttribute('srcset');
      this.breakPoint = Number(source.getAttribute('media').match(/(\d+)px/)[1]);
      this.isSP = !document.body.clientWidth <= this.breakPoint;
      this.update();
    }
    return Picture;
  }());
  new PicturePolyfill();
}());
