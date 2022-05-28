(function() {
  'use strict';

  angular
    .module('app.panitia')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'panitia',
        config: {
          url: '/panitia',
          templateUrl: '/template/panitia/table',
          controller: 'PanitiaController',
          controllerAs: 'vm',
          title: 'panitia',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Panitia</span>',
            parent: 'Konfigurasi',
            roles: ['admin']
          }
        }
      },
      {
        state: 'panitia_form',
        config: {
          url: '/panitia/form/:dataId',
          templateUrl: '/template/panitia/form',
          controller: 'PanitiaFormController',
          controllerAs: 'vm',
          title: 'panitia'
        }
      },
      {
        state: 'panitia_detail',
        config: {
          url: '/panitia/{dataId}',
          templateUrl: '/template/panitia/detail',
          controller: 'PanitiaDetailController',
          controllerAs: 'vm',
          title: 'panitia'
        }
      }
    ];
  }
})();
