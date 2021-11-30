(function() {
  'use strict';

  angular
    .module('app.paket')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'paket',
        config: {
          url: '/paket',
          templateUrl: '/template/paket/table',
          controller: 'PaketController',
          controllerAs: 'vm',
          title: 'paket',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Paket</span>',
            parent: 'Keuangan',
            roles: ['admin', 'keuangan']
          }
        }
      },
      {
        state: 'paket_form',
        config: {
          url: '/paket/form/:dataId',
          templateUrl: '/template/paket/form',
          controller: 'PaketFormController',
          controllerAs: 'vm',
          title: 'paket_form'
        }
      },
      {
        state: 'paket_detail',
        config: {
          url: '/paket/{dataId}',
          templateUrl: '/template/paket/detail',
          controller: 'PaketDetailController',
          controllerAs: 'vm',
          title: 'paket_detail'
        }
      }
    ];
  }
})();
