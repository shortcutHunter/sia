(function() {
  'use strict';

  angular
    .module('app.verifikasi_pmb')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'verifikasi_pmb',
        config: {
          url: '/verifikasi_pmb',
          templateUrl: '/template/verifikasi_pmb/table',
          controller: 'VerifikasiPmbController',
          controllerAs: 'vm',
          title: 'verifikasi_pmb',
          settings: {
            nav: 1,
            content: '<i class="fa fa-users"></i> <span>Verifikasi PMB</span>',
            parent: 'Kemahasiswaan',
            roles: ['panitia']
          }
        }
      },
      {
        state: 'verifikasi_pmb_form',
        config: {
          url: '/verifikasi_pmb/form/:dataId',
          templateUrl: '/template/verifikasi_pmb/form',
          controller: 'VerifikasiPmbFormController',
          controllerAs: 'vm',
          title: 'verifikasi_pmb'
        }
      },
      {
        state: 'verifikasi_pmb_detail',
        config: {
          url: '/verifikasi_pmb/{dataId}',
          templateUrl: '/template/verifikasi_pmb/detail',
          controller: 'VerifikasiPmbDetailController',
          controllerAs: 'vm',
          title: 'verifikasi_pmb'
        }
      }
    ];
  }
})();
