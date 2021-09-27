(function() {
  'use strict';

  angular
    .module('app.tahun_ajaran')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'tahun_ajaran',
        config: {
          url: '/tahun_ajaran',
          templateUrl: '/template/tahun_ajaran/table',
          controller: 'TahunAjaranController',
          controllerAs: 'vm',
          title: 'tahun_ajaran',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Tahun Ajaran</span>'
          }
        }
      },
      {
        state: 'tahun_ajaran_form',
        config: {
          url: '/tahun_ajaran/form/:dataId',
          templateUrl: '/template/tahun_ajaran/form',
          controller: 'TahunAjaranFormController',
          controllerAs: 'vm',
          title: 'tahun_ajaran_form'
        }
      },
      {
        state: 'tahun_ajaran_detail',
        config: {
          url: '/tahun_ajaran/{dataId}',
          templateUrl: '/template/tahun_ajaran/detail',
          controller: 'TahunAjaranDetailController',
          controllerAs: 'vm',
          title: 'tahun_ajaran_detail'
        }
      }
    ];
  }
})();
