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

        // vm.navRoutes = {};
        // $.each(states, function(i, v){
        //   if (v.settings) {
        //     let HasAccess = data.role.some( (element) => { return v.settings.roles.includes(element.value)});
        //     if (v.settings.parent &&  HasAccess) {
        //       if (vm.navRoutes[v.settings.parent] === undefined) {
        //         vm.navRoutes[v.settings.parent] = [];
        //       }

        //       vm.navRoutes[v.settings.parent].push(v);
        //     }
        //   }
          
        // });

        vm.navRoutes = states.filter(function(r) {
          return !!(r.settings && r.settings.parent && data.role.some((element) => {
            return r.settings.roles.includes(element.value);
          }));
        }).sort(function(r1, r2) {
          return r1.settings.parent - r2.settings.parent;
        });

      });
    }

    function logout() {
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

    // jQuery function
    // $('body').on('click', '.sidebar-item.has-sub .sidebar-link', function(e){
    //   e.preventDefault();
        
    //   let submenu = $(this).parent().find('.submenu');

    //   if (submenu.hasClass('active')) {
    //     submenu.removeClass('active');
    //     submenu.slideUp();
    //   } else {
    //     submenu.addClass('active');
    //     submenu.slideDown();
    //   }

    // });

  }
})();
