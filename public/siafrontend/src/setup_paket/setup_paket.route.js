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
            content: '<i class="fa fa-list"></i> <span>Setup Paket</span>',
            parent: 'Keuangan',
            roles: ['admin', 'keuangan']
          }
        }
      },
      {
        state: 'setup_paket_form',
        config: {
          url: '/setup_paket/form/:dataId',
          templateUrl: '/template/setup_paket/form',
          controller: 'SetupPaketFormController',
          controllerAs: 'vm',
          title: 'setup_paket_form'
        }
      },
      {
        state: 'setup_paket_detail',
        config: {
          url: '/setup_paket/{dataId}',
          templateUrl: '/template/setup_paket/detail',
          controller: 'SetupPaketDetailController',
          controllerAs: 'vm',
          title: 'setup_paket_detail'
        }
      },
      {
        state: 'setup_paket_tagihan',
        config: {
          url: '/setup_paket/tagihan/{dataId}',
          templateUrl: '/template/setup_paket/tagihan',
          controller: 'SetupPaketTagihanDetailController',
          controllerAs: 'vm',
          title: 'setup_paket_tagihan_detail'
        }
      }
    ];
  }
})();
