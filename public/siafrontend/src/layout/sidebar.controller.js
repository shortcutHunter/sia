(function() {
  'use strict';

  angular
    .module('app.layout')
    .controller('SidebarController', SidebarController);

  SidebarController.$inject = ['$state', 'routerHelper', '$http', 'dataservice'];
  /* @ngInject */
  function SidebarController($state, routerHelper, $http, dataservice) {
    var vm = this;
    var states = routerHelper.getStates();
    vm.isCurrent = isCurrent;

    vm.logout = logout;

    activate();

    function activate() { getNavRoutes(); }

    function getNavRoutes() {

      $http.get('/session').then(function(response){
        let data = response.data;
        dataservice.user = data;

        vm.navRoutes = states.filter(function(r) {
          return !!(r.settings && r.settings.parent && r.settings.roles.includes(data.role));
        }).sort(function(r1, r2) {
          return r1.settings.parent - r2.settings.parent;
        });

      });
    }

    function logout() {
      console.log('Running ?');
      return $http.post('/logout' , {})
      .then(function(){
        window.location = '/';
      });
    }

    function isCurrent(route) {
      if (!route.title || !$state.current || !$state.current.title) {
        return '';
      }
      var menuName = route.name;
      return $state.current.name.substr(0, menuName.length) === menuName ? 'active' : '';
    }

    function slideToggle(t,e,o){0===t.clientHeight?j(t,e,o,!0):j(t,e,o)}function slideUp(t,e,o){j(t,e,o)}function slideDown(t,e,o){j(t,e,o,!0)}function j(t,e,o,i){void 0===e&&(e=400),void 0===i&&(i=!1),t.style.overflow="hidden",i&&(t.style.display="block");var p,l=window.getComputedStyle(t),n=parseFloat(l.getPropertyValue("height")),a=parseFloat(l.getPropertyValue("padding-top")),s=parseFloat(l.getPropertyValue("padding-bottom")),r=parseFloat(l.getPropertyValue("margin-top")),d=parseFloat(l.getPropertyValue("margin-bottom")),g=n/e,y=a/e,m=s/e,u=r/e,h=d/e;window.requestAnimationFrame(function l(x){void 0===p&&(p=x);var f=x-p;i?(t.style.height=g*f+"px",t.style.paddingTop=y*f+"px",t.style.paddingBottom=m*f+"px",t.style.marginTop=u*f+"px",t.style.marginBottom=h*f+"px"):(t.style.height=n-g*f+"px",t.style.paddingTop=a-y*f+"px",t.style.paddingBottom=s-m*f+"px",t.style.marginTop=r-u*f+"px",t.style.marginBottom=d-h*f+"px"),f>=e?(t.style.height="",t.style.paddingTop="",t.style.paddingBottom="",t.style.marginTop="",t.style.marginBottom="",t.style.overflow="",i||(t.style.display="none"),"function"==typeof o&&o()):window.requestAnimationFrame(l)})}

    // jQuery function
    $('body').on('click', '.sidebar-item.has-sub .sidebar-link', function(e){
      e.preventDefault();
        
      let submenu = $(this).parent().find('.submenu');
      if( submenu.hasClass('active') ) submenu.css('display', 'block')

      if( submenu.css('display') == "none" ) submenu.addClass('active')
      else submenu.removeClass('active')
      slideToggle(submenu[0], 300)
    });

  }
})();
