(function() {
  'use strict';

  angular
    .module('app.pendaftaran')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'pendaftaran',
        config: {
          url: '/pendaftaran',
          templateUrl: '/template/pendaftaran/table',
          controller: 'PendaftaranController',
          controllerAs: 'vm',
          title: 'pendaftaran',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>pendaftaran</span>',
            parent: 'Kemahasiswaan',
            roles: ['admin', 'akademik']
          }
        }
      },
      {
        state: 'pendaftaran_form',
        config: {
          url: '/pendaftaran/form/:dataId',
          templateUrl: '/template/pendaftaran/form',
          controller: 'PendaftaranFormController',
          controllerAs: 'vm',
          title: 'pendaftaran_form'
        }
      },
      {
        state: 'pendaftaran_detail',
        config: {
          url: '/pendaftaran/{dataId}',
          templateUrl: '/template/pendaftaran/detail',
          controller: 'PendaftaranDetailController',
          controllerAs: 'vm',
          title: 'pendaftaran_detail'
        }
      }
    ];
  }
})();
