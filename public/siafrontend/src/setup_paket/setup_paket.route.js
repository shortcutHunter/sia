(function() {
  'use strict';

  angular
    .module('app.setup_paket')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'setup_paket',
        config: {
          url: '/setup_paket',
          templateUrl: '/template/setup_paket/table',
          controller: 'SetupPaketController',
          controllerAs: 'vm',
          title: 'setup_paket',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Setup Paket</span>'
          }
        }
      }
    ];
  }
})();
