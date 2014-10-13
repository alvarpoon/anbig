/* ========================================================================
 * Bootstrap: transition.js v3.2.0
 * http://getbootstrap.com/javascript/#transitions
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
  // ============================================================

  function transitionEnd() {
    var el = document.createElement('bootstrap')

    var transEndEventNames = {
      WebkitTransition : 'webkitTransitionEnd',
      MozTransition    : 'transitionend',
      OTransition      : 'oTransitionEnd otransitionend',
      transition       : 'transitionend'
    }

    for (var name in transEndEventNames) {
      if (el.style[name] !== undefined) {
        return { end: transEndEventNames[name] }
      }
    }

    return false // explicit for ie8 (  ._.)
  }

  // http://blog.alexmaccaw.com/css-transitions
  $.fn.emulateTransitionEnd = function (duration) {
    var called = false
    var $el = this
    $(this).one('bsTransitionEnd', function () { called = true })
    var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
    setTimeout(callback, duration)
    return this
  }

  $(function () {
    $.support.transition = transitionEnd()

    if (!$.support.transition) return

    $.event.special.bsTransitionEnd = {
      bindType: $.support.transition.end,
      delegateType: $.support.transition.end,
      handle: function (e) {
        if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
      }
    }
  })

}(jQuery);
;/* ========================================================================
 * Bootstrap: alert.js v3.2.0
 * http://getbootstrap.com/javascript/#alerts
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // ALERT CLASS DEFINITION
  // ======================

  var dismiss = '[data-dismiss="alert"]'
  var Alert   = function (el) {
    $(el).on('click', dismiss, this.close)
  }

  Alert.VERSION = '3.2.0'

  Alert.prototype.close = function (e) {
    var $this    = $(this)
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = $(selector)

    if (e) e.preventDefault()

    if (!$parent.length) {
      $parent = $this.hasClass('alert') ? $this : $this.parent()
    }

    $parent.trigger(e = $.Event('close.bs.alert'))

    if (e.isDefaultPrevented()) return

    $parent.removeClass('in')

    function removeElement() {
      // detach from parent, fire event then clean up data
      $parent.detach().trigger('closed.bs.alert').remove()
    }

    $.support.transition && $parent.hasClass('fade') ?
      $parent
        .one('bsTransitionEnd', removeElement)
        .emulateTransitionEnd(150) :
      removeElement()
  }


  // ALERT PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.alert')

      if (!data) $this.data('bs.alert', (data = new Alert(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.alert

  $.fn.alert             = Plugin
  $.fn.alert.Constructor = Alert


  // ALERT NO CONFLICT
  // =================

  $.fn.alert.noConflict = function () {
    $.fn.alert = old
    return this
  }


  // ALERT DATA-API
  // ==============

  $(document).on('click.bs.alert.data-api', dismiss, Alert.prototype.close)

}(jQuery);
;/* ========================================================================
 * Bootstrap: button.js v3.2.0
 * http://getbootstrap.com/javascript/#buttons
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // BUTTON PUBLIC CLASS DEFINITION
  // ==============================

  var Button = function (element, options) {
    this.$element  = $(element)
    this.options   = $.extend({}, Button.DEFAULTS, options)
    this.isLoading = false
  }

  Button.VERSION  = '3.2.0'

  Button.DEFAULTS = {
    loadingText: 'loading...'
  }

  Button.prototype.setState = function (state) {
    var d    = 'disabled'
    var $el  = this.$element
    var val  = $el.is('input') ? 'val' : 'html'
    var data = $el.data()

    state = state + 'Text'

    if (data.resetText == null) $el.data('resetText', $el[val]())

    $el[val](data[state] == null ? this.options[state] : data[state])

    // push to event loop to allow forms to submit
    setTimeout($.proxy(function () {
      if (state == 'loadingText') {
        this.isLoading = true
        $el.addClass(d).attr(d, d)
      } else if (this.isLoading) {
        this.isLoading = false
        $el.removeClass(d).removeAttr(d)
      }
    }, this), 0)
  }

  Button.prototype.toggle = function () {
    var changed = true
    var $parent = this.$element.closest('[data-toggle="buttons"]')

    if ($parent.length) {
      var $input = this.$element.find('input')
      if ($input.prop('type') == 'radio') {
        if ($input.prop('checked') && this.$element.hasClass('active')) changed = false
        else $parent.find('.active').removeClass('active')
      }
      if (changed) $input.prop('checked', !this.$element.hasClass('active')).trigger('change')
    }

    if (changed) this.$element.toggleClass('active')
  }


  // BUTTON PLUGIN DEFINITION
  // ========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.button')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.button', (data = new Button(this, options)))

      if (option == 'toggle') data.toggle()
      else if (option) data.setState(option)
    })
  }

  var old = $.fn.button

  $.fn.button             = Plugin
  $.fn.button.Constructor = Button


  // BUTTON NO CONFLICT
  // ==================

  $.fn.button.noConflict = function () {
    $.fn.button = old
    return this
  }


  // BUTTON DATA-API
  // ===============

  $(document).on('click.bs.button.data-api', '[data-toggle^="button"]', function (e) {
    var $btn = $(e.target)
    if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn')
    Plugin.call($btn, 'toggle')
    e.preventDefault()
  })

}(jQuery);
;/* ========================================================================
 * Bootstrap: carousel.js v3.2.0
 * http://getbootstrap.com/javascript/#carousel
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // CAROUSEL CLASS DEFINITION
  // =========================

  var Carousel = function (element, options) {
    this.$element    = $(element).on('keydown.bs.carousel', $.proxy(this.keydown, this))
    this.$indicators = this.$element.find('.carousel-indicators')
    this.options     = options
    this.paused      =
    this.sliding     =
    this.interval    =
    this.$active     =
    this.$items      = null

    this.options.pause == 'hover' && this.$element
      .on('mouseenter.bs.carousel', $.proxy(this.pause, this))
      .on('mouseleave.bs.carousel', $.proxy(this.cycle, this))
  }

  Carousel.VERSION  = '3.2.0'

  Carousel.DEFAULTS = {
    interval: 5000,
    pause: 'hover',
    wrap: true
  }

  Carousel.prototype.keydown = function (e) {
    switch (e.which) {
      case 37: this.prev(); break
      case 39: this.next(); break
      default: return
    }

    e.preventDefault()
  }

  Carousel.prototype.cycle = function (e) {
    e || (this.paused = false)

    this.interval && clearInterval(this.interval)

    this.options.interval
      && !this.paused
      && (this.interval = setInterval($.proxy(this.next, this), this.options.interval))

    return this
  }

  Carousel.prototype.getItemIndex = function (item) {
    this.$items = item.parent().children('.item')
    return this.$items.index(item || this.$active)
  }

  Carousel.prototype.to = function (pos) {
    var that        = this
    var activeIndex = this.getItemIndex(this.$active = this.$element.find('.item.active'))

    if (pos > (this.$items.length - 1) || pos < 0) return

    if (this.sliding)       return this.$element.one('slid.bs.carousel', function () { that.to(pos) }) // yes, "slid"
    if (activeIndex == pos) return this.pause().cycle()

    return this.slide(pos > activeIndex ? 'next' : 'prev', $(this.$items[pos]))
  }

  Carousel.prototype.pause = function (e) {
    e || (this.paused = true)

    if (this.$element.find('.next, .prev').length && $.support.transition) {
      this.$element.trigger($.support.transition.end)
      this.cycle(true)
    }

    this.interval = clearInterval(this.interval)

    return this
  }

  Carousel.prototype.next = function () {
    if (this.sliding) return
    return this.slide('next')
  }

  Carousel.prototype.prev = function () {
    if (this.sliding) return
    return this.slide('prev')
  }

  Carousel.prototype.slide = function (type, next) {
    var $active   = this.$element.find('.item.active')
    var $next     = next || $active[type]()
    var isCycling = this.interval
    var direction = type == 'next' ? 'left' : 'right'
    var fallback  = type == 'next' ? 'first' : 'last'
    var that      = this

    if (!$next.length) {
      if (!this.options.wrap) return
      $next = this.$element.find('.item')[fallback]()
    }

    if ($next.hasClass('active')) return (this.sliding = false)

    var relatedTarget = $next[0]
    var slideEvent = $.Event('slide.bs.carousel', {
      relatedTarget: relatedTarget,
      direction: direction
    })
    this.$element.trigger(slideEvent)
    if (slideEvent.isDefaultPrevented()) return

    this.sliding = true

    isCycling && this.pause()

    if (this.$indicators.length) {
      this.$indicators.find('.active').removeClass('active')
      var $nextIndicator = $(this.$indicators.children()[this.getItemIndex($next)])
      $nextIndicator && $nextIndicator.addClass('active')
    }

    var slidEvent = $.Event('slid.bs.carousel', { relatedTarget: relatedTarget, direction: direction }) // yes, "slid"
    if ($.support.transition && this.$element.hasClass('slide')) {
      $next.addClass(type)
      $next[0].offsetWidth // force reflow
      $active.addClass(direction)
      $next.addClass(direction)
      $active
        .one('bsTransitionEnd', function () {
          $next.removeClass([type, direction].join(' ')).addClass('active')
          $active.removeClass(['active', direction].join(' '))
          that.sliding = false
          setTimeout(function () {
            that.$element.trigger(slidEvent)
          }, 0)
        })
        .emulateTransitionEnd($active.css('transition-duration').slice(0, -1) * 1000)
    } else {
      $active.removeClass('active')
      $next.addClass('active')
      this.sliding = false
      this.$element.trigger(slidEvent)
    }

    isCycling && this.cycle()

    return this
  }


  // CAROUSEL PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.carousel')
      var options = $.extend({}, Carousel.DEFAULTS, $this.data(), typeof option == 'object' && option)
      var action  = typeof option == 'string' ? option : options.slide

      if (!data) $this.data('bs.carousel', (data = new Carousel(this, options)))
      if (typeof option == 'number') data.to(option)
      else if (action) data[action]()
      else if (options.interval) data.pause().cycle()
    })
  }

  var old = $.fn.carousel

  $.fn.carousel             = Plugin
  $.fn.carousel.Constructor = Carousel


  // CAROUSEL NO CONFLICT
  // ====================

  $.fn.carousel.noConflict = function () {
    $.fn.carousel = old
    return this
  }


  // CAROUSEL DATA-API
  // =================

  $(document).on('click.bs.carousel.data-api', '[data-slide], [data-slide-to]', function (e) {
    var href
    var $this   = $(this)
    var $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) // strip for ie7
    if (!$target.hasClass('carousel')) return
    var options = $.extend({}, $target.data(), $this.data())
    var slideIndex = $this.attr('data-slide-to')
    if (slideIndex) options.interval = false

    Plugin.call($target, options)

    if (slideIndex) {
      $target.data('bs.carousel').to(slideIndex)
    }

    e.preventDefault()
  })

  $(window).on('load', function () {
    $('[data-ride="carousel"]').each(function () {
      var $carousel = $(this)
      Plugin.call($carousel, $carousel.data())
    })
  })

}(jQuery);
;/* ========================================================================
 * Bootstrap: collapse.js v3.2.0
 * http://getbootstrap.com/javascript/#collapse
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // COLLAPSE PUBLIC CLASS DEFINITION
  // ================================

  var Collapse = function (element, options) {
    this.$element      = $(element)
    this.options       = $.extend({}, Collapse.DEFAULTS, options)
    this.transitioning = null

    if (this.options.parent) this.$parent = $(this.options.parent)
    if (this.options.toggle) this.toggle()
  }

  Collapse.VERSION  = '3.2.0'

  Collapse.DEFAULTS = {
    toggle: true
  }

  Collapse.prototype.dimension = function () {
    var hasWidth = this.$element.hasClass('width')
    return hasWidth ? 'width' : 'height'
  }

  Collapse.prototype.show = function () {
    if (this.transitioning || this.$element.hasClass('in')) return

    var startEvent = $.Event('show.bs.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    var actives = this.$parent && this.$parent.find('> .panel > .in')

    if (actives && actives.length) {
      var hasData = actives.data('bs.collapse')
      if (hasData && hasData.transitioning) return
      Plugin.call(actives, 'hide')
      hasData || actives.data('bs.collapse', null)
    }

    var dimension = this.dimension()

    this.$element
      .removeClass('collapse')
      .addClass('collapsing')[dimension](0)

    this.transitioning = 1

    var complete = function () {
      this.$element
        .removeClass('collapsing')
        .addClass('collapse in')[dimension]('')
      this.transitioning = 0
      this.$element
        .trigger('shown.bs.collapse')
    }

    if (!$.support.transition) return complete.call(this)

    var scrollSize = $.camelCase(['scroll', dimension].join('-'))

    this.$element
      .one('bsTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(350)[dimension](this.$element[0][scrollSize])
  }

  Collapse.prototype.hide = function () {
    if (this.transitioning || !this.$element.hasClass('in')) return

    var startEvent = $.Event('hide.bs.collapse')
    this.$element.trigger(startEvent)
    if (startEvent.isDefaultPrevented()) return

    var dimension = this.dimension()

    this.$element[dimension](this.$element[dimension]())[0].offsetHeight

    this.$element
      .addClass('collapsing')
      .removeClass('collapse')
      .removeClass('in')

    this.transitioning = 1

    var complete = function () {
      this.transitioning = 0
      this.$element
        .trigger('hidden.bs.collapse')
        .removeClass('collapsing')
        .addClass('collapse')
    }

    if (!$.support.transition) return complete.call(this)

    this.$element
      [dimension](0)
      .one('bsTransitionEnd', $.proxy(complete, this))
      .emulateTransitionEnd(350)
  }

  Collapse.prototype.toggle = function () {
    this[this.$element.hasClass('in') ? 'hide' : 'show']()
  }


  // COLLAPSE PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.collapse')
      var options = $.extend({}, Collapse.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data && options.toggle && option == 'show') option = !option
      if (!data) $this.data('bs.collapse', (data = new Collapse(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.collapse

  $.fn.collapse             = Plugin
  $.fn.collapse.Constructor = Collapse


  // COLLAPSE NO CONFLICT
  // ====================

  $.fn.collapse.noConflict = function () {
    $.fn.collapse = old
    return this
  }


  // COLLAPSE DATA-API
  // =================

  $(document).on('click.bs.collapse.data-api', '[data-toggle="collapse"]', function (e) {
    var href
    var $this   = $(this)
    var target  = $this.attr('data-target')
        || e.preventDefault()
        || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') // strip for ie7
    var $target = $(target)
    var data    = $target.data('bs.collapse')
    var option  = data ? 'toggle' : $this.data()
    var parent  = $this.attr('data-parent')
    var $parent = parent && $(parent)

    if (!data || !data.transitioning) {
      if ($parent) $parent.find('[data-toggle="collapse"][data-parent="' + parent + '"]').not($this).addClass('collapsed')
      $this[$target.hasClass('in') ? 'addClass' : 'removeClass']('collapsed')
    }

    Plugin.call($target, option)
  })

}(jQuery);
;/* ========================================================================
 * Bootstrap: dropdown.js v3.2.0
 * http://getbootstrap.com/javascript/#dropdowns
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // DROPDOWN CLASS DEFINITION
  // =========================

  var backdrop = '.dropdown-backdrop'
  var toggle   = '[data-toggle="dropdown"]'
  var Dropdown = function (element) {
    $(element).on('click.bs.dropdown', this.toggle)
  }

  Dropdown.VERSION = '3.2.0'

  Dropdown.prototype.toggle = function (e) {
    var $this = $(this)

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    clearMenus()

    if (!isActive) {
      if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
        // if mobile we use a backdrop because click events don't delegate
        $('<div class="dropdown-backdrop"/>').insertAfter($(this)).on('click', clearMenus)
      }

      var relatedTarget = { relatedTarget: this }
      $parent.trigger(e = $.Event('show.bs.dropdown', relatedTarget))

      if (e.isDefaultPrevented()) return

      $this.trigger('focus')

      $parent
        .toggleClass('open')
        .trigger('shown.bs.dropdown', relatedTarget)
    }

    return false
  }

  Dropdown.prototype.keydown = function (e) {
    if (!/(38|40|27)/.test(e.keyCode)) return

    var $this = $(this)

    e.preventDefault()
    e.stopPropagation()

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    if (!isActive || (isActive && e.keyCode == 27)) {
      if (e.which == 27) $parent.find(toggle).trigger('focus')
      return $this.trigger('click')
    }

    var desc = ' li:not(.divider):visible a'
    var $items = $parent.find('[role="menu"]' + desc + ', [role="listbox"]' + desc)

    if (!$items.length) return

    var index = $items.index($items.filter(':focus'))

    if (e.keyCode == 38 && index > 0)                 index--                        // up
    if (e.keyCode == 40 && index < $items.length - 1) index++                        // down
    if (!~index)                                      index = 0

    $items.eq(index).trigger('focus')
  }

  function clearMenus(e) {
    if (e && e.which === 3) return
    $(backdrop).remove()
    $(toggle).each(function () {
      var $parent = getParent($(this))
      var relatedTarget = { relatedTarget: this }
      if (!$parent.hasClass('open')) return
      $parent.trigger(e = $.Event('hide.bs.dropdown', relatedTarget))
      if (e.isDefaultPrevented()) return
      $parent.removeClass('open').trigger('hidden.bs.dropdown', relatedTarget)
    })
  }

  function getParent($this) {
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = selector && $(selector)

    return $parent && $parent.length ? $parent : $this.parent()
  }


  // DROPDOWN PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.dropdown')

      if (!data) $this.data('bs.dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.dropdown

  $.fn.dropdown             = Plugin
  $.fn.dropdown.Constructor = Dropdown


  // DROPDOWN NO CONFLICT
  // ====================

  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }


  // APPLY TO STANDARD DROPDOWN ELEMENTS
  // ===================================

  $(document)
    .on('click.bs.dropdown.data-api', clearMenus)
    .on('click.bs.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
    .on('click.bs.dropdown.data-api', toggle, Dropdown.prototype.toggle)
    .on('keydown.bs.dropdown.data-api', toggle + ', [role="menu"], [role="listbox"]', Dropdown.prototype.keydown)

}(jQuery);
;/* ========================================================================
 * Bootstrap: modal.js v3.2.0
 * http://getbootstrap.com/javascript/#modals
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // MODAL CLASS DEFINITION
  // ======================

  var Modal = function (element, options) {
    this.options        = options
    this.$body          = $(document.body)
    this.$element       = $(element)
    this.$backdrop      =
    this.isShown        = null
    this.scrollbarWidth = 0

    if (this.options.remote) {
      this.$element
        .find('.modal-content')
        .load(this.options.remote, $.proxy(function () {
          this.$element.trigger('loaded.bs.modal')
        }, this))
    }
  }

  Modal.VERSION  = '3.2.0'

  Modal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  }

  Modal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget)
  }

  Modal.prototype.show = function (_relatedTarget) {
    var that = this
    var e    = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.checkScrollbar()
    this.$body.addClass('modal-open')

    this.setScrollbar()
    this.escape()

    this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade')

      if (!that.$element.parent().length) {
        that.$element.appendTo(that.$body) // don't move modals dom position
      }

      that.$element
        .show()
        .scrollTop(0)

      if (transition) {
        that.$element[0].offsetWidth // force reflow
      }

      that.$element
        .addClass('in')
        .attr('aria-hidden', false)

      that.enforceFocus()

      var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

      transition ?
        that.$element.find('.modal-dialog') // wait for modal to slide in
          .one('bsTransitionEnd', function () {
            that.$element.trigger('focus').trigger(e)
          })
          .emulateTransitionEnd(300) :
        that.$element.trigger('focus').trigger(e)
    })
  }

  Modal.prototype.hide = function (e) {
    if (e) e.preventDefault()

    e = $.Event('hide.bs.modal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.$body.removeClass('modal-open')

    this.resetScrollbar()
    this.escape()

    $(document).off('focusin.bs.modal')

    this.$element
      .removeClass('in')
      .attr('aria-hidden', true)
      .off('click.dismiss.bs.modal')

    $.support.transition && this.$element.hasClass('fade') ?
      this.$element
        .one('bsTransitionEnd', $.proxy(this.hideModal, this))
        .emulateTransitionEnd(300) :
      this.hideModal()
  }

  Modal.prototype.enforceFocus = function () {
    $(document)
      .off('focusin.bs.modal') // guard against infinite focus loop
      .on('focusin.bs.modal', $.proxy(function (e) {
        if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
          this.$element.trigger('focus')
        }
      }, this))
  }

  Modal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keyup.dismiss.bs.modal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keyup.dismiss.bs.modal')
    }
  }

  Modal.prototype.hideModal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.$element.trigger('hidden.bs.modal')
    })
  }

  Modal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove()
    this.$backdrop = null
  }

  Modal.prototype.backdrop = function (callback) {
    var that = this
    var animate = this.$element.hasClass('fade') ? 'fade' : ''

    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate

      this.$backdrop = $('<div class="modal-backdrop ' + animate + '" />')
        .appendTo(this.$body)

      this.$element.on('click.dismiss.bs.modal', $.proxy(function (e) {
        if (e.target !== e.currentTarget) return
        this.options.backdrop == 'static'
          ? this.$element[0].focus.call(this.$element[0])
          : this.hide.call(this)
      }, this))

      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

      this.$backdrop.addClass('in')

      if (!callback) return

      doAnimate ?
        this.$backdrop
          .one('bsTransitionEnd', callback)
          .emulateTransitionEnd(150) :
        callback()

    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in')

      var callbackRemove = function () {
        that.removeBackdrop()
        callback && callback()
      }
      $.support.transition && this.$element.hasClass('fade') ?
        this.$backdrop
          .one('bsTransitionEnd', callbackRemove)
          .emulateTransitionEnd(150) :
        callbackRemove()

    } else if (callback) {
      callback()
    }
  }

  Modal.prototype.checkScrollbar = function () {
    if (document.body.clientWidth >= window.innerWidth) return
    this.scrollbarWidth = this.scrollbarWidth || this.measureScrollbar()
  }

  Modal.prototype.setScrollbar = function () {
    var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
    if (this.scrollbarWidth) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
  }

  Modal.prototype.resetScrollbar = function () {
    this.$body.css('padding-right', '')
  }

  Modal.prototype.measureScrollbar = function () { // thx walsh
    var scrollDiv = document.createElement('div')
    scrollDiv.className = 'modal-scrollbar-measure'
    this.$body.append(scrollDiv)
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
    this.$body[0].removeChild(scrollDiv)
    return scrollbarWidth
  }


  // MODAL PLUGIN DEFINITION
  // =======================

  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.modal')
      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
      if (typeof option == 'string') data[option](_relatedTarget)
      else if (options.show) data.show(_relatedTarget)
    })
  }

  var old = $.fn.modal

  $.fn.modal             = Plugin
  $.fn.modal.Constructor = Modal


  // MODAL NO CONFLICT
  // =================

  $.fn.modal.noConflict = function () {
    $.fn.modal = old
    return this
  }


  // MODAL DATA-API
  // ==============

  $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
    var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target.one('show.bs.modal', function (showEvent) {
      if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
      $target.one('hidden.bs.modal', function () {
        $this.is(':visible') && $this.trigger('focus')
      })
    })
    Plugin.call($target, option, this)
  })

}(jQuery);
;/* ========================================================================
 * Bootstrap: tooltip.js v3.2.0
 * http://getbootstrap.com/javascript/#tooltip
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // TOOLTIP PUBLIC CLASS DEFINITION
  // ===============================

  var Tooltip = function (element, options) {
    this.type       =
    this.options    =
    this.enabled    =
    this.timeout    =
    this.hoverState =
    this.$element   = null

    this.init('tooltip', element, options)
  }

  Tooltip.VERSION  = '3.2.0'

  Tooltip.DEFAULTS = {
    animation: true,
    placement: 'top',
    selector: false,
    template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
    trigger: 'hover focus',
    title: '',
    delay: 0,
    html: false,
    container: false,
    viewport: {
      selector: 'body',
      padding: 0
    }
  }

  Tooltip.prototype.init = function (type, element, options) {
    this.enabled   = true
    this.type      = type
    this.$element  = $(element)
    this.options   = this.getOptions(options)
    this.$viewport = this.options.viewport && $(this.options.viewport.selector || this.options.viewport)

    var triggers = this.options.trigger.split(' ')

    for (var i = triggers.length; i--;) {
      var trigger = triggers[i]

      if (trigger == 'click') {
        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
      } else if (trigger != 'manual') {
        var eventIn  = trigger == 'hover' ? 'mouseenter' : 'focusin'
        var eventOut = trigger == 'hover' ? 'mouseleave' : 'focusout'

        this.$element.on(eventIn  + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
      }
    }

    this.options.selector ?
      (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
      this.fixTitle()
  }

  Tooltip.prototype.getDefaults = function () {
    return Tooltip.DEFAULTS
  }

  Tooltip.prototype.getOptions = function (options) {
    options = $.extend({}, this.getDefaults(), this.$element.data(), options)

    if (options.delay && typeof options.delay == 'number') {
      options.delay = {
        show: options.delay,
        hide: options.delay
      }
    }

    return options
  }

  Tooltip.prototype.getDelegateOptions = function () {
    var options  = {}
    var defaults = this.getDefaults()

    this._options && $.each(this._options, function (key, value) {
      if (defaults[key] != value) options[key] = value
    })

    return options
  }

  Tooltip.prototype.enter = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type)

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    clearTimeout(self.timeout)

    self.hoverState = 'in'

    if (!self.options.delay || !self.options.delay.show) return self.show()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'in') self.show()
    }, self.options.delay.show)
  }

  Tooltip.prototype.leave = function (obj) {
    var self = obj instanceof this.constructor ?
      obj : $(obj.currentTarget).data('bs.' + this.type)

    if (!self) {
      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
      $(obj.currentTarget).data('bs.' + this.type, self)
    }

    clearTimeout(self.timeout)

    self.hoverState = 'out'

    if (!self.options.delay || !self.options.delay.hide) return self.hide()

    self.timeout = setTimeout(function () {
      if (self.hoverState == 'out') self.hide()
    }, self.options.delay.hide)
  }

  Tooltip.prototype.show = function () {
    var e = $.Event('show.bs.' + this.type)

    if (this.hasContent() && this.enabled) {
      this.$element.trigger(e)

      var inDom = $.contains(document.documentElement, this.$element[0])
      if (e.isDefaultPrevented() || !inDom) return
      var that = this

      var $tip = this.tip()

      var tipId = this.getUID(this.type)

      this.setContent()
      $tip.attr('id', tipId)
      this.$element.attr('aria-describedby', tipId)

      if (this.options.animation) $tip.addClass('fade')

      var placement = typeof this.options.placement == 'function' ?
        this.options.placement.call(this, $tip[0], this.$element[0]) :
        this.options.placement

      var autoToken = /\s?auto?\s?/i
      var autoPlace = autoToken.test(placement)
      if (autoPlace) placement = placement.replace(autoToken, '') || 'top'

      $tip
        .detach()
        .css({ top: 0, left: 0, display: 'block' })
        .addClass(placement)
        .data('bs.' + this.type, this)

      this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)

      var pos          = this.getPosition()
      var actualWidth  = $tip[0].offsetWidth
      var actualHeight = $tip[0].offsetHeight

      if (autoPlace) {
        var orgPlacement = placement
        var $parent      = this.$element.parent()
        var parentDim    = this.getPosition($parent)

        placement = placement == 'bottom' && pos.top   + pos.height       + actualHeight - parentDim.scroll > parentDim.height ? 'top'    :
                    placement == 'top'    && pos.top   - parentDim.scroll - actualHeight < 0                                   ? 'bottom' :
                    placement == 'right'  && pos.right + actualWidth      > parentDim.width                                    ? 'left'   :
                    placement == 'left'   && pos.left  - actualWidth      < parentDim.left                                     ? 'right'  :
                    placement

        $tip
          .removeClass(orgPlacement)
          .addClass(placement)
      }

      var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight)

      this.applyPlacement(calculatedOffset, placement)

      var complete = function () {
        that.$element.trigger('shown.bs.' + that.type)
        that.hoverState = null
      }

      $.support.transition && this.$tip.hasClass('fade') ?
        $tip
          .one('bsTransitionEnd', complete)
          .emulateTransitionEnd(150) :
        complete()
    }
  }

  Tooltip.prototype.applyPlacement = function (offset, placement) {
    var $tip   = this.tip()
    var width  = $tip[0].offsetWidth
    var height = $tip[0].offsetHeight

    // manually read margins because getBoundingClientRect includes difference
    var marginTop = parseInt($tip.css('margin-top'), 10)
    var marginLeft = parseInt($tip.css('margin-left'), 10)

    // we must check for NaN for ie 8/9
    if (isNaN(marginTop))  marginTop  = 0
    if (isNaN(marginLeft)) marginLeft = 0

    offset.top  = offset.top  + marginTop
    offset.left = offset.left + marginLeft

    // $.fn.offset doesn't round pixel values
    // so we use setOffset directly with our own function B-0
    $.offset.setOffset($tip[0], $.extend({
      using: function (props) {
        $tip.css({
          top: Math.round(props.top),
          left: Math.round(props.left)
        })
      }
    }, offset), 0)

    $tip.addClass('in')

    // check to see if placing tip in new offset caused the tip to resize itself
    var actualWidth  = $tip[0].offsetWidth
    var actualHeight = $tip[0].offsetHeight

    if (placement == 'top' && actualHeight != height) {
      offset.top = offset.top + height - actualHeight
    }

    var delta = this.getViewportAdjustedDelta(placement, offset, actualWidth, actualHeight)

    if (delta.left) offset.left += delta.left
    else offset.top += delta.top

    var arrowDelta          = delta.left ? delta.left * 2 - width + actualWidth : delta.top * 2 - height + actualHeight
    var arrowPosition       = delta.left ? 'left'        : 'top'
    var arrowOffsetPosition = delta.left ? 'offsetWidth' : 'offsetHeight'

    $tip.offset(offset)
    this.replaceArrow(arrowDelta, $tip[0][arrowOffsetPosition], arrowPosition)
  }

  Tooltip.prototype.replaceArrow = function (delta, dimension, position) {
    this.arrow().css(position, delta ? (50 * (1 - delta / dimension) + '%') : '')
  }

  Tooltip.prototype.setContent = function () {
    var $tip  = this.tip()
    var title = this.getTitle()

    $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
    $tip.removeClass('fade in top bottom left right')
  }

  Tooltip.prototype.hide = function () {
    var that = this
    var $tip = this.tip()
    var e    = $.Event('hide.bs.' + this.type)

    this.$element.removeAttr('aria-describedby')

    function complete() {
      if (that.hoverState != 'in') $tip.detach()
      that.$element.trigger('hidden.bs.' + that.type)
    }

    this.$element.trigger(e)

    if (e.isDefaultPrevented()) return

    $tip.removeClass('in')

    $.support.transition && this.$tip.hasClass('fade') ?
      $tip
        .one('bsTransitionEnd', complete)
        .emulateTransitionEnd(150) :
      complete()

    this.hoverState = null

    return this
  }

  Tooltip.prototype.fixTitle = function () {
    var $e = this.$element
    if ($e.attr('title') || typeof ($e.attr('data-original-title')) != 'string') {
      $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
    }
  }

  Tooltip.prototype.hasContent = function () {
    return this.getTitle()
  }

  Tooltip.prototype.getPosition = function ($element) {
    $element   = $element || this.$element
    var el     = $element[0]
    var isBody = el.tagName == 'BODY'
    return $.extend({}, (typeof el.getBoundingClientRect == 'function') ? el.getBoundingClientRect() : null, {
      scroll: isBody ? document.documentElement.scrollTop || document.body.scrollTop : $element.scrollTop(),
      width:  isBody ? $(window).width()  : $element.outerWidth(),
      height: isBody ? $(window).height() : $element.outerHeight()
    }, isBody ? { top: 0, left: 0 } : $element.offset())
  }

  Tooltip.prototype.getCalculatedOffset = function (placement, pos, actualWidth, actualHeight) {
    return placement == 'bottom' ? { top: pos.top + pos.height,   left: pos.left + pos.width / 2 - actualWidth / 2  } :
           placement == 'top'    ? { top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2  } :
           placement == 'left'   ? { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth } :
        /* placement == 'right' */ { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width   }

  }

  Tooltip.prototype.getViewportAdjustedDelta = function (placement, pos, actualWidth, actualHeight) {
    var delta = { top: 0, left: 0 }
    if (!this.$viewport) return delta

    var viewportPadding = this.options.viewport && this.options.viewport.padding || 0
    var viewportDimensions = this.getPosition(this.$viewport)

    if (/right|left/.test(placement)) {
      var topEdgeOffset    = pos.top - viewportPadding - viewportDimensions.scroll
      var bottomEdgeOffset = pos.top + viewportPadding - viewportDimensions.scroll + actualHeight
      if (topEdgeOffset < viewportDimensions.top) { // top overflow
        delta.top = viewportDimensions.top - topEdgeOffset
      } else if (bottomEdgeOffset > viewportDimensions.top + viewportDimensions.height) { // bottom overflow
        delta.top = viewportDimensions.top + viewportDimensions.height - bottomEdgeOffset
      }
    } else {
      var leftEdgeOffset  = pos.left - viewportPadding
      var rightEdgeOffset = pos.left + viewportPadding + actualWidth
      if (leftEdgeOffset < viewportDimensions.left) { // left overflow
        delta.left = viewportDimensions.left - leftEdgeOffset
      } else if (rightEdgeOffset > viewportDimensions.width) { // right overflow
        delta.left = viewportDimensions.left + viewportDimensions.width - rightEdgeOffset
      }
    }

    return delta
  }

  Tooltip.prototype.getTitle = function () {
    var title
    var $e = this.$element
    var o  = this.options

    title = $e.attr('data-original-title')
      || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

    return title
  }

  Tooltip.prototype.getUID = function (prefix) {
    do prefix += ~~(Math.random() * 1000000)
    while (document.getElementById(prefix))
    return prefix
  }

  Tooltip.prototype.tip = function () {
    return (this.$tip = this.$tip || $(this.options.template))
  }

  Tooltip.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find('.tooltip-arrow'))
  }

  Tooltip.prototype.validate = function () {
    if (!this.$element[0].parentNode) {
      this.hide()
      this.$element = null
      this.options  = null
    }
  }

  Tooltip.prototype.enable = function () {
    this.enabled = true
  }

  Tooltip.prototype.disable = function () {
    this.enabled = false
  }

  Tooltip.prototype.toggleEnabled = function () {
    this.enabled = !this.enabled
  }

  Tooltip.prototype.toggle = function (e) {
    var self = this
    if (e) {
      self = $(e.currentTarget).data('bs.' + this.type)
      if (!self) {
        self = new this.constructor(e.currentTarget, this.getDelegateOptions())
        $(e.currentTarget).data('bs.' + this.type, self)
      }
    }

    self.tip().hasClass('in') ? self.leave(self) : self.enter(self)
  }

  Tooltip.prototype.destroy = function () {
    clearTimeout(this.timeout)
    this.hide().$element.off('.' + this.type).removeData('bs.' + this.type)
  }


  // TOOLTIP PLUGIN DEFINITION
  // =========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.tooltip')
      var options = typeof option == 'object' && option

      if (!data && option == 'destroy') return
      if (!data) $this.data('bs.tooltip', (data = new Tooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.tooltip

  $.fn.tooltip             = Plugin
  $.fn.tooltip.Constructor = Tooltip


  // TOOLTIP NO CONFLICT
  // ===================

  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old
    return this
  }

}(jQuery);
;/* ========================================================================
 * Bootstrap: popover.js v3.2.0
 * http://getbootstrap.com/javascript/#popovers
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // POPOVER PUBLIC CLASS DEFINITION
  // ===============================

  var Popover = function (element, options) {
    this.init('popover', element, options)
  }

  if (!$.fn.tooltip) throw new Error('Popover requires tooltip.js')

  Popover.VERSION  = '3.2.0'

  Popover.DEFAULTS = $.extend({}, $.fn.tooltip.Constructor.DEFAULTS, {
    placement: 'right',
    trigger: 'click',
    content: '',
    template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
  })


  // NOTE: POPOVER EXTENDS tooltip.js
  // ================================

  Popover.prototype = $.extend({}, $.fn.tooltip.Constructor.prototype)

  Popover.prototype.constructor = Popover

  Popover.prototype.getDefaults = function () {
    return Popover.DEFAULTS
  }

  Popover.prototype.setContent = function () {
    var $tip    = this.tip()
    var title   = this.getTitle()
    var content = this.getContent()

    $tip.find('.popover-title')[this.options.html ? 'html' : 'text'](title)
    $tip.find('.popover-content').empty()[ // we use append for html objects to maintain js events
      this.options.html ? (typeof content == 'string' ? 'html' : 'append') : 'text'
    ](content)

    $tip.removeClass('fade top bottom left right in')

    // IE8 doesn't accept hiding via the `:empty` pseudo selector, we have to do
    // this manually by checking the contents.
    if (!$tip.find('.popover-title').html()) $tip.find('.popover-title').hide()
  }

  Popover.prototype.hasContent = function () {
    return this.getTitle() || this.getContent()
  }

  Popover.prototype.getContent = function () {
    var $e = this.$element
    var o  = this.options

    return $e.attr('data-content')
      || (typeof o.content == 'function' ?
            o.content.call($e[0]) :
            o.content)
  }

  Popover.prototype.arrow = function () {
    return (this.$arrow = this.$arrow || this.tip().find('.arrow'))
  }

  Popover.prototype.tip = function () {
    if (!this.$tip) this.$tip = $(this.options.template)
    return this.$tip
  }


  // POPOVER PLUGIN DEFINITION
  // =========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.popover')
      var options = typeof option == 'object' && option

      if (!data && option == 'destroy') return
      if (!data) $this.data('bs.popover', (data = new Popover(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.popover

  $.fn.popover             = Plugin
  $.fn.popover.Constructor = Popover


  // POPOVER NO CONFLICT
  // ===================

  $.fn.popover.noConflict = function () {
    $.fn.popover = old
    return this
  }

}(jQuery);
;/* ========================================================================
 * Bootstrap: scrollspy.js v3.2.0
 * http://getbootstrap.com/javascript/#scrollspy
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // SCROLLSPY CLASS DEFINITION
  // ==========================

  function ScrollSpy(element, options) {
    var process  = $.proxy(this.process, this)

    this.$body          = $('body')
    this.$scrollElement = $(element).is('body') ? $(window) : $(element)
    this.options        = $.extend({}, ScrollSpy.DEFAULTS, options)
    this.selector       = (this.options.target || '') + ' .nav li > a'
    this.offsets        = []
    this.targets        = []
    this.activeTarget   = null
    this.scrollHeight   = 0

    this.$scrollElement.on('scroll.bs.scrollspy', process)
    this.refresh()
    this.process()
  }

  ScrollSpy.VERSION  = '3.2.0'

  ScrollSpy.DEFAULTS = {
    offset: 10
  }

  ScrollSpy.prototype.getScrollHeight = function () {
    return this.$scrollElement[0].scrollHeight || Math.max(this.$body[0].scrollHeight, document.documentElement.scrollHeight)
  }

  ScrollSpy.prototype.refresh = function () {
    var offsetMethod = 'offset'
    var offsetBase   = 0

    if (!$.isWindow(this.$scrollElement[0])) {
      offsetMethod = 'position'
      offsetBase   = this.$scrollElement.scrollTop()
    }

    this.offsets = []
    this.targets = []
    this.scrollHeight = this.getScrollHeight()

    var self     = this

    this.$body
      .find(this.selector)
      .map(function () {
        var $el   = $(this)
        var href  = $el.data('target') || $el.attr('href')
        var $href = /^#./.test(href) && $(href)

        return ($href
          && $href.length
          && $href.is(':visible')
          && [[$href[offsetMethod]().top + offsetBase, href]]) || null
      })
      .sort(function (a, b) { return a[0] - b[0] })
      .each(function () {
        self.offsets.push(this[0])
        self.targets.push(this[1])
      })
  }

  ScrollSpy.prototype.process = function () {
    var scrollTop    = this.$scrollElement.scrollTop() + this.options.offset
    var scrollHeight = this.getScrollHeight()
    var maxScroll    = this.options.offset + scrollHeight - this.$scrollElement.height()
    var offsets      = this.offsets
    var targets      = this.targets
    var activeTarget = this.activeTarget
    var i

    if (this.scrollHeight != scrollHeight) {
      this.refresh()
    }

    if (scrollTop >= maxScroll) {
      return activeTarget != (i = targets[targets.length - 1]) && this.activate(i)
    }

    if (activeTarget && scrollTop <= offsets[0]) {
      return activeTarget != (i = targets[0]) && this.activate(i)
    }

    for (i = offsets.length; i--;) {
      activeTarget != targets[i]
        && scrollTop >= offsets[i]
        && (!offsets[i + 1] || scrollTop <= offsets[i + 1])
        && this.activate(targets[i])
    }
  }

  ScrollSpy.prototype.activate = function (target) {
    this.activeTarget = target

    $(this.selector)
      .parentsUntil(this.options.target, '.active')
      .removeClass('active')

    var selector = this.selector +
        '[data-target="' + target + '"],' +
        this.selector + '[href="' + target + '"]'

    var active = $(selector)
      .parents('li')
      .addClass('active')

    if (active.parent('.dropdown-menu').length) {
      active = active
        .closest('li.dropdown')
        .addClass('active')
    }

    active.trigger('activate.bs.scrollspy')
  }


  // SCROLLSPY PLUGIN DEFINITION
  // ===========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.scrollspy')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.scrollspy', (data = new ScrollSpy(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.scrollspy

  $.fn.scrollspy             = Plugin
  $.fn.scrollspy.Constructor = ScrollSpy


  // SCROLLSPY NO CONFLICT
  // =====================

  $.fn.scrollspy.noConflict = function () {
    $.fn.scrollspy = old
    return this
  }


  // SCROLLSPY DATA-API
  // ==================

  $(window).on('load.bs.scrollspy.data-api', function () {
    $('[data-spy="scroll"]').each(function () {
      var $spy = $(this)
      Plugin.call($spy, $spy.data())
    })
  })

}(jQuery);
;/* ========================================================================
 * Bootstrap: tab.js v3.2.0
 * http://getbootstrap.com/javascript/#tabs
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // TAB CLASS DEFINITION
  // ====================

  var Tab = function (element) {
    this.element = $(element)
  }

  Tab.VERSION = '3.2.0'

  Tab.prototype.show = function () {
    var $this    = this.element
    var $ul      = $this.closest('ul:not(.dropdown-menu)')
    var selector = $this.data('target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    if ($this.parent('li').hasClass('active')) return

    var previous = $ul.find('.active:last a')[0]
    var e        = $.Event('show.bs.tab', {
      relatedTarget: previous
    })

    $this.trigger(e)

    if (e.isDefaultPrevented()) return

    var $target = $(selector)

    this.activate($this.closest('li'), $ul)
    this.activate($target, $target.parent(), function () {
      $this.trigger({
        type: 'shown.bs.tab',
        relatedTarget: previous
      })
    })
  }

  Tab.prototype.activate = function (element, container, callback) {
    var $active    = container.find('> .active')
    var transition = callback
      && $.support.transition
      && $active.hasClass('fade')

    function next() {
      $active
        .removeClass('active')
        .find('> .dropdown-menu > .active')
        .removeClass('active')

      element.addClass('active')

      if (transition) {
        element[0].offsetWidth // reflow for transition
        element.addClass('in')
      } else {
        element.removeClass('fade')
      }

      if (element.parent('.dropdown-menu')) {
        element.closest('li.dropdown').addClass('active')
      }

      callback && callback()
    }

    transition ?
      $active
        .one('bsTransitionEnd', next)
        .emulateTransitionEnd(150) :
      next()

    $active.removeClass('in')
  }


  // TAB PLUGIN DEFINITION
  // =====================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.tab')

      if (!data) $this.data('bs.tab', (data = new Tab(this)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.tab

  $.fn.tab             = Plugin
  $.fn.tab.Constructor = Tab


  // TAB NO CONFLICT
  // ===============

  $.fn.tab.noConflict = function () {
    $.fn.tab = old
    return this
  }


  // TAB DATA-API
  // ============

  $(document).on('click.bs.tab.data-api', '[data-toggle="tab"], [data-toggle="pill"]', function (e) {
    e.preventDefault()
    Plugin.call($(this), 'show')
  })

}(jQuery);
;/* ========================================================================
 * Bootstrap: affix.js v3.2.0
 * http://getbootstrap.com/javascript/#affix
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // AFFIX CLASS DEFINITION
  // ======================

  var Affix = function (element, options) {
    this.options = $.extend({}, Affix.DEFAULTS, options)

    this.$target = $(this.options.target)
      .on('scroll.bs.affix.data-api', $.proxy(this.checkPosition, this))
      .on('click.bs.affix.data-api',  $.proxy(this.checkPositionWithEventLoop, this))

    this.$element     = $(element)
    this.affixed      =
    this.unpin        =
    this.pinnedOffset = null

    this.checkPosition()
  }

  Affix.VERSION  = '3.2.0'

  Affix.RESET    = 'affix affix-top affix-bottom'

  Affix.DEFAULTS = {
    offset: 0,
    target: window
  }

  Affix.prototype.getPinnedOffset = function () {
    if (this.pinnedOffset) return this.pinnedOffset
    this.$element.removeClass(Affix.RESET).addClass('affix')
    var scrollTop = this.$target.scrollTop()
    var position  = this.$element.offset()
    return (this.pinnedOffset = position.top - scrollTop)
  }

  Affix.prototype.checkPositionWithEventLoop = function () {
    setTimeout($.proxy(this.checkPosition, this), 1)
  }

  Affix.prototype.checkPosition = function () {
    if (!this.$element.is(':visible')) return

    var scrollHeight = $(document).height()
    var scrollTop    = this.$target.scrollTop()
    var position     = this.$element.offset()
    var offset       = this.options.offset
    var offsetTop    = offset.top
    var offsetBottom = offset.bottom

    if (typeof offset != 'object')         offsetBottom = offsetTop = offset
    if (typeof offsetTop == 'function')    offsetTop    = offset.top(this.$element)
    if (typeof offsetBottom == 'function') offsetBottom = offset.bottom(this.$element)

    var affix = this.unpin   != null && (scrollTop + this.unpin <= position.top) ? false :
                offsetBottom != null && (position.top + this.$element.height() >= scrollHeight - offsetBottom) ? 'bottom' :
                offsetTop    != null && (scrollTop <= offsetTop) ? 'top' : false

    if (this.affixed === affix) return
    if (this.unpin != null) this.$element.css('top', '')

    var affixType = 'affix' + (affix ? '-' + affix : '')
    var e         = $.Event(affixType + '.bs.affix')

    this.$element.trigger(e)

    if (e.isDefaultPrevented()) return

    this.affixed = affix
    this.unpin = affix == 'bottom' ? this.getPinnedOffset() : null

    this.$element
      .removeClass(Affix.RESET)
      .addClass(affixType)
      .trigger($.Event(affixType.replace('affix', 'affixed')))

    if (affix == 'bottom') {
      this.$element.offset({
        top: scrollHeight - this.$element.height() - offsetBottom
      })
    }
  }


  // AFFIX PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.affix')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.affix', (data = new Affix(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  var old = $.fn.affix

  $.fn.affix             = Plugin
  $.fn.affix.Constructor = Affix


  // AFFIX NO CONFLICT
  // =================

  $.fn.affix.noConflict = function () {
    $.fn.affix = old
    return this
  }


  // AFFIX DATA-API
  // ==============

  $(window).on('load', function () {
    $('[data-spy="affix"]').each(function () {
      var $spy = $(this)
      var data = $spy.data()

      data.offset = data.offset || {}

      if (data.offsetBottom) data.offset.bottom = data.offsetBottom
      if (data.offsetTop)    data.offset.top    = data.offsetTop

      Plugin.call($spy, data)
    })
  })

}(jQuery);
;/*!
 * fancyBox - jQuery Plugin
 * version: 2.1.5 (Fri, 14 Jun 2013)
 * @requires jQuery v1.6 or later
 *
 * Examples at http://fancyapps.com/fancybox/
 * License: www.fancyapps.com/fancybox/#license
 *
 * Copyright 2012 Janis Skarnelis - janis@fancyapps.com
 *
 */

(function (window, document, $, undefined) {
	"use strict";

	var H = $("html"),
		W = $(window),
		D = $(document),
		F = $.fancybox = function () {
			F.open.apply( this, arguments );
		},
		IE =  navigator.userAgent.match(/msie/i),
		didUpdate	= null,
		isTouch		= document.createTouch !== undefined,

		isQuery	= function(obj) {
			return obj && obj.hasOwnProperty && obj instanceof $;
		},
		isString = function(str) {
			return str && $.type(str) === "string";
		},
		isPercentage = function(str) {
			return isString(str) && str.indexOf('%') > 0;
		},
		isScrollable = function(el) {
			return (el && !(el.style.overflow && el.style.overflow === 'hidden') && ((el.clientWidth && el.scrollWidth > el.clientWidth) || (el.clientHeight && el.scrollHeight > el.clientHeight)));
		},
		getScalar = function(orig, dim) {
			var value = parseInt(orig, 10) || 0;

			if (dim && isPercentage(orig)) {
				value = F.getViewport()[ dim ] / 100 * value;
			}

			return Math.ceil(value);
		},
		getValue = function(value, dim) {
			return getScalar(value, dim) + 'px';
		};

	$.extend(F, {
		// The current version of fancyBox
		version: '2.1.5',

		defaults: {
			padding : 15,
			margin  : 20,

			width     : 800,
			height    : 600,
			minWidth  : 100,
			minHeight : 100,
			maxWidth  : 9999,
			maxHeight : 9999,
			pixelRatio: 1, // Set to 2 for retina display support

			autoSize   : true,
			autoHeight : false,
			autoWidth  : false,

			autoResize  : true,
			autoCenter  : !isTouch,
			fitToView   : true,
			aspectRatio : false,
			topRatio    : 0.5,
			leftRatio   : 0.5,

			scrolling : 'auto', // 'auto', 'yes' or 'no'
			wrapCSS   : '',

			arrows     : true,
			closeBtn   : true,
			closeClick : false,
			nextClick  : false,
			mouseWheel : true,
			autoPlay   : false,
			playSpeed  : 3000,
			preload    : 3,
			modal      : false,
			loop       : true,

			ajax  : {
				dataType : 'html',
				headers  : { 'X-fancyBox': true }
			},
			iframe : {
				scrolling : 'auto',
				preload   : true
			},
			swf : {
				wmode: 'transparent',
				allowfullscreen   : 'true',
				allowscriptaccess : 'always'
			},

			keys  : {
				next : {
					13 : 'left', // enter
					34 : 'up',   // page down
					39 : 'left', // right arrow
					40 : 'up'    // down arrow
				},
				prev : {
					8  : 'right',  // backspace
					33 : 'down',   // page up
					37 : 'right',  // left arrow
					38 : 'down'    // up arrow
				},
				close  : [27], // escape key
				play   : [32], // space - start/stop slideshow
				toggle : [70]  // letter "f" - toggle fullscreen
			},

			direction : {
				next : 'left',
				prev : 'right'
			},

			scrollOutside  : true,

			// Override some properties
			index   : 0,
			type    : null,
			href    : null,
			content : null,
			title   : null,

			// HTML templates
			tpl: {
				wrap     : '<div class="fancybox-wrap" tabIndex="-1"><div class="fancybox-skin"><div class="fancybox-outer"><div class="fancybox-inner"></div></div></div></div>',
				image    : '<img class="fancybox-image" src="{href}" alt="" />',
				iframe   : '<iframe id="fancybox-frame{rnd}" name="fancybox-frame{rnd}" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen' + (IE ? ' allowtransparency="true"' : '') + '></iframe>',
				error    : '<p class="fancybox-error">The requested content cannot be loaded.<br/>Please try again later.</p>',
				closeBtn : '<a title="Close" class="fancybox-item fancybox-close" href="javascript:;"></a>',
				next     : '<a title="Next" class="fancybox-nav fancybox-next" href="javascript:;"><span></span></a>',
				prev     : '<a title="Previous" class="fancybox-nav fancybox-prev" href="javascript:;"><span></span></a>'
			},

			// Properties for each animation type
			// Opening fancyBox
			openEffect  : 'fade', // 'elastic', 'fade' or 'none'
			openSpeed   : 250,
			openEasing  : 'swing',
			openOpacity : true,
			openMethod  : 'zoomIn',

			// Closing fancyBox
			closeEffect  : 'fade', // 'elastic', 'fade' or 'none'
			closeSpeed   : 250,
			closeEasing  : 'swing',
			closeOpacity : true,
			closeMethod  : 'zoomOut',

			// Changing next gallery item
			nextEffect : 'elastic', // 'elastic', 'fade' or 'none'
			nextSpeed  : 250,
			nextEasing : 'swing',
			nextMethod : 'changeIn',

			// Changing previous gallery item
			prevEffect : 'elastic', // 'elastic', 'fade' or 'none'
			prevSpeed  : 250,
			prevEasing : 'swing',
			prevMethod : 'changeOut',

			// Enable default helpers
			helpers : {
				overlay : true,
				title   : true
			},

			// Callbacks
			onCancel     : $.noop, // If canceling
			beforeLoad   : $.noop, // Before loading
			afterLoad    : $.noop, // After loading
			beforeShow   : $.noop, // Before changing in current item
			afterShow    : $.noop, // After opening
			beforeChange : $.noop, // Before changing gallery item
			beforeClose  : $.noop, // Before closing
			afterClose   : $.noop  // After closing
		},

		//Current state
		group    : {}, // Selected group
		opts     : {}, // Group options
		previous : null,  // Previous element
		coming   : null,  // Element being loaded
		current  : null,  // Currently loaded element
		isActive : false, // Is activated
		isOpen   : false, // Is currently open
		isOpened : false, // Have been fully opened at least once

		wrap  : null,
		skin  : null,
		outer : null,
		inner : null,

		player : {
			timer    : null,
			isActive : false
		},

		// Loaders
		ajaxLoad   : null,
		imgPreload : null,

		// Some collections
		transitions : {},
		helpers     : {},

		/*
		 *	Static methods
		 */

		open: function (group, opts) {
			if (!group) {
				return;
			}

			if (!$.isPlainObject(opts)) {
				opts = {};
			}

			// Close if already active
			if (false === F.close(true)) {
				return;
			}

			// Normalize group
			if (!$.isArray(group)) {
				group = isQuery(group) ? $(group).get() : [group];
			}

			// Recheck if the type of each element is `object` and set content type (image, ajax, etc)
			$.each(group, function(i, element) {
				var obj = {},
					href,
					title,
					content,
					type,
					rez,
					hrefParts,
					selector;

				if ($.type(element) === "object") {
					// Check if is DOM element
					if (element.nodeType) {
						element = $(element);
					}

					if (isQuery(element)) {
						obj = {
							href    : element.data('fancybox-href') || element.attr('href'),
							title   : element.data('fancybox-title') || element.attr('title'),
							isDom   : true,
							element : element
						};

						if ($.metadata) {
							$.extend(true, obj, element.metadata());
						}

					} else {
						obj = element;
					}
				}

				href  = opts.href  || obj.href || (isString(element) ? element : null);
				title = opts.title !== undefined ? opts.title : obj.title || '';

				content = opts.content || obj.content;
				type    = content ? 'html' : (opts.type  || obj.type);

				if (!type && obj.isDom) {
					type = element.data('fancybox-type');

					if (!type) {
						rez  = element.prop('class').match(/fancybox\.(\w+)/);
						type = rez ? rez[1] : null;
					}
				}

				if (isString(href)) {
					// Try to guess the content type
					if (!type) {
						if (F.isImage(href)) {
							type = 'image';

						} else if (F.isSWF(href)) {
							type = 'swf';

						} else if (href.charAt(0) === '#') {
							type = 'inline';

						} else if (isString(element)) {
							type    = 'html';
							content = element;
						}
					}

					// Split url into two pieces with source url and content selector, e.g,
					// "/mypage.html #my_id" will load "/mypage.html" and display element having id "my_id"
					if (type === 'ajax') {
						hrefParts = href.split(/\s+/, 2);
						href      = hrefParts.shift();
						selector  = hrefParts.shift();
					}
				}

				if (!content) {
					if (type === 'inline') {
						if (href) {
							content = $( isString(href) ? href.replace(/.*(?=#[^\s]+$)/, '') : href ); //strip for ie7

						} else if (obj.isDom) {
							content = element;
						}

					} else if (type === 'html') {
						content = href;

					} else if (!type && !href && obj.isDom) {
						type    = 'inline';
						content = element;
					}
				}

				$.extend(obj, {
					href     : href,
					type     : type,
					content  : content,
					title    : title,
					selector : selector
				});

				group[ i ] = obj;
			});

			// Extend the defaults
			F.opts = $.extend(true, {}, F.defaults, opts);

			// All options are merged recursive except keys
			if (opts.keys !== undefined) {
				F.opts.keys = opts.keys ? $.extend({}, F.defaults.keys, opts.keys) : false;
			}

			F.group = group;

			return F._start(F.opts.index);
		},

		// Cancel image loading or abort ajax request
		cancel: function () {
			var coming = F.coming;

			if (!coming || false === F.trigger('onCancel')) {
				return;
			}

			F.hideLoading();

			if (F.ajaxLoad) {
				F.ajaxLoad.abort();
			}

			F.ajaxLoad = null;

			if (F.imgPreload) {
				F.imgPreload.onload = F.imgPreload.onerror = null;
			}

			if (coming.wrap) {
				coming.wrap.stop(true, true).trigger('onReset').remove();
			}

			F.coming = null;

			// If the first item has been canceled, then clear everything
			if (!F.current) {
				F._afterZoomOut( coming );
			}
		},

		// Start closing animation if is open; remove immediately if opening/closing
		close: function (event) {
			F.cancel();

			if (false === F.trigger('beforeClose')) {
				return;
			}

			F.unbindEvents();

			if (!F.isActive) {
				return;
			}

			if (!F.isOpen || event === true) {
				$('.fancybox-wrap').stop(true).trigger('onReset').remove();

				F._afterZoomOut();

			} else {
				F.isOpen = F.isOpened = false;
				F.isClosing = true;

				$('.fancybox-item, .fancybox-nav').remove();

				F.wrap.stop(true, true).removeClass('fancybox-opened');

				F.transitions[ F.current.closeMethod ]();
			}
		},

		// Manage slideshow:
		//   $.fancybox.play(); - toggle slideshow
		//   $.fancybox.play( true ); - start
		//   $.fancybox.play( false ); - stop
		play: function ( action ) {
			var clear = function () {
					clearTimeout(F.player.timer);
				},
				set = function () {
					clear();

					if (F.current && F.player.isActive) {
						F.player.timer = setTimeout(F.next, F.current.playSpeed);
					}
				},
				stop = function () {
					clear();

					D.unbind('.player');

					F.player.isActive = false;

					F.trigger('onPlayEnd');
				},
				start = function () {
					if (F.current && (F.current.loop || F.current.index < F.group.length - 1)) {
						F.player.isActive = true;

						D.bind({
							'onCancel.player beforeClose.player' : stop,
							'onUpdate.player'   : set,
							'beforeLoad.player' : clear
						});

						set();

						F.trigger('onPlayStart');
					}
				};

			if (action === true || (!F.player.isActive && action !== false)) {
				start();
			} else {
				stop();
			}
		},

		// Navigate to next gallery item
		next: function ( direction ) {
			var current = F.current;

			if (current) {
				if (!isString(direction)) {
					direction = current.direction.next;
				}

				F.jumpto(current.index + 1, direction, 'next');
			}
		},

		// Navigate to previous gallery item
		prev: function ( direction ) {
			var current = F.current;

			if (current) {
				if (!isString(direction)) {
					direction = current.direction.prev;
				}

				F.jumpto(current.index - 1, direction, 'prev');
			}
		},

		// Navigate to gallery item by index
		jumpto: function ( index, direction, router ) {
			var current = F.current;

			if (!current) {
				return;
			}

			index = getScalar(index);

			F.direction = direction || current.direction[ (index >= current.index ? 'next' : 'prev') ];
			F.router    = router || 'jumpto';

			if (current.loop) {
				if (index < 0) {
					index = current.group.length + (index % current.group.length);
				}

				index = index % current.group.length;
			}

			if (current.group[ index ] !== undefined) {
				F.cancel();

				F._start(index);
			}
		},

		// Center inside viewport and toggle position type to fixed or absolute if needed
		reposition: function (e, onlyAbsolute) {
			var current = F.current,
				wrap    = current ? current.wrap : null,
				pos;

			if (wrap) {
				pos = F._getPosition(onlyAbsolute);

				if (e && e.type === 'scroll') {
					delete pos.position;

					wrap.stop(true, true).animate(pos, 200);

				} else {
					wrap.css(pos);

					current.pos = $.extend({}, current.dim, pos);
				}
			}
		},

		update: function (e) {
			var type = (e && e.type),
				anyway = !type || type === 'orientationchange';

			if (anyway) {
				clearTimeout(didUpdate);

				didUpdate = null;
			}

			if (!F.isOpen || didUpdate) {
				return;
			}

			didUpdate = setTimeout(function() {
				var current = F.current;

				if (!current || F.isClosing) {
					return;
				}

				F.wrap.removeClass('fancybox-tmp');

				if (anyway || type === 'load' || (type === 'resize' && current.autoResize)) {
					F._setDimension();
				}

				if (!(type === 'scroll' && current.canShrink)) {
					F.reposition(e);
				}

				F.trigger('onUpdate');

				didUpdate = null;

			}, (anyway && !isTouch ? 0 : 300));
		},

		// Shrink content to fit inside viewport or restore if resized
		toggle: function ( action ) {
			if (F.isOpen) {
				F.current.fitToView = $.type(action) === "boolean" ? action : !F.current.fitToView;

				// Help browser to restore document dimensions
				if (isTouch) {
					F.wrap.removeAttr('style').addClass('fancybox-tmp');

					F.trigger('onUpdate');
				}

				F.update();
			}
		},

		hideLoading: function () {
			D.unbind('.loading');

			$('#fancybox-loading').remove();
		},

		showLoading: function () {
			var el, viewport;

			F.hideLoading();

			el = $('<div id="fancybox-loading"><div></div></div>').click(F.cancel).appendTo('body');

			// If user will press the escape-button, the request will be canceled
			D.bind('keydown.loading', function(e) {
				if ((e.which || e.keyCode) === 27) {
					e.preventDefault();

					F.cancel();
				}
			});

			if (!F.defaults.fixed) {
				viewport = F.getViewport();

				el.css({
					position : 'absolute',
					top  : (viewport.h * 0.5) + viewport.y,
					left : (viewport.w * 0.5) + viewport.x
				});
			}
		},

		getViewport: function () {
			var locked = (F.current && F.current.locked) || false,
				rez    = {
					x: W.scrollLeft(),
					y: W.scrollTop()
				};

			if (locked) {
				rez.w = locked[0].clientWidth;
				rez.h = locked[0].clientHeight;

			} else {
				// See http://bugs.jquery.com/ticket/6724
				rez.w = isTouch && window.innerWidth  ? window.innerWidth  : W.width();
				rez.h = isTouch && window.innerHeight ? window.innerHeight : W.height();
			}

			return rez;
		},

		// Unbind the keyboard / clicking actions
		unbindEvents: function () {
			if (F.wrap && isQuery(F.wrap)) {
				F.wrap.unbind('.fb');
			}

			D.unbind('.fb');
			W.unbind('.fb');
		},

		bindEvents: function () {
			var current = F.current,
				keys;

			if (!current) {
				return;
			}

			// Changing document height on iOS devices triggers a 'resize' event,
			// that can change document height... repeating infinitely
			W.bind('orientationchange.fb' + (isTouch ? '' : ' resize.fb') + (current.autoCenter && !current.locked ? ' scroll.fb' : ''), F.update);

			keys = current.keys;

			if (keys) {
				D.bind('keydown.fb', function (e) {
					var code   = e.which || e.keyCode,
						target = e.target || e.srcElement;

					// Skip esc key if loading, because showLoading will cancel preloading
					if (code === 27 && F.coming) {
						return false;
					}

					// Ignore key combinations and key events within form elements
					if (!e.ctrlKey && !e.altKey && !e.shiftKey && !e.metaKey && !(target && (target.type || $(target).is('[contenteditable]')))) {
						$.each(keys, function(i, val) {
							if (current.group.length > 1 && val[ code ] !== undefined) {
								F[ i ]( val[ code ] );

								e.preventDefault();
								return false;
							}

							if ($.inArray(code, val) > -1) {
								F[ i ] ();

								e.preventDefault();
								return false;
							}
						});
					}
				});
			}

			if ($.fn.mousewheel && current.mouseWheel) {
				F.wrap.bind('mousewheel.fb', function (e, delta, deltaX, deltaY) {
					var target = e.target || null,
						parent = $(target),
						canScroll = false;

					while (parent.length) {
						if (canScroll || parent.is('.fancybox-skin') || parent.is('.fancybox-wrap')) {
							break;
						}

						canScroll = isScrollable( parent[0] );
						parent    = $(parent).parent();
					}

					if (delta !== 0 && !canScroll) {
						if (F.group.length > 1 && !current.canShrink) {
							if (deltaY > 0 || deltaX > 0) {
								F.prev( deltaY > 0 ? 'down' : 'left' );

							} else if (deltaY < 0 || deltaX < 0) {
								F.next( deltaY < 0 ? 'up' : 'right' );
							}

							e.preventDefault();
						}
					}
				});
			}
		},

		trigger: function (event, o) {
			var ret, obj = o || F.coming || F.current;

			if (!obj) {
				return;
			}

			if ($.isFunction( obj[event] )) {
				ret = obj[event].apply(obj, Array.prototype.slice.call(arguments, 1));
			}

			if (ret === false) {
				return false;
			}

			if (obj.helpers) {
				$.each(obj.helpers, function (helper, opts) {
					if (opts && F.helpers[helper] && $.isFunction(F.helpers[helper][event])) {
						F.helpers[helper][event]($.extend(true, {}, F.helpers[helper].defaults, opts), obj);
					}
				});
			}

			D.trigger(event);
		},

		isImage: function (str) {
			return isString(str) && str.match(/(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp|svg)((\?|#).*)?$)/i);
		},

		isSWF: function (str) {
			return isString(str) && str.match(/\.(swf)((\?|#).*)?$/i);
		},

		_start: function (index) {
			var coming = {},
				obj,
				href,
				type,
				margin,
				padding;

			index = getScalar( index );
			obj   = F.group[ index ] || null;

			if (!obj) {
				return false;
			}

			coming = $.extend(true, {}, F.opts, obj);

			// Convert margin and padding properties to array - top, right, bottom, left
			margin  = coming.margin;
			padding = coming.padding;

			if ($.type(margin) === 'number') {
				coming.margin = [margin, margin, margin, margin];
			}

			if ($.type(padding) === 'number') {
				coming.padding = [padding, padding, padding, padding];
			}

			// 'modal' propery is just a shortcut
			if (coming.modal) {
				$.extend(true, coming, {
					closeBtn   : false,
					closeClick : false,
					nextClick  : false,
					arrows     : false,
					mouseWheel : false,
					keys       : null,
					helpers: {
						overlay : {
							closeClick : false
						}
					}
				});
			}

			// 'autoSize' property is a shortcut, too
			if (coming.autoSize) {
				coming.autoWidth = coming.autoHeight = true;
			}

			if (coming.width === 'auto') {
				coming.autoWidth = true;
			}

			if (coming.height === 'auto') {
				coming.autoHeight = true;
			}

			/*
			 * Add reference to the group, so it`s possible to access from callbacks, example:
			 * afterLoad : function() {
			 *     this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
			 * }
			 */

			coming.group  = F.group;
			coming.index  = index;

			// Give a chance for callback or helpers to update coming item (type, title, etc)
			F.coming = coming;

			if (false === F.trigger('beforeLoad')) {
				F.coming = null;

				return;
			}

			type = coming.type;
			href = coming.href;

			if (!type) {
				F.coming = null;

				//If we can not determine content type then drop silently or display next/prev item if looping through gallery
				if (F.current && F.router && F.router !== 'jumpto') {
					F.current.index = index;

					return F[ F.router ]( F.direction );
				}

				return false;
			}

			F.isActive = true;

			if (type === 'image' || type === 'swf') {
				coming.autoHeight = coming.autoWidth = false;
				coming.scrolling  = 'visible';
			}

			if (type === 'image') {
				coming.aspectRatio = true;
			}

			if (type === 'iframe' && isTouch) {
				coming.scrolling = 'scroll';
			}

			// Build the neccessary markup
			coming.wrap = $(coming.tpl.wrap).addClass('fancybox-' + (isTouch ? 'mobile' : 'desktop') + ' fancybox-type-' + type + ' fancybox-tmp ' + coming.wrapCSS).appendTo( coming.parent || 'body' );

			$.extend(coming, {
				skin  : $('.fancybox-skin',  coming.wrap),
				outer : $('.fancybox-outer', coming.wrap),
				inner : $('.fancybox-inner', coming.wrap)
			});

			$.each(["Top", "Right", "Bottom", "Left"], function(i, v) {
				coming.skin.css('padding' + v, getValue(coming.padding[ i ]));
			});

			F.trigger('onReady');

			// Check before try to load; 'inline' and 'html' types need content, others - href
			if (type === 'inline' || type === 'html') {
				if (!coming.content || !coming.content.length) {
					return F._error( 'content' );
				}

			} else if (!href) {
				return F._error( 'href' );
			}

			if (type === 'image') {
				F._loadImage();

			} else if (type === 'ajax') {
				F._loadAjax();

			} else if (type === 'iframe') {
				F._loadIframe();

			} else {
				F._afterLoad();
			}
		},

		_error: function ( type ) {
			$.extend(F.coming, {
				type       : 'html',
				autoWidth  : true,
				autoHeight : true,
				minWidth   : 0,
				minHeight  : 0,
				scrolling  : 'no',
				hasError   : type,
				content    : F.coming.tpl.error
			});

			F._afterLoad();
		},

		_loadImage: function () {
			// Reset preload image so it is later possible to check "complete" property
			var img = F.imgPreload = new Image();

			img.onload = function () {
				this.onload = this.onerror = null;

				F.coming.width  = this.width / F.opts.pixelRatio;
				F.coming.height = this.height / F.opts.pixelRatio;

				F._afterLoad();
			};

			img.onerror = function () {
				this.onload = this.onerror = null;

				F._error( 'image' );
			};

			img.src = F.coming.href;

			if (img.complete !== true) {
				F.showLoading();
			}
		},

		_loadAjax: function () {
			var coming = F.coming;

			F.showLoading();

			F.ajaxLoad = $.ajax($.extend({}, coming.ajax, {
				url: coming.href,
				error: function (jqXHR, textStatus) {
					if (F.coming && textStatus !== 'abort') {
						F._error( 'ajax', jqXHR );

					} else {
						F.hideLoading();
					}
				},
				success: function (data, textStatus) {
					if (textStatus === 'success') {
						coming.content = data;

						F._afterLoad();
					}
				}
			}));
		},

		_loadIframe: function() {
			var coming = F.coming,
				iframe = $(coming.tpl.iframe.replace(/\{rnd\}/g, new Date().getTime()))
					.attr('scrolling', isTouch ? 'auto' : coming.iframe.scrolling)
					.attr('src', coming.href);

			// This helps IE
			$(coming.wrap).bind('onReset', function () {
				try {
					$(this).find('iframe').hide().attr('src', '//about:blank').end().empty();
				} catch (e) {}
			});

			if (coming.iframe.preload) {
				F.showLoading();

				iframe.one('load', function() {
					$(this).data('ready', 1);

					// iOS will lose scrolling if we resize
					if (!isTouch) {
						$(this).bind('load.fb', F.update);
					}

					// Without this trick:
					//   - iframe won't scroll on iOS devices
					//   - IE7 sometimes displays empty iframe
					$(this).parents('.fancybox-wrap').width('100%').removeClass('fancybox-tmp').show();

					F._afterLoad();
				});
			}

			coming.content = iframe.appendTo( coming.inner );

			if (!coming.iframe.preload) {
				F._afterLoad();
			}
		},

		_preloadImages: function() {
			var group   = F.group,
				current = F.current,
				len     = group.length,
				cnt     = current.preload ? Math.min(current.preload, len - 1) : 0,
				item,
				i;

			for (i = 1; i <= cnt; i += 1) {
				item = group[ (current.index + i ) % len ];

				if (item.type === 'image' && item.href) {
					new Image().src = item.href;
				}
			}
		},

		_afterLoad: function () {
			var coming   = F.coming,
				previous = F.current,
				placeholder = 'fancybox-placeholder',
				current,
				content,
				type,
				scrolling,
				href,
				embed;

			F.hideLoading();

			if (!coming || F.isActive === false) {
				return;
			}

			if (false === F.trigger('afterLoad', coming, previous)) {
				coming.wrap.stop(true).trigger('onReset').remove();

				F.coming = null;

				return;
			}

			if (previous) {
				F.trigger('beforeChange', previous);

				previous.wrap.stop(true).removeClass('fancybox-opened')
					.find('.fancybox-item, .fancybox-nav')
					.remove();
			}

			F.unbindEvents();

			current   = coming;
			content   = coming.content;
			type      = coming.type;
			scrolling = coming.scrolling;

			$.extend(F, {
				wrap  : current.wrap,
				skin  : current.skin,
				outer : current.outer,
				inner : current.inner,
				current  : current,
				previous : previous
			});

			href = current.href;

			switch (type) {
				case 'inline':
				case 'ajax':
				case 'html':
					if (current.selector) {
						content = $('<div>').html(content).find(current.selector);

					} else if (isQuery(content)) {
						if (!content.data(placeholder)) {
							content.data(placeholder, $('<div class="' + placeholder + '"></div>').insertAfter( content ).hide() );
						}

						content = content.show().detach();

						current.wrap.bind('onReset', function () {
							if ($(this).find(content).length) {
								content.hide().replaceAll( content.data(placeholder) ).data(placeholder, false);
							}
						});
					}
				break;

				case 'image':
					content = current.tpl.image.replace('{href}', href);
				break;

				case 'swf':
					content = '<object id="fancybox-swf" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="100%"><param name="movie" value="' + href + '"></param>';
					embed   = '';

					$.each(current.swf, function(name, val) {
						content += '<param name="' + name + '" value="' + val + '"></param>';
						embed   += ' ' + name + '="' + val + '"';
					});

					content += '<embed src="' + href + '" type="application/x-shockwave-flash" width="100%" height="100%"' + embed + '></embed></object>';
				break;
			}

			if (!(isQuery(content) && content.parent().is(current.inner))) {
				current.inner.append( content );
			}

			// Give a chance for helpers or callbacks to update elements
			F.trigger('beforeShow');

			// Set scrolling before calculating dimensions
			current.inner.css('overflow', scrolling === 'yes' ? 'scroll' : (scrolling === 'no' ? 'hidden' : scrolling));

			// Set initial dimensions and start position
			F._setDimension();

			F.reposition();

			F.isOpen = false;
			F.coming = null;

			F.bindEvents();

			if (!F.isOpened) {
				$('.fancybox-wrap').not( current.wrap ).stop(true).trigger('onReset').remove();

			} else if (previous.prevMethod) {
				F.transitions[ previous.prevMethod ]();
			}

			F.transitions[ F.isOpened ? current.nextMethod : current.openMethod ]();

			F._preloadImages();
		},

		_setDimension: function () {
			var viewport   = F.getViewport(),
				steps      = 0,
				canShrink  = false,
				canExpand  = false,
				wrap       = F.wrap,
				skin       = F.skin,
				inner      = F.inner,
				current    = F.current,
				width      = current.width,
				height     = current.height,
				minWidth   = current.minWidth,
				minHeight  = current.minHeight,
				maxWidth   = current.maxWidth,
				maxHeight  = current.maxHeight,
				scrolling  = current.scrolling,
				scrollOut  = current.scrollOutside ? current.scrollbarWidth : 0,
				margin     = current.margin,
				wMargin    = getScalar(margin[1] + margin[3]),
				hMargin    = getScalar(margin[0] + margin[2]),
				wPadding,
				hPadding,
				wSpace,
				hSpace,
				origWidth,
				origHeight,
				origMaxWidth,
				origMaxHeight,
				ratio,
				width_,
				height_,
				maxWidth_,
				maxHeight_,
				iframe,
				body;

			// Reset dimensions so we could re-check actual size
			wrap.add(skin).add(inner).width('auto').height('auto').removeClass('fancybox-tmp');

			wPadding = getScalar(skin.outerWidth(true)  - skin.width());
			hPadding = getScalar(skin.outerHeight(true) - skin.height());

			// Any space between content and viewport (margin, padding, border, title)
			wSpace = wMargin + wPadding;
			hSpace = hMargin + hPadding;

			origWidth  = isPercentage(width)  ? (viewport.w - wSpace) * getScalar(width)  / 100 : width;
			origHeight = isPercentage(height) ? (viewport.h - hSpace) * getScalar(height) / 100 : height;

			if (current.type === 'iframe') {
				iframe = current.content;

				if (current.autoHeight && iframe.data('ready') === 1) {
					try {
						if (iframe[0].contentWindow.document.location) {
							inner.width( origWidth ).height(9999);

							body = iframe.contents().find('body');

							if (scrollOut) {
								body.css('overflow-x', 'hidden');
							}

							origHeight = body.outerHeight(true);
						}

					} catch (e) {}
				}

			} else if (current.autoWidth || current.autoHeight) {
				inner.addClass( 'fancybox-tmp' );

				// Set width or height in case we need to calculate only one dimension
				if (!current.autoWidth) {
					inner.width( origWidth );
				}

				if (!current.autoHeight) {
					inner.height( origHeight );
				}

				if (current.autoWidth) {
					origWidth = inner.width();
				}

				if (current.autoHeight) {
					origHeight = inner.height();
				}

				inner.removeClass( 'fancybox-tmp' );
			}

			width  = getScalar( origWidth );
			height = getScalar( origHeight );

			ratio  = origWidth / origHeight;

			// Calculations for the content
			minWidth  = getScalar(isPercentage(minWidth) ? getScalar(minWidth, 'w') - wSpace : minWidth);
			maxWidth  = getScalar(isPercentage(maxWidth) ? getScalar(maxWidth, 'w') - wSpace : maxWidth);

			minHeight = getScalar(isPercentage(minHeight) ? getScalar(minHeight, 'h') - hSpace : minHeight);
			maxHeight = getScalar(isPercentage(maxHeight) ? getScalar(maxHeight, 'h') - hSpace : maxHeight);

			// These will be used to determine if wrap can fit in the viewport
			origMaxWidth  = maxWidth;
			origMaxHeight = maxHeight;

			if (current.fitToView) {
				maxWidth  = Math.min(viewport.w - wSpace, maxWidth);
				maxHeight = Math.min(viewport.h - hSpace, maxHeight);
			}

			maxWidth_  = viewport.w - wMargin;
			maxHeight_ = viewport.h - hMargin;

			if (current.aspectRatio) {
				if (width > maxWidth) {
					width  = maxWidth;
					height = getScalar(width / ratio);
				}

				if (height > maxHeight) {
					height = maxHeight;
					width  = getScalar(height * ratio);
				}

				if (width < minWidth) {
					width  = minWidth;
					height = getScalar(width / ratio);
				}

				if (height < minHeight) {
					height = minHeight;
					width  = getScalar(height * ratio);
				}

			} else {
				width = Math.max(minWidth, Math.min(width, maxWidth));

				if (current.autoHeight && current.type !== 'iframe') {
					inner.width( width );

					height = inner.height();
				}

				height = Math.max(minHeight, Math.min(height, maxHeight));
			}

			// Try to fit inside viewport (including the title)
			if (current.fitToView) {
				inner.width( width ).height( height );

				wrap.width( width + wPadding );

				// Real wrap dimensions
				width_  = wrap.width();
				height_ = wrap.height();

				if (current.aspectRatio) {
					while ((width_ > maxWidth_ || height_ > maxHeight_) && width > minWidth && height > minHeight) {
						if (steps++ > 19) {
							break;
						}

						height = Math.max(minHeight, Math.min(maxHeight, height - 10));
						width  = getScalar(height * ratio);

						if (width < minWidth) {
							width  = minWidth;
							height = getScalar(width / ratio);
						}

						if (width > maxWidth) {
							width  = maxWidth;
							height = getScalar(width / ratio);
						}

						inner.width( width ).height( height );

						wrap.width( width + wPadding );

						width_  = wrap.width();
						height_ = wrap.height();
					}

				} else {
					width  = Math.max(minWidth,  Math.min(width,  width  - (width_  - maxWidth_)));
					height = Math.max(minHeight, Math.min(height, height - (height_ - maxHeight_)));
				}
			}

			if (scrollOut && scrolling === 'auto' && height < origHeight && (width + wPadding + scrollOut) < maxWidth_) {
				width += scrollOut;
			}

			inner.width( width ).height( height );

			wrap.width( width + wPadding );

			width_  = wrap.width();
			height_ = wrap.height();

			canShrink = (width_ > maxWidth_ || height_ > maxHeight_) && width > minWidth && height > minHeight;
			canExpand = current.aspectRatio ? (width < origMaxWidth && height < origMaxHeight && width < origWidth && height < origHeight) : ((width < origMaxWidth || height < origMaxHeight) && (width < origWidth || height < origHeight));

			$.extend(current, {
				dim : {
					width	: getValue( width_ ),
					height	: getValue( height_ )
				},
				origWidth  : origWidth,
				origHeight : origHeight,
				canShrink  : canShrink,
				canExpand  : canExpand,
				wPadding   : wPadding,
				hPadding   : hPadding,
				wrapSpace  : height_ - skin.outerHeight(true),
				skinSpace  : skin.height() - height
			});

			if (!iframe && current.autoHeight && height > minHeight && height < maxHeight && !canExpand) {
				inner.height('auto');
			}
		},

		_getPosition: function (onlyAbsolute) {
			var current  = F.current,
				viewport = F.getViewport(),
				margin   = current.margin,
				width    = F.wrap.width()  + margin[1] + margin[3],
				height   = F.wrap.height() + margin[0] + margin[2],
				rez      = {
					position: 'absolute',
					top  : margin[0],
					left : margin[3]
				};

			if (current.autoCenter && current.fixed && !onlyAbsolute && height <= viewport.h && width <= viewport.w) {
				rez.position = 'fixed';

			} else if (!current.locked) {
				rez.top  += viewport.y;
				rez.left += viewport.x;
			}

			rez.top  = getValue(Math.max(rez.top,  rez.top  + ((viewport.h - height) * current.topRatio)));
			rez.left = getValue(Math.max(rez.left, rez.left + ((viewport.w - width)  * current.leftRatio)));

			return rez;
		},

		_afterZoomIn: function () {
			var current = F.current;

			if (!current) {
				return;
			}

			F.isOpen = F.isOpened = true;

			F.wrap.css('overflow', 'visible').addClass('fancybox-opened');

			F.update();

			// Assign a click event
			if ( current.closeClick || (current.nextClick && F.group.length > 1) ) {
				F.inner.css('cursor', 'pointer').bind('click.fb', function(e) {
					if (!$(e.target).is('a') && !$(e.target).parent().is('a')) {
						e.preventDefault();

						F[ current.closeClick ? 'close' : 'next' ]();
					}
				});
			}

			// Create a close button
			if (current.closeBtn) {
				$(current.tpl.closeBtn).appendTo(F.skin).bind('click.fb', function(e) {
					e.preventDefault();

					F.close();
				});
			}

			// Create navigation arrows
			if (current.arrows && F.group.length > 1) {
				if (current.loop || current.index > 0) {
					$(current.tpl.prev).appendTo(F.outer).bind('click.fb', F.prev);
				}

				if (current.loop || current.index < F.group.length - 1) {
					$(current.tpl.next).appendTo(F.outer).bind('click.fb', F.next);
				}
			}

			F.trigger('afterShow');

			// Stop the slideshow if this is the last item
			if (!current.loop && current.index === current.group.length - 1) {
				F.play( false );

			} else if (F.opts.autoPlay && !F.player.isActive) {
				F.opts.autoPlay = false;

				F.play();
			}
		},

		_afterZoomOut: function ( obj ) {
			obj = obj || F.current;

			$('.fancybox-wrap').trigger('onReset').remove();

			$.extend(F, {
				group  : {},
				opts   : {},
				router : false,
				current   : null,
				isActive  : false,
				isOpened  : false,
				isOpen    : false,
				isClosing : false,
				wrap   : null,
				skin   : null,
				outer  : null,
				inner  : null
			});

			F.trigger('afterClose', obj);
		}
	});

	/*
	 *	Default transitions
	 */

	F.transitions = {
		getOrigPosition: function () {
			var current  = F.current,
				element  = current.element,
				orig     = current.orig,
				pos      = {},
				width    = 50,
				height   = 50,
				hPadding = current.hPadding,
				wPadding = current.wPadding,
				viewport = F.getViewport();

			if (!orig && current.isDom && element.is(':visible')) {
				orig = element.find('img:first');

				if (!orig.length) {
					orig = element;
				}
			}

			if (isQuery(orig)) {
				pos = orig.offset();

				if (orig.is('img')) {
					width  = orig.outerWidth();
					height = orig.outerHeight();
				}

			} else {
				pos.top  = viewport.y + (viewport.h - height) * current.topRatio;
				pos.left = viewport.x + (viewport.w - width)  * current.leftRatio;
			}

			if (F.wrap.css('position') === 'fixed' || current.locked) {
				pos.top  -= viewport.y;
				pos.left -= viewport.x;
			}

			pos = {
				top     : getValue(pos.top  - hPadding * current.topRatio),
				left    : getValue(pos.left - wPadding * current.leftRatio),
				width   : getValue(width  + wPadding),
				height  : getValue(height + hPadding)
			};

			return pos;
		},

		step: function (now, fx) {
			var ratio,
				padding,
				value,
				prop       = fx.prop,
				current    = F.current,
				wrapSpace  = current.wrapSpace,
				skinSpace  = current.skinSpace;

			if (prop === 'width' || prop === 'height') {
				ratio = fx.end === fx.start ? 1 : (now - fx.start) / (fx.end - fx.start);

				if (F.isClosing) {
					ratio = 1 - ratio;
				}

				padding = prop === 'width' ? current.wPadding : current.hPadding;
				value   = now - padding;

				F.skin[ prop ](  getScalar( prop === 'width' ?  value : value - (wrapSpace * ratio) ) );
				F.inner[ prop ]( getScalar( prop === 'width' ?  value : value - (wrapSpace * ratio) - (skinSpace * ratio) ) );
			}
		},

		zoomIn: function () {
			var current  = F.current,
				startPos = current.pos,
				effect   = current.openEffect,
				elastic  = effect === 'elastic',
				endPos   = $.extend({opacity : 1}, startPos);

			// Remove "position" property that breaks older IE
			delete endPos.position;

			if (elastic) {
				startPos = this.getOrigPosition();

				if (current.openOpacity) {
					startPos.opacity = 0.1;
				}

			} else if (effect === 'fade') {
				startPos.opacity = 0.1;
			}

			F.wrap.css(startPos).animate(endPos, {
				duration : effect === 'none' ? 0 : current.openSpeed,
				easing   : current.openEasing,
				step     : elastic ? this.step : null,
				complete : F._afterZoomIn
			});
		},

		zoomOut: function () {
			var current  = F.current,
				effect   = current.closeEffect,
				elastic  = effect === 'elastic',
				endPos   = {opacity : 0.1};

			if (elastic) {
				endPos = this.getOrigPosition();

				if (current.closeOpacity) {
					endPos.opacity = 0.1;
				}
			}

			F.wrap.animate(endPos, {
				duration : effect === 'none' ? 0 : current.closeSpeed,
				easing   : current.closeEasing,
				step     : elastic ? this.step : null,
				complete : F._afterZoomOut
			});
		},

		changeIn: function () {
			var current   = F.current,
				effect    = current.nextEffect,
				startPos  = current.pos,
				endPos    = { opacity : 1 },
				direction = F.direction,
				distance  = 200,
				field;

			startPos.opacity = 0.1;

			if (effect === 'elastic') {
				field = direction === 'down' || direction === 'up' ? 'top' : 'left';

				if (direction === 'down' || direction === 'right') {
					startPos[ field ] = getValue(getScalar(startPos[ field ]) - distance);
					endPos[ field ]   = '+=' + distance + 'px';

				} else {
					startPos[ field ] = getValue(getScalar(startPos[ field ]) + distance);
					endPos[ field ]   = '-=' + distance + 'px';
				}
			}

			// Workaround for http://bugs.jquery.com/ticket/12273
			if (effect === 'none') {
				F._afterZoomIn();

			} else {
				F.wrap.css(startPos).animate(endPos, {
					duration : current.nextSpeed,
					easing   : current.nextEasing,
					complete : F._afterZoomIn
				});
			}
		},

		changeOut: function () {
			var previous  = F.previous,
				effect    = previous.prevEffect,
				endPos    = { opacity : 0.1 },
				direction = F.direction,
				distance  = 200;

			if (effect === 'elastic') {
				endPos[ direction === 'down' || direction === 'up' ? 'top' : 'left' ] = ( direction === 'up' || direction === 'left' ? '-' : '+' ) + '=' + distance + 'px';
			}

			previous.wrap.animate(endPos, {
				duration : effect === 'none' ? 0 : previous.prevSpeed,
				easing   : previous.prevEasing,
				complete : function () {
					$(this).trigger('onReset').remove();
				}
			});
		}
	};

	/*
	 *	Overlay helper
	 */

	F.helpers.overlay = {
		defaults : {
			closeClick : true,      // if true, fancyBox will be closed when user clicks on the overlay
			speedOut   : 200,       // duration of fadeOut animation
			showEarly  : true,      // indicates if should be opened immediately or wait until the content is ready
			css        : {},        // custom CSS properties
			locked     : !isTouch,  // if true, the content will be locked into overlay
			fixed      : true       // if false, the overlay CSS position property will not be set to "fixed"
		},

		overlay : null,      // current handle
		fixed   : false,     // indicates if the overlay has position "fixed"
		el      : $('html'), // element that contains "the lock"

		// Public methods
		create : function(opts) {
			opts = $.extend({}, this.defaults, opts);

			if (this.overlay) {
				this.close();
			}

			this.overlay = $('<div class="fancybox-overlay"></div>').appendTo( F.coming ? F.coming.parent : opts.parent );
			this.fixed   = false;

			if (opts.fixed && F.defaults.fixed) {
				this.overlay.addClass('fancybox-overlay-fixed');

				this.fixed = true;
			}
		},

		open : function(opts) {
			var that = this;

			opts = $.extend({}, this.defaults, opts);

			if (this.overlay) {
				this.overlay.unbind('.overlay').width('auto').height('auto');

			} else {
				this.create(opts);
			}

			if (!this.fixed) {
				W.bind('resize.overlay', $.proxy( this.update, this) );

				this.update();
			}

			if (opts.closeClick) {
				this.overlay.bind('click.overlay', function(e) {
					if ($(e.target).hasClass('fancybox-overlay')) {
						if (F.isActive) {
							F.close();
						} else {
							that.close();
						}

						return false;
					}
				});
			}

			this.overlay.css( opts.css ).show();
		},

		close : function() {
			var scrollV, scrollH;

			W.unbind('resize.overlay');

			if (this.el.hasClass('fancybox-lock')) {
				$('.fancybox-margin').removeClass('fancybox-margin');

				scrollV = W.scrollTop();
				scrollH = W.scrollLeft();

				this.el.removeClass('fancybox-lock');

				W.scrollTop( scrollV ).scrollLeft( scrollH );
			}

			$('.fancybox-overlay').remove().hide();

			$.extend(this, {
				overlay : null,
				fixed   : false
			});
		},

		// Private, callbacks

		update : function () {
			var width = '100%', offsetWidth;

			// Reset width/height so it will not mess
			this.overlay.width(width).height('100%');

			// jQuery does not return reliable result for IE
			if (IE) {
				offsetWidth = Math.max(document.documentElement.offsetWidth, document.body.offsetWidth);

				if (D.width() > offsetWidth) {
					width = D.width();
				}

			} else if (D.width() > W.width()) {
				width = D.width();
			}

			this.overlay.width(width).height(D.height());
		},

		// This is where we can manipulate DOM, because later it would cause iframes to reload
		onReady : function (opts, obj) {
			var overlay = this.overlay;

			$('.fancybox-overlay').stop(true, true);

			if (!overlay) {
				this.create(opts);
			}

			if (opts.locked && this.fixed && obj.fixed) {
				if (!overlay) {
					this.margin = D.height() > W.height() ? $('html').css('margin-right').replace("px", "") : false;
				}

				obj.locked = this.overlay.append( obj.wrap );
				obj.fixed  = false;
			}

			if (opts.showEarly === true) {
				this.beforeShow.apply(this, arguments);
			}
		},

		beforeShow : function(opts, obj) {
			var scrollV, scrollH;

			if (obj.locked) {
				if (this.margin !== false) {
					$('*').filter(function(){
						return ($(this).css('position') === 'fixed' && !$(this).hasClass("fancybox-overlay") && !$(this).hasClass("fancybox-wrap") );
					}).addClass('fancybox-margin');

					this.el.addClass('fancybox-margin');
				}

				scrollV = W.scrollTop();
				scrollH = W.scrollLeft();

				this.el.addClass('fancybox-lock');

				W.scrollTop( scrollV ).scrollLeft( scrollH );
			}

			this.open(opts);
		},

		onUpdate : function() {
			if (!this.fixed) {
				this.update();
			}
		},

		afterClose: function (opts) {
			// Remove overlay if exists and fancyBox is not opening
			// (e.g., it is not being open using afterClose callback)
			//if (this.overlay && !F.isActive) {
			if (this.overlay && !F.coming) {
				this.overlay.fadeOut(opts.speedOut, $.proxy( this.close, this ));
			}
		}
	};

	/*
	 *	Title helper
	 */

	F.helpers.title = {
		defaults : {
			type     : 'float', // 'float', 'inside', 'outside' or 'over',
			position : 'bottom' // 'top' or 'bottom'
		},

		beforeShow: function (opts) {
			var current = F.current,
				text    = current.title,
				type    = opts.type,
				title,
				target;

			if ($.isFunction(text)) {
				text = text.call(current.element, current);
			}

			if (!isString(text) || $.trim(text) === '') {
				return;
			}

			title = $('<div class="fancybox-title fancybox-title-' + type + '-wrap">' + text + '</div>');

			switch (type) {
				case 'inside':
					target = F.skin;
				break;

				case 'outside':
					target = F.wrap;
				break;

				case 'over':
					target = F.inner;
				break;

				default: // 'float'
					target = F.skin;

					title.appendTo('body');

					if (IE) {
						title.width( title.width() );
					}

					title.wrapInner('<span class="child"></span>');

					//Increase bottom margin so this title will also fit into viewport
					F.current.margin[2] += Math.abs( getScalar(title.css('margin-bottom')) );
				break;
			}

			title[ (opts.position === 'top' ? 'prependTo'  : 'appendTo') ](target);
		}
	};

	// jQuery plugin initialization
	$.fn.fancybox = function (options) {
		var index,
			that     = $(this),
			selector = this.selector || '',
			run      = function(e) {
				var what = $(this).blur(), idx = index, relType, relVal;

				if (!(e.ctrlKey || e.altKey || e.shiftKey || e.metaKey) && !what.is('.fancybox-wrap')) {
					relType = options.groupAttr || 'data-fancybox-group';
					relVal  = what.attr(relType);

					if (!relVal) {
						relType = 'rel';
						relVal  = what.get(0)[ relType ];
					}

					if (relVal && relVal !== '' && relVal !== 'nofollow') {
						what = selector.length ? $(selector) : that;
						what = what.filter('[' + relType + '="' + relVal + '"]');
						idx  = what.index(this);
					}

					options.index = idx;

					// Stop an event from bubbling if everything is fine
					if (F.open(what, options) !== false) {
						e.preventDefault();
					}
				}
			};

		options = options || {};
		index   = options.index || 0;

		if (!selector || options.live === false) {
			that.unbind('click.fb-start').bind('click.fb-start', run);

		} else {
			D.undelegate(selector, 'click.fb-start').delegate(selector + ":not('.fancybox-item, .fancybox-nav')", 'click.fb-start', run);
		}

		this.filter('[data-fancybox-start=1]').trigger('click');

		return this;
	};

	// Tests that need a body at doc ready
	D.ready(function() {
		var w1, w2;

		if ( $.scrollbarWidth === undefined ) {
			// http://benalman.com/projects/jquery-misc-plugins/#scrollbarwidth
			$.scrollbarWidth = function() {
				var parent = $('<div style="width:50px;height:50px;overflow:auto"><div/></div>').appendTo('body'),
					child  = parent.children(),
					width  = child.innerWidth() - child.height( 99 ).innerWidth();

				parent.remove();

				return width;
			};
		}

		if ( $.support.fixedPosition === undefined ) {
			$.support.fixedPosition = (function() {
				var elem  = $('<div style="position:fixed;top:20px;"></div>').appendTo('body'),
					fixed = ( elem[0].offsetTop === 20 || elem[0].offsetTop === 15 );

				elem.remove();

				return fixed;
			}());
		}

		$.extend(F.defaults, {
			scrollbarWidth : $.scrollbarWidth(),
			fixed  : $.support.fixedPosition,
			parent : $('body')
		});

		//Get real width of page scroll-bar
		w1 = $(window).width();

		H.addClass('fancybox-lock-test');

		w2 = $(window).width();

		H.removeClass('fancybox-lock-test');

		$("<style type='text/css'>.fancybox-margin{margin-right:" + (w2 - w1) + "px;}</style>").appendTo("head");
	});

}(window, document, jQuery));;/*!
 * jScrollPane - v2.0.19 - 2013-11-16
 * http://jscrollpane.kelvinluck.com/
 *
 * Copyright (c) 2013 Kelvin Luck
 * Dual licensed under the MIT or GPL licenses.
 */
!function(a,b,c){a.fn.jScrollPane=function(d){function e(d,e){function f(b){var e,h,j,l,m,n,q=!1,r=!1;if(P=b,Q===c)m=d.scrollTop(),n=d.scrollLeft(),d.css({overflow:"hidden",padding:0}),R=d.innerWidth()+tb,S=d.innerHeight(),d.width(R),Q=a('<div class="jspPane" />').css("padding",sb).append(d.children()),T=a('<div class="jspContainer" />').css({width:R+"px",height:S+"px"}).append(Q).appendTo(d);else{if(d.css("width",""),q=P.stickToBottom&&C(),r=P.stickToRight&&D(),l=d.innerWidth()+tb!=R||d.outerHeight()!=S,l&&(R=d.innerWidth()+tb,S=d.innerHeight(),T.css({width:R+"px",height:S+"px"})),!l&&ub==U&&Q.outerHeight()==V)return d.width(R),void 0;ub=U,Q.css("width",""),d.width(R),T.find(">.jspVerticalBar,>.jspHorizontalBar").remove().end()}Q.css("overflow","auto"),U=b.contentWidth?b.contentWidth:Q[0].scrollWidth,V=Q[0].scrollHeight,Q.css("overflow",""),W=U/R,X=V/S,Y=X>1,Z=W>1,Z||Y?(d.addClass("jspScrollable"),e=P.maintainPosition&&(ab||db),e&&(h=A(),j=B()),g(),i(),k(),e&&(y(r?U-R:h,!1),x(q?V-S:j,!1)),H(),E(),N(),P.enableKeyboardNavigation&&J(),P.clickOnTrack&&o(),L(),P.hijackInternalLinks&&M()):(d.removeClass("jspScrollable"),Q.css({top:0,left:0,width:T.width()-tb}),F(),I(),K(),p()),P.autoReinitialise&&!rb?rb=setInterval(function(){f(P)},P.autoReinitialiseDelay):!P.autoReinitialise&&rb&&clearInterval(rb),m&&d.scrollTop(0)&&x(m,!1),n&&d.scrollLeft(0)&&y(n,!1),d.trigger("jsp-initialised",[Z||Y])}function g(){Y&&(T.append(a('<div class="jspVerticalBar" />').append(a('<div class="jspCap jspCapTop" />'),a('<div class="jspTrack" />').append(a('<div class="jspDrag" />').append(a('<div class="jspDragTop" />'),a('<div class="jspDragBottom" />'))),a('<div class="jspCap jspCapBottom" />'))),eb=T.find(">.jspVerticalBar"),fb=eb.find(">.jspTrack"),$=fb.find(">.jspDrag"),P.showArrows&&(jb=a('<a class="jspArrow jspArrowUp" />').bind("mousedown.jsp",m(0,-1)).bind("click.jsp",G),kb=a('<a class="jspArrow jspArrowDown" />').bind("mousedown.jsp",m(0,1)).bind("click.jsp",G),P.arrowScrollOnHover&&(jb.bind("mouseover.jsp",m(0,-1,jb)),kb.bind("mouseover.jsp",m(0,1,kb))),l(fb,P.verticalArrowPositions,jb,kb)),hb=S,T.find(">.jspVerticalBar>.jspCap:visible,>.jspVerticalBar>.jspArrow").each(function(){hb-=a(this).outerHeight()}),$.hover(function(){$.addClass("jspHover")},function(){$.removeClass("jspHover")}).bind("mousedown.jsp",function(b){a("html").bind("dragstart.jsp selectstart.jsp",G),$.addClass("jspActive");var c=b.pageY-$.position().top;return a("html").bind("mousemove.jsp",function(a){r(a.pageY-c,!1)}).bind("mouseup.jsp mouseleave.jsp",q),!1}),h())}function h(){fb.height(hb+"px"),ab=0,gb=P.verticalGutter+fb.outerWidth(),Q.width(R-gb-tb);try{0===eb.position().left&&Q.css("margin-left",gb+"px")}catch(a){}}function i(){Z&&(T.append(a('<div class="jspHorizontalBar" />').append(a('<div class="jspCap jspCapLeft" />'),a('<div class="jspTrack" />').append(a('<div class="jspDrag" />').append(a('<div class="jspDragLeft" />'),a('<div class="jspDragRight" />'))),a('<div class="jspCap jspCapRight" />'))),lb=T.find(">.jspHorizontalBar"),mb=lb.find(">.jspTrack"),bb=mb.find(">.jspDrag"),P.showArrows&&(pb=a('<a class="jspArrow jspArrowLeft" />').bind("mousedown.jsp",m(-1,0)).bind("click.jsp",G),qb=a('<a class="jspArrow jspArrowRight" />').bind("mousedown.jsp",m(1,0)).bind("click.jsp",G),P.arrowScrollOnHover&&(pb.bind("mouseover.jsp",m(-1,0,pb)),qb.bind("mouseover.jsp",m(1,0,qb))),l(mb,P.horizontalArrowPositions,pb,qb)),bb.hover(function(){bb.addClass("jspHover")},function(){bb.removeClass("jspHover")}).bind("mousedown.jsp",function(b){a("html").bind("dragstart.jsp selectstart.jsp",G),bb.addClass("jspActive");var c=b.pageX-bb.position().left;return a("html").bind("mousemove.jsp",function(a){t(a.pageX-c,!1)}).bind("mouseup.jsp mouseleave.jsp",q),!1}),nb=T.innerWidth(),j())}function j(){T.find(">.jspHorizontalBar>.jspCap:visible,>.jspHorizontalBar>.jspArrow").each(function(){nb-=a(this).outerWidth()}),mb.width(nb+"px"),db=0}function k(){if(Z&&Y){var b=mb.outerHeight(),c=fb.outerWidth();hb-=b,a(lb).find(">.jspCap:visible,>.jspArrow").each(function(){nb+=a(this).outerWidth()}),nb-=c,S-=c,R-=b,mb.parent().append(a('<div class="jspCorner" />').css("width",b+"px")),h(),j()}Z&&Q.width(T.outerWidth()-tb+"px"),V=Q.outerHeight(),X=V/S,Z&&(ob=Math.ceil(1/W*nb),ob>P.horizontalDragMaxWidth?ob=P.horizontalDragMaxWidth:ob<P.horizontalDragMinWidth&&(ob=P.horizontalDragMinWidth),bb.width(ob+"px"),cb=nb-ob,u(db)),Y&&(ib=Math.ceil(1/X*hb),ib>P.verticalDragMaxHeight?ib=P.verticalDragMaxHeight:ib<P.verticalDragMinHeight&&(ib=P.verticalDragMinHeight),$.height(ib+"px"),_=hb-ib,s(ab))}function l(a,b,c,d){var e,f="before",g="after";"os"==b&&(b=/Mac/.test(navigator.platform)?"after":"split"),b==f?g=b:b==g&&(f=b,e=c,c=d,d=e),a[f](c)[g](d)}function m(a,b,c){return function(){return n(a,b,this,c),this.blur(),!1}}function n(b,c,d,e){d=a(d).addClass("jspActive");var f,g,h=!0,i=function(){0!==b&&vb.scrollByX(b*P.arrowButtonSpeed),0!==c&&vb.scrollByY(c*P.arrowButtonSpeed),g=setTimeout(i,h?P.initialDelay:P.arrowRepeatFreq),h=!1};i(),f=e?"mouseout.jsp":"mouseup.jsp",e=e||a("html"),e.bind(f,function(){d.removeClass("jspActive"),g&&clearTimeout(g),g=null,e.unbind(f)})}function o(){p(),Y&&fb.bind("mousedown.jsp",function(b){if(b.originalTarget===c||b.originalTarget==b.currentTarget){var d,e=a(this),f=e.offset(),g=b.pageY-f.top-ab,h=!0,i=function(){var a=e.offset(),c=b.pageY-a.top-ib/2,f=S*P.scrollPagePercent,k=_*f/(V-S);if(0>g)ab-k>c?vb.scrollByY(-f):r(c);else{if(!(g>0))return j(),void 0;c>ab+k?vb.scrollByY(f):r(c)}d=setTimeout(i,h?P.initialDelay:P.trackClickRepeatFreq),h=!1},j=function(){d&&clearTimeout(d),d=null,a(document).unbind("mouseup.jsp",j)};return i(),a(document).bind("mouseup.jsp",j),!1}}),Z&&mb.bind("mousedown.jsp",function(b){if(b.originalTarget===c||b.originalTarget==b.currentTarget){var d,e=a(this),f=e.offset(),g=b.pageX-f.left-db,h=!0,i=function(){var a=e.offset(),c=b.pageX-a.left-ob/2,f=R*P.scrollPagePercent,k=cb*f/(U-R);if(0>g)db-k>c?vb.scrollByX(-f):t(c);else{if(!(g>0))return j(),void 0;c>db+k?vb.scrollByX(f):t(c)}d=setTimeout(i,h?P.initialDelay:P.trackClickRepeatFreq),h=!1},j=function(){d&&clearTimeout(d),d=null,a(document).unbind("mouseup.jsp",j)};return i(),a(document).bind("mouseup.jsp",j),!1}})}function p(){mb&&mb.unbind("mousedown.jsp"),fb&&fb.unbind("mousedown.jsp")}function q(){a("html").unbind("dragstart.jsp selectstart.jsp mousemove.jsp mouseup.jsp mouseleave.jsp"),$&&$.removeClass("jspActive"),bb&&bb.removeClass("jspActive")}function r(a,b){Y&&(0>a?a=0:a>_&&(a=_),b===c&&(b=P.animateScroll),b?vb.animate($,"top",a,s):($.css("top",a),s(a)))}function s(a){a===c&&(a=$.position().top),T.scrollTop(0),ab=a;var b=0===ab,e=ab==_,f=a/_,g=-f*(V-S);(wb!=b||yb!=e)&&(wb=b,yb=e,d.trigger("jsp-arrow-change",[wb,yb,xb,zb])),v(b,e),Q.css("top",g),d.trigger("jsp-scroll-y",[-g,b,e]).trigger("scroll")}function t(a,b){Z&&(0>a?a=0:a>cb&&(a=cb),b===c&&(b=P.animateScroll),b?vb.animate(bb,"left",a,u):(bb.css("left",a),u(a)))}function u(a){a===c&&(a=bb.position().left),T.scrollTop(0),db=a;var b=0===db,e=db==cb,f=a/cb,g=-f*(U-R);(xb!=b||zb!=e)&&(xb=b,zb=e,d.trigger("jsp-arrow-change",[wb,yb,xb,zb])),w(b,e),Q.css("left",g),d.trigger("jsp-scroll-x",[-g,b,e]).trigger("scroll")}function v(a,b){P.showArrows&&(jb[a?"addClass":"removeClass"]("jspDisabled"),kb[b?"addClass":"removeClass"]("jspDisabled"))}function w(a,b){P.showArrows&&(pb[a?"addClass":"removeClass"]("jspDisabled"),qb[b?"addClass":"removeClass"]("jspDisabled"))}function x(a,b){var c=a/(V-S);r(c*_,b)}function y(a,b){var c=a/(U-R);t(c*cb,b)}function z(b,c,d){var e,f,g,h,i,j,k,l,m,n=0,o=0;try{e=a(b)}catch(p){return}for(f=e.outerHeight(),g=e.outerWidth(),T.scrollTop(0),T.scrollLeft(0);!e.is(".jspPane");)if(n+=e.position().top,o+=e.position().left,e=e.offsetParent(),/^body|html$/i.test(e[0].nodeName))return;h=B(),j=h+S,h>n||c?l=n-P.horizontalGutter:n+f>j&&(l=n-S+f+P.horizontalGutter),isNaN(l)||x(l,d),i=A(),k=i+R,i>o||c?m=o-P.horizontalGutter:o+g>k&&(m=o-R+g+P.horizontalGutter),isNaN(m)||y(m,d)}function A(){return-Q.position().left}function B(){return-Q.position().top}function C(){var a=V-S;return a>20&&a-B()<10}function D(){var a=U-R;return a>20&&a-A()<10}function E(){T.unbind(Bb).bind(Bb,function(a,b,c,d){var e=db,f=ab,g=a.deltaFactor||P.mouseWheelSpeed;return vb.scrollBy(c*g,-d*g,!1),e==db&&f==ab})}function F(){T.unbind(Bb)}function G(){return!1}function H(){Q.find(":input,a").unbind("focus.jsp").bind("focus.jsp",function(a){z(a.target,!1)})}function I(){Q.find(":input,a").unbind("focus.jsp")}function J(){function b(){var a=db,b=ab;switch(c){case 40:vb.scrollByY(P.keyboardSpeed,!1);break;case 38:vb.scrollByY(-P.keyboardSpeed,!1);break;case 34:case 32:vb.scrollByY(S*P.scrollPagePercent,!1);break;case 33:vb.scrollByY(-S*P.scrollPagePercent,!1);break;case 39:vb.scrollByX(P.keyboardSpeed,!1);break;case 37:vb.scrollByX(-P.keyboardSpeed,!1)}return e=a!=db||b!=ab}var c,e,f=[];Z&&f.push(lb[0]),Y&&f.push(eb[0]),Q.focus(function(){d.focus()}),d.attr("tabindex",0).unbind("keydown.jsp keypress.jsp").bind("keydown.jsp",function(d){if(d.target===this||f.length&&a(d.target).closest(f).length){var g=db,h=ab;switch(d.keyCode){case 40:case 38:case 34:case 32:case 33:case 39:case 37:c=d.keyCode,b();break;case 35:x(V-S),c=null;break;case 36:x(0),c=null}return e=d.keyCode==c&&g!=db||h!=ab,!e}}).bind("keypress.jsp",function(a){return a.keyCode==c&&b(),!e}),P.hideFocus?(d.css("outline","none"),"hideFocus"in T[0]&&d.attr("hideFocus",!0)):(d.css("outline",""),"hideFocus"in T[0]&&d.attr("hideFocus",!1))}function K(){d.attr("tabindex","-1").removeAttr("tabindex").unbind("keydown.jsp keypress.jsp")}function L(){if(location.hash&&location.hash.length>1){var b,c,d=escape(location.hash.substr(1));try{b=a("#"+d+', a[name="'+d+'"]')}catch(e){return}b.length&&Q.find(d)&&(0===T.scrollTop()?c=setInterval(function(){T.scrollTop()>0&&(z(b,!0),a(document).scrollTop(T.position().top),clearInterval(c))},50):(z(b,!0),a(document).scrollTop(T.position().top)))}}function M(){a(document.body).data("jspHijack")||(a(document.body).data("jspHijack",!0),a(document.body).delegate("a[href*=#]","click",function(c){var d,e,f,g,h,i,j=this.href.substr(0,this.href.indexOf("#")),k=location.href;if(-1!==location.href.indexOf("#")&&(k=location.href.substr(0,location.href.indexOf("#"))),j===k){d=escape(this.href.substr(this.href.indexOf("#")+1));try{e=a("#"+d+', a[name="'+d+'"]')}catch(l){return}e.length&&(f=e.closest(".jspScrollable"),g=f.data("jsp"),g.scrollToElement(e,!0),f[0].scrollIntoView&&(h=a(b).scrollTop(),i=e.offset().top,(h>i||i>h+a(b).height())&&f[0].scrollIntoView()),c.preventDefault())}}))}function N(){var a,b,c,d,e,f=!1;T.unbind("touchstart.jsp touchmove.jsp touchend.jsp click.jsp-touchclick").bind("touchstart.jsp",function(g){var h=g.originalEvent.touches[0];a=A(),b=B(),c=h.pageX,d=h.pageY,e=!1,f=!0}).bind("touchmove.jsp",function(g){if(f){var h=g.originalEvent.touches[0],i=db,j=ab;return vb.scrollTo(a+c-h.pageX,b+d-h.pageY),e=e||Math.abs(c-h.pageX)>5||Math.abs(d-h.pageY)>5,i==db&&j==ab}}).bind("touchend.jsp",function(){f=!1}).bind("click.jsp-touchclick",function(){return e?(e=!1,!1):void 0})}function O(){var a=B(),b=A();d.removeClass("jspScrollable").unbind(".jsp"),d.replaceWith(Ab.append(Q.children())),Ab.scrollTop(a),Ab.scrollLeft(b),rb&&clearInterval(rb)}var P,Q,R,S,T,U,V,W,X,Y,Z,$,_,ab,bb,cb,db,eb,fb,gb,hb,ib,jb,kb,lb,mb,nb,ob,pb,qb,rb,sb,tb,ub,vb=this,wb=!0,xb=!0,yb=!1,zb=!1,Ab=d.clone(!1,!1).empty(),Bb=a.fn.mwheelIntent?"mwheelIntent.jsp":"mousewheel.jsp";"border-box"===d.css("box-sizing")?(sb=0,tb=0):(sb=d.css("paddingTop")+" "+d.css("paddingRight")+" "+d.css("paddingBottom")+" "+d.css("paddingLeft"),tb=(parseInt(d.css("paddingLeft"),10)||0)+(parseInt(d.css("paddingRight"),10)||0)),a.extend(vb,{reinitialise:function(b){b=a.extend({},P,b),f(b)},scrollToElement:function(a,b,c){z(a,b,c)},scrollTo:function(a,b,c){y(a,c),x(b,c)},scrollToX:function(a,b){y(a,b)},scrollToY:function(a,b){x(a,b)},scrollToPercentX:function(a,b){y(a*(U-R),b)},scrollToPercentY:function(a,b){x(a*(V-S),b)},scrollBy:function(a,b,c){vb.scrollByX(a,c),vb.scrollByY(b,c)},scrollByX:function(a,b){var c=A()+Math[0>a?"floor":"ceil"](a),d=c/(U-R);t(d*cb,b)},scrollByY:function(a,b){var c=B()+Math[0>a?"floor":"ceil"](a),d=c/(V-S);r(d*_,b)},positionDragX:function(a,b){t(a,b)},positionDragY:function(a,b){r(a,b)},animate:function(a,b,c,d){var e={};e[b]=c,a.animate(e,{duration:P.animateDuration,easing:P.animateEase,queue:!1,step:d})},getContentPositionX:function(){return A()},getContentPositionY:function(){return B()},getContentWidth:function(){return U},getContentHeight:function(){return V},getPercentScrolledX:function(){return A()/(U-R)},getPercentScrolledY:function(){return B()/(V-S)},getIsScrollableH:function(){return Z},getIsScrollableV:function(){return Y},getContentPane:function(){return Q},scrollToBottom:function(a){r(_,a)},hijackInternalLinks:a.noop,destroy:function(){O()}}),f(e)}return d=a.extend({},a.fn.jScrollPane.defaults,d),a.each(["arrowButtonSpeed","trackClickSpeed","keyboardSpeed"],function(){d[this]=d[this]||d.speed}),this.each(function(){var b=a(this),c=b.data("jsp");c?c.reinitialise(d):(a("script",b).filter('[type="text/javascript"],:not([type])').remove(),c=new e(b,d),b.data("jsp",c))})},a.fn.jScrollPane.defaults={showArrows:!1,maintainPosition:!0,stickToBottom:!1,stickToRight:!1,clickOnTrack:!0,autoReinitialise:!1,autoReinitialiseDelay:500,verticalDragMinHeight:0,verticalDragMaxHeight:99999,horizontalDragMinWidth:0,horizontalDragMaxWidth:99999,contentWidth:c,animateScroll:!1,animateDuration:300,animateEase:"linear",hijackInternalLinks:!1,verticalGutter:4,horizontalGutter:4,mouseWheelSpeed:3,arrowButtonSpeed:0,arrowRepeatFreq:50,arrowScrollOnHover:!1,trackClickSpeed:0,trackClickRepeatFreq:70,verticalArrowPositions:"split",horizontalArrowPositions:"split",enableKeyboardNavigation:!0,hideFocus:!1,keyboardSpeed:0,initialDelay:300,speed:30,scrollPagePercent:.8}}(jQuery,this);;/*! jquery.mlens.js - magnifying lens jQuery plugin for images by Federica Sibella (@musings.it) - Double licensed MIT and GPLv3 */
!function(e){function r(e){if("string"==typeof e){var r=e.indexOf("_");-1!=r&&(e=e.substr(r+1))}return e}var t=[],i=0,o={init:function(r){var o={lensShape:"square",lensSize:100,borderSize:4,borderColor:"#888",borderRadius:0,imgSrc:"",imgSrc2x:"",lensCss:"",imgOverlay:"",overlayAdapt:!0,zoomLevel:1},n=e.extend({},o,r);this.each(function(){var r=e(this),a=r.data("mlens"),s=e(),d=e(),c=e(),u=e(),g=r.attr("src"),p="auto";("number"!=typeof n.zoomLevel||n.zoomLevel<=0)&&(n.zoomLevel=o.zoomLevel);var l=new Image;""!==n.imgSrc2x&&window.devicePixelRatio>1?(g=n.imgSrc2x,l.onload=function(){p=String(parseInt(this.width/2)*n.zoomLevel)+"px",s.css({backgroundSize:p+" auto"}),u.css({width:p})},l.src=g):(""!==n.imgSrc&&(g=n.imgSrc),l.onload=function(){p=String(parseInt(this.width)*n.zoomLevel)+"px",s.css({backgroundSize:p+" auto"}),u.css({width:p})},l.src=g);var h="background-position: 0px 0px;width: "+String(n.lensSize)+"px;height: "+String(n.lensSize)+"px;float: left;display: none;border: "+String(n.borderSize)+"px solid "+n.borderColor+";background-repeat: no-repeat;position: absolute;",b="position: absolute; width: 100%; height: 100%; left: 0; top: 0; background-position: center center; background-repeat: no-repeat; z-index: 1;";switch(n.overlayAdapt===!0&&(b+="background-position: center center fixed; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"),n.lensShape){case"square":h=h+"border-radius:"+String(n.borderRadius)+"px;",b=b+"border-radius:"+String(n.borderRadius)+"px;";break;case"":h=h+"border-radius:"+String(n.borderRadius)+"px;",b=b+"border-radius:"+String(n.borderRadius)+"px;";break;case"circle":h=h+"border-radius: "+String(n.lensSize/2+n.borderSize)+"px;",b=b+"border-radius: "+String(n.lensSize/2+n.borderSize)+"px;";break;default:h=h+"border-radius:"+String(n.borderRadius)+"px;",b=b+"border-radius:"+String(n.borderRadius)+"px;"}return r.wrap("<div id='mlens_wrapper_"+i+"' />"),c=r.parent(),c.css({width:r.width()}),s=e("<div id='mlens_target_"+i+"' style='"+h+"' class='"+n.lensCss+"'>&nbsp;</div>").appendTo(c),s.css({backgroundImage:"url('"+g+"')",backgroundSize:p+" auto",cursor:"none"}),u=e("<img style='display:none;width:"+p+";height:auto;max-width:none;max-height;none;' src='"+g+"' />").appendTo(c),""!==n.imgOverlay&&(d=e("<div id='mlens_overlay_"+i+"' style='"+b+"'>&nbsp;</div>"),d.css({backgroundImage:"url('"+n.imgOverlay+"')",cursor:"none"}),d.appendTo(s)),r.attr("data-id","mlens_"+i),s.mousemove(function(t){e.fn.mlens("move",r.attr("data-id"),t)}),r.mousemove(function(t){e.fn.mlens("move",r.attr("data-id"),t)}),s.on("touchmove",function(t){t.preventDefault();var i=t.originalEvent.touches[0]||t.originalEvent.changedTouches[0];e.fn.mlens("move",r.attr("data-id"),i)}),r.on("touchmove",function(t){t.preventDefault();var i=t.originalEvent.touches[0]||t.originalEvent.changedTouches[0];e.fn.mlens("move",r.attr("data-id"),i)}),s.hover(function(){e(this).show()},function(){e(this).hide()}),r.hover(function(){s.show()},function(){s.hide()}),s.on("touchstart",function(r){r.preventDefault(),e(this).show()}),s.on("touchend",function(r){r.preventDefault(),e(this).hide()}),r.on("touchstart",function(e){e.preventDefault(),s.show()}),r.on("touchend",function(e){e.preventDefault(),s.hide()}),r.data("mlens",{image:r,settings:n,target:s,imageTag:u,imgSrc:g,imgWidth:p,imageWrapper:c,overlay:d,instance:i}),a=r.data("mlens"),t[i]=a,i++,t})},move:function(e,i){e=r(e);var o=t[e],n=o.image,a=o.target,s=o.imageTag,d=n.offset(),c=parseInt(i.pageX-d.left),u=parseInt(i.pageY-d.top),g=s.width()/n.width(),p=s.height()/n.height();return c>0&&u>0&&c<n.width()&&u<n.height()&&(c=String(-((i.pageX-d.left)*g-a.width()/2)),u=String(-((i.pageY-d.top)*p-a.height()/2)),a.css({backgroundPosition:c+"px "+u+"px"}),c=String(i.pageX-d.left-a.width()/2),u=String(i.pageY-d.top-a.height()/2),a.css({left:c+"px",top:u+"px"})),o.target=a,t[e]=o,t},update:function(i){var o=r(e(this).attr("data-id")),n=t[o],a=n.image,s=n.target,d=n.overlay,c=n.imageTag,u=n.imgSrc,g=n.settings,p=e.extend({},g,i),l="auto",h=new Image;""!==p.imgSrc2x&&window.devicePixelRatio>1?(u=p.imgSrc2x,h.onload=function(){l=String(parseInt(this.width/2))+"px",s.css({backgroundSize:l+" auto"}),c.css({width:l})},h.src=u):(""!==p.imgSrc&&(u=p.imgSrc),h.onload=function(){l=String(parseInt(this.width)*p.zoomLevel)+"px",s.css({backgroundSize:l+" auto"}),c.css({width:l})},h.src=u);var b="background-position: 0px 0px;width: "+String(p.lensSize)+"px;height: "+String(p.lensSize)+"px;float: left;display: none;border: "+String(p.borderSize)+"px solid "+p.borderColor+";background-repeat: no-repeat;position: absolute;",m="position: absolute; width: 100%; height: 100%; left: 0; top: 0; background-position: center center; background-repeat: no-repeat; z-index: 1;";switch(p.overlayAdapt===!0&&(m+="background-position: center center fixed; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;"),p.lensShape){case"square":b=b+"border-radius:"+String(p.borderRadius)+"px;",m=m+"border-radius:"+String(p.borderRadius)+"px;";break;case"":b=b+"border-radius:"+String(p.borderRadius)+"px;",m=m+"border-radius:"+String(p.borderRadius)+"px;";break;case"circle":b=b+"border-radius: "+String(p.lensSize/2+p.borderSize)+"px;",m=m+"border-radius: "+String(p.lensSize/2+p.borderSize)+"px;";break;default:b=b+"border-radius:"+String(p.borderRadius)+"px;",m=m+"border-radius:"+String(p.borderRadius)+"px;"}return s.attr("style",b),c.attr("src",u),c.css({width:l}),s.css({backgroundImage:"url('"+u+"')",backgroundSize:l+" auto",cursor:"none"}),d.attr("style",m),d.css({backgroundImage:"url('"+p.imgOverlay+"')",cursor:"none"}),n.image=a,n.target=s,n.overlay=d,n.settings=p,n.imgSrc=u,n.imageTag=c,t[o]=n,t},destroy:function(){var i=r(e(this).attr("data-id")),o=t[i];o.target.remove(),o.imageTag.remove(),o.image.unwrap(),o.overlay.remove(),e.removeData(o,"mlens"),this.unbind(),this.element=null}};e.fn.mlens=function(r){return o[r]?o[r].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof r&&r?void e.error("Method "+r+" does not exist on jQuery.mlens"):o.init.apply(this,arguments)}}(jQuery);;/*! Copyright (c) 2013 Brandon Aaron (http://brandon.aaron.sh)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Version: 3.1.9
 *
 * Requires: jQuery 1.2.2+
 */

(function (factory) {
    if ( typeof define === 'function' && define.amd ) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var toFix  = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'],
        toBind = ( 'onwheel' in document || document.documentMode >= 9 ) ?
                    ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'],
        slice  = Array.prototype.slice,
        nullLowestDeltaTimeout, lowestDelta;

    if ( $.event.fixHooks ) {
        for ( var i = toFix.length; i; ) {
            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
        }
    }

    var special = $.event.special.mousewheel = {
        version: '3.1.9',

        setup: function() {
            if ( this.addEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.addEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = handler;
            }
            // Store the line height and page height for this particular element
            $.data(this, 'mousewheel-line-height', special.getLineHeight(this));
            $.data(this, 'mousewheel-page-height', special.getPageHeight(this));
        },

        teardown: function() {
            if ( this.removeEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.removeEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = null;
            }
        },

        getLineHeight: function(elem) {
            return parseInt($(elem)['offsetParent' in $.fn ? 'offsetParent' : 'parent']().css('fontSize'), 10);
        },

        getPageHeight: function(elem) {
            return $(elem).height();
        },

        settings: {
            adjustOldDeltas: true
        }
    };

    $.fn.extend({
        mousewheel: function(fn) {
            return fn ? this.bind('mousewheel', fn) : this.trigger('mousewheel');
        },

        unmousewheel: function(fn) {
            return this.unbind('mousewheel', fn);
        }
    });


    function handler(event) {
        var orgEvent   = event || window.event,
            args       = slice.call(arguments, 1),
            delta      = 0,
            deltaX     = 0,
            deltaY     = 0,
            absDelta   = 0;
        event = $.event.fix(orgEvent);
        event.type = 'mousewheel';

        // Old school scrollwheel delta
        if ( 'detail'      in orgEvent ) { deltaY = orgEvent.detail * -1;      }
        if ( 'wheelDelta'  in orgEvent ) { deltaY = orgEvent.wheelDelta;       }
        if ( 'wheelDeltaY' in orgEvent ) { deltaY = orgEvent.wheelDeltaY;      }
        if ( 'wheelDeltaX' in orgEvent ) { deltaX = orgEvent.wheelDeltaX * -1; }

        // Firefox < 17 horizontal scrolling related to DOMMouseScroll event
        if ( 'axis' in orgEvent && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
            deltaX = deltaY * -1;
            deltaY = 0;
        }

        // Set delta to be deltaY or deltaX if deltaY is 0 for backwards compatabilitiy
        delta = deltaY === 0 ? deltaX : deltaY;

        // New school wheel delta (wheel event)
        if ( 'deltaY' in orgEvent ) {
            deltaY = orgEvent.deltaY * -1;
            delta  = deltaY;
        }
        if ( 'deltaX' in orgEvent ) {
            deltaX = orgEvent.deltaX;
            if ( deltaY === 0 ) { delta  = deltaX * -1; }
        }

        // No change actually happened, no reason to go any further
        if ( deltaY === 0 && deltaX === 0 ) { return; }

        // Need to convert lines and pages to pixels if we aren't already in pixels
        // There are three delta modes:
        //   * deltaMode 0 is by pixels, nothing to do
        //   * deltaMode 1 is by lines
        //   * deltaMode 2 is by pages
        if ( orgEvent.deltaMode === 1 ) {
            var lineHeight = $.data(this, 'mousewheel-line-height');
            delta  *= lineHeight;
            deltaY *= lineHeight;
            deltaX *= lineHeight;
        } else if ( orgEvent.deltaMode === 2 ) {
            var pageHeight = $.data(this, 'mousewheel-page-height');
            delta  *= pageHeight;
            deltaY *= pageHeight;
            deltaX *= pageHeight;
        }

        // Store lowest absolute delta to normalize the delta values
        absDelta = Math.max( Math.abs(deltaY), Math.abs(deltaX) );

        if ( !lowestDelta || absDelta < lowestDelta ) {
            lowestDelta = absDelta;

            // Adjust older deltas if necessary
            if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
                lowestDelta /= 40;
            }
        }

        // Adjust older deltas if necessary
        if ( shouldAdjustOldDeltas(orgEvent, absDelta) ) {
            // Divide all the things by 40!
            delta  /= 40;
            deltaX /= 40;
            deltaY /= 40;
        }

        // Get a whole, normalized value for the deltas
        delta  = Math[ delta  >= 1 ? 'floor' : 'ceil' ](delta  / lowestDelta);
        deltaX = Math[ deltaX >= 1 ? 'floor' : 'ceil' ](deltaX / lowestDelta);
        deltaY = Math[ deltaY >= 1 ? 'floor' : 'ceil' ](deltaY / lowestDelta);

        // Add information to the event object
        event.deltaX = deltaX;
        event.deltaY = deltaY;
        event.deltaFactor = lowestDelta;
        // Go ahead and set deltaMode to 0 since we converted to pixels
        // Although this is a little odd since we overwrite the deltaX/Y
        // properties with normalized deltas.
        event.deltaMode = 0;

        // Add event and delta to the front of the arguments
        args.unshift(event, delta, deltaX, deltaY);

        // Clearout lowestDelta after sometime to better
        // handle multiple device types that give different
        // a different lowestDelta
        // Ex: trackpad = 3 and mouse wheel = 120
        if (nullLowestDeltaTimeout) { clearTimeout(nullLowestDeltaTimeout); }
        nullLowestDeltaTimeout = setTimeout(nullLowestDelta, 200);

        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

    function nullLowestDelta() {
        lowestDelta = null;
    }

    function shouldAdjustOldDeltas(orgEvent, absDelta) {
        // If this is an older event and the delta is divisable by 120,
        // then we are assuming that the browser is treating this as an
        // older mouse wheel event and that we should divide the deltas
        // by 40 to try and get a more usable deltaFactor.
        // Side note, this actually impacts the reported scroll distance
        // in older browsers and can cause scrolling to be slower than native.
        // Turn this off by setting $.event.special.mousewheel.settings.adjustOldDeltas to false.
        return special.settings.adjustOldDeltas && orgEvent.type === 'mousewheel' && absDelta % 120 === 0;
    }

}));

;/**
 * @preserve
 *
 * slippry v1.2.1 - Simple responsive content slider
 * http://slippry.com
 *
 * Author(s): Lukas Jakob Hafner - @saftsaak 
 *
 * Copyright 2013, booncon oy - http://booncon.com
 *
 * Thanks @ http://bxslider.com for the inspiration!
 *
 * Released under the MIT license - http://opensource.org/licenses/MIT
 */

(function ($) {
  "use strict";
  var defaults;

  defaults = {
    // general elements & wrapper
    slippryWrapper: '<div class="sy-box" />', // wrapper to wrap everything, including pager
    slideWrapper: '<div class="sy-slides-wrap" />', // wrapper to wrap sildes & controls
    slideCrop: '<div class="sy-slides-crop" />', //additional wrapper around just the slides
    boxClass: 'sy-list', // class that goes to original element
    elements: 'li', // elments cointaining slide content
    activeClass: 'sy-active', // class for current slide
    fillerClass: 'sy-filler', // class for element that acts as intrinsic placholder
    loadingClass: 'sy-loading',

    // options
    adaptiveHeight: true, // height of the sliders adapts to current slide
    start: 1, // num (starting from 1), random
    loop: true, // first -> last & last -> first arrows
    captionsSrc: 'img', // img, el [img takes caption from alt or title, el from title of slide element]
    captions: 'overlay', // Position: overlay, below, custom, false
    captionsEl: '.sy-caption', // $ selector for captions wrapper
    initSingle: true, // initialise even if there is only one slide
    responsive: true,
    preload: 'visible', // visible, all | resources to wait for until showing slider

    // pager
    pager: true,
    pagerClass: 'sy-pager',

    // controls
    controls: true,
    controlClass: 'sy-controls',
    prevClass: 'sy-prev',
    prevText: 'Previous',
    nextClass: 'sy-next',
    nextText: 'Next',
    hideOnEnd: true,

    // transitions
    transition: 'fade', // fade, horizontal, vertical, kenburns, false
    kenZoom: 120, // max zoom for kenburns (in %)
    slideMargin: 0, // spacing between slides (in %)
    transClass: 'transition', // [Class applied to [element] while a transition is taking place.]
    speed: 800, // time the transition takes (ms)
    easing: 'swing', // easing to use in the animation [(see... [jquery www])]
    continuous: true, // seamless first/ last transistion, only works with loop
    useCSS: true, // true, false -> fallback to js if no browser support

    //slideshow
    auto: true,
    autoDirection: 'next',
    autoHover: true,
    autoHoverDelay: 100,
    autoDelay: 500,
    pause: 4000,

    // callback functions
    onSliderLoad: function () { // when slider loaded
      return this;
    },
    onSlideBefore: function () { // before page transition starts
      return this;
    },
    onSlideAfter: function () {  // after page transition happened
      return this;
    }
  };

  $.fn.slippry = function (options) {
    var slip, el, prepareFiller, getFillerProportions, init, updateCaption, initPager, initControls, ready, transitionDone, whichTransitionEvent,
      initCaptions, updatePager, setFillerProportions, doTransition, updateSlide, updateControls, updatePos, supports, preload, start, elFromSel, doKens;

    // reference to the object calling the function
    el = this;

    // if no elements just stop
    if (el.length === 0) {
      return this;
    }
    // support mutltiple elements
    if (el.length > 1) {
      el.each(function () {
        $(this).slippry(options);
      });
      return this;
    }

    // variable to access the slider settings across the plugin
    slip = {};
    slip.vars = {};

    whichTransitionEvent = function () { // Thanks! http://stackoverflow.com/a/18672988
      var t, div, transitions;
      div = document.createElement('div');
      transitions = {
        'WebkitTransition' : 'webkitTransitionEnd',
        'MozTransition'    : 'transitionend',
        'MSTransition'     : 'msTransitionEnd',
        'OTransition'      : 'oTransitionEnd',
        'transition'       : 'transitionEnd transitionend'
      };
      for (t in transitions) {
        if (div.style[t] !== undefined) {
          return transitions[t];
        }
      }
    };

    supports = (function () {  // Thanks! http://net.tutsplus.com/tutorials/html-css-techniques/quick-tip-detect-css-support-in-browsers-with-javascript/
      var div = document.createElement('div'),
        vendors = ['Khtml', 'Ms', 'O', 'Moz', 'Webkit'],
        len = vendors.length;
      return function (prop) {
        if (prop in div.style) {
          return true;
        }
        prop = prop.replace(/^[a-z]/, function (val) {
          return val.toUpperCase();
        });
        while (len--) {
          if (vendors[len] + prop in div.style) {
            return true;
          }
        }
        return false;
      };
    }());

    elFromSel = function (sel, prop) {
      var props, newelement, id, className;
      props = prop.split('.');
      newelement = $(sel);
      id = '';
      className = '';
      $.each(props, function (i, val) {
        if (val.indexOf('#') >= 0) {
          id += val.replace(/^#/, '');
        } else {
          className += val + ' ';
        }
      });
      if (id.length) {
        newelement.attr('id', id);
      }
      if (className.length) {
        newelement.attr('class', $.trim(className));
      }
      return newelement;
    };

    doKens = function () {
      var kenStart, kenTime, animProp, cssProp;
      animProp = {};
      cssProp = {};
      kenStart = 100 - slip.settings.kenZoom;
      cssProp.width = slip.settings.kenZoom + '%';
      if (slip.vars.active.index() % 2 === 0) {
        cssProp.left = kenStart + '%';
        cssProp.top = kenStart + '%';
        animProp.left = '0%';
        animProp.top = '0%';
      } else {
        cssProp.left = '0%';
        cssProp.top = '0%';
        animProp.left = kenStart + '%';
        animProp.top = kenStart + '%';
      }
      kenTime = slip.settings.pause + slip.settings.speed * 2;
      slip.vars.active.css(cssProp);
      slip.vars.active.animate(animProp, {duration: kenTime, easing: slip.settings.easing, queue: false});
    };

    ready = function () {
      if (slip.vars.fresh) {
        slip.vars.slippryWrapper.removeClass(slip.settings.loadingClass);
        slip.vars.fresh = false;
        if (slip.settings.auto) {
          el.startAuto();
        }
        if (!slip.settings.useCSS && slip.settings.transition === 'kenburns') {
          doKens();
        }
        slip.settings.onSliderLoad.call(undefined, slip.vars.active.index());
      } else {
        $('.' + slip.settings.fillerClass, slip.vars.slideWrapper).addClass('ready');
      }
    };

    setFillerProportions = function (width, height) {
      var ratio, p_top, $filler;
      ratio = width / height;
      p_top = 1 / ratio * 100 + '%';  //cool intrinsic trick: http://alistapart.com/article/creating-intrinsic-ratios-for-video
      $filler = $('.' + slip.settings.fillerClass, slip.vars.slideWrapper);
      $filler.css({paddingTop: p_top}); // resizing without the need of js, true responsiveness :)
      ready();
    };

    // gets the aspect ratio of the filler element
    getFillerProportions = function ($slide) {
      var width, height;
      if (($('img', $slide).attr("src") !== undefined)) {
        $("<img />").load(function () {
          width = $slide.width();
          height = $slide.height();
          setFillerProportions(width, height);
        }).attr("src", $('img', $slide).attr("src"));
      } else {
        width = $slide.width();
        height = $slide.height();
        setFillerProportions(width, height);
      }
    };

    // prepares a div to occupy the needed space
    prepareFiller = function () {
      if ($('.' + slip.settings.fillerClass, slip.vars.slideWrapper).length === 0) {
        slip.vars.slideWrapper.append($('<div class="' + slip.settings.fillerClass + '" />'));
      }
      if (slip.settings.adaptiveHeight === true) {  // if the slides shoud alwas adapt to their content
        getFillerProportions($('.' + slip.settings.activeClass, el));  // set the filler height on the active element
      } else {  // otherwise get the highest element
        var $highest, height, loop;
        height = 0;
        loop = 0;
        $(slip.vars.slides).each(function () {
          if ($(this).height() > height) {
            $highest = $(this);
            height = $highest.height();
          }
          loop = loop + 1;
          if (loop === slip.vars.count) {
            if ($highest === undefined) {
              $highest = $($(slip.vars.slides)[0]);
            }
            getFillerProportions($highest);
          }
        });
      }
    };

    updatePager = function () {
      if (slip.settings.pager) {
        $('.' + slip.settings.pagerClass + ' li', slip.vars.slippryWrapper).removeClass(slip.settings.activeClass);
        $($('.' + slip.settings.pagerClass + ' li', slip.vars.slippryWrapper)[slip.vars.active.index()]).addClass(slip.settings.activeClass);
      }
    };

    updateControls = function () {
      if (!slip.settings.loop && slip.settings.hideOnEnd) {
        $('.' + slip.settings.prevClass, slip.vars.slippryWrapper)[slip.vars.first ? 'hide' : 'show']();
        $('.' + slip.settings.nextClass, slip.vars.slippryWrapper)[slip.vars.last ? 'hide' : 'show']();
      }
    };

    updateCaption = function () {
      var caption, wrapper;
      if (slip.settings.captions !== false) {
        if (slip.settings.captionsSrc !== 'img') {
          caption = slip.vars.active.attr('title');
        } else {
          caption = $('img', slip.vars.active).attr('title') !== undefined ? $('img', slip.vars.active).attr('title') : $('img', slip.vars.active).attr('alt');
        }
        if (slip.settings.captions !== 'custom') {
          wrapper = $(slip.settings.captionsEl, slip.vars.slippryWrapper);
        } else {
          wrapper = $(slip.settings.captionsEl);
        }
        if ((caption !== undefined) && (caption !== '')) {
          wrapper.html(caption).show();
        } else {
          wrapper.hide();
        }
      }
    };

    el.startAuto = function () {
      if ((slip.vars.timer === undefined) && (slip.vars.delay === undefined)) {
        slip.vars.delay = window.setTimeout(function () {
          slip.vars.autodelay = false;
          slip.vars.timer = window.setInterval(function () {
            slip.vars.trigger = 'auto';
            el.goToSlide(slip.settings.autoDirection);
          }, slip.settings.pause);
        }, slip.vars.autodelay ? slip.settings.autoHoverDelay : slip.settings.autoDelay);
      }
      if (slip.settings.autoHover) {
        slip.vars.slideWrapper.unbind('mouseenter').unbind('mouseleave').bind('mouseenter', function () {
          if (slip.vars.timer !== undefined) {
            slip.vars.hoverStop = true;
            el.stopAuto();
          } else {
            slip.vars.hoverStop = false;
          }
        }).bind('mouseleave', function () {
          if (slip.vars.hoverStop) {
            slip.vars.autodelay = true;
            el.startAuto();
          }
        });
      }
    };

    el.stopAuto = function () {
      window.clearInterval(slip.vars.timer);
      slip.vars.timer = undefined;
      window.clearTimeout(slip.vars.delay);
      slip.vars.delay = undefined;
    };

    // refreshes the already initialised slider
    el.refresh = function () {
      slip.vars.slides.removeClass(slip.settings.activeClass);
      slip.vars.active.addClass(slip.settings.activeClass);
      if (slip.settings.responsive) {
        prepareFiller();
      } else {
        ready();
      }
      updateControls();
      updatePager();
      updateCaption();
    };

    updateSlide = function () {
      el.refresh();
    };

    transitionDone = function () {
      slip.vars.moving = false;
      slip.vars.active.removeClass(slip.settings.transClass);
      if (!slip.vars.fresh) {
        slip.vars.old.removeClass('sy-ken');
      }
      slip.vars.old.removeClass(slip.settings.transClass);
      slip.settings.onSlideAfter.call(undefined, slip.vars.active, slip.vars.old.index(), slip.vars.active.index());
    };

    doTransition = function () {
      var pos, jump, old_left, old_pos, kenTime, ref, cssProp;
      slip.settings.onSlideBefore.call(undefined, slip.vars.active, slip.vars.old.index(), slip.vars.active.index());
      if (slip.settings.transition !== false) {
        slip.vars.moving = true;
        if ((slip.settings.transition === 'fade') || (slip.settings.transition === 'kenburns')) {
          if (slip.vars.fresh) {
            if (slip.settings.useCSS) {
              slip.vars.slides.css({transitionDuration: slip.settings.speed + 'ms', opacity: 0});
            } else {
              slip.vars.slides.css({opacity: 0});
            }
            slip.vars.active.css('opacity', 1);
            if (slip.settings.transition === 'kenburns') {
              if (slip.settings.useCSS) {
                kenTime = slip.settings.pause + slip.settings.speed * 2;
                slip.vars.slides.css({animationDuration: kenTime + 'ms'});
                slip.vars.active.addClass('sy-ken');
              }
            }
            transitionDone();
          } else {
            if (slip.settings.useCSS) {
              slip.vars.old.addClass(slip.settings.transClass).css('opacity', 0);
              slip.vars.active.addClass(slip.settings.transClass).css('opacity', 1);
              if (slip.settings.transition === 'kenburns') {
                slip.vars.active.addClass('sy-ken');
              }
              $(window).off('focus').on('focus', function () { // bugfix for safari 7 which doesn't always trigger ontransitionend when switching tab
                if (slip.vars.moving) {
                  slip.vars.old.trigger(slip.vars.transition);
                }
              });
              slip.vars.old.one(slip.vars.transition, function () {
                transitionDone();
                return this;
              });
            } else {
              if (slip.settings.transition === 'kenburns') {
                doKens();
              }
              slip.vars.old.addClass(slip.settings.transClass).animate({
                opacity: 0
              }, slip.settings.speed, slip.settings.easing, function () {
                transitionDone();
              });
              slip.vars.active.addClass(slip.settings.transClass).css('opacity', 0).animate({
                opacity: 1
              }, slip.settings.speed, slip.settings.easing);
            }
          }
          updateSlide();
        } else if ((slip.settings.transition === 'horizontal') || (slip.settings.transition === 'vertical')) {
          ref = (slip.settings.transition === 'horizontal') ? 'left' : 'top';
          pos = '-' + slip.vars.active.index() * (100 + slip.settings.slideMargin) + '%';
          if (slip.vars.fresh) {
            el.css(ref, pos);
            transitionDone();
          } else {
            cssProp = {};
            if (slip.settings.continuous) {
              if (slip.vars.jump && ((slip.vars.trigger === 'controls') || (slip.vars.trigger === 'auto'))) {
                jump = true;
                old_pos = pos;
                if (slip.vars.first) {
                  old_left = 0;
                  slip.vars.active.css(ref, slip.vars.count * (100 + slip.settings.slideMargin) + '%');
                  pos = '-' + slip.vars.count * (100 + slip.settings.slideMargin) + '%';
                } else {
                  old_left = (slip.vars.count - 1) * (100 + slip.settings.slideMargin) + '%';
                  slip.vars.active.css(ref, -(100 + slip.settings.slideMargin) + '%');
                  pos = (100 + slip.settings.slideMargin) + '%';
                }
              }
            }
            slip.vars.active.addClass(slip.settings.transClass);
            if (slip.settings.useCSS) {
              cssProp[ref] = pos;
              cssProp.transitionDuration = slip.settings.speed + 'ms';
              el.addClass(slip.settings.transition);
              el.css(cssProp);
              $(window).off('focus').on('focus', function () { // bugfix for safari 7 which doesn't always trigger ontransitionend when switching tab
                if (slip.vars.moving) {
                  el.trigger(slip.vars.transition);
                }
              });
              el.one(slip.vars.transition, function () {
                el.removeClass(slip.settings.transition);
                if (jump) {
                  slip.vars.active.css(ref, old_left);
                  cssProp[ref] = old_pos;
                  cssProp.transitionDuration = '0ms';
                  el.css(cssProp);
                }
                transitionDone();
                return this;
              });
            } else {
              cssProp[ref] = pos;
              el.stop().animate(cssProp, slip.settings.speed, slip.settings.easing, function () {
                if (jump) {
                  slip.vars.active.css(ref, old_left);
                  el.css(ref, old_pos);
                }
                transitionDone();
                return this;
              });
            }
          }
          updateSlide();
        }
      } else {
        updateSlide();
        transitionDone();
      }
    };

    updatePos = function (slide) {
      slip.vars.first = false;
      slip.vars.last = false;
      if ((slide === 'prev') || (slide === 0)) {
        slip.vars.first = true;
      } else if ((slide === 'next') || (slide === slip.vars.count - 1)) {
        slip.vars.last = true;
      }
    };

    el.goToSlide = function (slide) {
      var current;
      if (!slip.vars.moving) {
        current = slip.vars.active.index();
        if (slide === 'prev') {
          if (current > 0) {
            slide = current - 1;
          } else if (slip.settings.loop) {
            slide = slip.vars.count - 1;
          }
        } else if (slide === 'next') {
          if (current < slip.vars.count - 1) {
            slide = current + 1;
          } else if (slip.settings.loop) {
            slide = 0;
          }
        } else {
          slide = slide - 1;
        }
        slip.vars.jump = false;
        if ((slide !== 'prev') && (slide !== 'next') && ((slide !== current) || (slip.vars.fresh))) {
          updatePos(slide);
          slip.vars.old = slip.vars.active;
          slip.vars.active = $(slip.vars.slides[slide]);
          if (((current === 0) && (slide === slip.vars.count - 1)) || ((current === slip.vars.count - 1) && (slide === 0))) {
            slip.vars.jump = true;
          }
          doTransition();
        }
      }
    };

    el.goToNextSlide = function () {
      el.goToSlide('next');
    };

    el.goToPrevSlide = function () {
      el.goToSlide('prev');
    };

    initPager = function () {
      if ((slip.settings.pager) && (slip.vars.count > 1)) {
        var count, loop, pager;
        count = slip.vars.slides.length;
        pager = $('<ul class="' + slip.settings.pagerClass + '" />');
        for (loop = 1; loop < count + 1; loop = loop + 1) {
          pager.append($('<li />').append($('<a href="#' + loop + '">' + loop + '</a>')));
        }
        slip.vars.slippryWrapper.append(pager);
        $('.' + slip.settings.pagerClass + ' a', slip.vars.slippryWrapper).click(function () {
          slip.vars.trigger = 'pager';
          el.goToSlide(parseInt(this.hash.split('#')[1], 10));
          return false;
        });
        updatePager();
      }
    };

    initControls = function () {
      if ((slip.settings.controls) && (slip.vars.count > 1)) {
        slip.vars.slideWrapper.append(
          $('<ul class="' + slip.settings.controlClass + '" />')
            .append('<li class="' + slip.settings.prevClass + '"><a href="#prev">' + slip.settings.prevText + '</a></li>')
            .append('<li class="' + slip.settings.nextClass + '"><a href="#next">' + slip.settings.nextText + '</a></li>')
        );
        $('.' + slip.settings.controlClass + ' a', slip.vars.slippryWrapper).click(function () {
          slip.vars.trigger = 'controls';
          el.goToSlide(this.hash.split('#')[1]);
          return false;
        });
        updateControls();
      }
    };

    initCaptions = function () {
      if (slip.settings.captions !== false) {
        if (slip.settings.captions === 'overlay') {
          slip.vars.slideWrapper.append($('<div class="sy-caption-wrap" />').html(elFromSel('<div />', slip.settings.captionsEl)));
        } else if (slip.settings.captions === 'below') {
          slip.vars.slippryWrapper.append($('<div class="sy-caption-wrap" />').html(elFromSel('<div />', slip.settings.captionsEl)));
        }
      }
    };

    // actually show the first slide
    start = function () {
      el.goToSlide(slip.vars.active.index() + 1);
    };

    // wait for images, iframes to be loaded
    preload = function (slides) {
      var count, loop, elements, container;
      container = (slip.settings.preload === 'all') ? slides : slip.vars.active;
      elements = $('img, iframe', container);
      count = elements.length;
      if (count === 0) {
        start();
        return;
      }
      loop = 0;
      elements.each(function () {
        $(this).one('load error', function () {
          if (++loop === count) {
            start();
          }
        }).each(function () {
          if (this.complete) {
            $(this).load();
          }
        });
      });
    };

    el.getCurrentSlide = function () {
      return slip.vars.active;
    };

    el.getSlideCount = function () {
      return slip.vars.count;
    };

    el.destroySlider = function () {
      if (slip.vars.fresh === false) {
        el.stopAuto();
        slip.vars.moving = false;
        slip.vars.slides.each(function () {
          if ($(this).data("sy-cssBckup") !== undefined) {
            $(this).attr("style", $(this).data("sy-cssBckup"));
          } else {
            $(this).removeAttr('style');
          }
          if ($(this).data("sy-classBckup") !== undefined) {
            $(this).attr("class", $(this).data("sy-classBckup"));
          } else {
            $(this).removeAttr('class');
          }
        });
        if (el.data("sy-cssBckup") !== undefined) {
          el.attr("style", el.data("sy-cssBckup"));
        } else {
          el.removeAttr('style');
        }
        if (el.data("sy-classBckup") !== undefined) {
          el.attr("class", el.data("sy-classBckup"));
        } else {
          el.removeAttr('class');
        }
        slip.vars.slippryWrapper.before(el);
        slip.vars.slippryWrapper.remove();
        slip.vars.fresh = undefined;
      }
    };

    el.reloadSlider = function () {
      el.destroySlider();
      init();
    };

    // initialises the slider, creates needed markup
    init = function () {
      var first;
      slip.settings = $.extend({}, defaults, options);
      slip.vars.slides = $(slip.settings.elements, el);
      slip.vars.count = slip.vars.slides.length;
      if (slip.settings.useCSS) { // deactivate css transitions on unsupported browsers
        if (!supports('transition')) {
          slip.settings.useCSS = false;
        }
        slip.vars.transition = whichTransitionEvent();
      }
      el.data('sy-cssBckup', el.attr('style'));
      el.data('sy-classBackup', el.attr('class'));
      el.addClass(slip.settings.boxClass).wrap(slip.settings.slippryWrapper).wrap(slip.settings.slideWrapper).wrap(slip.settings.slideCrop);
      slip.vars.slideWrapper = el.parent().parent();
      slip.vars.slippryWrapper = slip.vars.slideWrapper.parent().addClass(slip.settings.loadingClass);
      slip.vars.fresh = true;
      slip.vars.slides.each(function () {
        $(this).addClass('sy-slide ' + slip.settings.transition);
        if (slip.settings.useCSS) {
          $(this).addClass('useCSS');
        }
        if (slip.settings.transition === 'horizontal') {
          $(this).css('left', $(this).index() * (100 + slip.settings.slideMargin) + '%');
        } else if (slip.settings.transition === 'vertical') {
          $(this).css('top', $(this).index() * (100 + slip.settings.slideMargin) + '%');
        }
      });
      if ((slip.vars.count > 1) || (slip.settings.initSingle)) {
        if ($('.' + slip.settings.activeClass, el).index() === -1) {
          if (slip.settings.start === 'random') {
            first = Math.round(Math.random() * (slip.vars.count - 1));
          } else if (slip.settings.start > 0 && slip.settings.start <= slip.vars.count) {
            first = slip.settings.start - 1;
          } else {
            first = 0;
          }
          slip.vars.active = $(slip.vars.slides[first]).addClass(slip.settings.activeClass);
        } else {
          slip.vars.active = $('.' + slip.settings.activeClass, el);
        }
        initControls();
        initPager();
        initCaptions();
        preload(slip.vars.slides);
      } else {
        return this;
      }
    };

    init(); // on startup initialise the slider

    return this;
  };
}(jQuery));;/*! Video.js v4.8.1 Copyright 2014 Brightcove, Inc. https://github.com/videojs/video.js/blob/master/LICENSE */ 
(function() {var b=void 0,f=!0,k=null,l=!1;function m(){return function(){}}function p(a){return function(){return this[a]}}function r(a){return function(){return a}}var s;document.createElement("video");document.createElement("audio");document.createElement("track");function t(a,c,d){if("string"===typeof a){0===a.indexOf("#")&&(a=a.slice(1));if(t.Ba[a])return t.Ba[a];a=t.v(a)}if(!a||!a.nodeName)throw new TypeError("The element or ID supplied is not valid. (videojs)");return a.player||new t.Player(a,c,d)}
var videojs=window.videojs=t;t.Ub="4.8";t.Sc="https:"==document.location.protocol?"https://":"http://";
t.options={techOrder:["html5","flash"],html5:{},flash:{},width:300,height:150,defaultVolume:0,playbackRates:[],inactivityTimeout:2E3,children:{mediaLoader:{},posterImage:{},textTrackDisplay:{},loadingSpinner:{},bigPlayButton:{},controlBar:{},errorDisplay:{}},language:document.getElementsByTagName("html")[0].getAttribute("lang")||navigator.languages&&navigator.languages[0]||navigator.ve||navigator.language||"en",languages:{},notSupportedMessage:"No compatible source was found for this video."};
"GENERATED_CDN_VSN"!==t.Ub&&(videojs.options.flash.swf=t.Sc+"vjs.zencdn.net/"+t.Ub+"/video-js.swf");t.dd=function(a,c){t.options.languages[a]=t.options.languages[a]!==b?t.ga.Va(t.options.languages[a],c):c;return t.options.languages};t.Ba={};"function"===typeof define&&define.amd?define([],function(){return videojs}):"object"===typeof exports&&"object"===typeof module&&(module.exports=videojs);t.qa=t.CoreObject=m();
t.qa.extend=function(a){var c,d;a=a||{};c=a.init||a.i||this.prototype.init||this.prototype.i||m();d=function(){c.apply(this,arguments)};d.prototype=t.h.create(this.prototype);d.prototype.constructor=d;d.extend=t.qa.extend;d.create=t.qa.create;for(var e in a)a.hasOwnProperty(e)&&(d.prototype[e]=a[e]);return d};t.qa.create=function(){var a=t.h.create(this.prototype);this.apply(a,arguments);return a};
t.d=function(a,c,d){if(t.h.isArray(c))return u(t.d,a,c,d);var e=t.getData(a);e.C||(e.C={});e.C[c]||(e.C[c]=[]);d.w||(d.w=t.w++);e.C[c].push(d);e.X||(e.disabled=l,e.X=function(c){if(!e.disabled){c=t.pc(c);var d=e.C[c.type];if(d)for(var d=d.slice(0),j=0,n=d.length;j<n&&!c.wc();j++)d[j].call(a,c)}});1==e.C[c].length&&(a.addEventListener?a.addEventListener(c,e.X,l):a.attachEvent&&a.attachEvent("on"+c,e.X))};
t.o=function(a,c,d){if(t.sc(a)){var e=t.getData(a);if(e.C){if(t.h.isArray(c))return u(t.o,a,c,d);if(c){var g=e.C[c];if(g){if(d){if(d.w)for(e=0;e<g.length;e++)g[e].w===d.w&&g.splice(e--,1)}else e.C[c]=[];t.jc(a,c)}}else for(g in e.C)c=g,e.C[c]=[],t.jc(a,c)}}};t.jc=function(a,c){var d=t.getData(a);0===d.C[c].length&&(delete d.C[c],a.removeEventListener?a.removeEventListener(c,d.X,l):a.detachEvent&&a.detachEvent("on"+c,d.X));t.Hb(d.C)&&(delete d.C,delete d.X,delete d.disabled);t.Hb(d)&&t.Fc(a)};
t.pc=function(a){function c(){return f}function d(){return l}if(!a||!a.Ib){var e=a||window.event;a={};for(var g in e)"layerX"!==g&&("layerY"!==g&&"keyboardEvent.keyLocation"!==g)&&("returnValue"==g&&e.preventDefault||(a[g]=e[g]));a.target||(a.target=a.srcElement||document);a.relatedTarget=a.fromElement===a.target?a.toElement:a.fromElement;a.preventDefault=function(){e.preventDefault&&e.preventDefault();a.returnValue=l;a.yd=c;a.defaultPrevented=f};a.yd=d;a.defaultPrevented=l;a.stopPropagation=function(){e.stopPropagation&&
e.stopPropagation();a.cancelBubble=f;a.Ib=c};a.Ib=d;a.stopImmediatePropagation=function(){e.stopImmediatePropagation&&e.stopImmediatePropagation();a.wc=c;a.stopPropagation()};a.wc=d;if(a.clientX!=k){g=document.documentElement;var h=document.body;a.pageX=a.clientX+(g&&g.scrollLeft||h&&h.scrollLeft||0)-(g&&g.clientLeft||h&&h.clientLeft||0);a.pageY=a.clientY+(g&&g.scrollTop||h&&h.scrollTop||0)-(g&&g.clientTop||h&&h.clientTop||0)}a.which=a.charCode||a.keyCode;a.button!=k&&(a.button=a.button&1?0:a.button&
4?1:a.button&2?2:0)}return a};t.l=function(a,c){var d=t.sc(a)?t.getData(a):{},e=a.parentNode||a.ownerDocument;"string"===typeof c&&(c={type:c,target:a});c=t.pc(c);d.X&&d.X.call(a,c);if(e&&!c.Ib()&&c.bubbles!==l)t.l(e,c);else if(!e&&!c.defaultPrevented&&(d=t.getData(c.target),c.target[c.type])){d.disabled=f;if("function"===typeof c.target[c.type])c.target[c.type]();d.disabled=l}return!c.defaultPrevented};
t.W=function(a,c,d){function e(){t.o(a,c,e);d.apply(this,arguments)}if(t.h.isArray(c))return u(t.W,a,c,d);e.w=d.w=d.w||t.w++;t.d(a,c,e)};function u(a,c,d,e){t.hc.forEach(d,function(d){a(c,d,e)})}var v=Object.prototype.hasOwnProperty;t.e=function(a,c){var d;c=c||{};d=document.createElement(a||"div");t.h.Y(c,function(a,c){-1!==a.indexOf("aria-")||"role"==a?d.setAttribute(a,c):d[a]=c});return d};t.aa=function(a){return a.charAt(0).toUpperCase()+a.slice(1)};t.h={};
t.h.create=Object.create||function(a){function c(){}c.prototype=a;return new c};t.h.Y=function(a,c,d){for(var e in a)v.call(a,e)&&c.call(d||this,e,a[e])};t.h.z=function(a,c){if(!c)return a;for(var d in c)v.call(c,d)&&(a[d]=c[d]);return a};t.h.md=function(a,c){var d,e,g;a=t.h.copy(a);for(d in c)v.call(c,d)&&(e=a[d],g=c[d],a[d]=t.h.Ta(e)&&t.h.Ta(g)?t.h.md(e,g):c[d]);return a};t.h.copy=function(a){return t.h.z({},a)};
t.h.Ta=function(a){return!!a&&"object"===typeof a&&"[object Object]"===a.toString()&&a.constructor===Object};t.h.isArray=Array.isArray||function(a){return"[object Array]"===Object.prototype.toString.call(a)};t.Ad=function(a){return a!==a};t.bind=function(a,c,d){function e(){return c.apply(a,arguments)}c.w||(c.w=t.w++);e.w=d?d+"_"+c.w:c.w;return e};t.ua={};t.w=1;t.expando="vdata"+(new Date).getTime();t.getData=function(a){var c=a[t.expando];c||(c=a[t.expando]=t.w++,t.ua[c]={});return t.ua[c]};
t.sc=function(a){a=a[t.expando];return!(!a||t.Hb(t.ua[a]))};t.Fc=function(a){var c=a[t.expando];if(c){delete t.ua[c];try{delete a[t.expando]}catch(d){a.removeAttribute?a.removeAttribute(t.expando):a[t.expando]=k}}};t.Hb=function(a){for(var c in a)if(a[c]!==k)return l;return f};t.n=function(a,c){-1==(" "+a.className+" ").indexOf(" "+c+" ")&&(a.className=""===a.className?c:a.className+" "+c)};
t.p=function(a,c){var d,e;if(-1!=a.className.indexOf(c)){d=a.className.split(" ");for(e=d.length-1;0<=e;e--)d[e]===c&&d.splice(e,1);a.className=d.join(" ")}};t.A=t.e("video");t.L=navigator.userAgent;t.Yc=/iPhone/i.test(t.L);t.Xc=/iPad/i.test(t.L);t.Zc=/iPod/i.test(t.L);t.Wc=t.Yc||t.Xc||t.Zc;var aa=t,x;var y=t.L.match(/OS (\d+)_/i);x=y&&y[1]?y[1]:b;aa.ke=x;t.Uc=/Android/i.test(t.L);var ba=t,z;var A=t.L.match(/Android (\d+)(?:\.(\d+))?(?:\.(\d+))*/i),B,C;
A?(B=A[1]&&parseFloat(A[1]),C=A[2]&&parseFloat(A[2]),z=B&&C?parseFloat(A[1]+"."+A[2]):B?B:k):z=k;ba.Tb=z;t.$c=t.Uc&&/webkit/i.test(t.L)&&2.3>t.Tb;t.Vc=/Firefox/i.test(t.L);t.le=/Chrome/i.test(t.L);t.dc=!!("ontouchstart"in window||window.Tc&&document instanceof window.Tc);t.Hc=function(a,c){t.h.Y(c,function(c,e){e===k||"undefined"===typeof e||e===l?a.removeAttribute(c):a.setAttribute(c,e===f?"":e)})};
t.za=function(a){var c,d,e,g;c={};if(a&&a.attributes&&0<a.attributes.length){d=a.attributes;for(var h=d.length-1;0<=h;h--){e=d[h].name;g=d[h].value;if("boolean"===typeof a[e]||-1!==",autoplay,controls,loop,muted,default,".indexOf(","+e+","))g=g!==k?f:l;c[e]=g}}return c};
t.re=function(a,c){var d="";document.defaultView&&document.defaultView.getComputedStyle?d=document.defaultView.getComputedStyle(a,"").getPropertyValue(c):a.currentStyle&&(d=a["client"+c.substr(0,1).toUpperCase()+c.substr(1)]+"px");return d};t.Gb=function(a,c){c.firstChild?c.insertBefore(a,c.firstChild):c.appendChild(a)};t.Oa={};t.v=function(a){0===a.indexOf("#")&&(a=a.slice(1));return document.getElementById(a)};
t.ya=function(a,c){c=c||a;var d=Math.floor(a%60),e=Math.floor(a/60%60),g=Math.floor(a/3600),h=Math.floor(c/60%60),j=Math.floor(c/3600);if(isNaN(a)||Infinity===a)g=e=d="-";g=0<g||0<j?g+":":"";return g+(((g||10<=h)&&10>e?"0"+e:e)+":")+(10>d?"0"+d:d)};t.gd=function(){document.body.focus();document.onselectstart=r(l)};t.ge=function(){document.onselectstart=r(f)};t.trim=function(a){return(a+"").replace(/^\s+|\s+$/g,"")};t.round=function(a,c){c||(c=0);return Math.round(a*Math.pow(10,c))/Math.pow(10,c)};
t.zb=function(a,c){return{length:1,start:function(){return a},end:function(){return c}}};
t.get=function(a,c,d,e){var g,h,j,n;d=d||m();"undefined"===typeof XMLHttpRequest&&(window.XMLHttpRequest=function(){try{return new window.ActiveXObject("Msxml2.XMLHTTP.6.0")}catch(a){}try{return new window.ActiveXObject("Msxml2.XMLHTTP.3.0")}catch(c){}try{return new window.ActiveXObject("Msxml2.XMLHTTP")}catch(d){}throw Error("This browser does not support XMLHttpRequest.");});h=new XMLHttpRequest;j=t.Td(a);n=window.location;j.protocol+j.host!==n.protocol+n.host&&window.XDomainRequest&&!("withCredentials"in
h)?(h=new window.XDomainRequest,h.onload=function(){c(h.responseText)},h.onerror=d,h.onprogress=m(),h.ontimeout=d):(g="file:"==j.protocol||"file:"==n.protocol,h.onreadystatechange=function(){4===h.readyState&&(200===h.status||g&&0===h.status?c(h.responseText):d(h.responseText))});try{h.open("GET",a,f),e&&(h.withCredentials=f)}catch(q){d(q);return}try{h.send()}catch(w){d(w)}};
t.Xd=function(a){try{var c=window.localStorage||l;c&&(c.volume=a)}catch(d){22==d.code||1014==d.code?t.log("LocalStorage Full (VideoJS)",d):18==d.code?t.log("LocalStorage not allowed (VideoJS)",d):t.log("LocalStorage Error (VideoJS)",d)}};t.rc=function(a){a.match(/^https?:\/\//)||(a=t.e("div",{innerHTML:'<a href="'+a+'">x</a>'}).firstChild.href);return a};
t.Td=function(a){var c,d,e,g;g="protocol hostname port pathname search hash host".split(" ");d=t.e("a",{href:a});if(e=""===d.host&&"file:"!==d.protocol)c=t.e("div"),c.innerHTML='<a href="'+a+'"></a>',d=c.firstChild,c.setAttribute("style","display:none; position:absolute;"),document.body.appendChild(c);a={};for(var h=0;h<g.length;h++)a[g[h]]=d[g[h]];e&&document.body.removeChild(c);return a};
function D(a,c){var d,e;d=Array.prototype.slice.call(c);e=m();e=window.console||{log:e,warn:e,error:e};a?d.unshift(a.toUpperCase()+":"):a="log";t.log.history.push(d);d.unshift("VIDEOJS:");if(e[a].apply)e[a].apply(e,d);else e[a](d.join(" "))}t.log=function(){D(k,arguments)};t.log.history=[];t.log.error=function(){D("error",arguments)};t.log.warn=function(){D("warn",arguments)};
t.ud=function(a){var c,d;a.getBoundingClientRect&&a.parentNode&&(c=a.getBoundingClientRect());if(!c)return{left:0,top:0};a=document.documentElement;d=document.body;return{left:t.round(c.left+(window.pageXOffset||d.scrollLeft)-(a.clientLeft||d.clientLeft||0)),top:t.round(c.top+(window.pageYOffset||d.scrollTop)-(a.clientTop||d.clientTop||0))}};t.hc={};t.hc.forEach=function(a,c,d){if(t.h.isArray(a)&&c instanceof Function)for(var e=0,g=a.length;e<g;++e)c.call(d||t,a[e],e,a);return a};t.ga={};
t.ga.Va=function(a,c){var d,e,g;a=t.h.copy(a);for(d in c)c.hasOwnProperty(d)&&(e=a[d],g=c[d],a[d]=t.h.Ta(e)&&t.h.Ta(g)?t.ga.Va(e,g):c[d]);return a};
t.a=t.qa.extend({i:function(a,c,d){this.c=a;this.k=t.h.copy(this.k);c=this.options(c);this.T=c.id||(c.el&&c.el.id?c.el.id:a.id()+"_component_"+t.w++);this.Gd=c.name||k;this.b=c.el||this.e();this.M=[];this.Pa={};this.Qa={};this.uc();this.I(d);if(c.Gc!==l){var e,g;e=t.bind(this.j(),this.j().reportUserActivity);this.d("touchstart",function(){e();clearInterval(g);g=setInterval(e,250)});a=function(){e();clearInterval(g)};this.d("touchmove",e);this.d("touchend",a);this.d("touchcancel",a)}}});s=t.a.prototype;
s.dispose=function(){this.l({type:"dispose",bubbles:l});if(this.M)for(var a=this.M.length-1;0<=a;a--)this.M[a].dispose&&this.M[a].dispose();this.Qa=this.Pa=this.M=k;this.o();this.b.parentNode&&this.b.parentNode.removeChild(this.b);t.Fc(this.b);this.b=k};s.c=f;s.j=p("c");s.options=function(a){return a===b?this.k:this.k=t.ga.Va(this.k,a)};s.e=function(a,c){return t.e(a,c)};s.s=function(a){var c=this.c.language(),d=this.c.languages();return d&&d[c]&&d[c][a]?d[c][a]:a};s.v=p("b");
s.ja=function(){return this.u||this.b};s.id=p("T");s.name=p("Gd");s.children=p("M");s.wd=function(a){return this.Pa[a]};s.ka=function(a){return this.Qa[a]};s.Q=function(a,c){var d,e;"string"===typeof a?(e=a,c=c||{},d=c.componentClass||t.aa(e),c.name=e,d=new window.videojs[d](this.c||this,c)):d=a;this.M.push(d);"function"===typeof d.id&&(this.Pa[d.id()]=d);(e=e||d.name&&d.name())&&(this.Qa[e]=d);"function"===typeof d.el&&d.el()&&this.ja().appendChild(d.el());return d};
s.removeChild=function(a){"string"===typeof a&&(a=this.ka(a));if(a&&this.M){for(var c=l,d=this.M.length-1;0<=d;d--)if(this.M[d]===a){c=f;this.M.splice(d,1);break}c&&(this.Pa[a.id]=k,this.Qa[a.name]=k,(c=a.v())&&c.parentNode===this.ja()&&this.ja().removeChild(a.v()))}};s.uc=function(){var a,c,d,e;a=this;if(c=this.options().children)if(t.h.isArray(c))for(var g=0;g<c.length;g++)d=c[g],"string"==typeof d?(e=d,d={}):e=d.name,a[e]=a.Q(e,d);else t.h.Y(c,function(c,d){d!==l&&(a[c]=a.Q(c,d))})};s.S=r("");
s.d=function(a,c){t.d(this.b,a,t.bind(this,c));return this};s.o=function(a,c){t.o(this.b,a,c);return this};s.W=function(a,c){t.W(this.b,a,t.bind(this,c));return this};s.l=function(a){t.l(this.b,a);return this};s.I=function(a){a&&(this.la?a.call(this):(this.ab===b&&(this.ab=[]),this.ab.push(a)));return this};s.Fa=function(){this.la=f;var a=this.ab;if(a&&0<a.length){for(var c=0,d=a.length;c<d;c++)a[c].call(this);this.ab=[];this.l("ready")}};s.n=function(a){t.n(this.b,a);return this};
s.p=function(a){t.p(this.b,a);return this};s.show=function(){this.b.style.display="block";return this};s.V=function(){this.b.style.display="none";return this};function E(a){a.p("vjs-lock-showing")}s.disable=function(){this.V();this.show=m()};s.width=function(a,c){return F(this,"width",a,c)};s.height=function(a,c){return F(this,"height",a,c)};s.pd=function(a,c){return this.width(a,f).height(c)};
function F(a,c,d,e){if(d!==b){if(d===k||t.Ad(d))d=0;a.b.style[c]=-1!==(""+d).indexOf("%")||-1!==(""+d).indexOf("px")?d:"auto"===d?"":d+"px";e||a.l("resize");return a}if(!a.b)return 0;d=a.b.style[c];e=d.indexOf("px");return-1!==e?parseInt(d.slice(0,e),10):parseInt(a.b["offset"+t.aa(c)],10)}
function G(a){var c,d,e,g,h,j,n,q;c=0;d=k;a.d("touchstart",function(a){1===a.touches.length&&(d=a.touches[0],c=(new Date).getTime(),g=f)});a.d("touchmove",function(a){1<a.touches.length?g=l:d&&(j=a.touches[0].pageX-d.pageX,n=a.touches[0].pageY-d.pageY,q=Math.sqrt(j*j+n*n),22<q&&(g=l))});h=function(){g=l};a.d("touchleave",h);a.d("touchcancel",h);a.d("touchend",function(a){d=k;g===f&&(e=(new Date).getTime()-c,250>e&&(a.preventDefault(),this.l("tap")))})}
t.t=t.a.extend({i:function(a,c){t.a.call(this,a,c);G(this);this.d("tap",this.r);this.d("click",this.r);this.d("focus",this.Ya);this.d("blur",this.Xa)}});s=t.t.prototype;
s.e=function(a,c){var d;c=t.h.z({className:this.S(),role:"button","aria-live":"polite",tabIndex:0},c);d=t.a.prototype.e.call(this,a,c);c.innerHTML||(this.u=t.e("div",{className:"vjs-control-content"}),this.xb=t.e("span",{className:"vjs-control-text",innerHTML:this.s(this.ta)||"Need Text"}),this.u.appendChild(this.xb),d.appendChild(this.u));return d};s.S=function(){return"vjs-control "+t.a.prototype.S.call(this)};s.r=m();s.Ya=function(){t.d(document,"keydown",t.bind(this,this.da))};
s.da=function(a){if(32==a.which||13==a.which)a.preventDefault(),this.r()};s.Xa=function(){t.o(document,"keydown",t.bind(this,this.da))};
t.P=t.a.extend({i:function(a,c){t.a.call(this,a,c);this.fd=this.ka(this.k.barName);this.handle=this.ka(this.k.handleName);this.d("mousedown",this.Za);this.d("touchstart",this.Za);this.d("focus",this.Ya);this.d("blur",this.Xa);this.d("click",this.r);this.c.d("controlsvisible",t.bind(this,this.update));a.d(this.Bc,t.bind(this,this.update));this.R={};this.R.move=t.bind(this,this.$a);this.R.end=t.bind(this,this.Lb)}});s=t.P.prototype;
s.e=function(a,c){c=c||{};c.className+=" vjs-slider";c=t.h.z({role:"slider","aria-valuenow":0,"aria-valuemin":0,"aria-valuemax":100,tabIndex:0},c);return t.a.prototype.e.call(this,a,c)};s.Za=function(a){a.preventDefault();t.gd();this.n("vjs-sliding");t.d(document,"mousemove",this.R.move);t.d(document,"mouseup",this.R.end);t.d(document,"touchmove",this.R.move);t.d(document,"touchend",this.R.end);this.$a(a)};s.$a=m();
s.Lb=function(){t.ge();this.p("vjs-sliding");t.o(document,"mousemove",this.R.move,l);t.o(document,"mouseup",this.R.end,l);t.o(document,"touchmove",this.R.move,l);t.o(document,"touchend",this.R.end,l);this.update()};s.update=function(){if(this.b){var a,c=this.Fb(),d=this.handle,e=this.fd;isNaN(c)&&(c=0);a=c;if(d){a=this.b.offsetWidth;var g=d.v().offsetWidth;a=g?g/a:0;c*=1-a;a=c+a/2;d.v().style.left=t.round(100*c,2)+"%"}e&&(e.v().style.width=t.round(100*a,2)+"%")}};
function H(a,c){var d,e,g,h;d=a.b;e=t.ud(d);h=g=d.offsetWidth;d=a.handle;if(a.options().vertical)return h=e.top,e=c.changedTouches?c.changedTouches[0].pageY:c.pageY,d&&(d=d.v().offsetHeight,h+=d/2,g-=d),Math.max(0,Math.min(1,(h-e+g)/g));g=e.left;e=c.changedTouches?c.changedTouches[0].pageX:c.pageX;d&&(d=d.v().offsetWidth,g+=d/2,h-=d);return Math.max(0,Math.min(1,(e-g)/h))}s.Ya=function(){t.d(document,"keyup",t.bind(this,this.da))};
s.da=function(a){if(37==a.which||40==a.which)a.preventDefault(),this.Kc();else if(38==a.which||39==a.which)a.preventDefault(),this.Lc()};s.Xa=function(){t.o(document,"keyup",t.bind(this,this.da))};s.r=function(a){a.stopImmediatePropagation();a.preventDefault()};t.Z=t.a.extend();t.Z.prototype.defaultValue=0;
t.Z.prototype.e=function(a,c){c=c||{};c.className+=" vjs-slider-handle";c=t.h.z({innerHTML:'<span class="vjs-control-text">'+this.defaultValue+"</span>"},c);return t.a.prototype.e.call(this,"div",c)};t.ha=t.a.extend();function ca(a,c){a.Q(c);c.d("click",t.bind(a,function(){E(this)}))}
t.ha.prototype.e=function(){var a=this.options().kc||"ul";this.u=t.e(a,{className:"vjs-menu-content"});a=t.a.prototype.e.call(this,"div",{append:this.u,className:"vjs-menu"});a.appendChild(this.u);t.d(a,"click",function(a){a.preventDefault();a.stopImmediatePropagation()});return a};t.H=t.t.extend({i:function(a,c){t.t.call(this,a,c);this.selected(c.selected)}});t.H.prototype.e=function(a,c){return t.t.prototype.e.call(this,"li",t.h.z({className:"vjs-menu-item",innerHTML:this.k.label},c))};
t.H.prototype.r=function(){this.selected(f)};t.H.prototype.selected=function(a){a?(this.n("vjs-selected"),this.b.setAttribute("aria-selected",f)):(this.p("vjs-selected"),this.b.setAttribute("aria-selected",l))};t.K=t.t.extend({i:function(a,c){t.t.call(this,a,c);this.Aa=this.wa();this.Q(this.Aa);this.N&&0===this.N.length&&this.V();this.d("keyup",this.da);this.b.setAttribute("aria-haspopup",f);this.b.setAttribute("role","button")}});s=t.K.prototype;s.sa=l;
s.wa=function(){var a=new t.ha(this.c);this.options().title&&a.ja().appendChild(t.e("li",{className:"vjs-menu-title",innerHTML:t.aa(this.options().title),de:-1}));if(this.N=this.createItems())for(var c=0;c<this.N.length;c++)ca(a,this.N[c]);return a};s.va=m();s.S=function(){return this.className+" vjs-menu-button "+t.t.prototype.S.call(this)};s.Ya=m();s.Xa=m();s.r=function(){this.W("mouseout",t.bind(this,function(){E(this.Aa);this.b.blur()}));this.sa?I(this):J(this)};
s.da=function(a){a.preventDefault();32==a.which||13==a.which?this.sa?I(this):J(this):27==a.which&&this.sa&&I(this)};function J(a){a.sa=f;a.Aa.n("vjs-lock-showing");a.b.setAttribute("aria-pressed",f);a.N&&0<a.N.length&&a.N[0].v().focus()}function I(a){a.sa=l;E(a.Aa);a.b.setAttribute("aria-pressed",l)}t.D=function(a){"number"===typeof a?this.code=a:"string"===typeof a?this.message=a:"object"===typeof a&&t.h.z(this,a);this.message||(this.message=t.D.nd[this.code]||"")};t.D.prototype.code=0;
t.D.prototype.message="";t.D.prototype.status=k;t.D.Sa="MEDIA_ERR_CUSTOM MEDIA_ERR_ABORTED MEDIA_ERR_NETWORK MEDIA_ERR_DECODE MEDIA_ERR_SRC_NOT_SUPPORTED MEDIA_ERR_ENCRYPTED".split(" ");
t.D.nd={1:"You aborted the video playback",2:"A network error caused the video download to fail part-way.",3:"The video playback was aborted due to a corruption problem or because the video used features your browser did not support.",4:"The video could not be loaded, either because the server or network failed or because the format is not supported.",5:"The video is encrypted and we do not have the keys to decrypt it."};for(var K=0;K<t.D.Sa.length;K++)t.D[t.D.Sa[K]]=K,t.D.prototype[t.D.Sa[K]]=K;
var L,M,N,O;
L=["requestFullscreen exitFullscreen fullscreenElement fullscreenEnabled fullscreenchange fullscreenerror".split(" "),"webkitRequestFullscreen webkitExitFullscreen webkitFullscreenElement webkitFullscreenEnabled webkitfullscreenchange webkitfullscreenerror".split(" "),"webkitRequestFullScreen webkitCancelFullScreen webkitCurrentFullScreenElement webkitCancelFullScreen webkitfullscreenchange webkitfullscreenerror".split(" "),"mozRequestFullScreen mozCancelFullScreen mozFullScreenElement mozFullScreenEnabled mozfullscreenchange mozfullscreenerror".split(" "),"msRequestFullscreen msExitFullscreen msFullscreenElement msFullscreenEnabled MSFullscreenChange MSFullscreenError".split(" ")];
M=L[0];for(O=0;O<L.length;O++)if(L[O][1]in document){N=L[O];break}if(N){t.Oa.Eb={};for(O=0;O<N.length;O++)t.Oa.Eb[M[O]]=N[O]}
t.Player=t.a.extend({i:function(a,c,d){this.O=a;a.id=a.id||"vjs_video_"+t.w++;this.ee=a&&t.za(a);c=t.h.z(da(a),c);this.Ua=c.language||t.options.language;this.Ed=c.languages||t.options.languages;this.F={};this.Cc=c.poster;this.yb=c.controls;a.controls=l;c.Gc=l;t.a.call(this,this,c,d);this.controls()?this.n("vjs-controls-enabled"):this.n("vjs-controls-disabled");t.Ba[this.T]=this;c.plugins&&t.h.Y(c.plugins,function(a,c){this[a](c)},this);var e,g,h,j,n,q;e=t.bind(this,this.reportUserActivity);this.d("mousedown",
function(){e();clearInterval(g);g=setInterval(e,250)});this.d("mousemove",function(a){if(a.screenX!=n||a.screenY!=q)n=a.screenX,q=a.screenY,e()});this.d("mouseup",function(){e();clearInterval(g)});this.d("keydown",e);this.d("keyup",e);h=setInterval(t.bind(this,function(){if(this.pa){this.pa=l;this.userActive(f);clearTimeout(j);var a=this.options().inactivityTimeout;0<a&&(j=setTimeout(t.bind(this,function(){this.pa||this.userActive(l)}),a))}}),250);this.d("dispose",function(){clearInterval(h);clearTimeout(j)})}});
s=t.Player.prototype;s.language=function(a){if(a===b)return this.Ua;this.Ua=a;return this};s.languages=p("Ed");s.k=t.options;s.dispose=function(){this.l("dispose");this.o("dispose");t.Ba[this.T]=k;this.O&&this.O.player&&(this.O.player=k);this.b&&this.b.player&&(this.b.player=k);this.m&&this.m.dispose();t.a.prototype.dispose.call(this)};
function da(a){var c={sources:[],tracks:[]};t.h.z(c,t.za(a));if(a.hasChildNodes()){var d,e,g,h;a=a.childNodes;g=0;for(h=a.length;g<h;g++)d=a[g],e=d.nodeName.toLowerCase(),"source"===e?c.sources.push(t.za(d)):"track"===e&&c.tracks.push(t.za(d))}return c}
s.e=function(){var a=this.b=t.a.prototype.e.call(this,"div"),c=this.O,d;c.removeAttribute("width");c.removeAttribute("height");if(c.hasChildNodes()){var e,g,h,j,n;e=c.childNodes;g=e.length;for(n=[];g--;)h=e[g],j=h.nodeName.toLowerCase(),"track"===j&&n.push(h);for(e=0;e<n.length;e++)c.removeChild(n[e])}d=t.za(c);t.h.Y(d,function(c){a.setAttribute(c,d[c])});c.id+="_html5_api";c.className="vjs-tech";c.player=a.player=this;this.n("vjs-paused");this.width(this.k.width,f);this.height(this.k.height,f);c.parentNode&&
c.parentNode.insertBefore(a,c);t.Gb(c,a);this.b=a;this.d("loadstart",this.Ld);this.d("waiting",this.Rd);this.d(["canplay","canplaythrough","playing","ended"],this.Qd);this.d("seeking",this.Od);this.d("seeked",this.Nd);this.d("ended",this.Hd);this.d("play",this.Nb);this.d("firstplay",this.Jd);this.d("pause",this.Mb);this.d("progress",this.Md);this.d("durationchange",this.zc);this.d("fullscreenchange",this.Kd);return a};
function P(a,c,d){a.m&&(a.la=l,a.m.dispose(),a.m=l);"Html5"!==c&&a.O&&(t.g.Bb(a.O),a.O=k);a.eb=c;a.la=l;var e=t.h.z({source:d,parentEl:a.b},a.k[c.toLowerCase()]);d&&(a.mc=d.type,d.src==a.F.src&&0<a.F.currentTime&&(e.startTime=a.F.currentTime),a.F.src=d.src);a.m=new window.videojs[c](a,e);a.m.I(function(){this.c.Fa()})}s.Ld=function(){this.error(k);this.paused()?(Q(this,l),this.W("play",function(){Q(this,f)})):this.l("firstplay")};s.tc=l;
function Q(a,c){c!==b&&a.tc!==c&&((a.tc=c)?(a.n("vjs-has-started"),a.l("firstplay")):a.p("vjs-has-started"))}s.Nb=function(){this.p("vjs-paused");this.n("vjs-playing")};s.Rd=function(){this.n("vjs-waiting")};s.Qd=function(){this.p("vjs-waiting")};s.Od=function(){this.n("vjs-seeking")};s.Nd=function(){this.p("vjs-seeking")};s.Jd=function(){this.k.starttime&&this.currentTime(this.k.starttime);this.n("vjs-has-started")};s.Mb=function(){this.p("vjs-playing");this.n("vjs-paused")};
s.Md=function(){1==this.bufferedPercent()&&this.l("loadedalldata")};s.Hd=function(){this.k.loop&&(this.currentTime(0),this.play())};s.zc=function(){var a=R(this,"duration");a&&(0>a&&(a=Infinity),this.duration(a),Infinity===a?this.n("vjs-live"):this.p("vjs-live"))};s.Kd=function(){this.isFullscreen()?this.n("vjs-fullscreen"):this.p("vjs-fullscreen")};function S(a,c,d){if(a.m&&!a.m.la)a.m.I(function(){this[c](d)});else try{a.m[c](d)}catch(e){throw t.log(e),e;}}
function R(a,c){if(a.m&&a.m.la)try{return a.m[c]()}catch(d){throw a.m[c]===b?t.log("Video.js: "+c+" method not defined for "+a.eb+" playback technology.",d):"TypeError"==d.name?(t.log("Video.js: "+c+" unavailable on "+a.eb+" playback technology element.",d),a.m.la=l):t.log(d),d;}}s.play=function(){S(this,"play");return this};s.pause=function(){S(this,"pause");return this};s.paused=function(){return R(this,"paused")===l?l:f};
s.currentTime=function(a){return a!==b?(S(this,"setCurrentTime",a),this):this.F.currentTime=R(this,"currentTime")||0};s.duration=function(a){if(a!==b)return this.F.duration=parseFloat(a),this;this.F.duration===b&&this.zc();return this.F.duration||0};s.remainingTime=function(){return this.duration()-this.currentTime()};s.buffered=function(){var a=R(this,"buffered");if(!a||!a.length)a=t.zb(0,0);return a};
s.bufferedPercent=function(){var a=this.duration(),c=this.buffered(),d=0,e,g;if(!a)return 0;for(var h=0;h<c.length;h++)e=c.start(h),g=c.end(h),g>a&&(g=a),d+=g-e;return d/a};s.volume=function(a){if(a!==b)return a=Math.max(0,Math.min(1,parseFloat(a))),this.F.volume=a,S(this,"setVolume",a),t.Xd(a),this;a=parseFloat(R(this,"volume"));return isNaN(a)?1:a};s.muted=function(a){return a!==b?(S(this,"setMuted",a),this):R(this,"muted")||l};s.Da=function(){return R(this,"supportsFullScreen")||l};s.vc=l;
s.isFullscreen=function(a){return a!==b?(this.vc=!!a,this):this.vc};s.requestFullscreen=function(){var a=t.Oa.Eb;this.isFullscreen(f);a?(t.d(document,a.fullscreenchange,t.bind(this,function(c){this.isFullscreen(document[a.fullscreenElement]);this.isFullscreen()===l&&t.o(document,a.fullscreenchange,arguments.callee);this.l("fullscreenchange")})),this.b[a.requestFullscreen]()):this.m.Da()?S(this,"enterFullScreen"):(this.oc(),this.l("fullscreenchange"));return this};
s.exitFullscreen=function(){var a=t.Oa.Eb;this.isFullscreen(l);if(a)document[a.exitFullscreen]();else this.m.Da()?S(this,"exitFullScreen"):(this.Cb(),this.l("fullscreenchange"));return this};s.oc=function(){this.zd=f;this.qd=document.documentElement.style.overflow;t.d(document,"keydown",t.bind(this,this.qc));document.documentElement.style.overflow="hidden";t.n(document.body,"vjs-full-window");this.l("enterFullWindow")};
s.qc=function(a){27===a.keyCode&&(this.isFullscreen()===f?this.exitFullscreen():this.Cb())};s.Cb=function(){this.zd=l;t.o(document,"keydown",this.qc);document.documentElement.style.overflow=this.qd;t.p(document.body,"vjs-full-window");this.l("exitFullWindow")};
s.selectSource=function(a){for(var c=0,d=this.k.techOrder;c<d.length;c++){var e=t.aa(d[c]),g=window.videojs[e];if(g){if(g.isSupported())for(var h=0,j=a;h<j.length;h++){var n=j[h];if(g.canPlaySource(n))return{source:n,m:e}}}else t.log.error('The "'+e+'" tech is undefined. Skipped browser support check for that tech.')}return l};
s.src=function(a){if(a===b)return R(this,"src");t.h.isArray(a)?T(this,a):"string"===typeof a?this.src({src:a}):a instanceof Object&&(a.type&&!window.videojs[this.eb].canPlaySource(a)?T(this,[a]):(this.F.src=a.src,this.mc=a.type||"",this.I(function(){S(this,"src",a.src);"auto"==this.k.preload&&this.load();this.k.autoplay&&this.play()})));return this};
function T(a,c){var d=a.selectSource(c),e;d?d.m===a.eb?a.src(d.source):P(a,d.m,d.source):(e=setTimeout(t.bind(a,function(){this.error({code:4,message:this.s(this.options().notSupportedMessage)})}),0),a.Fa(),a.d("dispose",function(){clearTimeout(e)}))}s.load=function(){S(this,"load");return this};s.currentSrc=function(){return R(this,"currentSrc")||this.F.src||""};s.Ra=function(){return this.mc||""};s.Ca=function(a){return a!==b?(S(this,"setPreload",a),this.k.preload=a,this):R(this,"preload")};
s.autoplay=function(a){return a!==b?(S(this,"setAutoplay",a),this.k.autoplay=a,this):R(this,"autoplay")};s.loop=function(a){return a!==b?(S(this,"setLoop",a),this.k.loop=a,this):R(this,"loop")};s.poster=function(a){if(a===b)return this.Cc;this.Cc=a;S(this,"setPoster",a);this.l("posterchange")};
s.controls=function(a){return a!==b?(a=!!a,this.yb!==a&&((this.yb=a)?(this.p("vjs-controls-disabled"),this.n("vjs-controls-enabled"),this.l("controlsenabled")):(this.p("vjs-controls-enabled"),this.n("vjs-controls-disabled"),this.l("controlsdisabled"))),this):this.yb};t.Player.prototype.Sb;s=t.Player.prototype;
s.usingNativeControls=function(a){return a!==b?(a=!!a,this.Sb!==a&&((this.Sb=a)?(this.n("vjs-using-native-controls"),this.l("usingnativecontrols")):(this.p("vjs-using-native-controls"),this.l("usingcustomcontrols"))),this):this.Sb};s.ca=k;s.error=function(a){if(a===b)return this.ca;if(a===k)return this.ca=a,this.p("vjs-error"),this;this.ca=a instanceof t.D?a:new t.D(a);this.l("error");this.n("vjs-error");t.log.error("(CODE:"+this.ca.code+" "+t.D.Sa[this.ca.code]+")",this.ca.message,this.ca);return this};
s.ended=function(){return R(this,"ended")};s.seeking=function(){return R(this,"seeking")};s.pa=f;s.reportUserActivity=function(){this.pa=f};s.Rb=f;s.userActive=function(a){return a!==b?(a=!!a,a!==this.Rb&&((this.Rb=a)?(this.pa=f,this.p("vjs-user-inactive"),this.n("vjs-user-active"),this.l("useractive")):(this.pa=l,this.m&&this.m.W("mousemove",function(a){a.stopPropagation();a.preventDefault()}),this.p("vjs-user-active"),this.n("vjs-user-inactive"),this.l("userinactive"))),this):this.Rb};
s.playbackRate=function(a){return a!==b?(S(this,"setPlaybackRate",a),this):this.m&&this.m.featuresPlaybackRate?R(this,"playbackRate"):1};t.Ia=t.a.extend();t.Ia.prototype.k={se:"play",children:{playToggle:{},currentTimeDisplay:{},timeDivider:{},durationDisplay:{},remainingTimeDisplay:{},liveDisplay:{},progressControl:{},fullscreenToggle:{},volumeControl:{},muteToggle:{},playbackRateMenuButton:{}}};t.Ia.prototype.e=function(){return t.e("div",{className:"vjs-control-bar"})};
t.Xb=t.a.extend({i:function(a,c){t.a.call(this,a,c)}});t.Xb.prototype.e=function(){var a=t.a.prototype.e.call(this,"div",{className:"vjs-live-controls vjs-control"});this.u=t.e("div",{className:"vjs-live-display",innerHTML:'<span class="vjs-control-text">'+this.s("Stream Type")+"</span>"+this.s("LIVE"),"aria-live":"off"});a.appendChild(this.u);return a};t.$b=t.t.extend({i:function(a,c){t.t.call(this,a,c);a.d("play",t.bind(this,this.Nb));a.d("pause",t.bind(this,this.Mb))}});s=t.$b.prototype;s.ta="Play";
s.S=function(){return"vjs-play-control "+t.t.prototype.S.call(this)};s.r=function(){this.c.paused()?this.c.play():this.c.pause()};s.Nb=function(){t.p(this.b,"vjs-paused");t.n(this.b,"vjs-playing");this.b.children[0].children[0].innerHTML=this.s("Pause")};s.Mb=function(){t.p(this.b,"vjs-playing");t.n(this.b,"vjs-paused");this.b.children[0].children[0].innerHTML=this.s("Play")};t.hb=t.a.extend({i:function(a,c){t.a.call(this,a,c);a.d("timeupdate",t.bind(this,this.fa))}});
t.hb.prototype.e=function(){var a=t.a.prototype.e.call(this,"div",{className:"vjs-current-time vjs-time-controls vjs-control"});this.u=t.e("div",{className:"vjs-current-time-display",innerHTML:'<span class="vjs-control-text">Current Time </span>0:00',"aria-live":"off"});a.appendChild(this.u);return a};t.hb.prototype.fa=function(){var a=this.c.bb?this.c.F.currentTime:this.c.currentTime();this.u.innerHTML='<span class="vjs-control-text">'+this.s("Current Time")+"</span> "+t.ya(a,this.c.duration())};
t.ib=t.a.extend({i:function(a,c){t.a.call(this,a,c);a.d("timeupdate",t.bind(this,this.fa))}});t.ib.prototype.e=function(){var a=t.a.prototype.e.call(this,"div",{className:"vjs-duration vjs-time-controls vjs-control"});this.u=t.e("div",{className:"vjs-duration-display",innerHTML:'<span class="vjs-control-text">'+this.s("Duration Time")+"</span> 0:00","aria-live":"off"});a.appendChild(this.u);return a};
t.ib.prototype.fa=function(){var a=this.c.duration();a&&(this.u.innerHTML='<span class="vjs-control-text">'+this.s("Duration Time")+"</span> "+t.ya(a))};t.fc=t.a.extend({i:function(a,c){t.a.call(this,a,c)}});t.fc.prototype.e=function(){return t.a.prototype.e.call(this,"div",{className:"vjs-time-divider",innerHTML:"<div><span>/</span></div>"})};t.pb=t.a.extend({i:function(a,c){t.a.call(this,a,c);a.d("timeupdate",t.bind(this,this.fa))}});
t.pb.prototype.e=function(){var a=t.a.prototype.e.call(this,"div",{className:"vjs-remaining-time vjs-time-controls vjs-control"});this.u=t.e("div",{className:"vjs-remaining-time-display",innerHTML:'<span class="vjs-control-text">'+this.s("Remaining Time")+"</span> -0:00","aria-live":"off"});a.appendChild(this.u);return a};t.pb.prototype.fa=function(){this.c.duration()&&(this.u.innerHTML='<span class="vjs-control-text">'+this.s("Remaining Time")+"</span> -"+t.ya(this.c.remainingTime()))};
t.Ja=t.t.extend({i:function(a,c){t.t.call(this,a,c)}});t.Ja.prototype.ta="Fullscreen";t.Ja.prototype.S=function(){return"vjs-fullscreen-control "+t.t.prototype.S.call(this)};t.Ja.prototype.r=function(){this.c.isFullscreen()?(this.c.exitFullscreen(),this.xb.innerHTML=this.s("Fullscreen")):(this.c.requestFullscreen(),this.xb.innerHTML=this.s("Non-Fullscreen"))};t.ob=t.a.extend({i:function(a,c){t.a.call(this,a,c)}});t.ob.prototype.k={children:{seekBar:{}}};
t.ob.prototype.e=function(){return t.a.prototype.e.call(this,"div",{className:"vjs-progress-control vjs-control"})};t.bc=t.P.extend({i:function(a,c){t.P.call(this,a,c);a.d("timeupdate",t.bind(this,this.oa));a.I(t.bind(this,this.oa))}});s=t.bc.prototype;s.k={children:{loadProgressBar:{},playProgressBar:{},seekHandle:{}},barName:"playProgressBar",handleName:"seekHandle"};s.Bc="timeupdate";s.e=function(){return t.P.prototype.e.call(this,"div",{className:"vjs-progress-holder","aria-label":"video progress bar"})};
s.oa=function(){var a=this.c.bb?this.c.F.currentTime:this.c.currentTime();this.b.setAttribute("aria-valuenow",t.round(100*this.Fb(),2));this.b.setAttribute("aria-valuetext",t.ya(a,this.c.duration()))};s.Fb=function(){return this.c.currentTime()/this.c.duration()};s.Za=function(a){t.P.prototype.Za.call(this,a);this.c.bb=f;this.ie=!this.c.paused();this.c.pause()};s.$a=function(a){a=H(this,a)*this.c.duration();a==this.c.duration()&&(a-=0.1);this.c.currentTime(a)};
s.Lb=function(a){t.P.prototype.Lb.call(this,a);this.c.bb=l;this.ie&&this.c.play()};s.Lc=function(){this.c.currentTime(this.c.currentTime()+5)};s.Kc=function(){this.c.currentTime(this.c.currentTime()-5)};t.lb=t.a.extend({i:function(a,c){t.a.call(this,a,c);a.d("progress",t.bind(this,this.update))}});t.lb.prototype.e=function(){return t.a.prototype.e.call(this,"div",{className:"vjs-load-progress",innerHTML:'<span class="vjs-control-text"><span>'+this.s("Loaded")+"</span>: 0%</span>"})};
t.lb.prototype.update=function(){var a,c,d,e,g=this.c.buffered();a=this.c.duration();var h,j=this.c;h=j.buffered();j=j.duration();h=h.end(h.length-1);h>j&&(h=j);j=this.b.children;this.b.style.width=100*(h/a||0)+"%";for(a=0;a<g.length;a++)c=g.start(a),d=g.end(a),(e=j[a])||(e=this.b.appendChild(t.e())),e.style.left=100*(c/h||0)+"%",e.style.width=100*((d-c)/h||0)+"%";for(a=j.length;a>g.length;a--)this.b.removeChild(j[a-1])};t.Zb=t.a.extend({i:function(a,c){t.a.call(this,a,c)}});
t.Zb.prototype.e=function(){return t.a.prototype.e.call(this,"div",{className:"vjs-play-progress",innerHTML:'<span class="vjs-control-text"><span>'+this.s("Progress")+"</span>: 0%</span>"})};t.La=t.Z.extend({i:function(a,c){t.Z.call(this,a,c);a.d("timeupdate",t.bind(this,this.fa))}});t.La.prototype.defaultValue="00:00";t.La.prototype.e=function(){return t.Z.prototype.e.call(this,"div",{className:"vjs-seek-handle","aria-live":"off"})};
t.La.prototype.fa=function(){var a=this.c.bb?this.c.F.currentTime:this.c.currentTime();this.b.innerHTML='<span class="vjs-control-text">'+t.ya(a,this.c.duration())+"</span>"};t.rb=t.a.extend({i:function(a,c){t.a.call(this,a,c);a.m&&a.m.featuresVolumeControl===l&&this.n("vjs-hidden");a.d("loadstart",t.bind(this,function(){a.m.featuresVolumeControl===l?this.n("vjs-hidden"):this.p("vjs-hidden")}))}});t.rb.prototype.k={children:{volumeBar:{}}};
t.rb.prototype.e=function(){return t.a.prototype.e.call(this,"div",{className:"vjs-volume-control vjs-control"})};t.qb=t.P.extend({i:function(a,c){t.P.call(this,a,c);a.d("volumechange",t.bind(this,this.oa));a.I(t.bind(this,this.oa))}});s=t.qb.prototype;s.oa=function(){this.b.setAttribute("aria-valuenow",t.round(100*this.c.volume(),2));this.b.setAttribute("aria-valuetext",t.round(100*this.c.volume(),2)+"%")};s.k={children:{volumeLevel:{},volumeHandle:{}},barName:"volumeLevel",handleName:"volumeHandle"};
s.Bc="volumechange";s.e=function(){return t.P.prototype.e.call(this,"div",{className:"vjs-volume-bar","aria-label":"volume level"})};s.$a=function(a){this.c.muted()&&this.c.muted(l);this.c.volume(H(this,a))};s.Fb=function(){return this.c.muted()?0:this.c.volume()};s.Lc=function(){this.c.volume(this.c.volume()+0.1)};s.Kc=function(){this.c.volume(this.c.volume()-0.1)};t.gc=t.a.extend({i:function(a,c){t.a.call(this,a,c)}});
t.gc.prototype.e=function(){return t.a.prototype.e.call(this,"div",{className:"vjs-volume-level",innerHTML:'<span class="vjs-control-text"></span>'})};t.sb=t.Z.extend();t.sb.prototype.defaultValue="00:00";t.sb.prototype.e=function(){return t.Z.prototype.e.call(this,"div",{className:"vjs-volume-handle"})};
t.ia=t.t.extend({i:function(a,c){t.t.call(this,a,c);a.d("volumechange",t.bind(this,this.update));a.m&&a.m.featuresVolumeControl===l&&this.n("vjs-hidden");a.d("loadstart",t.bind(this,function(){a.m.featuresVolumeControl===l?this.n("vjs-hidden"):this.p("vjs-hidden")}))}});t.ia.prototype.e=function(){return t.t.prototype.e.call(this,"div",{className:"vjs-mute-control vjs-control",innerHTML:'<div><span class="vjs-control-text">'+this.s("Mute")+"</span></div>"})};
t.ia.prototype.r=function(){this.c.muted(this.c.muted()?l:f)};t.ia.prototype.update=function(){var a=this.c.volume(),c=3;0===a||this.c.muted()?c=0:0.33>a?c=1:0.67>a&&(c=2);this.c.muted()?this.b.children[0].children[0].innerHTML!=this.s("Unmute")&&(this.b.children[0].children[0].innerHTML=this.s("Unmute")):this.b.children[0].children[0].innerHTML!=this.s("Mute")&&(this.b.children[0].children[0].innerHTML=this.s("Mute"));for(a=0;4>a;a++)t.p(this.b,"vjs-vol-"+a);t.n(this.b,"vjs-vol-"+c)};
t.ra=t.K.extend({i:function(a,c){t.K.call(this,a,c);a.d("volumechange",t.bind(this,this.update));a.m&&a.m.featuresVolumeControl===l&&this.n("vjs-hidden");a.d("loadstart",t.bind(this,function(){a.m.featuresVolumeControl===l?this.n("vjs-hidden"):this.p("vjs-hidden")}));this.n("vjs-menu-button")}});t.ra.prototype.wa=function(){var a=new t.ha(this.c,{kc:"div"}),c=new t.qb(this.c,t.h.z({vertical:f},this.k.we));a.Q(c);return a};t.ra.prototype.r=function(){t.ia.prototype.r.call(this);t.K.prototype.r.call(this)};
t.ra.prototype.e=function(){return t.t.prototype.e.call(this,"div",{className:"vjs-volume-menu-button vjs-menu-button vjs-control",innerHTML:'<div><span class="vjs-control-text">'+this.s("Mute")+"</span></div>"})};t.ra.prototype.update=t.ia.prototype.update;t.ac=t.K.extend({i:function(a,c){t.K.call(this,a,c);this.Qc();this.Pc();a.d("loadstart",t.bind(this,this.Qc));a.d("ratechange",t.bind(this,this.Pc))}});s=t.ac.prototype;
s.e=function(){var a=t.a.prototype.e.call(this,"div",{className:"vjs-playback-rate vjs-menu-button vjs-control",innerHTML:'<div class="vjs-control-content"><span class="vjs-control-text">'+this.s("Playback Rate")+"</span></div>"});this.xc=t.e("div",{className:"vjs-playback-rate-value",innerHTML:1});a.appendChild(this.xc);return a};s.wa=function(){var a=new t.ha(this.j()),c=this.j().options().playbackRates;if(c)for(var d=c.length-1;0<=d;d--)a.Q(new t.nb(this.j(),{rate:c[d]+"x"}));return a};
s.oa=function(){this.v().setAttribute("aria-valuenow",this.j().playbackRate())};s.r=function(){for(var a=this.j().playbackRate(),c=this.j().options().playbackRates,d=c[0],e=0;e<c.length;e++)if(c[e]>a){d=c[e];break}this.j().playbackRate(d)};function U(a){return a.j().m&&a.j().m.featuresPlaybackRate&&a.j().options().playbackRates&&0<a.j().options().playbackRates.length}s.Qc=function(){U(this)?this.p("vjs-hidden"):this.n("vjs-hidden")};
s.Pc=function(){U(this)&&(this.xc.innerHTML=this.j().playbackRate()+"x")};t.nb=t.H.extend({kc:"button",i:function(a,c){var d=this.label=c.rate,e=this.Ec=parseFloat(d,10);c.label=d;c.selected=1===e;t.H.call(this,a,c);this.j().d("ratechange",t.bind(this,this.update))}});t.nb.prototype.r=function(){t.H.prototype.r.call(this);this.j().playbackRate(this.Ec)};t.nb.prototype.update=function(){this.selected(this.j().playbackRate()==this.Ec)};
t.Ka=t.t.extend({i:function(a,c){t.t.call(this,a,c);a.poster()&&this.src(a.poster());(!a.poster()||!a.controls())&&this.V();a.d("posterchange",t.bind(this,function(){this.src(a.poster())}));a.d("play",t.bind(this,this.V))}});var ea="backgroundSize"in t.A.style;t.Ka.prototype.e=function(){var a=t.e("div",{className:"vjs-poster",tabIndex:-1});ea||a.appendChild(t.e("img"));return a};t.Ka.prototype.src=function(a){var c=this.v();a!==b&&(ea?c.style.backgroundImage='url("'+a+'")':c.firstChild.src=a)};
t.Ka.prototype.r=function(){this.j().controls()&&this.c.play()};t.Yb=t.a.extend({i:function(a,c){t.a.call(this,a,c)}});t.Yb.prototype.e=function(){return t.a.prototype.e.call(this,"div",{className:"vjs-loading-spinner"})};t.fb=t.t.extend();t.fb.prototype.e=function(){return t.t.prototype.e.call(this,"div",{className:"vjs-big-play-button",innerHTML:'<span aria-hidden="true"></span>',"aria-label":"play video"})};t.fb.prototype.r=function(){this.c.play()};
t.jb=t.a.extend({i:function(a,c){t.a.call(this,a,c);this.update();a.d("error",t.bind(this,this.update))}});t.jb.prototype.e=function(){var a=t.a.prototype.e.call(this,"div",{className:"vjs-error-display"});this.u=t.e("div");a.appendChild(this.u);return a};t.jb.prototype.update=function(){this.j().error()&&(this.u.innerHTML=this.s(this.j().error().message))};
t.q=t.a.extend({i:function(a,c,d){c=c||{};c.Gc=l;t.a.call(this,a,c,d);this.featuresProgressEvents||(this.yc=f,this.Dc=setInterval(t.bind(this,function(){var a=this.j().bufferedPercent();this.hd!=a&&this.j().l("progress");this.hd=a;1===a&&clearInterval(this.Dc)}),500));this.featuresTimeupdateEvents||(this.Kb=f,this.j().d("play",t.bind(this,this.Oc)),this.j().d("pause",t.bind(this,this.cb)),this.W("timeupdate",function(){this.featuresTimeupdateEvents=f;fa(this)}));var e,g;g=this;e=this.j();a=function(){if(e.controls()&&
!e.usingNativeControls()){var a;g.d("mousedown",g.r);g.d("touchstart",function(){a=this.c.userActive()});g.d("touchmove",function(){a&&this.j().reportUserActivity()});g.d("touchend",function(a){a.preventDefault()});G(g);g.d("tap",g.Pd)}};c=t.bind(g,g.Vd);this.I(a);e.d("controlsenabled",a);e.d("controlsdisabled",c);this.I(function(){this.networkState&&0<this.networkState()&&this.j().l("loadstart")})}});s=t.q.prototype;
s.Vd=function(){this.o("tap");this.o("touchstart");this.o("touchmove");this.o("touchleave");this.o("touchcancel");this.o("touchend");this.o("click");this.o("mousedown")};s.r=function(a){0===a.button&&this.j().controls()&&(this.j().paused()?this.j().play():this.j().pause())};s.Pd=function(){this.j().userActive(!this.j().userActive())};function fa(a){a.Kb=l;a.cb();a.o("play",a.Oc);a.o("pause",a.cb)}
s.Oc=function(){this.lc&&this.cb();this.lc=setInterval(t.bind(this,function(){this.j().l("timeupdate")}),250)};s.cb=function(){clearInterval(this.lc);this.j().l("timeupdate")};s.dispose=function(){this.yc&&(this.yc=l,clearInterval(this.Dc));this.Kb&&fa(this);t.a.prototype.dispose.call(this)};s.Pb=function(){this.Kb&&this.j().l("timeupdate")};s.Ic=m();t.q.prototype.featuresVolumeControl=f;t.q.prototype.featuresFullscreenResize=l;t.q.prototype.featuresPlaybackRate=l;
t.q.prototype.featuresProgressEvents=l;t.q.prototype.featuresTimeupdateEvents=l;t.media={};
t.g=t.q.extend({i:function(a,c,d){this.featuresVolumeControl=t.g.kd();this.featuresPlaybackRate=t.g.jd();this.movingMediaElementInDOM=!t.Wc;this.featuresProgressEvents=this.featuresFullscreenResize=f;t.q.call(this,a,c,d);for(d=t.g.kb.length-1;0<=d;d--)t.d(this.b,t.g.kb[d],t.bind(this,this.sd));if((c=c.source)&&this.b.currentSrc!==c.src)this.b.src=c.src;if(t.dc&&a.options().nativeControlsForTouch!==l){var e,g,h,j;e=this;g=this.j();c=g.controls();e.b.controls=!!c;h=function(){e.b.controls=f};j=function(){e.b.controls=
l};g.d("controlsenabled",h);g.d("controlsdisabled",j);c=function(){g.o("controlsenabled",h);g.o("controlsdisabled",j)};e.d("dispose",c);g.d("usingcustomcontrols",c);g.usingNativeControls(f)}a.I(function(){this.O&&(this.k.autoplay&&this.paused())&&(delete this.O.poster,this.play())});this.Fa()}});s=t.g.prototype;s.dispose=function(){t.g.Bb(this.b);t.q.prototype.dispose.call(this)};
s.e=function(){var a=this.c,c=a.O,d;if(!c||this.movingMediaElementInDOM===l)c?(d=c.cloneNode(l),t.g.Bb(c),c=d,a.O=k):(c=t.e("video"),t.Hc(c,t.h.z(a.ee||{},{id:a.id()+"_html5_api","class":"vjs-tech"}))),c.player=a,t.Gb(c,a.v());d=["autoplay","preload","loop","muted"];for(var e=d.length-1;0<=e;e--){var g=d[e],h={};"undefined"!==typeof a.k[g]&&(h[g]=a.k[g]);t.Hc(c,h)}return c};s.sd=function(a){"error"==a.type&&this.error()?this.j().error(this.error().code):(a.bubbles=l,this.j().l(a))};s.play=function(){this.b.play()};
s.pause=function(){this.b.pause()};s.paused=function(){return this.b.paused};s.currentTime=function(){return this.b.currentTime};s.Pb=function(a){try{this.b.currentTime=a}catch(c){t.log(c,"Video is not ready. (Video.js)")}};s.duration=function(){return this.b.duration||0};s.buffered=function(){return this.b.buffered};s.volume=function(){return this.b.volume};s.be=function(a){this.b.volume=a};s.muted=function(){return this.b.muted};s.Zd=function(a){this.b.muted=a};s.width=function(){return this.b.offsetWidth};
s.height=function(){return this.b.offsetHeight};s.Da=function(){return"function"==typeof this.b.webkitEnterFullScreen&&(/Android/.test(t.L)||!/Chrome|Mac OS X 10.5/.test(t.L))?f:l};s.nc=function(){var a=this.b;a.paused&&a.networkState<=a.je?(this.b.play(),setTimeout(function(){a.pause();a.webkitEnterFullScreen()},0)):a.webkitEnterFullScreen()};s.td=function(){this.b.webkitExitFullScreen()};s.src=function(a){if(a===b)return this.b.src;this.b.src=a};s.load=function(){this.b.load()};s.currentSrc=function(){return this.b.currentSrc};
s.poster=function(){return this.b.poster};s.Ic=function(a){this.b.poster=a};s.Ca=function(){return this.b.Ca};s.ae=function(a){this.b.Ca=a};s.autoplay=function(){return this.b.autoplay};s.Wd=function(a){this.b.autoplay=a};s.controls=function(){return this.b.controls};s.loop=function(){return this.b.loop};s.Yd=function(a){this.b.loop=a};s.error=function(){return this.b.error};s.seeking=function(){return this.b.seeking};s.ended=function(){return this.b.ended};s.playbackRate=function(){return this.b.playbackRate};
s.$d=function(a){this.b.playbackRate=a};s.networkState=function(){return this.b.networkState};t.g.isSupported=function(){try{t.A.volume=0.5}catch(a){return l}return!!t.A.canPlayType};t.g.vb=function(a){try{return!!t.A.canPlayType(a.type)}catch(c){return""}};t.g.kd=function(){var a=t.A.volume;t.A.volume=a/2+0.1;return a!==t.A.volume};t.g.jd=function(){var a=t.A.playbackRate;t.A.playbackRate=a/2+0.1;return a!==t.A.playbackRate};var V,ga=/^application\/(?:x-|vnd\.apple\.)mpegurl/i,ha=/^video\/mp4/i;
t.g.Ac=function(){4<=t.Tb&&(V||(V=t.A.constructor.prototype.canPlayType),t.A.constructor.prototype.canPlayType=function(a){return a&&ga.test(a)?"maybe":V.call(this,a)});t.$c&&(V||(V=t.A.constructor.prototype.canPlayType),t.A.constructor.prototype.canPlayType=function(a){return a&&ha.test(a)?"maybe":V.call(this,a)})};t.g.he=function(){var a=t.A.constructor.prototype.canPlayType;t.A.constructor.prototype.canPlayType=V;V=k;return a};t.g.Ac();t.g.kb="loadstart suspend abort error emptied stalled loadedmetadata loadeddata canplay canplaythrough playing waiting seeking seeked ended durationchange timeupdate progress play pause ratechange volumechange".split(" ");
t.g.Bb=function(a){if(a){a.player=k;for(a.parentNode&&a.parentNode.removeChild(a);a.hasChildNodes();)a.removeChild(a.firstChild);a.removeAttribute("src");if("function"===typeof a.load)try{a.load()}catch(c){}}};
t.f=t.q.extend({i:function(a,c,d){t.q.call(this,a,c,d);var e=c.source;d=c.parentEl;var g=this.b=t.e("div",{id:a.id()+"_temp_flash"}),h=a.id()+"_flash_api",j=a.k,j=t.h.z({readyFunction:"videojs.Flash.onReady",eventProxyFunction:"videojs.Flash.onEvent",errorEventProxyFunction:"videojs.Flash.onError",autoplay:j.autoplay,preload:j.Ca,loop:j.loop,muted:j.muted},c.flashVars),n=t.h.z({wmode:"opaque",bgcolor:"#000000"},c.params),h=t.h.z({id:h,name:h,"class":"vjs-tech"},c.attributes);e&&(e.type&&t.f.Cd(e.type)?
(e=t.f.Mc(e.src),j.rtmpConnection=encodeURIComponent(e.wb),j.rtmpStream=encodeURIComponent(e.Qb)):j.src=encodeURIComponent(t.rc(e.src)));t.Gb(g,d);c.startTime&&this.I(function(){this.load();this.play();this.currentTime(c.startTime)});t.Vc&&this.I(function(){t.d(this.v(),"mousemove",t.bind(this,function(){this.j().l({type:"mousemove",bubbles:l})}))});a.d("stageclick",a.reportUserActivity);this.b=t.f.rd(c.swf,g,j,n,h)}});t.f.prototype.dispose=function(){t.q.prototype.dispose.call(this)};
t.f.prototype.play=function(){this.b.vjs_play()};t.f.prototype.pause=function(){this.b.vjs_pause()};t.f.prototype.src=function(a){if(a===b)return this.currentSrc();t.f.Bd(a)?(a=t.f.Mc(a),this.te(a.wb),this.ue(a.Qb)):(a=t.rc(a),this.b.vjs_src(a));if(this.c.autoplay()){var c=this;setTimeout(function(){c.play()},0)}};t.f.prototype.setCurrentTime=function(a){this.Fd=a;this.b.vjs_setProperty("currentTime",a);t.q.prototype.Pb.call(this)};
t.f.prototype.currentTime=function(){return this.seeking()?this.Fd||0:this.b.vjs_getProperty("currentTime")};t.f.prototype.currentSrc=function(){var a=this.b.vjs_getProperty("currentSrc");if(a==k){var c=this.rtmpConnection(),d=this.rtmpStream();c&&d&&(a=t.f.ce(c,d))}return a};t.f.prototype.load=function(){this.b.vjs_load()};t.f.prototype.poster=function(){this.b.vjs_getProperty("poster")};t.f.prototype.setPoster=m();t.f.prototype.buffered=function(){return t.zb(0,this.b.vjs_getProperty("buffered"))};
t.f.prototype.Da=r(l);t.f.prototype.nc=r(l);function ia(){var a=W[X],c=a.charAt(0).toUpperCase()+a.slice(1);ja["set"+c]=function(c){return this.b.vjs_setProperty(a,c)}}function ka(a){ja[a]=function(){return this.b.vjs_getProperty(a)}}
var ja=t.f.prototype,W="rtmpConnection rtmpStream preload defaultPlaybackRate playbackRate autoplay loop mediaGroup controller controls volume muted defaultMuted".split(" "),la="error networkState readyState seeking initialTime duration startOffsetTime paused played seekable ended videoTracks audioTracks videoWidth videoHeight textTracks".split(" "),X;for(X=0;X<W.length;X++)ka(W[X]),ia();for(X=0;X<la.length;X++)ka(la[X]);t.f.isSupported=function(){return 10<=t.f.version()[0]};
t.f.vb=function(a){if(!a.type)return"";a=a.type.replace(/;.*/,"").toLowerCase();if(a in t.f.vd||a in t.f.Nc)return"maybe"};t.f.vd={"video/flv":"FLV","video/x-flv":"FLV","video/mp4":"MP4","video/m4v":"MP4"};t.f.Nc={"rtmp/mp4":"MP4","rtmp/flv":"FLV"};t.f.onReady=function(a){var c;if(c=(a=t.v(a))&&a.parentNode&&a.parentNode.player)a.player=c,t.f.checkReady(c.m)};t.f.checkReady=function(a){a.v()&&(a.v().vjs_getProperty?a.Fa():setTimeout(function(){t.f.checkReady(a)},50))};t.f.onEvent=function(a,c){t.v(a).player.l(c)};
t.f.onError=function(a,c){var d=t.v(a).player,e="FLASH: "+c;"srcnotfound"==c?d.error({code:4,message:e}):d.error(e)};t.f.version=function(){var a="0,0,0";try{a=(new window.ActiveXObject("ShockwaveFlash.ShockwaveFlash")).GetVariable("$version").replace(/\D+/g,",").match(/^,?(.+),?$/)[1]}catch(c){try{navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin&&(a=(navigator.plugins["Shockwave Flash 2.0"]||navigator.plugins["Shockwave Flash"]).description.replace(/\D+/g,",").match(/^,?(.+),?$/)[1])}catch(d){}}return a.split(",")};
t.f.rd=function(a,c,d,e,g){a=t.f.xd(a,d,e,g);a=t.e("div",{innerHTML:a}).childNodes[0];d=c.parentNode;c.parentNode.replaceChild(a,c);var h=d.childNodes[0];setTimeout(function(){h.style.display="block"},1E3);return a};
t.f.xd=function(a,c,d,e){var g="",h="",j="";c&&t.h.Y(c,function(a,c){g+=a+"="+c+"&amp;"});d=t.h.z({movie:a,flashvars:g,allowScriptAccess:"always",allowNetworking:"all"},d);t.h.Y(d,function(a,c){h+='<param name="'+a+'" value="'+c+'" />'});e=t.h.z({data:a,width:"100%",height:"100%"},e);t.h.Y(e,function(a,c){j+=a+'="'+c+'" '});return'<object type="application/x-shockwave-flash"'+j+">"+h+"</object>"};t.f.ce=function(a,c){return a+"&"+c};
t.f.Mc=function(a){var c={wb:"",Qb:""};if(!a)return c;var d=a.indexOf("&"),e;-1!==d?e=d+1:(d=e=a.lastIndexOf("/")+1,0===d&&(d=e=a.length));c.wb=a.substring(0,d);c.Qb=a.substring(e,a.length);return c};t.f.Cd=function(a){return a in t.f.Nc};t.f.bd=/^rtmp[set]?:\/\//i;t.f.Bd=function(a){return t.f.bd.test(a)};
t.ad=t.a.extend({i:function(a,c,d){t.a.call(this,a,c,d);if(!a.k.sources||0===a.k.sources.length){c=0;for(d=a.k.techOrder;c<d.length;c++){var e=t.aa(d[c]),g=window.videojs[e];if(g&&g.isSupported()){P(a,e);break}}}else a.src(a.k.sources)}});t.Player.prototype.textTracks=function(){return this.Ea=this.Ea||[]};
function ma(a,c,d,e,g){var h=a.Ea=a.Ea||[];g=g||{};g.kind=c;g.label=d;g.language=e;c=t.aa(c||"subtitles");var j=new window.videojs[c+"Track"](a,g);h.push(j);j.Ab()&&a.I(function(){setTimeout(function(){Y(j.j(),j.id())},0)})}function Y(a,c,d){for(var e=a.Ea,g=0,h=e.length,j,n;g<h;g++)j=e[g],j.id()===c?(j.show(),n=j):d&&(j.J()==d&&0<j.mode())&&j.disable();(c=n?n.J():d?d:l)&&a.l(c+"trackchange")}
t.B=t.a.extend({i:function(a,c){t.a.call(this,a,c);this.T=c.id||"vjs_"+c.kind+"_"+c.language+"_"+t.w++;this.Jc=c.src;this.od=c["default"]||c.dflt;this.fe=c.title;this.Ua=c.srclang;this.Dd=c.label;this.ba=[];this.tb=[];this.ma=this.na=0;this.c.d("fullscreenchange",t.bind(this,this.ed))}});s=t.B.prototype;s.J=p("G");s.src=p("Jc");s.Ab=p("od");s.title=p("fe");s.language=p("Ua");s.label=p("Dd");s.ld=p("ba");s.cd=p("tb");s.readyState=p("na");s.mode=p("ma");
s.ed=function(){this.b.style.fontSize=this.c.isFullscreen()?140*(screen.width/this.c.width())+"%":""};s.e=function(){return t.a.prototype.e.call(this,"div",{className:"vjs-"+this.G+" vjs-text-track"})};s.show=function(){na(this);this.ma=2;t.a.prototype.show.call(this)};s.V=function(){na(this);this.ma=1;t.a.prototype.V.call(this)};
s.disable=function(){2==this.ma&&this.V();this.c.o("timeupdate",t.bind(this,this.update,this.T));this.c.o("ended",t.bind(this,this.reset,this.T));this.reset();this.c.ka("textTrackDisplay").removeChild(this);this.ma=0};function na(a){0===a.na&&a.load();0===a.ma&&(a.c.d("timeupdate",t.bind(a,a.update,a.T)),a.c.d("ended",t.bind(a,a.reset,a.T)),("captions"===a.G||"subtitles"===a.G)&&a.c.ka("textTrackDisplay").Q(a))}
s.load=function(){0===this.na&&(this.na=1,t.get(this.Jc,t.bind(this,this.Sd),t.bind(this,this.Id)))};s.Id=function(a){this.error=a;this.na=3;this.l("error")};s.Sd=function(a){var c,d;a=a.split("\n");for(var e="",g=1,h=a.length;g<h;g++)if(e=t.trim(a[g])){-1==e.indexOf("--\x3e")?(c=e,e=t.trim(a[++g])):c=this.ba.length;c={id:c,index:this.ba.length};d=e.split(/[\t ]+/);c.startTime=oa(d[0]);c.xa=oa(d[2]);for(d=[];a[++g]&&(e=t.trim(a[g]));)d.push(e);c.text=d.join("<br/>");this.ba.push(c)}this.na=2;this.l("loaded")};
function oa(a){var c=a.split(":");a=0;var d,e,g;3==c.length?(d=c[0],e=c[1],c=c[2]):(d=0,e=c[0],c=c[1]);c=c.split(/\s+/);c=c.splice(0,1)[0];c=c.split(/\.|,/);g=parseFloat(c[1]);c=c[0];a+=3600*parseFloat(d);a+=60*parseFloat(e);a+=parseFloat(c);g&&(a+=g/1E3);return a}
s.update=function(){if(0<this.ba.length){var a=this.c.options().trackTimeOffset||0,a=this.c.currentTime()+a;if(this.Ob===b||a<this.Ob||this.Wa<=a){var c=this.ba,d=this.c.duration(),e=0,g=l,h=[],j,n,q,w;a>=this.Wa||this.Wa===b?w=this.Db!==b?this.Db:0:(g=f,w=this.Jb!==b?this.Jb:c.length-1);for(;;){q=c[w];if(q.xa<=a)e=Math.max(e,q.xa),q.Na&&(q.Na=l);else if(a<q.startTime){if(d=Math.min(d,q.startTime),q.Na&&(q.Na=l),!g)break}else g?(h.splice(0,0,q),n===b&&(n=w),j=w):(h.push(q),j===b&&(j=w),n=w),d=Math.min(d,
q.xa),e=Math.max(e,q.startTime),q.Na=f;if(g)if(0===w)break;else w--;else if(w===c.length-1)break;else w++}this.tb=h;this.Wa=d;this.Ob=e;this.Db=j;this.Jb=n;j=this.tb;n="";a=0;for(c=j.length;a<c;a++)n+='<span class="vjs-tt-cue">'+j[a].text+"</span>";this.b.innerHTML=n;this.l("cuechange")}}};s.reset=function(){this.Wa=0;this.Ob=this.c.duration();this.Jb=this.Db=0};t.Vb=t.B.extend();t.Vb.prototype.G="captions";t.cc=t.B.extend();t.cc.prototype.G="subtitles";t.Wb=t.B.extend();t.Wb.prototype.G="chapters";
t.ec=t.a.extend({i:function(a,c,d){t.a.call(this,a,c,d);if(a.k.tracks&&0<a.k.tracks.length){c=this.c;a=a.k.tracks;for(var e=0;e<a.length;e++)d=a[e],ma(c,d.kind,d.label,d.language,d)}}});t.ec.prototype.e=function(){return t.a.prototype.e.call(this,"div",{className:"vjs-text-track-display"})};t.$=t.H.extend({i:function(a,c){var d=this.ea=c.track;c.label=d.label();c.selected=d.Ab();t.H.call(this,a,c);this.c.d(d.J()+"trackchange",t.bind(this,this.update))}});
t.$.prototype.r=function(){t.H.prototype.r.call(this);Y(this.c,this.ea.T,this.ea.J())};t.$.prototype.update=function(){this.selected(2==this.ea.mode())};t.mb=t.$.extend({i:function(a,c){c.track={J:function(){return c.kind},j:a,label:function(){return c.kind+" off"},Ab:r(l),mode:r(l)};t.$.call(this,a,c);this.selected(f)}});t.mb.prototype.r=function(){t.$.prototype.r.call(this);Y(this.c,this.ea.T,this.ea.J())};
t.mb.prototype.update=function(){for(var a=this.c.textTracks(),c=0,d=a.length,e,g=f;c<d;c++)e=a[c],e.J()==this.ea.J()&&2==e.mode()&&(g=l);this.selected(g)};t.U=t.K.extend({i:function(a,c){t.K.call(this,a,c);1>=this.N.length&&this.V()}});t.U.prototype.va=function(){var a=[],c;a.push(new t.mb(this.c,{kind:this.G}));for(var d=0;d<this.c.textTracks().length;d++)c=this.c.textTracks()[d],c.J()===this.G&&a.push(new t.$(this.c,{track:c}));return a};
t.Ga=t.U.extend({i:function(a,c,d){t.U.call(this,a,c,d);this.b.setAttribute("aria-label","Captions Menu")}});t.Ga.prototype.G="captions";t.Ga.prototype.ta="Captions";t.Ga.prototype.className="vjs-captions-button";t.Ma=t.U.extend({i:function(a,c,d){t.U.call(this,a,c,d);this.b.setAttribute("aria-label","Subtitles Menu")}});t.Ma.prototype.G="subtitles";t.Ma.prototype.ta="Subtitles";t.Ma.prototype.className="vjs-subtitles-button";
t.Ha=t.U.extend({i:function(a,c,d){t.U.call(this,a,c,d);this.b.setAttribute("aria-label","Chapters Menu")}});s=t.Ha.prototype;s.G="chapters";s.ta="Chapters";s.className="vjs-chapters-button";s.va=function(){for(var a=[],c,d=0;d<this.c.textTracks().length;d++)c=this.c.textTracks()[d],c.J()===this.G&&a.push(new t.$(this.c,{track:c}));return a};
s.wa=function(){for(var a=this.c.textTracks(),c=0,d=a.length,e,g,h=this.N=[];c<d;c++)if(e=a[c],e.J()==this.G)if(0===e.readyState())e.load(),e.d("loaded",t.bind(this,this.wa));else{g=e;break}a=this.Aa;a===b&&(a=new t.ha(this.c),a.ja().appendChild(t.e("li",{className:"vjs-menu-title",innerHTML:t.aa(this.G),de:-1})));if(g){e=g.ba;for(var j,c=0,d=e.length;c<d;c++)j=e[c],j=new t.gb(this.c,{track:g,cue:j}),h.push(j),a.Q(j);this.Q(a)}0<this.N.length&&this.show();return a};
t.gb=t.H.extend({i:function(a,c){var d=this.ea=c.track,e=this.cue=c.cue,g=a.currentTime();c.label=e.text;c.selected=e.startTime<=g&&g<e.xa;t.H.call(this,a,c);d.d("cuechange",t.bind(this,this.update))}});t.gb.prototype.r=function(){t.H.prototype.r.call(this);this.c.currentTime(this.cue.startTime);this.update(this.cue.startTime)};t.gb.prototype.update=function(){var a=this.cue,c=this.c.currentTime();this.selected(a.startTime<=c&&c<a.xa)};
t.h.z(t.Ia.prototype.k.children,{subtitlesButton:{},captionsButton:{},chaptersButton:{}});
if("undefined"!==typeof window.JSON&&"function"===window.JSON.parse)t.JSON=window.JSON;else{t.JSON={};var Z=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;t.JSON.parse=function(a,c){function d(a,e){var j,n,q=a[e];if(q&&"object"===typeof q)for(j in q)Object.prototype.hasOwnProperty.call(q,j)&&(n=d(q,j),n!==b?q[j]=n:delete q[j]);return c.call(a,e,q)}var e;a=String(a);Z.lastIndex=0;Z.test(a)&&(a=a.replace(Z,function(a){return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)}));
if(/^[\],:{}\s]*$/.test(a.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,"")))return e=eval("("+a+")"),"function"===typeof c?d({"":e},""):e;throw new SyntaxError("JSON.parse(): invalid or malformed JSON data");}}
t.ic=function(){var a,c,d=document.getElementsByTagName("video");if(d&&0<d.length)for(var e=0,g=d.length;e<g;e++)if((c=d[e])&&c.getAttribute)c.player===b&&(a=c.getAttribute("data-setup"),a!==k&&(a=t.JSON.parse(a||"{}"),videojs(c,a)));else{t.ub();break}else t.Rc||t.ub()};t.ub=function(){setTimeout(t.ic,1)};"complete"===document.readyState?t.Rc=f:t.W(window,"load",function(){t.Rc=f});t.ub();t.Ud=function(a,c){t.Player.prototype[a]=c};var pa=this;function $(a,c){var d=a.split("."),e=pa;!(d[0]in e)&&e.execScript&&e.execScript("var "+d[0]);for(var g;d.length&&(g=d.shift());)!d.length&&c!==b?e[g]=c:e=e[g]?e[g]:e[g]={}};$("videojs",t);$("_V_",t);$("videojs.options",t.options);$("videojs.players",t.Ba);$("videojs.TOUCH_ENABLED",t.dc);$("videojs.cache",t.ua);$("videojs.Component",t.a);t.a.prototype.player=t.a.prototype.j;t.a.prototype.options=t.a.prototype.options;t.a.prototype.init=t.a.prototype.i;t.a.prototype.dispose=t.a.prototype.dispose;t.a.prototype.createEl=t.a.prototype.e;t.a.prototype.contentEl=t.a.prototype.ja;t.a.prototype.el=t.a.prototype.v;t.a.prototype.addChild=t.a.prototype.Q;
t.a.prototype.getChild=t.a.prototype.ka;t.a.prototype.getChildById=t.a.prototype.wd;t.a.prototype.children=t.a.prototype.children;t.a.prototype.initChildren=t.a.prototype.uc;t.a.prototype.removeChild=t.a.prototype.removeChild;t.a.prototype.on=t.a.prototype.d;t.a.prototype.off=t.a.prototype.o;t.a.prototype.one=t.a.prototype.W;t.a.prototype.trigger=t.a.prototype.l;t.a.prototype.triggerReady=t.a.prototype.Fa;t.a.prototype.show=t.a.prototype.show;t.a.prototype.hide=t.a.prototype.V;
t.a.prototype.width=t.a.prototype.width;t.a.prototype.height=t.a.prototype.height;t.a.prototype.dimensions=t.a.prototype.pd;t.a.prototype.ready=t.a.prototype.I;t.a.prototype.addClass=t.a.prototype.n;t.a.prototype.removeClass=t.a.prototype.p;t.a.prototype.buildCSSClass=t.a.prototype.S;t.a.prototype.localize=t.a.prototype.s;t.Player.prototype.ended=t.Player.prototype.ended;t.Player.prototype.enterFullWindow=t.Player.prototype.oc;t.Player.prototype.exitFullWindow=t.Player.prototype.Cb;
t.Player.prototype.preload=t.Player.prototype.Ca;t.Player.prototype.remainingTime=t.Player.prototype.remainingTime;t.Player.prototype.supportsFullScreen=t.Player.prototype.Da;t.Player.prototype.currentType=t.Player.prototype.Ra;t.Player.prototype.requestFullScreen=t.Player.prototype.Ra;t.Player.prototype.cancelFullScreen=t.Player.prototype.Ra;t.Player.prototype.isFullScreen=t.Player.prototype.Ra;$("videojs.MediaLoader",t.ad);$("videojs.TextTrackDisplay",t.ec);$("videojs.ControlBar",t.Ia);
$("videojs.Button",t.t);$("videojs.PlayToggle",t.$b);$("videojs.FullscreenToggle",t.Ja);$("videojs.BigPlayButton",t.fb);$("videojs.LoadingSpinner",t.Yb);$("videojs.CurrentTimeDisplay",t.hb);$("videojs.DurationDisplay",t.ib);$("videojs.TimeDivider",t.fc);$("videojs.RemainingTimeDisplay",t.pb);$("videojs.LiveDisplay",t.Xb);$("videojs.ErrorDisplay",t.jb);$("videojs.Slider",t.P);$("videojs.ProgressControl",t.ob);$("videojs.SeekBar",t.bc);$("videojs.LoadProgressBar",t.lb);$("videojs.PlayProgressBar",t.Zb);
$("videojs.SeekHandle",t.La);$("videojs.VolumeControl",t.rb);$("videojs.VolumeBar",t.qb);$("videojs.VolumeLevel",t.gc);$("videojs.VolumeMenuButton",t.ra);$("videojs.VolumeHandle",t.sb);$("videojs.MuteToggle",t.ia);$("videojs.PosterImage",t.Ka);$("videojs.Menu",t.ha);$("videojs.MenuItem",t.H);$("videojs.MenuButton",t.K);$("videojs.PlaybackRateMenuButton",t.ac);t.K.prototype.createItems=t.K.prototype.va;t.U.prototype.createItems=t.U.prototype.va;t.Ha.prototype.createItems=t.Ha.prototype.va;
$("videojs.SubtitlesButton",t.Ma);$("videojs.CaptionsButton",t.Ga);$("videojs.ChaptersButton",t.Ha);$("videojs.MediaTechController",t.q);t.q.prototype.featuresVolumeControl=t.q.prototype.qe;t.q.prototype.featuresFullscreenResize=t.q.prototype.me;t.q.prototype.featuresPlaybackRate=t.q.prototype.ne;t.q.prototype.featuresProgressEvents=t.q.prototype.oe;t.q.prototype.featuresTimeupdateEvents=t.q.prototype.pe;t.q.prototype.setPoster=t.q.prototype.Ic;$("videojs.Html5",t.g);t.g.Events=t.g.kb;
t.g.isSupported=t.g.isSupported;t.g.canPlaySource=t.g.vb;t.g.patchCanPlayType=t.g.Ac;t.g.unpatchCanPlayType=t.g.he;t.g.prototype.setCurrentTime=t.g.prototype.Pb;t.g.prototype.setVolume=t.g.prototype.be;t.g.prototype.setMuted=t.g.prototype.Zd;t.g.prototype.setPreload=t.g.prototype.ae;t.g.prototype.setAutoplay=t.g.prototype.Wd;t.g.prototype.setLoop=t.g.prototype.Yd;t.g.prototype.enterFullScreen=t.g.prototype.nc;t.g.prototype.exitFullScreen=t.g.prototype.td;t.g.prototype.playbackRate=t.g.prototype.playbackRate;
t.g.prototype.setPlaybackRate=t.g.prototype.$d;$("videojs.Flash",t.f);t.f.isSupported=t.f.isSupported;t.f.canPlaySource=t.f.vb;t.f.onReady=t.f.onReady;$("videojs.TextTrack",t.B);t.B.prototype.label=t.B.prototype.label;t.B.prototype.kind=t.B.prototype.J;t.B.prototype.mode=t.B.prototype.mode;t.B.prototype.cues=t.B.prototype.ld;t.B.prototype.activeCues=t.B.prototype.cd;$("videojs.CaptionsTrack",t.Vb);$("videojs.SubtitlesTrack",t.cc);$("videojs.ChaptersTrack",t.Wb);$("videojs.autoSetup",t.ic);
$("videojs.plugin",t.Ud);$("videojs.createTimeRange",t.zb);$("videojs.util",t.ga);t.ga.mergeOptions=t.ga.Va;t.addLanguage=t.dd;})();
;/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can 
 * always reference jQuery with $, even when in .noConflict() mode.
 *
 * Google CDN, Latest jQuery
 * To use the default WordPress version of jQuery, go to lib/config.php and
 * remove or comment out: add_theme_support('jquery-cdn');
 * ======================================================================== */

(function($) {

// Use this variable to set up the common and page specific functions. If you 
// rename this variable, you will also need to rename the namespace below.
var Roots = {
  // All pages
  common: {
    init: function() {
      // JavaScript to be fired on all pages
      function setMobileMenu(){
        //console.log('setMobileMenu');
        if(window.innerWidth<768){
          $(".nav-container").css({overflow:"auto", maxHeight: $(window).height() - $(".navbar-header").height() + "px" });
        }
        else{
          $(".nav-container").css({overflow:"inherit"});
        }
      }
	  
	  function setMediaMenu(){
		if($('.mobileMediaNav').length > 0){
			$('.mobileMediaToggle').click(function(){
				$('.mobileMediaNav').toggleClass('open');			
			});
		}
		if($('.mobileMediaSubNav').length > 0){
			$('.mobileMediaSubToggle').click(function(){
				$('.mobileMediaSubNav').toggleClass('open');			
			});
		}
	  }
	  
	  function testFire(){
		console.log('fire');  
	  }
	  
	  function setPagination(){
		  $('.next').text(function(){
			return $(this).text().replace("Next", "");  
		  });
		  
		  $('.prev').text(function(){
			return $(this).text().replace("Previous", "");  
		  });
	  }
	  
	  function setbbpressSearch(){
		$("#bbp_search").val('Search...');
		$("#bbp_search").focus(function(){
			var value = $.trim($(this).val());
			if(value === "Search..."){
				$(this).val('');
			}
		});
		$("#bbp_search").blur(function(){
			var value = $.trim($(this).val());
			if(value === ""){
				$(this).val('Search...');
			}
		});
	  }
	  
	  $("a.imgLink").click(function() {
		$.fancybox({
			'padding'       : 30,
			'maxwidth'		: '90%',
			'fitToView'		: true,
			'href'          : this.href,
			'autoResize'    : true,
			'autoSize'      : true,
			'showCloseButton': true,
			'autoScale'		: true,
			'type'        : 'iframe',
			'scrolling'   : 'no',
			'content'       : "<div class=\"forumlightboxVideoContainer\"><img class=\"fullImgWidth\" src=\""+this.href+"\"/></div>"
		  });
		  return false;
	  });
	  
	  	  
      //fix the mobile menu scrolling problem
      $(document).ready(setMobileMenu);
	  $(document).ready(setMediaMenu);
	  $(document).ready(setPagination());
	  $(document).ready(setbbpressSearch());
      $(window).resize(setMobileMenu);
    }
  },
  // Home page
  home: {
    init: function() {
      // JavaScript to be fired on the home page
      $(document).ready(function(){
        //resizeBanner();

        $('#main-banner-container').slippry({
          // general elements & wrapper
          slippryWrapper: '<div class="sy-box news-slider" />', // wrapper to wrap everything, including pager
          elements: '.main-banner', // elments cointaining slide content
          // options
          captions: false,
          // transitions
          transition: 'horizontal', // fade, horizontal, kenburns, false
          speed: 1200,
          pause: 5000,
          pager: false,
          adaptiveHeight: false
        });

        $(".videoLink").click(function() {
            $.fancybox({
                'padding'       : 0,
                'width'         : '50%',
                'height'        : '70%',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
        'showCloseButton': true,
        'autoScale'   : true,
        'type'        : 'iframe',
          'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:100% !important; height:auto !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>"
                });
              return false;
            });
      });
    }
  },
  page_template_template_video_listing_text_php:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
        $(".btnVideoDetail").click(function() {
			
            $.fancybox({
                'padding'       : 30,
                'width'         : '50%',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:100%  !important; height:100% !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
				afterShow : function() {	
					$('.lightboxContentContainer').each(
						function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						});
					}
           	  });
              return false;
        });
			
      });
    }
  },
  page_template_template_video_listing_image_php:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
        $(".videoLink").click(function() {
            $.fancybox({
                'padding'       : 30,
                'width'         : '50%',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:100%  !important; height:100% !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div><div class=\"lightboxContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
				afterShow : function() {	
					$('.lightboxContentContainer').each(
						function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						});
					}
           	  });
              return false;
        });
			
      });
    }
  },
  page_template_template_listing_img_php:  {
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
		var nbiFlag = false;
		function setImageFilter(){
			$('.lightBoxImg').children('div').each(function(){
				//$(this).css('display','none');
				$('.originalImgContainer').css('visibility','visible');
				$('.btnOriginalImage').addClass("active");
			});
			
			if(!nbiFlag){
				$('.btnNBI').css({'display':'none'});
				return;
			}
			
			$('.btnOriginalImage').click(function(){
				$('.imageFilterContainer a').each(function(){
					$(this).removeClass("active");	
				});
				$('.lightBoxImg').children('div').each(function(){
					$(this).css('display','none');
					$(this).css('visibility','hidden');
					$('.originalImgContainer').css({'display':'block','position':'relative', 'visibility':'visible'});
					$('.btnOriginalImage').addClass("active");
				});
			});
			
			$('.btnNBI').click(function(){
				$('.imageFilterContainer a').each(function(){
					$(this).removeClass("active");	
				});
				$('.lightBoxImg').children('div').each(function(){
					$(this).css('display','none');
					$(this).css('visibility','hidden');
					$('.nbiImgContainer').css({'display':'block','position':'relative', 'visibility':'visible'});
					$('.btnNBI').addClass("active");
				});
			});
		}
		
		function imageResize(){
			var imgHeight = 0;
			var wHeight = $(window).height();
			console.log(wHeight);
			if(wHeight >= 900){
				//console.log(1);
				imgHeight = 500;
			}else if(wHeight >=700 && wHeight <900){
				imgHeight = 350;
			}else if(wHeight >360 && wHeight <700){
				//console.log(3);
				imgHeight = 180;
			} 
			else{
				//console.log(3);
				imgHeight = 120;					
			} 
			$('.lightBoxImg').css({"height":imgHeight+'px'});

		}
		
		function setNBIfilter(id,nImageSrc){
			if(!nbiFlag){return;}
			var native_width = 0;
			var native_height = 0;
			var nbiImageSrc = nImageSrc;
			$("#"+id+" .large").css({"background": "url('"+nbiImageSrc+"') no-repeat"});
		
			$("#"+id).mousemove(function(e){
				if(!native_width && !native_height)
				{
					var image_object = new Image();
					image_object.src = $("#"+id+" .small").attr("src");
					
					native_width = $("#"+id+" .small").width();
					native_height = $("#"+id+" .small").height();
					$(this).css({"width":native_width});
					//console.log(native_width+' '+native_height);
				}
				else
				{
					var magnify_offset = $(this).offset();
					var mx = e.pageX - magnify_offset.left;
					var my = e.pageY - magnify_offset.top;
					
					if(mx < $(this).width() && my < $(this).height() && mx > 0 && my > 0)
					{
						$("#"+id+" .large").fadeIn(100);
					}
					else
					{
						$("#"+id+" .large").fadeOut(100);
					}
					if($("#"+id+" .large").is(":visible"))
					{
						$("#"+id+" .large").css({"background-size":native_width+'px '+native_height+'px'});
						var rx = Math.round(mx/$("#"+id+" .small").width()*native_width - $("#"+id+" .large").width()/2)*-1;
						var ry = Math.round(my/$("#"+id+" .small").height()*native_height - $("#"+id+" .large").height()/2)*-1;
						var bgp = rx + "px " + ry + "px";
						
						var px = mx - $("#"+id+" .large").width()/2;
						var py = my - $("#"+id+" .large").height()/2;
		
						$("#"+id+" .large").css({left: px, top: py, backgroundPosition: bgp});
					}
				}
			});
		}		
		
        $(".imgArchievesLink").click(function() {
			if($(this).attr('nbi_image'))
			{
				console.log('no null');
				nbiFlag = true;
			}else{
				console.log('null');
				nbiFlag = false;
			}
            $.fancybox({
                'padding'       : 30,
                'width'         : '50%',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"lightboxImgContainer\"><div class=\"lightBoxImg\"><div id=\"original_image\" class=\"originalImgContainer\"><div class=\"large\"></div><img class=\"small fullImgWidth\" src=\""+this.href+"\" data-big=\""+$(this).attr("nbi_image")+"\"/></div><div id=\"nbi_image\" class=\"nbiImgContainer\"><div class=\"large\"></div><img class=\"small fullImgWidth\" src=\""+$(this).attr("nbi_image")+"\" data-big=\""+this.href+"\"/></div></div></div><div class=\"imageFilterContainer\"><a href=\"javascript:;\" class=\"btnOriginalImage\">Original image</a><a href=\"javascript:;\" class=\"btnNBI\">NBI image</a></div><div class=\"lightboxImgContentContainer\"><p>"+$(this).attr("person")+"</p><p>Description:"+$(this).attr("desp")+"</p></div>",
				 afterShow : function() {
					 $.when(
					 	imageResize(),
					 	$('.lightboxImgContentContainer').each(function()
						{
							var settings = {
								autoReinitialise: true
							};
							$(this).jScrollPane(settings);
							var api = $(this).data('jsp');
							var throttleTimeout;
							$(window).bind(
								'resize',
								function()
								{
									if (!throttleTimeout) {
										throttleTimeout = setTimeout(
											function()
											{
												api.reinitialise();
												throttleTimeout = null;
											},
											50
										);
									}
								});
						}),
						setNBIfilter('original_image', $("#original_image .small").attr("data-big")),
						setNBIfilter('nbi_image', $("#nbi_image .small").attr("data-big")),
						$(window).resize(function() {
							imageResize();
							setNBIfilter('original_image', $("#original_image .small").attr("data-big"));
							setNBIfilter('nbi_image', $("#nbi_image .small").attr("data-big"));
						})
					).then(setImageFilter());
				}
                });
              return false;
            });
        });
    }
  },
  // About us page, note the change from about-us to about_us.
  single_topic:{
    init: function() {
      // JavaScript to be fired on the about us page
      $(document).ready(function(){
        $("a.bbp-atticon-video").click(function() {
            $.fancybox({
                'padding'       : 30,
                'width'         : 'auto',
                'height'        : 'auto',
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"forumlightboxVideoContainer\"><video autoplay id=\"example_video_1\" class=\"video-js vjs-default-skin\" width=\"auto\" height=\"auto\" style=\"width:100%  !important; height:100% !important\" controls preload=\"none\" data-setup='{'autoplay': true, 'enterFullScreen':true}'><source src=\""+this.href+"\" type='video/mp4' /></video></div>"
           	  });
              return false;
        });
		
		$("a.bbp-atthumb").click(function() {
            $.fancybox({
                'padding'       : 30,
				'maxwidth'		: '90%',
				'fitToView'		: true,
                'href'          : this.href,
                'autoResize'    : true,
                'autoSize'      : true,
				'showCloseButton': true,
				'autoScale'		: true,
				'type'        : 'iframe',
			    'scrolling'   : 'no',
                'content'       : "<div class=\"forumlightboxVideoContainer\"><img class=\"fullImgWidth\" src=\""+this.href+"\"/></div>"
           	  });
              return false;
        });
		
      });
    }
  },  
  about_us: {
    init: function() {
      // JavaScript to be fired on the about us page
    }
  }
};

// The routing fires all common scripts, followed by the page specific scripts.
// Add additional events for more control over timing e.g. a finalize event
var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = Roots;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {
    UTIL.fire('common');

    $.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });
  }
};

$(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
