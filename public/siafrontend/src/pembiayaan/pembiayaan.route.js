(function() {
  'use strict';

  angular
    .module('app.pembiayaan')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'pembiayaan',
        config: {
          url: '/pembiayaan',
          templateUrl: '/template/pembiayaan/table',
          controller: 'PembiayaanController',
          controllerAs: 'vm',
          title: 'pembiayaan',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Pembiayaan</span>',
            parent: 'Keuangan',
            roles: ['admin', 'keuangan']
          }
        }
      },
      {
        state: 'pembiayaan_form',
        config: {
          url: '/pembiayaan/form/:dataId',
          templateUrl: '/template/pembiayaan/form',
          controller: 'PembiayaanFormController',
          controllerAs: 'vm',
          title: 'pembiayaan_form'
        }
      },
      {
        state: 'pembiayaan_detail',
        config: {
          url: '/pembiayaan/{dataId}',
          templateUrl: '/template/pembiayaan/detail',
          controller: 'PembiayaanDetailController',
          controllerAs: 'vm',
          title: 'pembiayaan_detail'
        }
      }
    ];
  }
})();
