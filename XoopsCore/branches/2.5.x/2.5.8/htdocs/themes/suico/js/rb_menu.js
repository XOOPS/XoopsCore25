/*
 * simple sliding menu using jQuery and Interface - http://www.getintothis.com
 * 
 * note: this library depends on jquery (http://www.jquery.com) and
 * interface (http://interface.eyecon.ro)
 *
 * Copyright (c) 2006 Ramin Bozorgzadeh
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LECENSE.txt) linceses.
 */

$.fn.rb_menu = function(options) {
  var $self = this;

  $self.options = {
    // transitions: http://gsgd.co.uk/sandbox/jquery/easing/
    transition:    'easeOutBounce',
    // trigger events: mouseover, mousedown, mouseup, click, dblclick
    triggerEvent:  'mouseover',
    // number of ms to delay before hiding menu (on page load)
    loadHideDelay : 1000,
    // number of ms to delay before hiding menu (on mouseout)
    blurHideDelay:  500,
    // number of ms for transition effect
    effectDuration: 1000,
    // hide the menu when the page loads
    hideOnLoad: true,
    // automatically hide menu when mouse leaves area
    autoHide: true
  }

  // make sure to check if options are given!
  if(options) {
    $.extend($self.options, options);
  }

  return this.each(function() {
    var $menu = $(this);
    var $menuItems = $menu.find('.items');
    var $toggle = $menu.find('.toggle');

  	// add 'hover' class to trigger for css styling
  	$toggle.hover(
  	  function() {
  		  $toggle.addClass('toggle-hover');
  	  },
  	  function() {
  		  $toggle.removeClass('toggle-hover');
  	  }
  	);

  	if($self.options.hideOnLoad) {
  		if($self.options.loadHideDelay <= 0) {
  			$menuItems.hide();
  			$menu.closed = true;
  			$menu.unbind();
  		} else {
  			// let's hide the menu when the page is loading
  			// after {loadHideDelay} milliseconds
  			setTimeout( function() {
  				$menu.hideMenu();
  			}, $self.options.loadHideDelay);
  		}
  	}

    // bind event defined by {triggerEvent} to the trigger
    // to open and close the menu
    $toggle.bind($self.options.triggerEvent, function() {
      // if the trigger event is click or dblclick, we want
      // to close the menu if its open
      if(($self.options.triggerEvent == 'click' || $self.options.triggerEvent == 'dblclick') && !$menu.closed) {
        $menu.hideMenu();
      } else {
        $menu.showMenu();
      }
    });

    $menu.hideMenu = function() {
      if($menuItems.css('display') == 'block' && !$menu.closed) {
        $menuItems.hide(
          'slide',
          {
            direction: 'left',
            easing: $self.options.transition
          },
          $self.options.effectDuration,
          function() {
            $menu.closed = true;
            $menu.unbind();
          }
        );
      }
    }

    $menu.showMenu = function() {
      if($menuItems.css('display') == 'none' && $menu.closed) {
        $menuItems.show(
          'slide',
          {
            direction: 'left',
            easing: $self.options.transition
          },
          $self.options.effectDuration,
          function() {
            $menu.closed = false;
            if($self.options.autoHide) {
              $menu.hover(
                function(e) {
                  clearTimeout($menu.timeout);
                }, 
                function(e) {
                  $menu.timeout = setTimeout(function() {
                      $menu.hideMenu();
                  }, $self.options.blurHideDelay);
                }
              );
            }
          }
        );
      }
    }
  });
};