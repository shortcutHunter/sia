(function() {
  'use strict';

  angular
    .module('app.pembiayaan_lainnya')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'pembiayaan_lainnya',
        config: {
          url: '/pembiayaan_lainnya',
          templateUrl: '/template/pembiayaan_lainnya/table',
          controller: 'PembiayaanLainnyaController',
          controllerAs: 'vm',
          title: 'pembiayaan_lainnya',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Pembiayaan Lainnya</span>',
            parent: 'Konfigurasi',
            roles: ['admin']
          }
        }
      },
      {
        state: 'pembiayaan_lainnya_form',
        config: {
          url: '/pembiayaan_lainnya/form/:dataId',
          templateUrl: '/template/pembiayaan_lainnya/form',
          controller: 'PembiayaanLainnyaFormController',
          controllerAs: 'vm',
          title: 'pembiayaan_lainnya'
        }
      },
      {
        state: 'pembiayaan_lainnya_detail',
        config: {
          url: '/pembiayaan_lainnya/{dataId}',
          templateUrl: '/template/pembiayaan_lainnya/detail',
          controller: 'PembiayaanLainnyaDetailController',
          controllerAs: 'vm',
          title: 'pembiayaan_lainnya'
        }
      }
    ];
  }
})();
