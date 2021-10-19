(function() {
  'use strict';

  angular
    .module('app.konfigurasi')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'konfigurasi',
        config: {
          url: '/konfigurasi',
          templateUrl: '/template/konfigurasi/detail',
          controller: 'KonfigurasiDetailController',
          controllerAs: 'vm',
          title: 'konfigurasi',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Konfigurasi</span>'
          }
        }
      },
      {
        state: 'konfigurasi_form',
        config: {
          url: '/konfigurasi/form',
          templateUrl: '/template/konfigurasi/form',
          controller: 'KonfigurasiFormController',
          controllerAs: 'vm',
          title: 'konfigurasi_form'
        }
      }
    ];
  }
})();
