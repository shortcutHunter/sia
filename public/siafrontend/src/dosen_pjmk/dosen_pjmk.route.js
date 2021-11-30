(function() {
  'use strict';

  angular
    .module('app.dosen_pjmk')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'dosen_pjmk',
        config: {
          url: '/dosen_pjmk',
          templateUrl: '/template/dosen_pjmk/table',
          controller: 'DosenPjmkController',
          controllerAs: 'vm',
          title: 'dosen_pjmk',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Dosen PJMK</span>',
            parent: 'Dosen',
            roles: ['admin', 'akademik']
          }
        }
      },
      {
        state: 'dosen_pjmk_form',
        config: {
          url: '/dosen_pjmk/form/:dataId',
          templateUrl: '/template/dosen_pjmk/form',
          controller: 'DosenPjmkFormController',
          controllerAs: 'vm',
          title: 'dosen_pjmk_form'
        }
      },
      {
        state: 'dosen_pjmk_detail',
        config: {
          url: '/dosen_pjmk/{dataId}',
          templateUrl: '/template/dosen_pjmk/detail',
          controller: 'DosenPjmkDetailController',
          controllerAs: 'vm',
          title: 'dosen_pjmk_detail'
        }
      }
    ];
  }
})();
