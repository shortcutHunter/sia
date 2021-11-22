(function() {
  'use strict';

  angular
    .module('app.dosen_pa')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'dosen_pa',
        config: {
          url: '/dosen_pa',
          templateUrl: '/template/dosen_pa/table',
          controller: 'DosenPaController',
          controllerAs: 'vm',
          title: 'dosen_pa',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Dosen PA</span>',
            parent: 'Dosen'
          }
        }
      },
      {
        state: 'dosen_pa_form',
        config: {
          url: '/dosen_pa/form/:dataId',
          templateUrl: '/template/dosen_pa/form',
          controller: 'DosenPaFormController',
          controllerAs: 'vm',
          title: 'dosen_pa_form'
        }
      },
      {
        state: 'dosen_pa_detail',
        config: {
          url: '/dosen_pa/{dataId}',
          templateUrl: '/template/dosen_pa/detail',
          controller: 'DosenPaDetailController',
          controllerAs: 'vm',
          title: 'dosen_pa_detail'
        }
      }
    ];
  }
})();
